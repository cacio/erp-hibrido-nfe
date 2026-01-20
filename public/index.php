<?php

// /public/index.php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

/**
 * URI normalizada (ANTES de qualquer uso)
 */
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

/**
 * 🔒 Proteção de instalação
 */
if (!file_exists(__DIR__ . '/../storage/installed.lock')) {
    if ($uri !== '/install') {
        header('Location: /install');
        exit;
    }
}
// 1. Inicializa o despachante de rotas do FastRoute
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    // --- Definição de Rotas ---
    // Sintaxe: $r->addRoute('MÉTODO_HTTP', '/sua-rota', 'SeuController@seuMetodo');

    // Rotas de Autenticação
    $r->addRoute('GET', '/login', 'AuthController@showLoginForm');
    $r->addRoute('POST', '/login', 'AuthController@login');
    $r->addRoute('GET', '/logout', 'AuthController@logout');
    $r->addRoute('POST', '/trocar-filial', 'AuthController@trocarFilial');

    // Rotas do Dashboard
    $r->addRoute('GET', '/dashboard', 'DashboardController@index');

    $r->addRoute('GET', '/selecionar-filial', 'FilialController@selecionarFilial');
    $r->addRoute('POST', '/confirmar-filial', 'FilialController@confirmarFilial');

    // Rota da Home (pode redirecionar ou ser a mesma do dashboard)
    $r->addRoute('GET', '/', 'DashboardController@index');

    $r->addRoute('GET', '/install', 'InstallController@index');
    $r->addRoute('POST', '/install', 'InstallController@store');

    $r->addRoute('GET',  '/admin/permissoes', 'AdminPermissionController@index');
    $r->addRoute('POST', '/admin/permissoes', 'AdminPermissionController@store');
    $r->addRoute('GET',  '/admin/permissoes/{id}/edit',         'AdminPermissionController@edit');
    $r->addRoute('POST', '/admin/permissoes/{id}/update',       'AdminPermissionController@update');
    $r->addRoute('GET',  '/admin/permissoes/{id}/delete',       'AdminPermissionController@delete');

    $r->addRoute('GET',  '/admin/roles', 'AdminRoleController@index');
    $r->addRoute('POST', '/admin/roles', 'AdminRoleController@store');
    $r->addRoute('GET',  '/admin/roles/{id}/edit',         'AdminRoleController@edit');
    $r->addRoute('POST', '/admin/roles/{id}/update',       'AdminRoleController@update');
    $r->addRoute('GET',  '/admin/roles/{id}/delete',       'AdminRoleController@delete');

    $r->addRoute('GET',  '/admin/usuarios',                'AdminUserController@index');
    $r->addRoute('POST', '/admin/usuarios',                'AdminUserController@store');
    $r->addRoute('GET',  '/admin/usuarios/{id}/edit',      'AdminUserController@edit');
    $r->addRoute('POST', '/admin/usuarios/{id}/update',    'AdminUserController@update');
    $r->addRoute('GET',  '/admin/usuarios/{id}/reset',     'AdminUserController@resetPassword');

    $r->addRoute('GET',  '/admin/usuarios/{id}/acesso',   'AdminUserAccessController@index');
    $r->addRoute('POST', '/admin/usuarios/acesso/salvar','AdminUserAccessController@save');

    $r->addRoute('GET',  '/admin/filiais/{filialId}/acessos',        'AdminFilialAccessController@index');
    $r->addRoute('POST', '/admin/filiais/acessos/adicionar',  'AdminFilialAccessController@addUsuario');
    $r->addRoute('POST', '/admin/filiais/acessos/remover',    'AdminFilialAccessController@removeUsuario');

    $r->addRoute('GET',  '/admin/filiais',                'AdminFilialController@index');
    $r->addRoute('POST', '/admin/filiais',                'AdminFilialController@store');
    $r->addRoute('GET',  '/admin/filiais/{id}/edit',      'AdminFilialController@edit');
    $r->addRoute('POST', '/admin/filiais/{id}/update',    'AdminFilialController@update');
    $r->addRoute('GET',  '/admin/filiais/{id}/delete',    'AdminFilialController@delete');

    // Gerenciar roles do usuário na filial (GET = tela)
    $r->addRoute('GET','/admin/filiais/{filialId}/usuarios/{userId}/roles','AdminFilialAccessController@roles');

    // Salvar roles do usuário na filial (POST)
    $r->addRoute('POST','/admin/filiais/{filialId}/usuarios/{userId}/roles','AdminFilialAccessController@saveRoles');

    // Adicionar usuário à filial
    $r->addRoute('POST','/admin/filiais/{filialId}/usuarios/adicionar','AdminFilialAccessController@addUser');

    // Remover usuário da filial
    $r->addRoute('GET','/admin/filiais/{filialId}/usuarios/{userId}/remover','AdminFilialAccessController@removeUser');

    $r->addRoute('GET',  '/participantes',             'ParticipanteController@index');
    $r->addRoute('POST', '/participantes',             'ParticipanteController@store');
    $r->addRoute('GET',  '/participantes/create',      'ParticipanteController@create');
    $r->addRoute('GET',  '/participantes/{id}/edit',   'ParticipanteController@edit');
    $r->addRoute('POST', '/participantes/{id}',        'ParticipanteController@update');
    $r->addRoute('GET',  '/participantes/buscar-doc',  'ParticipanteController@buscarDocumento');
    $r->addRoute('GET','/participantes/buscar-cnpj-externo', 'ParticipanteController@buscarCnpjExterno');
    $r->addRoute('GET','/enderecos/buscar-cep','ParticipanteController@buscarCep');


    $r->addRoute('GET',  '/produtos',                'ProdutoController@index');
    $r->addRoute('GET',  '/produtos/create',         'ProdutoController@create');
    $r->addRoute('POST', '/produtos',                'ProdutoController@store');
    $r->addRoute('GET',  '/produtos/{id}/edit',      'ProdutoController@edit');
    $r->addRoute('POST', '/produtos/{id}',           'ProdutoController@update');

    $r->addRoute('GET',  '/estoque/ajuste', 'AjusteEstoqueController@create');
    $r->addRoute('POST', '/estoque/ajuste', 'AjusteEstoqueController@store');

    $r->addRoute('GET', '/estoque/saldos', 'ConsultaSaldoController@index');
    $r->addRoute('GET', '/estoque/kardex', 'KardexController@index');

    $r->addRoute('POST','/api/sync/desk','Api\\SyncDeskController@receive');
    $r->addRoute('GET','/api/sync/ack','Api\\SyncAckController@status');

    $r->addRoute('POST', '/api/sync', 'SyncController@receive');

    $r->get('/api/sync/pull', 'Api\\SyncPullController@pull');
    $r->post('/api/sync/ack',   'Api\\SyncAckController@ack');

    $r->get('/sync', 'SyncDashboardController@index');
    $r->post('/sync/reprocessar', 'SyncDashboardController@reprocessar');
    $r->get('/sync/payload', 'SyncDashboardController@payload');


    // Exemplo de rota com parâmetro: buscar um usuário por ID
    // O {id:\d+} garante que o ID seja um ou mais dígitos numéricos
    $r->addRoute('GET', '/users/{id:\d+}', 'UserController@show');

    // Exemplo de agrupamento de rotas (ótimo para painel admin, API, etc.)
    $r->addGroup('/api', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/products', 'ApiController@listProducts');
        $r->addRoute('GET', '/products/{id:\d+}', 'ApiController@getProduct');
    });




});

// 2. Busca o método HTTP e a URI da requisição
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove a query string (ex: ?foo=bar ) da URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// 3. Despacha a rota
$routeInfo = $dispatcher->dispatch($httpMethod, $uri );

// 4. Trata o resultado do despacho
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... Lógica para erro 404
        http_response_code(404 );
        echo "Página não encontrada (404).";
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        // ... Lógica para erro 405
        $allowedMethods = $routeInfo[1];
        http_response_code(405 );
        echo "Método não permitido (405).";
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; // 'SeuController@seuMetodo'
        $vars = $routeInfo[2];    // Parâmetros da rota (ex: ['id' => '123'])

        list($controller, $method) = explode('@', $handler);
        $controller = "App\\Controllers\\{$controller}";

        if (class_exists($controller) && method_exists($controller, $method)) {
            $controllerInstance = new $controller();
            // Passa os parâmetros da rota para o método do controller
            call_user_func_array([$controllerInstance, $method], $vars);
        } else {
            http_response_code(500 );
            echo "Erro interno: Controller ou método não encontrado.";
        }
        break;
}
