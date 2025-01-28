<?php

namespace julio101290\boilerplatesells\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model {

    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id'
        , 'idSell'
        , 'importPayment'
        , 'importBack'
        , 'datePayment'
        , 'metodPayment'
        , 'idComplemento'
        , 'observaciones'
        , 'idNotaCredito'
        , 'tipo'
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
