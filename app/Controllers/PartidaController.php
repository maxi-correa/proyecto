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
        helper('session');
        $session = session();
        $idUsuario = $session->get('id');

        $partidaModel = new \App\Models\PartidaModel();
        $partida = $partidaModel->find($idPartida);

        if (!$partida) {
            return redirect()->to('/partida/unirse')->with('error', 'La partida no existe.');
        }

        $esCreador = ($partida['idCreador'] == $idUsuario);

        return view('espera', [
            'idPartida' => $idPartida,
            'esCreador' => $esCreador
        ]);
    }

    public function listarPartidas()
    {
        $partidaModel = new \App\Models\PartidaModel();
        
        // Obtenemos todas las partidas que están esperando jugadores y hacemos join con la tabla tableros
        $partidas = $partidaModel->select('partidas.*, tableros.filas, tableros.columnas')
            ->join('tableros', 'partidas.idTablero = tableros.idTablero')
            ->where('estado', 'esperando')
            ->orderBy('fechaPartida', 'DESC')
            ->findAll();
        
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        
        // Lógica para agregar en $partidas la cantidad de jugadores conectados a cada partida
        foreach ($partidas as &$partida) { // La & permite modificar el array original
            $partida['jugadores_conectados'] = $partidaUsuarioModel
            ->where('idPartida', $partida['idPartida'])
            ->countAllResults();
        }
        
        return view('listarPartidas', ['partidas' => $partidas]);
    }

    public function estadoPartida($idPartida)
    {
        $partidaModel = new \App\Models\PartidaModel();
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();

        $partida = $partidaModel->find($idPartida);
        $jugadoresConectados = $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->countAllResults();

        return $this->response->setJSON([
            'conectados' => $jugadoresConectados,
            'limite' => $partida['cantidad_jugadores'],
            'completo' => ($jugadoresConectados >= $partida['cantidad_jugadores'])
        ]);
    }

    public function verificarEspera($idPartida)
    {
        helper('session');
        $session = session();

        $idUsuario = $session->get('id');
        if (!$idUsuario) {
            return redirect()->to('/login'); // Seguridad básica
        }

        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $yaExiste = $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->where('idUsuario', $idUsuario)
            ->first();

        if (!$yaExiste) {
            // Antes de insertar, verificar si aún hay lugar
            $partidaModel = new \App\Models\PartidaModel();
            $partida = $partidaModel->find($idPartida);

            $cantidadConectados = $partidaUsuarioModel
                ->where('idPartida', $idPartida)
                ->countAllResults();

            if ($cantidadConectados >= $partida['cantidad_jugadores']) {
                return redirect()->to('/partida/unirse')->with('error', 'La partida ya está llena.');
            }

            // Insertar jugador a la partida
            $partidaUsuarioModel->insert([
                'idPartida' => $idPartida,
                'idUsuario' => $idUsuario,
                'orden_turno' => null,
            ]);
        }
        return redirect()->to('/partida/espera/' . $idPartida);
    }

    public function salirDeEspera($idPartida)
    {
        helper('session');
        $session = session();

        $idUsuario = $session->get('id');
        if (!$idUsuario) {
            return redirect()->to('/login');
        }

        // Borrar al jugador de la partida
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->where('idUsuario', $idUsuario)
            ->delete();

        return redirect()->to('/partida/unirse');
    }

    public function asignarTurnos($idPartida)
    {
        helper('session');
        $session = session();
        $idUsuario = $session->get('id');

        $partidaModel = new \App\Models\PartidaModel();
        $usuarioPartidaModel = new \App\Models\PartidaUsuarioModel();
        $usuarioModel = new \App\Models\UsuarioModel();

        $partida = $partidaModel->find($idPartida);
        if (!$partida) {
            return redirect()->to('/partida/listar')->with('error', 'Partida no encontrada.');
        }

        // Obtenemos todos los jugadores de esa partida
        $jugadores = $usuarioPartidaModel
            ->where('idPartida', $idPartida)
            ->findAll();

        // Verificamos si ya se asignaron turnos
        $hayTurnosAsignados = $usuarioPartidaModel
            ->where('idPartida', $idPartida)
            ->where('ordenTurnos IS NOT', null)
            ->countAllResults() > 0;

        if (!$hayTurnosAsignados) {
            // Hacemos el shuffle solo si NADIE tiene turno
            shuffle($jugadores);

            $orden = 1;
            foreach ($jugadores as $jugador) {
                $usuarioPartidaModel->update(
                    ['idPartida' => $jugador['idPartida'], 'idUsuario' => $jugador['idUsuario']],
                    ['ordenTurnos' => $orden]
                );
                $orden++;
            }
        }

        // Obtener nombres
        $jugadoresConNombre = [];
        foreach ($jugadores as $jugador) {
            $usuario = $usuarioModel->find($jugador['idUsuario']);
            $jugadoresConNombre[] = [
                'nombre' => $usuario['nombreUsuario'],
                'turno' => $jugador['ordenTurnos'] ?? null
            ];
        }

        return view('asignarTurnos', [
            'idPartida' => $idPartida,
            'jugadores' => $jugadoresConNombre
        ]);
    }
}