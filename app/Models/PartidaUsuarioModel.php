<?php
namespace App\Models;
use CodeIgniter\Model;

class PartidaUsuarioModel extends Model
{
    protected $table = 'partidas_usuarios';
    protected $primaryKey = 'idPartidaUsuario';
    protected $useAutoIncrement = true;

    // Campos que se pueden insertar o actualizar
    protected $allowedFields = [
        'idPartida',
        'idUsuario',
        'ordenTurno',
        'puntos',
    ];
    
    protected $useTimestamps = false;
}