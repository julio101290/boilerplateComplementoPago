<?php

namespace julio101290\boilerplatesells\Models;

use CodeIgniter\Model;

class SellsModel extends Model {

    protected $table = 'sells';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id',
        'idEmpresa',
        'folio',
        'idUser',
        'idCustumer',
        'listProducts',
        'taxes',
        'IVARetenido',
        'ISRRetenido',
        'subTotal',
        'total',
        'balance',
        'date',
        'dateVen',
        'generalObservations',
        'quoteTo',
        'delivaryTime',
        'created_at',
        'updated_at',
        'idQuote',
        'tipoComprobanteRD',
        'folioComprobanteRD',
        'RFCReceptor',
        'usoCFDI',
        'metodoPago',
        'formaPago',
        'razonSocialReceptor',
        'codigoPostalReceptor',
        'regimenFiscalReceptor',
        'idVehiculo',
        'idChofer',
        'idSucursal',
        'idArqueoCaja',
        'tipoVehiculo',
        'esFacturaGlobal',
        'periodicidad',
        'tasaCero',
        'mes',
        'anio',
        'tasaCero',
        'tipoDocumentoRelacionado',
        'UUIDRelacion',
        'UUID'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
        'idCustumer' => 'required|',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function mdlGetSells($empresas) {

        $result = $this->db->table('sells a, custumers b, empresas c')
                ->select('a.UUID,a.id,concat(b.firstname,\' \',b.lastname) as nameCustumer
                    ,a.idCustumer
                    ,b.razonSocial
                    ,a.folio
                    ,a.date
                    ,b.email as correoCliente
                    ,a.dateVen
                    ,a.total
                    ,a.taxes
                    ,a.subTotal
                    ,a.balance
                    ,a.delivaryTime
                    ,a.generalObservations
                    ,a.idQuote
                    ,a.IVARetenido
                    ,a.ISRRetenido
                    ,a.tipoComprobanteRD
                    ,a.folioComprobanteRD
                    ,a.idSucursal


                    ,a.RFCReceptor
                    ,a.usoCFDI
                    ,a.metodoPago
                    ,a.formaPago
                    ,a.razonSocialReceptor
                    ,a.codigoPostalReceptor
                    ,a.regimenFiscalReceptor
                    
                    ,a.idVehiculo
                    ,a.idChofer
                    ,a.tipoVehiculo
                    ,a.idArqueoCaja
                    ,a.tasaCero
                    
                    ,a.esFacturaGlobal
                    ,a.periodicidad
                    ,a.mes
                    ,a.anio
                    
                    ,a.tipoDocumentoRelacionado
                    ,a.UUIDRelacion
                    
                    ,a.created_at
                    ,a.updated_at
                    ,a.deleted_at')
                ->where('a.idCustumer', 'b.id', FALSE)
                ->where('a.idEmpresa', 'c.id', FALSE)
                ->whereIn('a.idEmpresa', $empresas);

        return $result;
    }

    /**
     * Search by filters
     */
    public function mdlGetSellsFilters($empresas
            , $from
            , $to
            , $allSells
            , $empresa = 0
            , $sucursal = 0
            , $cliente = 0
    ) {

        $result = $this->db->table('sells a, custumers b, empresas c')
                ->select('a.UUID,a.id,concat(b.firstname,\' \',b.lastname) as nameCustumer
                    ,a.idCustumer
                    ,b.razonSocial
                    ,a.folio
                    ,a.date
                    ,b.email as correoCliente
                    ,a.dateVen
                    ,a.total
                    ,a.taxes
                    ,a.subTotal
                    ,a.balance
                    ,a.delivaryTime
                    ,a.generalObservations
                    ,a.idQuote
                    ,a.IVARetenido
                    ,a.ISRRetenido
                    ,a.tipoComprobanteRD
                    ,a.folioComprobanteRD
                    ,a.idSucursal


                    ,a.RFCReceptor
                    ,a.usoCFDI
                    ,a.metodoPago
                    ,a.formaPago
                    ,a.razonSocialReceptor
                    ,a.codigoPostalReceptor
                    ,a.regimenFiscalReceptor
                    
                    ,a.idVehiculo
                    ,a.idChofer
                    ,a.tipoVehiculo
                    ,a.idArqueoCaja
                    ,a.tasaCero
                    
                    ,a.esFacturaGlobal
                    ,a.periodicidad
                    ,a.mes
                    ,a.anio
                    
                    ,a.tipoDocumentoRelacionado
                    ,a.UUIDRelacion

                    ,a.created_at
                    ,a.updated_at
                    ,a.deleted_at')
                ->where('a.idCustumer', 'b.id', FALSE)
                ->where('a.idEmpresa', 'c.id', FALSE)
                ->where('a.date >=', $from . ' 00:00:00')
                ->where('a.date <=', $to . ' 23:59:59')
                ->groupStart()
                ->where('\'true\'', $allSells, true)
                ->orWhere('a.balance>', '0')
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $empresa, true)
                ->orWhere('a.idEmpresa', $empresa)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $sucursal, true)
                ->orWhere('a.idSucursal', $sucursal)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $cliente, true)
                ->orWhere('a.idCustumer', $cliente)
                ->groupEnd()
                ->whereIn('a.idEmpresa', $empresas)
        ;

        return $result;
    }

    public function mdlCarteraVencida($empresas, $sucursales) {

        $resultado = $this->db->table('sells a, custumers b')
                        ->select('b.id,b.razonSocial,SUM(a.balance) AS deuda')
                        ->where('balance >', 1)
                        ->where('a.idCustumer', 'b.id', FALSE)
                        ->whereIn('a.idEmpresa', $empresas)
                        ->whereIn('a.idEmpresa', $sucursales)
                        ->groupBy(array('b.id', 'b.razonSocial'))
                        ->orderBy('SUM(a.balance) DESC')
                        ->limit(10)
                        ->get()->getResultArray();

        return $resultado;
    }

    /**
     * Obtener Cotización por UUID
     */
    public function mdlGetSellUUID($uuid, $empresas) {

        $result = $this->db->table('sells a, custumers b, empresas c')
                        ->select('a.idCustumer
            ,a.folio
            ,a.quoteTo
            ,a.UUID
            ,a.idUser
            ,a.id
            ,concat(b.firstname,\' \',b.lastname) as nameCustumer
            ,a.idEmpresa
            ,c.nombre as nombreEmpresa
            ,a.listProducts
            ,a.date
            ,a.dateVen
            ,a.total
            ,a.taxes
            ,a.IVARetenido
            ,a.ISRRetenido
            ,a.subTotal
            ,a.tipoComprobanteRD
            ,a.folioComprobanteRD
            ,a.idQuote
            ,a.delivaryTime
            ,a.generalObservations

            ,a.RFCReceptor
            ,a.usoCFDI
            ,a.metodoPago
            ,a.formaPago
            ,a.razonSocialReceptor
            ,a.codigoPostalReceptor
            ,a.regimenFiscalReceptor
            ,a.idSucursal
            
            ,a.idVehiculo
            ,a.idChofer
            ,a.tipoVehiculo
            ,a.idArqueoCaja
            ,a.tasaCero
            
            ,a.esFacturaGlobal
            ,a.periodicidad
            ,a.mes
            ,a.anio
            
            ,a.tipoDocumentoRelacionado
            ,a.UUIDRelacion

            ,a.created_at
            ,a.updated_at,
            a.deleted_at')
                        ->where('a.idCustumer', 'b.id', FALSE)
                        ->where('a.idEmpresa', 'c.id', FALSE)
                        ->where('UUID', $uuid)
                        ->whereIn('a.idEmpresa', $empresas)
                        ->get()->getRowArray();

        return $result;
    }

    /**
     * 
     * @param type $idEmpresa
     * @param type $idSucursal
     * @param type $idProducto
     * Ventas Filtradas por Empresas, Sucursales y productos
     */
    public function mdlVentasPorProductos($idEmpresa = 0
            , $idSucursal = 0
            , $idProducto = 0
            , $from
            , $to
            , $idEmpresas
            , $idSucursales
            , $idCliente
    ) {



        $result = $this->db->table('sells a
                                    , sellsdetails b
                                    , empresas c
                                    , branchoffices d
                                    , custumers e
                                    ')
                ->select('a.id
                        ,a.idEmpresa
                        ,a.idSucursal
                        ,c.nombre as nombreEmpresa
                        ,d.name as nombreSucursal
                        ,e.firstname as nombreCliente
                        ,e.lastname as apellidoCliente
                        ,e.razonSocial as razonSocialCliente
                        ,a.folio
                        ,a.date
                        ,b.idProduct
                        ,b.description
                        ,b.codeProduct
                        ,b.cant
                        ,b.price
                        ,b.porcentTax
                        ,b.tax as impuestoProducto
                        ,b.total as totalProducto
                        
                        ,a.esFacturaGlobal
                        ,a.periodicidad
                        ,a.mes
                        ,a.anio
                        ,a.tipoDocumentoRelacionado
                        ,a.UUIDRelacion
                        
                        ,a.taxes
                        ,a.subTotal
                        ,b.neto')
                ->where('a.id', 'b.idSell', FALSE)
                ->where('a.idEmpresa', 'c.id', FALSE)
                ->where('a.idSucursal', 'd.id', FALSE)
                ->groupStart()
                ->where('\'0\'', $idEmpresa, true)
                ->orWhere('a.idEmpresa', $idEmpresa)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $idSucursal, true)
                ->orWhere('a.idSucursal', $idSucursal)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $idCliente, true)
                ->orWhere('a.idCustumer', $idCliente)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $idProducto, true)
                ->orWhere('b.idProduct', $idProducto)
                ->groupEnd()
                ->where('a.date >=', $from . ' 00:00:00')
                ->where('a.date <=', $to . ' 23:59:59')
                ->whereIn('a.idEmpresa', $idEmpresas)
                ->whereIn('a.idSucursal', $idSucursales)
                ->where('a.idCustumer', 'e.id', false);

        return $result;
    }

    /**
     * 
     * @param type $idEmpresa
     * @param type $idSucursal
     * @param type $idProducto
     * Ventas Filtradas por Empresas, Sucursales y productos
     */
    public function mdlVentasPorProductosAgrupado($idEmpresa = 0
            , $idSucursal = 0
            , $idProducto = 0
            , $from
            , $to
            , $idEmpresas
            , $idSucursales
    ) {



        $result = $this->db->table('sells a
                                    , sellsdetails b
                                    , empresas c
                                    , branchoffices d
                                    , products e
                                    ')
                        ->select('b.idProduct,sum(cant) as cant,e.description')
                        ->where('a.id', 'b.idSell', FALSE)
                        ->where('b.idProduct', 'e.id', FALSE)
                        ->where('a.idEmpresa', 'c.id', FALSE)
                        ->where('a.idSucursal', 'd.id', FALSE)
                        ->groupStart()
                        ->where('\'0\'', $idEmpresa, true)
                        ->orWhere('a.idEmpresa', $idEmpresa)
                        ->groupEnd()
                        ->groupStart()
                        ->where('\'0\'', $idSucursal, true)
                        ->orWhere('a.idSucursal', $idSucursal)
                        ->groupEnd()
                        ->groupStart()
                        ->where('\'0\'', $idProducto, true)
                        ->orWhere('b.idProduct', $idProducto)
                        ->groupEnd()
                        ->where('a.date >=', $from . ' 00:00:00')
                        ->where('a.date <=', $to . ' 23:59:59')
                        ->whereIn('a.idEmpresa', $idEmpresas)
                        ->whereIn('a.idSucursal', $idSucursales)
                        ->orderBy("sum(cant)")
                        ->limit(10)
                        ->groupBy("b.idProduct,e.description")
                        ->get()->getResultArray();

        return $result;
    }

    public function mdlIVARetenidoTotales($id) {


        $result = $this->db->table('sellsdetails')
                        ->select('porcentIVARetenido,sum(IVARetenido) as importeTotal')
                        ->where('porcentIVARetenido >', 0)
                        ->where('idSell', $id)
                        ->groupBy('porcentIVARetenido')
                        ->get()->getResultArray();

        return $result;
    }

    public function mdlISRRetenidoTotales($id) {


        $result = $this->db->table('sellsdetails')
                        ->select('porcentISRRetenido,sum(ISRRetenido) as importeTotal')
                        ->where('porcentISRRetenido >', 0)
                        ->where('idSell', $id)
                        ->groupBy('porcentISRRetenido')
                        ->get()->getResultArray();

        return $result;
    }
}
