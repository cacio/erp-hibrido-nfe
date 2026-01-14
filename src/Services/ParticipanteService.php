<?php

namespace App\Services;

use App\Models\Participante;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ParticipanteService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // =========================
    // BUSCAS
    // =========================

    public function buscarPorDocumento(
        string $tenantId,
        string $cpfCnpj
    ): ?Participante {
        $doc = $this->normalizarDocumento($cpfCnpj);

        return $this->em->getRepository(Participante::class)
            ->findOneBy([
                'tenantId' => $tenantId,
                'cpfCnpj'  => $doc
            ]);
    }

    public function buscarPaginado(
        string $tenantId,
        array $filtros = [],
        int $pagina = 1,
        int $limite = 20
    ): array {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Participante::class, 'p')
            ->where('p.tenantId = :tenant')
            ->setParameter('tenant', $tenantId)
            ->orderBy('p.nomeRazao', 'ASC');

        if (!empty($filtros['q'])) {
            $qb->andWhere(
                'p.nomeRazao LIKE :q OR p.nomeFantasia LIKE :q OR p.cpfCnpj LIKE :q'
            )->setParameter('q', '%' . $filtros['q'] . '%');
        }

        if (!empty($filtros['tipo'])) {
            $qb->andWhere('p.tipoCadastro LIKE :tipo')
                ->setParameter('tipo', '%' . $filtros['tipo'] . '%');
        }

        if(isset($filtros['ativo'])) {
            $qb->andWhere('p.ativo = :ativo')
                ->setParameter('ativo', (bool) $filtros['ativo']);
        }


        $qb->setFirstResult(($pagina - 1) * $limite)
            ->setMaxResults($limite);

        $paginator = new Paginator($qb);

        return [
            'dados'      => iterator_to_array($paginator),
            'total'      => count($paginator),
            'pagina'     => $pagina,
            'limite'     => $limite,
            'totalPages' => (int) ceil(count($paginator) / $limite),
        ];
    }

    // =========================
    // CRIAÇÃO
    // =========================

    public function criar(
        string $tenantId,
        array $dados
    ): Participante {

        if (!empty($dados['cpf_cnpj'])) {
            $existente = $this->buscarPorDocumento(
                $tenantId,
                $dados['cpf_cnpj']
            );

            if ($existente) {
                throw new \DomainException(
                    'Já existe um participante com este CPF/CNPJ.'
                );
            }
        }

        $participante = new Participante(
            Uuid::uuid4()->toString(),
            $tenantId
        );

        $this->mapearDados($participante, $dados);

        $this->em->persist($participante);
        $this->em->flush();

        return $participante;
    }

    // =========================
    // ATUALIZAÇÃO
    // =========================

    public function atualizar(
        Participante $participante,
        array $dados
    ): Participante {

        if (!empty($dados['cpf_cnpj'])) {
            $doc = $this->normalizarDocumento($dados['cpf_cnpj']);

            if ($doc !== $participante->getCpfCnpj()) {
                $existente = $this->buscarPorDocumento(
                    $participante->getTenantId(),
                    $doc
                );

                if ($existente) {
                    throw new \DomainException(
                        'CPF/CNPJ já utilizado por outro participante.'
                    );
                }
            }
        }

        $this->mapearDados($participante, $dados);

        $this->em->flush();

        return $participante;
    }

    // =========================
    // MAPEAR DADOS
    // =========================

    private function mapearDados(
        Participante $p,
        array $dados
    ): void {

        if (isset($dados['cpf_cnpj'])) {
            $p->setCpfCnpj($dados['cpf_cnpj']);
        }

        if (!empty($dados['nome_razao'])) {
            $p->setNomeRazao($dados['nome_razao']);
        }

        if (array_key_exists('nome_fantasia', $dados)) {
            $p->setNomeFantasia($dados['nome_fantasia']);
        }

        if (!empty($dados['tipo_cadastro'])) {
            $p->setTipoCadastro((array) $dados['tipo_cadastro']);
        }

        if (isset($dados['ind_iedest'])) {
            $p->setIndIeDest((int) $dados['ind_iedest']);
        }

        if (array_key_exists('ie', $dados)) {
            $p->setIe($dados['ie']);
        }

        if (array_key_exists('telefone', $dados)) {
            $p->setTelefone($dados['telefone']);
        }

        if (array_key_exists('email', $dados)) {
            $p->setEmail($dados['email']);
        }

        if (array_key_exists('ativo', $dados)) {
            $p->setAtivo((bool) $dados['ativo']);
        }

        if (!empty($dados['enderecos'])) {
            $p->setEnderecoJson(
                $this->normalizarEnderecos($dados['enderecos'])
            );
        }
    }

    // =========================
    // HELPERS
    // =========================

    private function normalizarDocumento(string $doc): string
    {
        return preg_replace('/\D/', '', $doc);
    }

    private function normalizarEnderecos(array $enderecos): array
    {
        $resultado = [];

        foreach ($enderecos as $tipo => $endereco) {
            $resultado[$tipo] = [
                'logradouro'    => $endereco['logradouro'] ?? '',
                'numero'        => $endereco['numero'] ?? '',
                'complemento'   => $endereco['complemento'] ?? '',
                'bairro'        => $endereco['bairro'] ?? '',
                'cep'           => preg_replace('/\D/', '', $endereco['cep'] ?? ''),
                'municipio'     => $endereco['municipio'] ?? '',
                'cod_municipio' => $endereco['cod_municipio'] ?? '',
                'uf'            => $endereco['uf'] ?? '',
                'pais'          => $endereco['pais'] ?? '1058'
            ];
        }

        return $resultado;
    }

    public function listarPorTenant(string $tenantId): array
    {
        return $this->em->getRepository(Participante::class)
            ->findBy(
                ['tenantId' => $tenantId],
                ['nomeRazao' => 'ASC']
            );
    }

    public function buscarPorId(
        string $tenantId,
        string $id
    ): ?Participante {
        return $this->em->getRepository(Participante::class)
            ->findOneBy([
                'id'       => $id,
                'tenantId' => $tenantId
            ]);
    }
}
