<?php
namespace App\Models;
use CodeIgniter\Model;

class PaisModel extends Model
{
    protected $table      = 'paises';
    protected $primaryKey = 'idPais';
    protected $allowedFields = ['nombrePais'];

    // Función personalizada que ordena los países por nombre en orden ascendente
    public function obtenerTodos()
    {
        return $this->orderBy('nombrePais', 'ASC')->findAll();
    }
}

?>