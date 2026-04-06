<?php

namespace App\Services;

use Doctrine\DBAL\Connection;

class FiscalDashboardService
{
    public function __construct(private Connection $conn) {}

    public function resumoPorFilial(string $filialId): array
    {
        return [
            'hoje'  => $this->hoje($filialId),
            'mes'   => $this->mes($filialId),
            'status'=> $this->status($filialId),
        ];
    }

    private function hoje(string $filialId): array
    {
        $sql = "
            SELECT
                COUNT(*) as total,
                COALESCE(SUM(valor_total),0) as valor
            FROM ven_cabecalho
            WHERE filial_id = :filial
              AND DATE(created_at) = CURDATE()
              AND status = '100'
        ";

        return $this->conn
            ->executeQuery($sql, ['filial' => $filialId])
            ->fetchAssociative();
    }

    private function mes(string $filialId): array
    {
        $sql = "
            SELECT
                COUNT(*) as total,
                COALESCE(SUM(valor_total),0) as valor
            FROM ven_cabecalho
            WHERE filial_id = :filial
              AND MONTH(created_at) = MONTH(CURDATE())
              AND YEAR(created_at) = YEAR(CURDATE())
              AND status = '100'
        ";

        return $this->conn
            ->executeQuery($sql, ['filial' => $filialId])
            ->fetchAssociative();
    }

    private function status(string $filialId): array
    {
        $sql = "
            SELECT status, COUNT(*) total
            FROM ven_cabecalho
            WHERE filial_id = :filial
            GROUP BY status
        ";

        $rows = $this->conn
            ->executeQuery($sql, ['filial' => $filialId])
            ->fetchAllAssociative();

        return array_column($rows, 'total', 'status');
    }
}
