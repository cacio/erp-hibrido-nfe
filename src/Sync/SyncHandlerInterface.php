<?php

namespace App\Sync;

interface SyncHandlerInterface
{
    public function upsert(array $payload, SyncContext $ctx): void;
    public function delete(array $payload, SyncContext $ctx): void;
}
