<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombreUsuario', 'email', 'fechaNacimiento', 'idPais', 'password', 'creado_en'];
}

?>