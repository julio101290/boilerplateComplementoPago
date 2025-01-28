<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Enlacexml extends Migration {

    public function up() {
        // Enlacexml
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idDocumento' => ['type' => 'bigint', 'constraint' => 20, 'null' => false],
            'uuidXML' => ['type' => 'varchar', 'constraint' => 36, 'null' => false],
            'tipo' => ['type' => 'varchar', 'constraint' => 16, 'null' => false],
            'importe' => ['type' => 'decimal', 'constraint' => 18, 'null' => false],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('enlacexml', true);
    }

    public function down() {
        $this->forge->dropTable('enlacexml', true);
    }
}
