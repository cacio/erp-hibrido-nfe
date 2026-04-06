<?php

namespace App\Sync;

use App\Models\Participante;
use App\Services\SyncMapService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ParticipanteSyncHandler implements SyncHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SyncMapService $map
    ) {}

    public function upsert(array $payload, SyncContext $ctx): void
    {
        // 1️⃣ Tenta resolver pelo sync_map (id_desk → id_web)
        $idWeb = $this->map->buscarPorDesk(
            $ctx->filialId,
            'cad_participantes',
            $ctx->idDesk
        );

        if ($idWeb) {
            $participante = $this->em->find(Participante::class, $idWeb);
        } else {
            // 2️⃣ Fallback: tenta achar por CPF/CNPJ dentro do tenant
            $participante = null;

            if (!empty($payload['cpf_cnpj'])) {
                $participante = $this->em->getRepository(Participante::class)
                    ->findOneBy([
                        'tenantId' => $payload['tenant_id'],
                        'cpfCnpj'  => $payload['cpf_cnpj'],
                    ]);
            }

            // 3️⃣ Se não achou, cria
            if (!$participante) {
                $participante = new Participante(
                    Uuid::uuid4()->toString(),
                    $payload['tenant_id']
                );
                $this->em->persist($participante);
            }
        }

        // 4️⃣ Mapeia dados
        $participante->setCpfCnpj($payload['cpf_cnpj'] ?? null);
        $participante->setNomeRazao($payload['nome_razao']);
        $participante->setNomeFantasia($payload['nome_fantasia'] ?? null);
      // if (array_key_exists('tipo_cadastro', $payload)) {
            $tipos = (array) ($payload['tipo_cadastro'] ?? []);
            $participante->setTipoCadastro($tipos);
      //  }


        $participante->setIndIeDest((int) ($payload['ind_iedest'] ?? 9));
        $participante->setIe($payload['ie'] ?? null);
        $participante->setTelefone($payload['telefone'] ?? null);
        $participante->setEmail($payload['email'] ?? null);
        $participante->setAtivo(
            (bool) ($payload['ativo'] ?? true)
        );

        // Endereços (JSON completo)
        if (!empty($payload['enderecos'])) {

            $participante->setEnderecoJson($this->normalizarEnderecos($payload['enderecos']));
        }

        $this->em->flush();

        // 5️⃣ Atualiza sync_map
        $this->map->salvar(
            $ctx->filialId,
            'cad_participantes',
            $participante->getId(),
            $ctx->idDesk
        );
    }

    public function delete(array $payload, SyncContext $ctx): void
    {
        $idWeb = $this->map->buscarPorDesk(
            $ctx->filialId,
            'cad_participantes',
            $ctx->idDesk
        );

        if (!$idWeb) {
            return;
        }

        $participante = $this->em->find(Participante::class, $idWeb);
        if ($participante) {
            $participante->setAtivo(false);
            $this->em->flush();
        }
    }

    // =========================
    // HELPERS
    // =========================


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
}
