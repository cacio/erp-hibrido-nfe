<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\Produto;
use Doctrine\ORM\EntityManagerInterface;

class ProdutoSearchController extends Controller
{
    private EntityManagerInterface $em;

    public function __construct()
    {
        $this->em = EntityManagerFactory::create();
    }
    public function search(): void
    {
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            $this->json([]);
            return;
        }

        $qb = $this->em->createQueryBuilder();

        $qb->select('p')
            ->from(Produto::class, 'p')
            ->where('p.ativo = 1')
            ->andWhere('(p.descricao LIKE :q OR p.codigoSku LIKE :q)')
            ->setParameter('q', "%{$q}%")
            ->setMaxResults(10);

        $produtos = $qb->getQuery()->getResult();

        $ret = [];

        foreach ($produtos as $p) {
            $ret[] = [
                'id'    => $p->getId(),
                'label' => $p->getDescricao(),
                'codigo' => $p->getCodigoSku(),
                'ncm'   => $p->getNcm(),
                'preco' => $p->getPrecoVenda()
            ];
        }

        $this->json($ret);
    }

    private function json(array $data, int $status = 200): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
    }
}
