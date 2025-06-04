<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuario extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'nombre' => ['type' => 'VARCHAR', 'constraint' => 100],
            'apellido' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'creado_en' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
