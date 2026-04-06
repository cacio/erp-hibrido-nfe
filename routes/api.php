<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addGroup('/api', function (RouteCollector $r) {

        $r->addRoute('GET', '/participantes/search', 'Api\\ParticipanteSearchController@search');
        $r->addRoute('GET', '/planos/search', 'Api\\PlanoContaSearchController@search');

        $r->addRoute('POST', '/sync', 'SyncController@receive');
    });
};
