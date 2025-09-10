<?php

$routes->group('admin', function ($routes) {


    $routes->resource('listCompPag', [
        'filter' => 'permission:listapagos-permission',
        'namespace' => 'julio101290\boilerplatecomplementopago\Controllers',
        'controller' => 'PagosController',
        'except' => 'show',
      
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
            , ['namespace' => 'julio101290\boilerplatesells\Controllers']
    );

    $routes->resource('pagos', [
        'filter' => 'permission:listapagos-permission',
        'controller' => 'PagosController',
        'except' => 'show',
        'namespace' => 'julio101290\boilerplatecomplementopago\Controllers',
    ]);

    $routes->get('xmlenlace/getXMLEnlazadosPagos/(:any)'
            , 'PagosController::getXMLEnlazadosPagos/$1'
            , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
    );

    $routes->post('pagos/getLastCode'
            , 'PagosController::getLastCode'
            , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
    );

    $routes->post('sells/obtenerVentasPendientes'
            , 'PagosController::obtenerFacturasPendientes'
            , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
    );

    $routes->post('complementoPago/save'
            , 'PagosController::save'
            , ['namespace' => 'julio101290\boilerplatecomplementopago\Controllers']
    );
    
    
    $routes->get('xml/generarPDFDesdePago/(:any)'
            , 'XmlController::generaPDFDesdePago/$1'
             , ['namespace' => 'julio101290\boilerplateCFDI\Controllers']
            );
    
    
        // Envio de facturas
    $routes->post('mailSettings/sendMailComplemento'
            , 'SettingsMailController::sendMailComplementoPDF'
            , ['namespace' => 'julio101290\boilerplatesells\Controllers']
            );
    
});
