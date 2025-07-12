<?php
namespace App\Models;
use CodeIgniter\Model;

class PartidaModel extends Model
{
    protected $table = 'partidas';
    protected $primaryKey = 'idPartida';
    protected $allowedFields = [
        'idCreador',
        'fechaPartida',
        'cantidad_jugadores',
        'idTablero',
        'idGanador',
        'estado'
    ];
    protected $useTimestamps = false; //timestamps es false porque no usamos created_at ni updated_at

    // Métodos personalizados en caso de ser necesarios
}
