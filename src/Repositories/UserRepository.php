<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use App\Models\User;

class UserRepository extends EntityRepository
{
    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
        ->leftJoin('u.tenant', 't')
        ->leftJoin('u.filiais', 'f')
        ->addSelect('t', 'f')
        ->where('u.email = :email')
        ->setParameter('email', $email)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
