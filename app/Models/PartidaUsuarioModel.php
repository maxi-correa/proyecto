<?php
namespace App\Models;
use CodeIgniter\Model;

class PartidaUsuarioModel extends Model
{
    protected $table = 'partidas_usuarios';

    // Indicamos que no hay clave primaria simple
    protected $primaryKey = 'idPartida,idUsuario'; // Solo para evitar errores, no la usaremos directamente

    protected $allowedFields = [
        'idPartida',
        'idUsuario',
        'ordenTurnos',
        'puntos',
    ];

    protected $useAutoIncrement = false;
    protected $useTimestamps = false;

    // IMPORTANTE: desactivamos la validación de clave primaria para evitar errores de CI
    protected $skipValidation = true;
}