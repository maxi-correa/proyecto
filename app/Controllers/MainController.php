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

        // Verificar si la partida está finalizada
        if ($partida['estado'] === 'finalizada') {
            return $this->response->setJSON([
                'success' => true,
                'finalizada' => true,
                'redirect' => base_url("partida/resultados/$idPartida")
            ]);
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

        // Ver si hay una propuesta de fin en curso
        $hayVotacion = $db->query("
            SELECT COUNT(*) AS total,
            SUM(votoFin = 1) AS a_favor,
            SUM(votoFin = -1) AS en_contra
            FROM partidas_usuarios
            WHERE idPartida = ? AND retirado = 0
        ", [$idPartida])->getRowArray();

        // Ver si este jugador ya votó
        $miVoto = $db->query("
            SELECT votoFin FROM partidas_usuarios
            WHERE idPartida = ? AND idUsuario = ?
        ", [$idPartida, $idUsuario])->getRow('votoFin');

        $consenso = null;
        if ($hayVotacion['a_favor'] > 0 && $hayVotacion['en_contra'] == 0) {
            $consenso = [
                'enCurso' => true,
                'yoPropuse' => ($miVoto == 1 && $hayVotacion['a_favor'] == 1),
                'yaVote' => ($miVoto != 0),
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'jugador_turno' => $nombreJugadorTurno,
            'puntajes' => $puntajes,
            'filas' => $partida['filas'],
            'columnas' => $partida['columnas'],
            'tablero' => $estadoTablero,
            'consenso' => $consenso
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

        if ($anaFormada > 0) 
        {
            // Sumar puntos
            $nuevoPuntaje = $jugador['puntos'] + $anaFormada;

            $db->query(
                "UPDATE partidas_usuarios SET puntos = ? WHERE idPartida = ? AND idUsuario = ?",
                [$nuevoPuntaje, $idPartida, $idUsuario]
            );

            // Verificamos si terminó la partida
            if ($this->verificarFinDeJuego($idPartida)) {
                return $this->response->setJSON([
                    'success' => true,
                    'finalizada' => true,
                    'redirect' => base_url("partida/resultados/$idPartida")
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "¡Formaste ANA $anaFormada vez(ces)! Sumás punto(s).",
                'repiteTurno' => true
            ]);
        } else {
            // Pasar el turno al siguiente
            $totalJugadores = $partida['cantidad_jugadores'];
            $siguienteTurno = $partida['turnoActual'] % $totalJugadores + 1;

            $db->query(
                "UPDATE partidas SET turnoActual = ? WHERE idPartida = ?",
                [$siguienteTurno, $idPartida]
            );

            // Verificamos si terminó la partida
            if ($this->verificarFinDeJuego($idPartida)) {
                return $this->response->setJSON([
                    'success' => true,
                    'finalizada' => true,
                    'redirect' => base_url("partida/resultados/$idPartida")
                ]);
            }

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

        // Tomamos las letras cargadas hasta el momento en el tablero
        $query = $db->table('tablero_estado')
            ->select('fila, columna, letra')
            ->where('idPartida', $idPartida)
            ->get();

        // $tablero tiene las letras en sus posiciones
        foreach ($query->getResult() as $celda) {
            $tablero[$celda->fila][$celda->columna] = $celda->letra;
        }

        // $direcciones contiene las direcciones en las que se puede formar ANA
        $direcciones = [
            [0, 1],   // derecha
            [1, 0],   // abajo
            [1, 1],   // diagonal abajo-derecha
            [1, -1]   // diagonal abajo-izquierda
        ];

        // Contador de veces que se forma ANA
        $totalANA = 0;

        // Recorremos las direcciones para verificar si se forma ANA
        foreach ($direcciones as $dir) {
            $dx = $dir[0]; // Desplazamiento en la fila
            $dy = $dir[1]; // Desplazamiento en la columna

            // Verificamos si se forma ANA con la letra en el medio
            $a1 = $tablero[$fila - $dx][$columna - $dy] ?? null; // Letra anterior
            $n =  $tablero[$fila][$columna] ?? null; // Letra actual
            $a2 = $tablero[$fila + $dx][$columna + $dy] ?? null; // Letra siguiente

            if ($a1 === 'A' && $n === 'N' && $a2 === 'A') {
                $totalANA++;
            }

            // Verificamos si se forma ANA con la letra al principio
            $n = $tablero[$fila + $dx][$columna + $dy] ?? null; // Letra siguiente
            $a2 = $tablero[$fila + 2*$dx][$columna + 2*$dy] ?? null; // Letra siguiente a la siguiente
            if (($tablero[$fila][$columna] ?? null) === 'A' && $n === 'N' && $a2 === 'A') {
                $totalANA++;
            }

            // Verificamos si se forma ANA con la letra al final
            $a1 = $tablero[$fila - 2*$dx][$columna - 2*$dy] ?? null; // Letra anterior a la anterior
            $n = $tablero[$fila - $dx][$columna - $dy] ?? null; // Letra anterior
            if (($tablero[$fila][$columna] ?? null) === 'A' && $n === 'N' && $a1 === 'A') {
                $totalANA++;
            }
        }

        return $totalANA;
    }

    private function verificarFinDeJuego($idPartida)
    {
        $db = \Config\Database::connect();

        // Obtener dimensiones del tablero
        $partida = $db->table('partidas p')
            ->select('p.*, t.filas, t.columnas')
            ->join('tableros t', 'p.idTablero = t.idTablero')
            ->where('p.idPartida', $idPartida)
            ->get()
            ->getRow();

        if (!$partida) {
            return false;
        }

        $totalCeldas = $partida->filas * $partida->columnas;

        // Contar celdas ocupadas
        $celdasOcupadas = $db->table('tablero_estado')
            ->where('idPartida', $idPartida)
            ->countAllResults();

        if ($celdasOcupadas < $totalCeldas) {
            return false; // Todavía hay casillas vacías
        }

        // Obtener al jugador con más puntos
        $ganador = $db->query("
            SELECT idUsuario 
            FROM partidas_usuarios 
            WHERE idPartida = ?
            ORDER BY puntos DESC, ordenTurnos ASC
            LIMIT 1
        ", [$idPartida])->getRow();

        if (!$ganador) {
            return false;
        }

        // Marcar la partida como finalizada
        $db->table('partidas')
            ->where('idPartida', $idPartida)
            ->update([
                'estado' => 'finalizada',
                'idGanador' => $ganador->idUsuario
            ]);

        return true;
    }

    public function retirarseAJAX()
    {
        $datos = $this->request->getJSON();
        $idPartida = $datos->idPartida ?? null;
        $idUsuario = session()->get('id');

        // 1. marcar retirado
        $db = \Config\Database::connect();
        $db->query("UPDATE partidas_usuarios 
                    SET retirado = 1 
                    WHERE idPartida = ? AND idUsuario = ?", 
                    [$idPartida, $idUsuario]);

        // 2. ¿cuántos activos quedan?
        $activos = $db->query("
            SELECT COUNT(*) AS c
            FROM partidas_usuarios
            WHERE idPartida = ? AND retirado = 0
        ", [$idPartida])->getRow('c');

        if ($activos <= 1) {                 // queda 1 o ninguno
            // Ganador si queda 1
            $ganador = null;
            if ($activos == 1) {
                $ganador = $db->query("
                    SELECT idUsuario FROM partidas_usuarios
                    WHERE idPartida = ? AND retirado = 0 LIMIT 1
                ", [$idPartida])->getRow('idUsuario');
            }
            // finalizamos
            $db->query("UPDATE partidas 
                        SET estado = 'finalizada', idGanador = ?
                        WHERE idPartida = ?", [$ganador, $idPartida]);

            return $this->response->setJSON([
                'success'   => true,
                'finalizada'=> true,
                'redirect'  => base_url("partida/resultados/$idPartida")
            ]);
        }

        // Solo sale este jugador
        return $this->response->setJSON([
            'success' => true,
            'finalizada' => false,
            'redirect' => base_url('/partida')
        ]);
    }

    public function votarFinAJAX()
    {
        $datos = $this->request->getJSON();
        $idPartida = $datos->idPartida ?? null;
        $acepto    = $datos->acepto ?? false;
        $idUsuario = session()->get('id');

        $db = \Config\Database::connect();
        $db->query("
            UPDATE partidas_usuarios 
            SET votoFin = ? 
            WHERE idPartida = ? AND idUsuario = ?",
            [$acepto ? 1 : -1, $idPartida, $idUsuario]);

        if (!$acepto) {
            // alguien canceló → resetear votos y deshabilitar para todos
            $db->query("UPDATE partidas_usuarios 
                        SET votoFin = -1 
                        WHERE idPartida = ?", [$idPartida]);

            return $this->response->setJSON(['success'=>true,'cancelado'=>true]);
        }

        // ¿Todos los activos ya votaron 1?
        $todos = $db->query("
            SELECT COUNT(*) AS total,
            SUM(votoFin=1) AS a_favor
            FROM partidas_usuarios
            WHERE idPartida = ? AND retirado = 0
        ", [$idPartida])->getRowArray();

        if ($todos['total'] == $todos['a_favor']) {
            // finalizar sin ganador
            $db->query("UPDATE partidas 
                        SET estado='finalizada', idGanador = NULL
                        WHERE idPartida = ?", [$idPartida]);

            return $this->response->setJSON([
                'success'=>true,
                'finalizada'=>true,
                'redirect'=>base_url("partida/resultados/$idPartida")
            ]);
        }

        return $this->response->setJSON(['success'=>true,'enCurso'=>true]);
    }
}
?>