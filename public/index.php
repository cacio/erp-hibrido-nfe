<?php

declare(strict_types=1);

// /public/index.php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

if (file_exists(__DIR__ . '/config/version.php')) {
    require __DIR__ . '/config/version.php';
} else {
    define('APP_VERSION', 'dev');
}


use FastRoute\Dispatcher;
use App\Controllers\ErrorController;
use App\Core\Logger;

/**
 * =========================
 * MODO MANUTENÇÃO
 * =========================
 */
if (
    file_exists(__DIR__ . '/../storage/maintenance.lock')
    && !str_starts_with($_SERVER['REQUEST_URI'], '/api')
) {
    http_response_code(503);
    require __DIR__ . '/../views/errors/503.php';
    exit;
}

/**
 * =========================
 * Normalização da URI
 * =========================
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];

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

/**
 * =========================
 * Dispatch
 * =========================
 */

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

    // Rotas de Instalação
    $r->addRoute('GET', '/install', 'InstallController@index');
    $r->addRoute('POST', '/install', 'InstallController@store');

    /* Rotas de Administração */
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
    $r->addRoute('GET',  '/admin/filiais/{id}/nfe',       'FilialNfeController@edit');
    $r->addRoute('POST', '/admin/filiais/{id}/nfe',       'FilialNfeController@update');
    $r->addRoute('GET','/admin/filiais/{filialId}/usuarios/{userId}/roles','AdminFilialAccessController@roles');
    $r->addRoute('POST','/admin/filiais/{filialId}/usuarios/{userId}/roles','AdminFilialAccessController@saveRoles');
    $r->addRoute('POST','/admin/filiais/{filialId}/usuarios/adicionar','AdminFilialAccessController@addUser');
    $r->addRoute('GET','/admin/filiais/{filialId}/usuarios/{userId}/remover','AdminFilialAccessController@removeUser');

    /* Rotas de Particpante */
    $r->addRoute('GET',  '/participantes',             'ParticipanteController@index');
    $r->addRoute('POST', '/participantes',             'ParticipanteController@store');
    $r->addRoute('GET',  '/participantes/create',      'ParticipanteController@create');
    $r->addRoute('GET',  '/participantes/{id}/edit',   'ParticipanteController@edit');
    $r->addRoute('POST', '/participantes/{id}',        'ParticipanteController@update');
    $r->addRoute('GET',  '/participantes/buscar-doc',  'ParticipanteController@buscarDocumento');
    $r->addRoute('GET','/participantes/buscar-cnpj-externo', 'ParticipanteController@buscarCnpjExterno');
    $r->addRoute('GET','/enderecos/buscar-cep','ParticipanteController@buscarCep');

    /* Rotas de Produtos */
    $r->addRoute('GET',  '/produtos',                'ProdutoController@index');
    $r->addRoute('GET',  '/produtos/create',         'ProdutoController@create');
    $r->addRoute('POST', '/produtos',                'ProdutoController@store');
    $r->addRoute('GET',  '/produtos/{id}/edit',      'ProdutoController@edit');
    $r->addRoute('POST', '/produtos/{id}',           'ProdutoController@update');

    /* Rotas de Estoque */
    $r->addRoute('GET',  '/estoque/ajuste', 'AjusteEstoqueController@create');
    $r->addRoute('POST', '/estoque/ajuste', 'AjusteEstoqueController@store');
    $r->addRoute('GET', '/estoque/saldos', 'ConsultaSaldoController@index');
    $r->addRoute('GET', '/estoque/kardex', 'KardexController@index');

    /* Rotas de Sincronização via API */
    $r->addRoute('POST','/api/sync/desk','Api\\SyncDeskController@receive');
    $r->addRoute('GET','/api/sync/ack','Api\\SyncAckController@status');
    $r->addRoute('POST', '/api/sync', 'SyncController@receive');
    $r->get('/api/sync/pull', 'Api\\SyncPullController@pull');
    $r->post('/api/sync/ack',   'Api\\SyncAckController@ack');

    /* Rotas do Dashboard de Sincronização */
    $r->get('/sync', 'SyncDashboardController@index');
    $r->post('/sync/reprocessar', 'SyncDashboardController@reprocessar');
    $r->get('/sync/payload', 'SyncDashboardController@payload');

    /* Rotas de busca na API */
    $r->addRoute('GET', '/api/participantes/search', 'Api\\ParticipanteSearchController@search');
    $r->addRoute('GET', '/api/planos/search', 'Api\\PlanoContaSearchController@search');
    $r->addRoute('GET','/api/produtos/search','Api\\ProdutoSearchController@search');

    /*  Rotas de finaceiro */
    $r->addRoute('GET',  '/financeiro',        'FinanceiroController@index');
    $r->addRoute('GET',  '/financeiro/create', 'FinanceiroController@create');
    $r->addRoute('POST', '/financeiro',        'FinanceiroController@store');
    $r->addRoute('POST', '/financeiro/{id}/pagar',   'FinanceiroController@pagar');
    $r->addRoute('POST', '/financeiro/{id}/cancelar','FinanceiroController@cancelar');
    $r->addRoute('GET', '/financeiro/detalhes/{id}', 'FinanceiroController@detalhes');
    $r->addRoute('POST', '/financeiro/baixa/{id}', 'FinanceiroController@baixaRapida');
    $r->addRoute('POST', '/financeiro/baixa-lote', 'FinanceiroController@baixaLote');
    $r->addRoute('POST', '/financeiro/baixa-lote/preview', 'FinanceiroController@previewBaixaLote');
    $r->addRoute('GET',  '/financeiro/{id}/edit', 'FinanceiroController@edit');
    $r->addRoute('POST', '/financeiro/{id}/update', 'FinanceiroController@update');


    /* Rotas de Vendas */
    // View
    $r->addRoute('GET', '/nfe', 'NfeController@index');
    $r->addRoute('GET', '/nfe/create', 'NfeController@create');
    $r->addRoute('GET', '/nfe/{id}/edit', 'NfeController@edit');
    $r->addRoute('POST','/nfe/{id}/destinatario','NfeController@setDestinatario');
    $r->addRoute('GET', '/nfe/{id}/dados', 'NfeController@dados');
    $r->addRoute('POST', '/nfe/{id}/rascunho', 'NfeController@salvarRascunho');

    // Salvar rascunho
    $r->addRoute('POST', '/nfe', 'NfeController@store');

    // Itens
    $r->addRoute('POST', '/nfe/{vendaId}/itens', 'NfeItemController@store');
    $r->addRoute('PUT',  '/nfe/{id}/itens/{itemId}', 'NfeItemController@update');
    $r->addRoute('DELETE','/nfe/{id}/itens/{itemId}', 'NfeItemController@delete');
    $r->addRoute('POST','/vendas/{vendaId}/itens/{itemId}/calcular-impostos','VendaItemFiscalController@calcular');
    $r->addRoute('POST','/api/fiscal/preview','Api\\FiscalPreviewController@preview');
    //$r->addRoute('POST','/nfe/{vendaId}/itens','NfeItemController@store');

    //changelog
    $r->addRoute('GET', '/changelog', 'ChangeLogController@index');


    // Exemplo de rota com parâmetro: buscar um usuário por ID
    // O {id:\d+} garante que o ID seja um ou mais dígitos numéricos
    $r->addRoute('GET', '/users/{id:\d+}', 'UserController@show');

    // Exemplo de agrupamento de rotas (ótimo para painel admin, API, etc.)
    $r->addGroup('/api', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/products', 'ApiController@listProducts');
        $r->addRoute('GET', '/products/{id:\d+}', 'ApiController@getProduct');
    });





});

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * =========================
 * Tratamento de erros
 * =========================
 */
$errorController = new ErrorController();

switch ($routeInfo[0]) {

    case Dispatcher::NOT_FOUND:
        $errorController->notFound();
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $errorController->methodNotAllowed($routeInfo[1]);
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];

        [$controller, $method] = explode('@', $handler);
        $controller = "App\\Controllers\\{$controller}";

        try {
            if (!class_exists($controller) || !method_exists($controller, $method)) {
                throw new RuntimeException('Controller ou método não encontrado');
            }

            $instance = new $controller();
            call_user_func_array([$instance, $method], $vars);

        } catch (Throwable $e) {
            Logger::error($e);
            $errorController->internalError();
        }

        break;

    default:
        $errorController->internalError();
}
