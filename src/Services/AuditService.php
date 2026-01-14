<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\AuditLog;
use Ramsey\Uuid\Uuid;

class AuditService
{
    public static function log(
        string $action,
        string $entity,
        ?string $entityId,
        string $description
    ): void {
        $em = EntityManagerFactory::create();

        $log = new AuditLog();
        $log->setId(Uuid::uuid4()->toString());
        $log->setTenantId($_SESSION['auth']['tenant_id']);
        $log->setUserId($_SESSION['auth']['user_id'] ?? null);
        $log->setAction($action);
        $log->setEntity($entity);
        $log->setEntityId($entityId);
        $log->setDescription($description);
        $log->setIpAddress($_SERVER['REMOTE_ADDR'] ?? 'cli');
        $log->setCreatedAt(new \DateTime());

        $em->persist($log);
        $em->flush();
    }
}
