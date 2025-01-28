<?php

namespace julio101290\boilerplatequotes\Models;

use CodeIgniter\Model;

class QuotesDetailsModel extends Model {

    protected $table = 'quotesdetails';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id'
        , 'idQuote'
        , 'idProduct'
        , 'idAlmacen'
        , 'lote'
        , 'description'
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
