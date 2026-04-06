<?php
// App\Services\PermissionAdminService.php
namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\Permission;
use Ramsey\Uuid\Uuid;

class PermissionAdminService
{
    public function all(): array
    {
        return EntityManagerFactory::create()
            ->getRepository(Permission::class)
            ->findBy([], ['nome' => 'ASC']);
    }

    public function find(string $id): ?Permission
    {
        return EntityManagerFactory::create()
            ->find(Permission::class, $id);
    }

    public function create(string $nome, ?string $descricao): void
    {
        $em = EntityManagerFactory::create();

        if ($em->getRepository(Permission::class)->findOneBy(['nome' => $nome])) {
            throw new \Exception('Permissão já existe');
        }

        $permission = new Permission();
        $permission->setId(Uuid::uuid4()->toString());
        $permission->setNome($nome);
        $permission->setDescricao($descricao);

        try {
            $em->persist($permission);
            $em->flush();
        } catch (\Exception $e) {
            echo "MENSAGEM:\n";
            var_dump($e->getMessage());

            echo "\nCLASSE DO ERRO:\n";
            var_dump(get_class($e));

            echo "\nTRACE:\n";
            var_dump($e->getTraceAsString());

            echo '</pre>';
        }


    }

    public function update(string $id, string $nome, ?string $descricao): void
    {
        $em = EntityManagerFactory::create();

        $permission = $em->find(Permission::class, $id);

        if (!$permission) {
            throw new \Exception('Permissão não encontrada');
        }

        $permission->setNome($nome);
        $permission->setDescricao($descricao);

        $em->flush();
    }

    public function delete(string $id): void
    {
        $em = EntityManagerFactory::create();

        $permission = $em->find(Permission::class, $id);

        if (!$permission) {
            throw new \Exception('Permissão não encontrada');
        }

        // Proteção básica: não deletar permissões críticas
        if (str_starts_with($permission->getNome(), 'admin.')) {
            throw new \Exception('Permissões administrativas não podem ser removidas');
        }

        $em->remove($permission);
        $em->flush();
    }
}

?>