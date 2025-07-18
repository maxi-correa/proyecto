<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre' => 'Usuario',
            'apellido' => 'Demo',
            'email' => 'demo@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'creado_en' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('usuarios')->insert($data);
    }
}
