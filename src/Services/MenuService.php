<?php

namespace App\Services;

class MenuService
{
    public static function getMenu(): array
    {
        $menu = require __DIR__ . '/../../config/menu.php';
        $permission = new PermissionService();

        return self::filterMenu($menu, $permission);
    }

    private static function filterMenu(array $items, PermissionService $permission): array
    {
        $filtered = [];

        foreach ($items as $item) {
            // item simples
            if (isset($item['permission']) && !$permission->can($item['permission'])) {
                continue;
            }

            // submenu
            if (isset($item['children'])) {
                $item['children'] = self::filterMenu($item['children'], $permission);

                if (empty($item['children'])) {
                    continue;
                }
            }

            $filtered[] = $item;
        }

        return $filtered;
    }
}
