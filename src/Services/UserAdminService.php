<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\User;
use App\Models\Filial;
use App\Models\Role;
use App\Models\Tenant;
use Ramsey\Uuid\Uuid;

class UserAdminService
{
    public function all(): array
    {
        return EntityManagerFactory::create()
            ->getRepository(User::class)
            ->findBy([], ['nome' => 'ASC']);
    }

    public function find(string $id): ?User
    {
        return EntityManagerFactory::create()->find(User::class, $id);
    }

    public function create(array $data): void
    {
        if (empty($data['filiais'])) {
            throw new \Exception('Selecione ao menos uma filial');
        }
        $em = EntityManagerFactory::create();

        $user = new User();
        $user->setId(Uuid::uuid4()->toString());
        $user->setNome($data['nome']);
        $user->setEmail($data['email']);
        $user->setSenhaHash(password_hash($data['senha'], PASSWORD_DEFAULT));
        $user->setAtivo(true);
        $user->setTenant($em->getReference(Tenant::class, $_SESSION['auth']['tenant_id']));

        foreach ($data['filiais'] ?? [] as $fid) {
            $user->getFiliais()->add($em->getReference(Filial::class, $fid));
        }

        $em->persist($user);
        $em->flush();

         // 🔗 vínculo usuário ↔ filial
        foreach ($data['filiais'] as $filialId) {
            $em->getConnection()->insert('sis_users_filiais', [
                'user_id'   => $user->getId(),
                'filial_id' => $filialId,
            ]);
        }
    }

    public function update(string $id, array $data): void
    {

        if (empty($data['filiais'])) {
            throw new \Exception('Selecione ao menos uma filial');
        }
        $em = EntityManagerFactory::create();
        $user = $em->find(User::class, $id);

        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        $user->setNome($data['nome']);
        $user->setEmail($data['email']);
        $user->setAtivo(isset($data['ativo']));

        // limpa vínculos atuais
        $em->getConnection()->executeStatement(
            'DELETE FROM sis_users_filiais WHERE user_id = ?',
            [$id]
        );

        // recria vínculos
        foreach ($data['filiais'] as $filialId) {
            $em->getConnection()->insert('sis_users_filiais', [
                'user_id'   => $id,
                'filial_id' => $filialId,
            ]);
        }

        $em->flush();
    }

    public function resetPassword(string $id): void
    {
        $em = EntityManagerFactory::create();
        $user = $em->find(User::class, $id);

        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        $user->setSenhaHash(password_hash('123456', PASSWORD_DEFAULT));
        $em->flush();
    }
}
