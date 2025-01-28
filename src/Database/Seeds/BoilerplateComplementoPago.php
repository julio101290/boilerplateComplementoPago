<?php

namespace julio101290\boilerplatecomplementopago\Database\Seeds;

use CodeIgniter\Config\Services;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

/**
 * Class BoilerplateSeeder.
 */
class BoilerplateComplementoPago extends Seeder {

    /**
     * @var Authorize
     */
    protected $authorize;

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var Users
     */
    protected $users;

    public function __construct() {
        $this->authorize = Services::authorization();
        $this->db = \Config\Database::connect();
        $this->users = new UserModel();
    }

    public function run() {


        // Permission
        $this->authorize->createPermission('listapagos-permission', 'Permission to payment complement CFDI4.0');

        // Assign Permission to user
        $this->authorize->addPermissionToUser('listapagos-permission', 1);

    }

    public function down() {
        //
    }
}
