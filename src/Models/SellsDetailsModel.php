<?php

namespace julio101290\boilerplatesells\Models;

use CodeIgniter\Model;

class SellsDetailsModel extends Model {

    protected $table = 'sellsdetails';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id'
        , 'idSell'
        , 'idProduct'
        , 'description'
        , 'idAlmacen'
        , 'lote'
        , 'claveProductoSAT'
        , 'claveUnidadSAT'
        , 'unidad'
        , 'codeProduct'
        , 'cant'
        , 'price'
        , 'porcentTax'
        , 'tax'
        , 'porcentIVARetenido'
        , 'IVARetenido'
        , 'porcentISRRetenido'
        , 'ISRRetenido'
        , 'neto'
        , 'total'
        , 'tasaCero'
        , 'importeExento'
        , 'predial'
        , 'created_at'
        , 'updated_at'
        , 'deleted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
