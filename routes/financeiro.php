<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addGroup('/financeiro', function (RouteCollector $r) {

        $r->addRoute('GET', '', 'FinanceiroController@index');
        $r->addRoute('GET', '/create', 'FinanceiroController@create');
        $r->addRoute('POST', '', 'FinanceiroController@store');

        $r->addRoute('GET', '/detalhes/{id}', 'FinanceiroController@detalhes');
        $r->addRoute('POST', '/{id}/pagar', 'FinanceiroController@pagar');
        $r->addRoute('POST', '/{id}/cancelar', 'FinanceiroController@cancelar');

        $r->addRoute('POST', '/baixa/{id}', 'FinanceiroController@baixaRapida');
        $r->addRoute('POST', '/baixa-lote', 'FinanceiroController@baixaLote');
        $r->addRoute('POST', '/baixa-lote/preview', 'FinanceiroController@previewBaixaLote');
    });
};
