<?php
namespace App\Controllers;

use App\Models\Partida;

class PartidaController extends BaseController
{
    public function eleccionPartida()
    {
        return view('partida');
    }

    public function vistaCrearPartida()
    {
        return view('vistaCrearPartida');
    }

    public function crearPartida()
    {
        helper('session'); // Cargamos el helper de sesión
        $session = session(); // Obtenemos la sesión actual

        $idUsuario = $session->get('id'); // quien crea la partida
        $cantidadJugadores = $this->request->getPost('cantidad_jugadores');
        $tamanoTablero = $this->request->getPost('tamano_tablero');

        $partidaModel = new \App\Models\PartidaModel();

        $idPartida = $partidaModel->insert([
            'idCreador' => $idUsuario,
            'idTablero' => $tamanoTablero,
            'cantidad_jugadores' => $cantidadJugadores,
            'estado' => 'esperando',
            'fecha_partida' => date('Y-m-d H:i:s'),
        ], true); // 'true' para devolver el ID insertado

        // Insertamos al primer jugador en la partida
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $partidaUsuarioModel->insert([
            'idPartida' => $idPartida,
            'idUsuario' => $idUsuario,
            'orden_turno' => null, // se sorteará después
        ]);

    return redirect()->to('/partida/espera/' . $idPartida);
    }

    public function espera($idPartida)
    {
    return view('espera', ['idPartida' => $idPartida]);
    }
}