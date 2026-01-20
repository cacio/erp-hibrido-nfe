<?php

return [
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'icon'  => '🏠',
        'permission' => 'dashboard.view',
        'children' => [
            [
                'icon' => '🏠',
                'label' => 'Visão Geral',
                'route' => '/dashboard',
                'permission' => 'dashboard.view',
            ],
            [
                'icon' => '📊',
                'label' => 'Relatórios',
                'route' => '/dashboard/relatorios',
                'permission' => 'dashboard.reports',
            ],
        ],
    ],

    [
        'id' => 'cadastros',
        'label' => 'Cadastros',
        'icon'  => '📦',
        'children' => [

            // 🔸 GRUPO: ADMINISTRAÇÃO
            [
                'group' => 'Administração',
                'items' => [
                    [
                        'icon' => '👤',
                        'label' => 'Usuários',
                        'route' => '/admin/usuarios',
                        'permission' => 'admin.users',
                    ],
                    [
                        'icon' => '⚙️',
                        'label' => 'Roles',
                        'route' => '/admin/roles',
                        'permission' => 'admin.roles',
                    ],
                    [
                        'icon' => '🔐',
                        'label' => 'Permissões',
                        'route' => '/admin/permissoes',
                        'permission' => 'admin.permissions',
                    ],
                    [
                        'icon' => '👥',
                        'label' => 'Participantes',
                        'route' => '/participantes',
                        'permission' => 'cadastro.participantes.view',
                    ],
                    [
                        'icon' => '📦',
                        'label' => 'Produtos',
                        'route' => '/produtos',
                        'permission' => 'cadastro.produto.view',
                    ]
                ],
            ],

            // 🔸 GRUPO: ESTRUTURA
            [
                'group' => 'Estrutura',
                'items' => [
                    [
                        'icon' => '🏢',
                        'label' => 'Filiais',
                        'route' => '/admin/filiais',
                        'permission' => 'admin.filiais',
                    ],
                ],
            ],
        ],
    ],

    [
        'id' => 'estoque',
        'label' => 'Estoque',
        'icon'  => '🏷️',
        'children' => [

            [
                'group' => 'Movimentação',
                'items' => [
                    [
                        'icon' => '🛠️',
                        'label' => 'Ajuste de Estoque',
                        'route' => '/estoque/ajuste',
                        'permission' => 'ajustar_estoque.create',
                    ],
                ],
            ],

            [
                'group' => 'Consultas',
                'items' => [
                    [
                        'icon' => '📊',
                        'label' => 'Saldo por Produto',
                        'route' => '/estoque/saldos',
                        'permission' => 'estoque.saldo.view',
                    ],
                    [
                        'icon' => '🏷️',
                        'label' => 'Lotes',
                        'route' => '/estoque/lotes',
                        'permission' => 'estoque.lotes.view',
                    ],
                    [
                        'icon' => '🧾',
                        'label' => 'Kardex',
                        'route' => '/estoque/kardex',
                        'permission' => 'estoque.kardex.view',
                    ],
                ],
            ],
        ],
    ],
    [
        'id' => 'sincronizacao',
        'icon' => '🔄',
        'label' => 'Sincronização',
        'route' => '/sync',
        'permission' => 'sync.view',
        'children' => [
            [
                'icon' => '⚙️',
                'label' => 'Listagem',
                'route' => '/sync',
                'permission' => 'sync.view',
            ],
            [
                'icon' => '📈',
                'label' => 'Status',
                'route' => '/sync/status',
                'permission' => 'sync.status.view',
            ],
        ],
    ],
    [
        'id' => 'auditoria',
        'label' => 'Auditoria',
        'icon'  => '🧾',
        'route' => '/admin/auditoria',
        'permission' => 'admin.audit',
    ],
];
