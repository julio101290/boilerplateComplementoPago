<?php

$routes->group('admin', function ($routes) {


    $routes->resource('listCompPag', [
        'filter' => 'permission:listaPagos-permission',
        'controller' => 'PagosController',
        'except' => 'show',
        'namespace' => 'julio101290\boilerplatecomplementopago\Controllers',
    ]);
    
    $routes->get('newPago'
            , 'PagosController::newPago'
            , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
        );

    $routes->get('pagos/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'
                , 'PagosController::PagosFilters/$1/$2/$3/$4/$5/$6'
                , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
                );
    $routes->get('editPago/(:any)'
                , 'PagosController::editPago/$1'
                , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
                );
    
    $routes->get('pagos/delete/(:any)'
                , 'PagosController::delete/$1'
                , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
        );
    $routes->get('timbrarComplemento/(:any)'
                , 'FacturaElectronicaController::timbrarPago/$1'
                , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
                );
});
