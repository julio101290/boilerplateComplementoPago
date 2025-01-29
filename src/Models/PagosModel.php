<?php

namespace julio101290\boilerplatecomplementopago\Models;

use CodeIgniter\Model;

class PagosModel extends Model {

    protected $table = 'pagos';
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
        'listPagos',
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
        'noCTAOrdenante',
        'noCTABeneficiario',
        'RFCCTAOrdenante',
        'RFCCTABeneficiario',
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

    public function mdlGetPagos($empresas) {

        $result = $this->db->table('pagos a, custumers b, empresas c')
                ->select('a.UUID,a.id,concat(b.firstname,\' \',b.lastname) as nameCustumer
                    ,a.idCustumer
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
    public function mdlGetPagosFilters($empresas
            , $from
            , $to
            , $allSells
            , $empresa = 0
            , $sucursal = 0
            , $cliente = 0
    ) {

        $result = $this->db->table('pagos a, custumers b, empresas c')
                ->select('a.UUID,a.id,concat(b.firstname,\' \',b.lastname) as nameCustumer
                    ,a.idCustumer
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
                ->where('\'0\'', $empresa)
                ->orWhere('a.idEmpresa', $empresa)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $sucursal)
                ->orWhere('a.idSucursal', $sucursal)
                ->groupEnd()
                ->groupStart()
                ->where('\'0\'', $cliente,true)
                ->orWhere('a.idCustumer', $cliente)
                ->groupEnd()
                ->whereIn('a.idEmpresa', $empresas);

        return $result;
    }

    /**
     * Obtener Cotización por UUID
     */
    public function mdlGetPagoUUID($uuid, $empresas) {

        $result = $this->db->table('pagos a, custumers b, empresas c')
                        ->select('a.idCustumer
            ,a.folio
            ,a.quoteTo
            ,a.UUID
            ,a.idUser
            ,a.id
            ,concat(b.firstname,\' \',b.lastname) as nameCustumer
            ,a.idEmpresa
            ,c.nombre as nombreEmpresa
            ,a.listPagos
            ,a.date
            ,a.dateVen
            ,a.total
            ,a.taxes
            ,a.IVARetenido
            ,a.ISRRetenido
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

    public function mdlObtenerVentasFacturadasPendientesDePago(
            $idEmpresa
            , $idSucursal
            , $idCustumer
    ) {


        $resultado = $this->db->table('sells a, xml c, enlacexml b, custumers e')
                        ->select('
                                a.id
                                ,a.folio
                                ,a.idCustumer
                                ,a.total
                                ,a.balance
                                ,c.serie
                                ,a.date
                                ,a.dateVen
                                ,a.taxes
                                ')
                        ->where('a.id', 'b.idDocumento', FALSE)
                        ->where('c.uuidTimbre', 'b.uuidXML', FALSE)
                        ->where('a.balance >', 0)
                        ->where('a.idEmpresa', $idEmpresa)
                        ->where('a.idSucursal', $idSucursal)
                        ->where('a.idCustumer', $idCustumer)
                        ->where('a.deleted_at', NULL)
                        ->where('a.idCustumer', 'e.id', FALSE)
                        ->get()->getResultArray();

        return $resultado;
    }

}
