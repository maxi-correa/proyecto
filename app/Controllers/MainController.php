<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MainController extends BaseController
{
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (session()->get('logueado')) {
            // Cargar la vista principal del usuario
            return view('main', [
                'nombre' => session()->get('nombre'),
                'apellido' => session()->get('apellido'),
            ]);
        } else {
            // Redirigir al inicio de sesión si no está autenticado
            return redirect()->to('/login')->with('error', 'Por favor, inicie sesión para continuar.');
        }
    }

    public function logout()
    {
        // Cerrar sesión
        session()->destroy();
        return redirect()->to('/login')->with('exito', 'Has cerrado sesión exitosamente.');
    }

    public function jugar($idPartida)
    {
        // Verificar si el usuario está autenticado
        if (!session()->get('logueado')) {
            return redirect()->to('/login')->with('error', 'Por favor, inicie sesión para jugar.');
        }

        $idUsuario = session()->get('id');

        // Traer los datos de la partida con join a la tabla tableros
        $partidaModel = new \App\Models\PartidaModel();
        $partida = $partidaModel
            ->select('partidas.*, tableros.filas, tableros.columnas')
            ->join('tableros', 'tableros.idTablero = partidas.idTablero')
            ->where('partidas.idPartida', $idPartida)
            ->first();

        // Verificar que exista la partida
        if (!$partida) {
            return redirect()->to('/partida/unirse')->with('error', 'La partida no existe.');
        }

        // Verificar si el usuario pertenece a esta partida
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $estaEnPartida = $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->where('idUsuario', $idUsuario)
            ->countAllResults();

        if (!$estaEnPartida) {
            return redirect()->to('/partida/unirse')->with('error', 'No estás registrado en esta partida.');
        }

        // Enviar datos a la vista del juego
        return view('main', [
            'idPartida'       => $idPartida,
            'nombre'          => session()->get('nombre'),
            'apellido'        => session()->get('apellido'),
            'filas'           => $partida['filas'],
            'columnas'        => $partida['columnas'],
            'turnoActual'     => $partida['turnoActual'],
            'idUsuario'       => $idUsuario
        ]);
    }

    public function estadoAJAX($idPartida)
    {
        $session = session();
        $idUsuario = $session->get('id');

        $partidaModel = new \App\Models\PartidaModel();
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $usuarioModel = new \App\Models\UsuarioModel();

        // Obtener partida con dimensiones del tablero
        $partida = $partidaModel
            ->select('partidas.*, tableros.filas, tableros.columnas')
            ->join('tableros', 'partidas.idTablero = tableros.idTablero')
            ->where('idPartida', $idPartida)
            ->first();

        if (!$partida) {
            return $this->response->setJSON(['success' => false, 'message' => 'Partida no encontrada']);
        }

        // Jugador que tiene el turno actual
        $jugadorTurno = $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->where('ordenTurnos', $partida['turnoActual'])
            ->first();

        $nombreJugadorTurno = $usuarioModel->find($jugadorTurno['idUsuario'])['nombreUsuario'];

        // Obtener todos los jugadores con sus puntajes
        $jugadores = $partidaUsuarioModel
            ->where('idPartida', $idPartida)
            ->findAll();

        $puntajes = [];
        foreach ($jugadores as $jugador) {
            $usuario = $usuarioModel->find($jugador['idUsuario']);
            $puntajes[] = [
                'nombre' => $usuario['nombreUsuario'],
                'puntos' => $jugador['puntos']
            ];
        }

        // Obtener el estado actual del tablero desde la base
        // Supongamos que lo guardás en una tabla `tablero_estado` con columnas:
        // idPartida, fila, columna, letra
        $db = \Config\Database::connect();
        $query = $db->table('tablero_estado')
            ->where('idPartida', $idPartida)
            ->get();

        $estadoTablero = [];
        for ($i = 0; $i < $partida['filas']; $i++) {
            $estadoTablero[$i] = array_fill(0, $partida['columnas'], '');
        }

        foreach ($query->getResult() as $casilla) {
            $estadoTablero[$casilla->fila][$casilla->columna] = $casilla->letra;
        }

        return $this->response->setJSON([
            'success' => true,
            'jugador_turno' => $nombreJugadorTurno,
            'puntajes' => $puntajes,
            'filas' => $partida['filas'],
            'columnas' => $partida['columnas'],
            'tablero' => $estadoTablero
        ]);
    }

    public function jugarAJAX()
{
    $session = session();
    $idUsuario = $session->get('id');

    $data = json_decode($this->request->getBody(), true);

    $idPartida = (int)($data['idPartida'] ?? 0);
    $fila = (int)($data['fila'] ?? -1);
    $columna = (int)($data['columna'] ?? -1);
    $letra = strtoupper(trim($data['letra'] ?? ''));

    if (!in_array($letra, ['A', 'N'])) {
        return $this->response->setJSON(['success' => false, 'message' => 'Letra inválida']);
    }

    $partidaModel = new \App\Models\PartidaModel();
    $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();

    $partida = $partidaModel->find($idPartida);
    if (!$partida) {
        return $this->response->setJSON(['success' => false, 'message' => 'Partida no encontrada']);
    }

    // Verificar si es el turno del jugador
    //CORREGIDO: Conexión directa a la base de datos para evitar problemas con claves compuestas
    $db = \Config\Database::connect(); // Conectarse a la base de datos
    $sql = "SELECT * FROM partidas_usuarios WHERE idPartida = ? AND idUsuario = ? LIMIT 1";
    $query = $db->query($sql, [$idPartida, $idUsuario]);
    $jugador = $query->getRowArray(); // Devuelve el resultado como array asociativo

    if (!$jugador || $jugador['ordenTurnos'] != $partida['turnoActual']) {
        return $this->response->setJSON(['success' => false, 'message' => 'No es tu turno']);
    }

    // Verificar que la celda esté vacía
    $db = \Config\Database::connect();
    $yaOcupada = $db->table('tablero_estado')
        ->where(['idPartida' => $idPartida, 'fila' => $fila, 'columna' => $columna])
        ->countAllResults();

    if ($yaOcupada > 0) {
        return $this->response->setJSON(['success' => false, 'message' => 'Casilla ocupada']);
    }

    // Guardar la jugada
    $db->table('tablero_estado')->insert([
        'idPartida' => $idPartida,
        'fila' => $fila,
        'columna' => $columna,
        'letra' => $letra
    ]);

    // Verificar si formó ANA
    $anaFormada = $this->verificarANA($idPartida, $fila, $columna);

    if ($anaFormada > 0) {
        // Sumar puntos
        $nuevoPuntaje = $jugador['puntos'] + $anaFormada;
        
        //CORREGIDO: Actualizar el puntaje del jugador en la base de datos de forma directa
        $db = \Config\Database::connect();
        $db->query(
            "UPDATE partidas_usuarios SET puntos = ? WHERE idPartida = ? AND idUsuario = ?",
            [$nuevoPuntaje, $idPartida, $idUsuario]
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => "¡Formaste ANA $anaFormada vez(ces)! Sumás punto(s).",
            'repiteTurno' => true
        ]);
    } else {
        // Pasar el turno al siguiente
        $totalJugadores = $partida['cantidad_jugadores'];
        $siguienteTurno = $partida['turnoActual'] % $totalJugadores + 1;

        // Actualizar el turno actual en la partida (CORREGIDO: Conexión directa a la base de datos)
        $sqlUpdateTurno = "UPDATE partidas 
                            SET turnoActual = ? 
                            WHERE idPartida = ?";
        $db->query($sqlUpdateTurno, [$siguienteTurno, $idPartida]);

        return $this->response->setJSON([
            'success' => true,
            'message' => "No formaste ANA. Turno para el siguiente jugador.",
            'repiteTurno' => false
        ]);
    }
}

    private function verificarANA($idPartida, $fila, $columna) //Es private porque solo se usa internamente
    {
        $db = \Config\Database::connect();
        $tablero = [];

        // Cargamos todo el tablero en un array bidimensional
        $query = $db->table('tablero_estado')
            ->select('fila, columna, letra')
            ->where('idPartida', $idPartida)
            ->get();

        foreach ($query->getResult() as $celda) {
            $tablero[$celda->fila][$celda->columna] = $celda->letra;
        }

        $direcciones = [
            [0, 1],   // derecha
            [1, 0],   // abajo
            [1, 1],   // diagonal abajo-derecha
            [-1, 0],  // arriba
            [0, -1],  // izquierda
            [-1, -1], // diagonal arriba-izquierda
            [-1, 1],  // diagonal arriba-derecha
            [1, -1]   // diagonal abajo-izquierda
        ];

        $totalANA = 0;

        foreach ($direcciones as $dir) {
            $dx = $dir[0];
            $dy = $dir[1];

            // Verificamos si se forma ANA con la letra en el medio
            $a1 = $tablero[$fila - $dx][$columna - $dy] ?? null;
            $n =  $tablero[$fila][$columna] ?? null;
            $a2 = $tablero[$fila + $dx][$columna + $dy] ?? null;

            if ($a1 === 'A' && $n === 'N' && $a2 === 'A') {
                $totalANA++;
            }

            // Verificamos si se forma ANA con la letra al principio
            $n = $tablero[$fila + $dx][$columna + $dy] ?? null;
            $a2 = $tablero[$fila + 2*$dx][$columna + 2*$dy] ?? null;
            if (($tablero[$fila][$columna] ?? null) === 'A' && $n === 'N' && $a2 === 'A') {
                $totalANA++;
            }

            // Verificamos si se forma ANA con la letra al final
            $a1 = $tablero[$fila - 2*$dx][$columna - 2*$dy] ?? null;
            $n = $tablero[$fila - $dx][$columna - $dy] ?? null;
            if (($tablero[$fila][$columna] ?? null) === 'A' && $n === 'N' && $a1 === 'A') {
                $totalANA++;
            }
        }

        return $totalANA;
    }

}
?>