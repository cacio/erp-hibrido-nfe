<?php

namespace App\Services;

use App\Models\Produto;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ProdutoService
{

     private readonly SyncFilaService $syncFilaService;
     private readonly SyncMapService $syncMapService;

    public function __construct(
        private EntityManagerInterface $em
    ) {
        $this->syncFilaService = new SyncFilaService($em);
        $this->syncMapService = new SyncMapService($em);
    }

    // =========================
    // LISTAGEM SIMPLES (usada pelo paginado)
    // =========================
    public function listarPorTenant(string $tenantId): array
    {
        return $this->em->getRepository(Produto::class)
            ->findBy(
                ['tenantId' => $tenantId],
                ['descricao' => 'ASC']
            );
    }

    public function listarPaginado(
        string $tenantId,
        array $filtros,
        int $pagina = 1,
        int $limite = 20
    ): array {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Produto::class, 'p')
            ->where('p.tenantId = :tenant')
            ->setParameter('tenant', $tenantId);

        // 🔍 Busca por descrição / SKU / GTIN
        if (!empty($filtros['q'])) {
            $qb->andWhere(
                'p.descricao LIKE :q
             OR p.codigoSku LIKE :q
             OR p.gtinEan LIKE :q'
            )
                ->setParameter('q', '%' . $filtros['q'] . '%');
        }

        // 📦 Status
        if ($filtros['ativo'] !== '' && $filtros['ativo'] !== null) {
            $qb->andWhere('p.ativo = :ativo')
                ->setParameter('ativo', (bool) $filtros['ativo']);
        }

        // ⚖️ Unidade
        if (!empty($filtros['unidade'])) {
            $qb->andWhere('p.unidade = :unidade')
                ->setParameter('unidade', strtoupper($filtros['unidade']));
        }

        // 🔢 TOTAL
        $total = (clone $qb)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // 📄 PAGINADO
        $dados = $qb
            ->orderBy('p.descricao', 'ASC')
            ->setFirstResult(($pagina - 1) * $limite)
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();

        return [
            'dados' => $dados,
            'total' => (int) $total
        ];
    }


    // =========================
    // BUSCAS
    // =========================
    public function buscarPorId(string $tenantId, string $id): ?Produto
    {
        return $this->em->getRepository(Produto::class)
            ->findOneBy([
                'id'       => $id,
                'tenantId' => $tenantId
            ]);
    }

    public function buscarPorSku(string $tenantId, string $sku): ?Produto
    {
        return $this->em->getRepository(Produto::class)
            ->findOneBy([
                'tenantId'  => $tenantId,
                'codigoSku' => $sku
            ]);
    }

    // =========================
    // CRIAR
    // =========================
    public function criar(string $tenantId, array $dados): Produto
    {
        $this->validarDados($dados);

        if (!empty($dados['codigo_sku'])) {
            if ($this->buscarPorSku($tenantId, $dados['codigo_sku'])) {
                throw new \DomainException('Já existe produto com este código (SKU).');
            }
        }

        $produto = new Produto(
            Uuid::uuid4()->toString(),
            $tenantId
        );

        $this->mapearDados($produto, $dados);

        $this->em->persist($produto);
        $this->em->flush(); // 🔥 aqui o produto já existe no banco

        // ==================================================
        // 🔁 SYNC WEB → DESK (APÓS FLUSH)
        // ==================================================
        $filialId = $_SESSION['auth']['filial_id'];

        $payload = [
            'id'          => $produto->getId(),
            'descricao'   => $produto->getDescricao(),
            'codigo_sku'  => $produto->getCodigoSku(),
            'ncm'         => $produto->getNcm(),
            'unidade'     => $produto->getUnidade(),
            'ativo'       => $produto->isAtivo(),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->syncFilaService->criar(
            $filialId,          // 🔑 FILIAL
            'cad_produtos',     // tabela
            $produto->getId(),  // id_web
            '0',                // id_desk (0 = ainda não existe no desktop)
            'UPSERT',
            'WEB_TO_DESK',
            $payload
        );

        return $produto;
    }


    // =========================
    // ATUALIZAR
    // =========================
    public function atualizar(Produto $produto, array $dados): Produto
    {
        $this->validarDados($dados, $produto);

        if (
            !empty($dados['codigo_sku']) &&
            $dados['codigo_sku'] !== $produto->getCodigoSku()
        ) {
            if ($this->buscarPorSku($produto->getTenantId(), $dados['codigo_sku'])) {
                throw new \DomainException('Código SKU já utilizado por outro produto.');
            }
        }

        // 1️⃣ Atualiza entidade
        $this->mapearDados($produto, $dados);

        // 2️⃣ Persiste
        $this->em->flush();

        // ==================================================
        // 🔁 SYNC WEB → DESK (APÓS FLUSH)
        // ==================================================
        $filialId = $_SESSION['auth']['filial_id'];

        // tenta resolver ID Desk
        $idDesk = $this->syncMapService->buscarPorWeb(
            $filialId,
            'cad_produtos',
            $produto->getId()
        ) ?? '0';

        $payload = [
            'id'          => $produto->getId(),
            'descricao'   => $produto->getDescricao(),
            'codigo_sku'  => $produto->getCodigoSku(),
            'ncm'         => $produto->getNcm(),
            'unidade'     => $produto->getUnidade(),
            'ativo'       => $produto->isAtivo(),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->syncFilaService->criar(
            $filialId,          // 🔑 escopo da filial
            'cad_produtos',
            $produto->getId(),  // id_web
            $idDesk,            // id_desk (ou 0)
            'UPSERT',
            'WEB_TO_DESK',
            $payload
        );

        return $produto;
    }


    // =========================
    // VALIDAÇÕES
    // =========================
    private function validarDados(array $dados, ?Produto $produto = null): void
    {
        // DESCRIÇÃO
        $descricao = trim(
            $dados['descricao']
                ?? $produto?->getDescricao()
                ?? ''
        );

        if ($descricao === '') {
            throw new \DomainException('Descrição do produto é obrigatória.');
        }

        // SKU (opcional, mas se vier precisa ser válido)
        if (!empty($dados['codigo_sku']) && strlen($dados['codigo_sku']) > 50) {
            throw new \DomainException('Código SKU inválido.');
        }

        // GTIN / EAN
        if (!empty($dados['gtin_ean'])) {
            $gtin = preg_replace('/\D/', '', $dados['gtin_ean']);
            if (!in_array(strlen($gtin), [8, 12, 13, 14])) {
                throw new \DomainException('GTIN/EAN inválido.');
            }
        }

        // NCM
        if (!empty($dados['ncm'])) {
            $ncm = preg_replace('/\D/', '', $dados['ncm']);
            if (strlen($ncm) !== 8) {
                throw new \DomainException('NCM deve conter 8 dígitos.');
            }
        }

        // UNIDADE
        $unidade = strtoupper(
            $dados['unidade']
                ?? $produto?->getUnidade()
                ?? 'UN'
        );

        if (!in_array($unidade, ['UN', 'KG', 'PC', 'CX'])) {
            throw new \DomainException('Unidade inválida.');
        }

        // PESOS
        foreach (['peso_liquido', 'peso_bruto'] as $campo) {
            if (isset($dados[$campo]) && $dados[$campo] !== '') {
                if (!is_numeric($dados[$campo]) || $dados[$campo] < 0) {
                    throw new \DomainException("{$campo} inválido.");
                }
            }
        }

        // PREÇOS
        foreach (['preco_venda', 'preco_custo'] as $campo) {
            if (isset($dados[$campo]) && $dados[$campo] !== '') {
                if (!is_numeric($dados[$campo]) || $dados[$campo] < 0) {
                    throw new \DomainException("{$campo} inválido.");
                }
            }
        }
    }

    // =========================
    // MAPEAR DADOS
    // =========================
    private function mapearDados(Produto $p, array $dados): void
    {
        if (isset($dados['descricao'])) {
            $p->setDescricao(trim($dados['descricao']));
        }

        if (array_key_exists('codigo_sku', $dados)) {
            $p->setCodigoSku(
                $dados['codigo_sku'] !== ''
                    ? trim($dados['codigo_sku'])
                    : null
            );
        }

        if (array_key_exists('gtin_ean', $dados)) {
            $p->setGtinEan(
                $dados['gtin_ean'] !== ''
                    ? preg_replace('/\D/', '', $dados['gtin_ean'])
                    : null
            );
        }

        if (array_key_exists('ncm', $dados)) {
            $p->setNcm(
                $dados['ncm'] !== ''
                    ? preg_replace('/\D/', '', $dados['ncm'])
                    : null
            );
        }

        if (array_key_exists('cest', $dados)) {
            $p->setCest(
                $dados['cest'] !== ''
                    ? preg_replace('/\D/', '', $dados['cest'])
                    : null
            );
        }

        if (isset($dados['unidade'])) {
            $p->setUnidade(strtoupper($dados['unidade']));
        }

        if (isset($dados['tipo_item_sped'])) {
            $p->setTipoItemSped($dados['tipo_item_sped']);
        }

        if (array_key_exists('peso_liquido', $dados)) {
            $p->setPesoLiquido((float) ($dados['peso_liquido'] ?? 0));
        }

        if (array_key_exists('peso_bruto', $dados)) {
            $p->setPesoBruto((float) ($dados['peso_bruto'] ?? 0));
        }

        if (array_key_exists('preco_venda', $dados)) {
            $p->setPrecoVenda((float) ($dados['preco_venda'] ?? 0));
        }

        if (array_key_exists('preco_custo', $dados)) {
            $p->setPrecoCusto((float) ($dados['preco_custo'] ?? 0));
        }

        if (array_key_exists('ativo', $dados)) {
            $p->setAtivo((bool) $dados['ativo']);
        }
    }
}
