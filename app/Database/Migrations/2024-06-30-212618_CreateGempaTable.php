<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGempaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idgempa' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tgl' => [
                'type' => 'DATE',
            ],
            'lat' => [
                'type' => 'DOUBLE',
            ],
            'lon' => [
                'type' => 'DOUBLE',
            ],
            'depth' => [
                'type' => 'DOUBLE',
            ],
            'mag' => [
                'type' => 'DOUBLE',
            ],
            'remark' => [
                'type' => 'LONGTEXT',
            ],
        ]);
        $this->forge->addKey('idgempa', true);
        $this->forge->createTable('gempa');
    }

    public function down()
    {
        $this->forge->dropTable('gempa');
    }
}