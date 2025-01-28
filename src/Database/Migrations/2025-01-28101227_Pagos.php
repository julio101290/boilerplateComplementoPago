<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pagos extends Migration {

    public function up() {
        // Pagos
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idEmpresa' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'idSucursal' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'idCustumer' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'folio' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'idUser' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'listPagos' => ['type' => 'text', 'null' => true],
            'taxes' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'IVARetenido' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'ISRRetenido' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'subTotal' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'total' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'balance' => ['type' => 'decimal', 'constraint' => 18, 'null' => true],
            'date' => ['type' => 'date', 'null' => true],
            'dateVen' => ['type' => 'date', 'null' => true],
            'quoteTo' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'delivaryTime' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'generalObservations' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'UUID' => ['type' => 'varchar', 'constraint' => 36, 'null' => true],
            'idQuote' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'tipoComprobanteRD' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'folioCombrobanteRD' => ['type' => 'bigint', 'constraint' => 11, 'null' => true],
            'RFCReceptor' => ['type' => 'varchar', 'constraint' => 15, 'null' => true],
            'usoCFDI' => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'metodoPago' => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'formaPago' => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'regimenFiscalReceptor' => ['type' => 'varchar', 'constraint' => 1024, 'null' => true],
            'razonSocialReceptor' => ['type' => 'varchar', 'constraint' => 1024, 'null' => true],
            'codigoPostalReceptor' => ['type' => 'varchar', 'constraint' => 5, 'null' => true],
            'idVehiculo' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'idChofer' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'tipoVehiculo' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'idArqueoCaja' => ['type' => 'bigint', 'constraint' => 20, 'null' => true],
            'noCTAOrdenante' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'noCTABeneficiario' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'RFCCTAOrdenante' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'RFCCTABeneficiario' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pagos', true);
    }

    public function down() {
        $this->forge->dropTable('pagos', true);
    }
}
