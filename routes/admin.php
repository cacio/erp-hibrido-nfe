<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addGroup('/admin', function (RouteCollector $r) {

        $r->addRoute('GET', '/usuarios', 'AdminUserController@index');
        $r->addRoute('POST', '/usuarios', 'AdminUserController@store');
        $r->addRoute('GET', '/usuarios/{id}/edit', 'AdminUserController@edit');

        $r->addRoute('GET', '/roles', 'AdminRoleController@index');
        $r->addRoute('POST', '/roles', 'AdminRoleController@store');

        $r->addRoute('GET',  '/permissoes', 'AdminPermissionController@index');
        $r->addRoute('POST', '/admin/permissoes', 'AdminPermissionController@store');
        $r->addRoute('GET',  '/admin/permissoes/{id}/edit',         'AdminPermissionController@edit');
        $r->addRoute('POST', '/admin/permissoes/{id}/update',       'AdminPermissionController@update');
        $r->addRoute('GET',  '/admin/permissoes/{id}/delete',       'AdminPermissionController@delete');


        $r->addRoute('GET',  '/admin/roles/{id}/edit',         'AdminRoleController@edit');
        $r->addRoute('POST', '/admin/roles/{id}/update',       'AdminRoleController@update');
        $r->addRoute('GET',  '/admin/roles/{id}/delete',       'AdminRoleController@delete');

        $r->addRoute('GET',  '/admin/usuarios',                'AdminUserController@index');
        $r->addRoute('POST', '/admin/usuarios',                'AdminUserController@store');
        $r->addRoute('GET',  '/admin/usuarios/{id}/edit',      'AdminUserController@edit');
        $r->addRoute('POST', '/admin/usuarios/{id}/update',    'AdminUserController@update');
        $r->addRoute('GET',  '/admin/usuarios/{id}/reset',     'AdminUserController@resetPassword');

        $r->addRoute('GET',  '/admin/usuarios/{id}/acesso',   'AdminUserAccessController@index');
        $r->addRoute('POST', '/admin/usuarios/acesso/salvar', 'AdminUserAccessController@save');

        $r->addRoute('GET',  '/admin/filiais/{filialId}/acessos',        'AdminFilialAccessController@index');
        $r->addRoute('POST', '/admin/filiais/acessos/adicionar',  'AdminFilialAccessController@addUsuario');
        $r->addRoute('POST', '/admin/filiais/acessos/remover',    'AdminFilialAccessController@removeUsuario');

        $r->addRoute('GET',  '/admin/filiais',                'AdminFilialController@index');
        $r->addRoute('POST', '/admin/filiais',                'AdminFilialController@store');
        $r->addRoute('GET',  '/admin/filiais/{id}/edit',      'AdminFilialController@edit');
        $r->addRoute('POST', '/admin/filiais/{id}/update',    'AdminFilialController@update');
        $r->addRoute('GET',  '/admin/filiais/{id}/delete',    'AdminFilialController@delete');
        $r->addRoute('GET',  '/admin/filiais/{id}/nfe',       'FilialNfeController@edit');
        $r->addRoute('POST', '/admin/filiais/{id}/nfe',       'FilialNfeController@update');
        // Gerenciar roles do usuário na filial (GET = tela)
        $r->addRoute('GET', '/admin/filiais/{filialId}/usuarios/{userId}/roles', 'AdminFilialAccessController@roles');

        // Salvar roles do usuário na filial (POST)
        $r->addRoute('POST', '/admin/filiais/{filialId}/usuarios/{userId}/roles', 'AdminFilialAccessController@saveRoles');

        // Adicionar usuário à filial
        $r->addRoute('POST', '/admin/filiais/{filialId}/usuarios/adicionar', 'AdminFilialAccessController@addUser');

        // Remover usuário da filial
        $r->addRoute('GET', '/admin/filiais/{filialId}/usuarios/{userId}/remover', 'AdminFilialAccessController@removeUser');
    });
};
