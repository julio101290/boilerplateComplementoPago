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
        'idEmpresa' => 'required|is_natural_no_zero',
        'idSucursal' => 'required|is_natural_no_zero',
        'idCustumer' => 'required|is_natural_no_zero',
        'folio' => 'required|is_natural',
        'idUser' => 'required|is_natural_no_zero',
        'listPagos' => 'required|string',
        'taxes' => 'required|decimal',
        'IVARetenido' => 'required|decimal',
        'ISRRetenido' => 'required|decimal',
        'subTotal' => 'required|decimal',
        'total' => 'required|decimal',
        'balance' => 'required|decimal',
        'date' => 'required|valid_date[Y-m-d]',
        'dateVen' => 'required|valid_date[Y-m-d]',
        'quoteTo' => 'permit_empty|max_length[512]',
        'delivaryTime' => 'permit_empty|max_length[512]',
        'generalObservations' => 'permit_empty|max_length[512]',
        'UUID' => 'required|max_length[36]',
        'idQuote' => 'permit_empty|is_natural',
        'tipoComprobanteRD' => 'permit_empty|is_natural',
        'folioCombrobanteRD' => 'permit_empty|is_natural',
        'RFCReceptor' => 'permit_empty|max_length[15]',
        'usoCFDI' => 'permit_empty|max_length[32]',
        'metodoPago' => 'permit_empty|max_length[32]',
        'formaPago' => 'permit_empty|max_length[32]',
        'regimenFiscalReceptor' => 'permit_empty|max_length[1024]',
        'razonSocialReceptor' => 'permit_empty|max_length[1024]',
        'codigoPostalReceptor' => 'permit_empty|exact_length[5]|numeric',
        'idVehiculo' => 'permit_empty|is_natural',
        'idChofer' => 'permit_empty|is_natural',
        'tipoVehiculo' => 'permit_empty|is_natural',
        'idArqueoCaja' => 'permit_empty|is_natural',
        'noCTAOrdenante' => 'permit_empty|max_length[64]',
        'noCTABeneficiario' => 'permit_empty|max_length[64]',
        'RFCCTAOrdenante' => 'permit_empty|max_length[64]',
        'RFCCTABeneficiario' => 'permit_empty|max_length[64]',
        'created_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'updated_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'deleted_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function mdlGetPagos($empresas = null) {
        $builder = $this->db->table('pagos a');
        $builder->join('custumers b', 'a.idCustumer = b.id');
        $builder->join('empresas c', 'a.idEmpresa = c.id');

        $builder->select('
        a.UUID,
        a.id,
        CONCAT(b.firstname, \' \', b.lastname) AS "nameCustumer",
        a.idCustumer,
        a.folio,
        a.date,
        b.email AS "correoCliente",
        a.dateVen,
        a.total,
        a.taxes,
        a.subTotal,
        a.balance,
        a.delivaryTime,
        a.generalObservations,
        a.idQuote,
        a.IVARetenido,
        a.ISRRetenido,
        a.idSucursal,
        a.RFCReceptor,
        a.usoCFDI,
        a.metodoPago,
        a.formaPago,
        a.razonSocialReceptor,
        a.codigoPostalReceptor,
        a.regimenFiscalReceptor,
        a.idVehiculo,
        a.idChofer,
        a.tipoVehiculo,
        a.idArqueoCaja,
        a.created_at,
        a.updated_at,
        a.deleted_at
    ');

        if (!empty($empresas) && is_array($empresas)) {
            $builder->whereIn('a.idEmpresa', $empresas);
        }

        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Search by filters
     */
    public function mdlGetPagosFilters($empresas = null, $from = null, $to = null, $allSells = null, $empresa = 0, $sucursal = 0, $cliente = 0) {
        $builder = $this->db->table('pagos a');
        $builder->join('custumers b', 'a.idCustumer = b.id');
        $builder->join('empresas c', 'a.idEmpresa = c.id');

        $builder->select('
        a.UUID,
        a.id,
        CONCAT(b.firstname, \' \', b.lastname) AS "nameCustumer",
        a.idCustumer,
        a.folio,
        a.date,
        b.email AS "correoCliente",
        a.dateVen,
        a.total,
        a.taxes,
        a.subTotal,
        a.balance,
        a.delivaryTime,
        a.generalObservations,
        a.idQuote,
        a.IVARetenido,
        a.ISRRetenido,
        a.idSucursal,
        a.RFCReceptor,
        a.usoCFDI,
        a.metodoPago,
        a.formaPago,
        a.razonSocialReceptor,
        a.codigoPostalReceptor,
        a.regimenFiscalReceptor,
        a.idVehiculo,
        a.idChofer,
        a.tipoVehiculo,
        a.idArqueoCaja,
        a.created_at,
        a.updated_at,
        a.deleted_at
    ');

        // Rango de fechas
        $builder->where('a.date >=', $from . ' 00:00:00');
        $builder->where('a.date <=', $to . ' 23:59:59');

        // Todas las ventas o con balance > 0
        if ($allSells !== 'true') {
            $builder->where('a.balance >', 0);
        }

        // Filtro empresa
        if ($empresa != 0) {
            $builder->where('a.idEmpresa', $empresa);
        }

        // Filtro sucursal
        if ($sucursal != 0) {
            $builder->where('a.idSucursal', $sucursal);
        }

        // Filtro cliente
        if ($cliente != 0) {
            $builder->where('a.idCustumer', $cliente);
        }

        // Solo empresas permitidas
        if (!empty($empresas) && is_array($empresas)) {
            $builder->whereIn('a.idEmpresa', $empresas);
        }

        // Ejecutar y devolver resultado como array
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Obtener Cotización por UUID
     */
    public function mdlGetPagoUUID($uuid, $empresas) {
        $builder = $this->db->table('pagos a');
        $builder->join('custumers b', 'a.idCustumer = b.id');
        $builder->join('empresas c', 'a.idEmpresa = c.id');

        $builder->select('
        a.idCustumer,
        a.folio,
        a.quoteTo,
        a.UUID,
        a.idUser,
        a.id,
        CONCAT(b.firstname, \' \', b.lastname) AS "nameCustumer",
        a.idEmpresa,
        c.nombre AS "nombreEmpresa",
        a.listPagos,
        a.date,
        a.dateVen,
        a.total,
        a.taxes,
        a.IVARetenido,
        a.ISRRetenido,
        a.idQuote,
        a.delivaryTime,
        a.generalObservations,
        a.RFCReceptor,
        a.usoCFDI,
        a.metodoPago,
        a.formaPago,
        a.razonSocialReceptor,
        a.codigoPostalReceptor,
        a.regimenFiscalReceptor,
        a.idSucursal,
        a.idVehiculo,
        a.idChofer,
        a.tipoVehiculo,
        a.idArqueoCaja,
        a.created_at,
        a.updated_at,
        a.deleted_at
    ');

        $builder->where('a.UUID', $uuid);

        if (!empty($empresas) && is_array($empresas)) {
            $builder->whereIn('a.idEmpresa', $empresas);
        }

        return $builder->get()->getRowArray();
    }

    public function mdlObtenerVentasFacturadasPendientesDePago($idEmpresa, $idSucursal, $idCustumer) {
        $builder = $this->db->table('sells a');

        // Uniones explícitas entre tablas
        $builder->join('enlacexml b', 'a.id = b.idDocumento');
        $builder->join('xml c', 'c.uuidTimbre = b.uuidXML');
        $builder->join('custumers e', 'a.idCustumer = e.id');

        // Campos seleccionados
        $builder->select('
        a.id,
        a.folio,
        a.idCustumer,
        a.total,
        a.balance,
        c.serie,
        a.date,
        a.dateVen,
        a.taxes
    ');

        // Condiciones
        $builder->where('a.balance >', 0);
        $builder->where('a.idEmpresa', $idEmpresa);
        $builder->where('a.idSucursal', $idSucursal);
        $builder->where('a.idCustumer', $idCustumer);
        $builder->where('a.deleted_at IS NULL', null, false); // Compatibilidad PostgreSQL
        // Ejecutar consulta
        $query = $builder->get();
        return $query->getResultArray();
    }
}
