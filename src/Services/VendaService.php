<?php

namespace App\Services;

use App\Models\VenCabecalho;
use App\Models\VenItem;;

use App\Models\Produto;
use App\Models\Filial;
use App\Models\Participante;
use Doctrine\ORM\EntityManager;
use DomainException;
use Ramsey\Uuid\Uuid;

class VendaService
{
    public function __construct(private EntityManager $em) {}

    private function repo()
    {
        return $this->em->getRepository(VenCabecalho::class);
    }

    public function criarRascunho(array $dados): VenCabecalho
    {
        $this->em->beginTransaction();

        try {
            // 🔒 validações mínimas
            if (empty($dados['filial_id']) || empty($dados['participante_id'])) {
                throw new DomainException('Filial e cliente são obrigatórios.');
            }

            $filial = $this->em->find(Filial::class, $dados['filial_id']);
            $cliente = $this->em->find(Participante::class, $dados['participante_id']);

            if (!$filial || !$cliente) {
                throw new DomainException('Filial ou participante inválido.');
            }

            $venda = new VenCabecalho();
            $venda->setStatus(0); // DIGITAÇÃO
            $venda->setFilial($filial);
            $venda->setParticipante($cliente);
            $venda->setNaturezaOperacao($dados['natureza_operacao'] ?? 'VENDA');
            $venda->setTipoOperacao('SAIDA');
            $venda->setObservacoes($dados['observacoes'] ?? null);

            // 🧾 itens
            if (empty($dados['itens']) || !is_array($dados['itens'])) {
                throw new DomainException('Informe ao menos um item.');
            }

            $numeroItem = 1;
            foreach ($dados['itens'] as $itemData) {

                $produto = $this->em->find(Produto::class, $itemData['produto_id']);

                if (!$produto) {
                    throw new DomainException('Produto inválido.');
                }

                $item = new VenItem();
                $item->setProduto($produto);
                $item->setNumeroItem($numeroItem++);
                $item->setCfop($itemData['cfop']);
                $item->setQuantidade((float)$itemData['quantidade']);
                $item->setValorUnitario((float)$itemData['valor_unitario']);
                $item->setValorDesconto((float)($itemData['valor_desconto'] ?? 0));
                $item->calcularTotal();

                $venda->addItem($item);
            }

            $this->recalcularTotais($venda);

            $this->em->persist($venda);
            $this->em->flush();
            $this->em->commit();

            return $venda;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function editarRascunho(string $id, array $dados): VenCabecalho
    {
        $this->em->beginTransaction();

        try {
            $venda = $this->repo()->find($id);

            if (!$venda) {
                throw new DomainException('Venda não encontrada.');
            }

            if ($venda->getStatus() !== 0) {
                throw new DomainException('Nota não pode mais ser editada.');
            }

            // cabeçalho
            if (!empty($dados['participante_id'])) {
                $cliente = $this->em->find(Participante::class, $dados['participante_id']);
                $venda->setParticipante($cliente);
            }

            $venda->setObservacoes($dados['observacoes'] ?? null);

            // 🔁 remove itens antigos
            foreach ($venda->getItens() as $item) {
                $this->em->remove($item);
            }

            $this->em->flush(); // limpa orphanRemoval

            // recria itens
            $numeroItem = 1;
            foreach ($dados['itens'] as $itemData) {

                $produto = $this->em->find(Produto::class, $itemData['produto_id']);

                $item = new VenItem();
                $item->setProduto($produto);
                $item->setNumeroItem($numeroItem++);
                $item->setCfop($itemData['cfop']);
                $item->setQuantidade((float)$itemData['quantidade']);
                $item->setValorUnitario((float)$itemData['valor_unitario']);
                $item->setValorDesconto((float)($itemData['valor_desconto'] ?? 0));
                $item->calcularTotal();

                $venda->addItem($item);
            }

            $this->recalcularTotais($venda);

            $this->em->flush();
            $this->em->commit();

            return $venda;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    private function recalcularTotais(VenCabecalho $venda): void
    {
        $totalProdutos = 0;

        foreach ($venda->getItens() as $item) {
            $totalProdutos += $item->getValorTotal();
        }

        $venda->setValorProdutos($totalProdutos);
        $venda->setValorTotal(
            $totalProdutos
                + $venda->getValorFrete()
                - $venda->getValorDesconto()
        );
    }

    public function validarFiscal(string $vendaId): void
    {
        $venda = $this->repo()->find($vendaId);

        if (!$venda) {
            throw new DomainException('Venda não encontrada.');
        }

        if ($venda->getStatus() !== 0) {
            throw new DomainException('Somente notas em digitação podem ser validadas.');
        }

        // 1️⃣ Cabeçalho
        $filial = $venda->getFilial();
        $cliente = $venda->getParticipante();

        if (!$filial->getCnpj()) {
            throw new DomainException('Filial sem CNPJ.');
        }

        if (!$filial->getUf()) {
            throw new DomainException('Filial sem UF.');
        }

        if (!$cliente->getDocumento()) {
            throw new DomainException('Cliente sem CPF/CNPJ.');
        }

        if (!$cliente->getUf()) {
            throw new DomainException('Cliente sem UF.');
        }

        if (!in_array($venda->getModelo(), ['55', '65'])) {
            throw new DomainException('Modelo fiscal inválido.');
        }

        // 2️⃣ Itens
        if ($venda->getItens()->count() === 0) {
            throw new DomainException('Nota sem itens.');
        }

        foreach ($venda->getItens() as $item) {

            $produto = $item->getProduto();

            if (!$produto->getNcm()) {
                throw new DomainException(
                    "Produto {$produto->getNome()} sem NCM."
                );
            }

            if (!$item->getCfop() || strlen($item->getCfop()) !== 4) {
                throw new DomainException(
                    "CFOP inválido no item {$item->getNumeroItem()}."
                );
            }

            if ($item->getQuantidade() <= 0) {
                throw new DomainException(
                    "Quantidade inválida no item {$item->getNumeroItem()}."
                );
            }

            if ($item->getValorUnitario() <= 0) {
                throw new DomainException(
                    "Valor unitário inválido no item {$item->getNumeroItem()}."
                );
            }
        }

        // 3️⃣ Totais
        if ($venda->getValorTotal() <= 0) {
            throw new DomainException('Valor total da nota inválido.');
        }
    }

    public function numerar(string $vendaId): VenCabecalho
    {
        $this->em->beginTransaction();

        try {
            $venda = $this->repo()->find($vendaId);

            if (!$venda) {
                throw new DomainException('Venda não encontrada.');
            }

            if ($venda->getStatus() !== 0) {
                throw new DomainException('Nota não pode ser numerada.');
            }

            // 🔒 garante validação fiscal antes
            $this->validarFiscal($vendaId);

            $filial = $venda->getFilial();
            $modelo = $venda->getModelo();
            $serie  = $venda->getSerie();

            // 🔐 trava sequência
            $conn = $this->em->getConnection();

            $sql = "
            SELECT * FROM fis_numeracao
            WHERE filial_id = :filial
              AND modelo = :modelo
              AND serie = :serie
            FOR UPDATE
        ";

            $seq = $conn->fetchAssociative($sql, [
                'filial' => $filial->getId(),
                'modelo' => $modelo,
                'serie'  => $serie
            ]);

            if (!$seq) {
                // cria sequência se não existir
                $conn->insert('fis_numeracao', [
                    'id' => Uuid::uuid4()->toString(),
                    'filial_id' => $filial->getId(),
                    'modelo' => $modelo,
                    'serie' => $serie,
                    'ultimo_numero' => 0
                ]);

                $ultimoNumero = 0;
            } else {
                $ultimoNumero = (int)$seq['ultimo_numero'];
            }

            $proximoNumero = $ultimoNumero + 1;

            // atualiza sequência
            $conn->update(
                'fis_numeracao',
                ['ultimo_numero' => $proximoNumero],
                [
                    'filial_id' => $filial->getId(),
                    'modelo' => $modelo,
                    'serie' => $serie
                ]
            );

            // seta número na venda
            $venda->setNumero($proximoNumero);

            // gera chave
            $chave = $this->gerarChaveAcesso($venda);
            $venda->setChaveAcesso($chave);

            // trava a nota (pré-emissão)
            // ainda NÃO é autorizada
            // mas NÃO é mais editável
            $venda->setStatus(1); // 1 = NUMERADA / PRÉ-EMISSÃO

            $this->em->flush();
            $this->em->commit();

            return $venda;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
    private function gerarChaveAcesso(VenCabecalho $venda): string
    {
        $filial = $venda->getFilial();

        $uf   = 43;//$filial->getCodigoUf(); // ex: 43
        $ano  = date('y');
        $mes  = date('m');
        $cnpj = preg_replace('/\D/', '', $filial->getCnpj());
        $modelo = $venda->getModelo();
        $serie  = str_pad($venda->getSerie(), 3, '0', STR_PAD_LEFT);
        $numero = str_pad((string)$venda->getNumero(), 9, '0', STR_PAD_LEFT);
        $forma  = '1'; // normal
        $codigo = random_int(10000000, 99999999);

        $base = "{$uf}{$ano}{$mes}{$cnpj}{$modelo}{$serie}{$numero}{$forma}{$codigo}";

        return $base . $this->calcularDV($base);
    }

    private function calcularDV(string $chave): int
    {
        $peso = 2;
        $soma = 0;

        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += $chave[$i] * $peso;
            $peso = ($peso < 9) ? $peso + 1 : 2;
        }

        $resto = $soma % 11;
        return ($resto == 0 || $resto == 1) ? 0 : 11 - $resto;
    }
}
