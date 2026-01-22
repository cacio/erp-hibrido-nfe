<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    // Home / Dashboard
    $r->addRoute('GET', '/', 'DashboardController@index');
    $r->addRoute('GET', '/dashboard', 'DashboardController@index');

    // Auth
    $r->addRoute('GET', '/login', 'AuthController@showLoginForm');
    $r->addRoute('POST', '/login', 'AuthController@login');
    $r->addRoute('GET', '/logout', 'AuthController@logout');
    $r->addRoute('POST', '/trocar-filial', 'AuthController@trocarFilial');
    $r->addRoute('GET', '/selecionar-filial', 'FilialController@selecionarFilial');
    $r->addRoute('POST', '/confirmar-filial', 'FilialController@confirmarFilial');
    // Instalação
    $r->addRoute('GET', '/install', 'InstallController@index');
    $r->addRoute('POST', '/install', 'InstallController@store');
};
