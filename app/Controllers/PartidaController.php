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

        // Validamos que el usuario esté logueado
        if (!$idUsuario) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para crear una partida.');
        }

        // Validamos que se haya seleccionado una cantidad de jugadores y un tamaño de tablero
        if (!$cantidadJugadores || !$tamanoTablero) {
            return redirect()->back()->with('error', 'Debes seleccionar la cantidad de jugadores y el tamaño del tablero.');
        }

        $partidaModel = new \App\Models\PartidaModel();

        $idPartida = $partidaModel->insert([
            'idCreador' => $idUsuario,
            'idTablero' => $tamanoTablero,
            'cantidad_jugadores' => $cantidadJugadores,
            'estado' => 'esperando',
            'fecha_partida' => date('Y-m-d H:i:s'),
        ], true); // 'true' para devolver el ID insertado

        // Insertamos al primer jugador en la partida
        //CORREGIDO: Fue necesario usar el método de conexión directa a la base de datos
        $db = \Config\Database::connect();
        $db->table('partidas_usuarios')->insert([
            'idPartida' => $idPartida,
            'idUsuario' => $idUsuario,
            'ordenTurnos' => null,
            'puntos' => 0
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

        // Validamos que la partida exista
        if (!$partida) {
            return redirect()->to('/partida/unirse')->with('error', 'La partida no existe.');
        }
        // $esCreador verifica si el usuario actual es el creador de la partida
        $esCreador = ($partida['idCreador'] == $idUsuario);

        return view('espera', [
            'idPartida' => $idPartida,
            'esCreador' => $esCreador //La idea es que el creador no pueda deshacerse de la partida
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
        
        // Si no hay sesión, redirigir al login
        $idUsuario = $session->get('id');
        if (!$idUsuario) {
            return redirect()->to('/login'); 
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
            
            // Insertar al usuario en la partida
            // CORREGIDO: Fue necesario usar el método de conexión directa a la base de datos
            $db = \Config\Database::connect();
            $db->table('partidas_usuarios')->insert([
            'idPartida' => $idPartida,
            'idUsuario' => $idUsuario,
            'ordenTurnos' => null,
            'puntos' => 0
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
            return redirect()->to('/partida/unirse')->with('error', 'Partida no encontrada.');
        }
        
        // Verificamos si ya se asignaron turnos
        $hayTurnosAsignados = $usuarioPartidaModel
        ->where('idPartida', $idPartida)
        ->where('ordenTurnos IS NOT', null)
        ->countAllResults() > 0;
        
        if (!$hayTurnosAsignados) {
            // Obtenemos todos los jugadores de esa partida
            $jugadores = $usuarioPartidaModel
            ->where('idPartida', $idPartida)
            ->findAll();
            
            // Hacemos el shuffle solo si NADIE tiene turno
            shuffle($jugadores);
            
            $db = \Config\Database::connect();
            
            $orden = 1;
            foreach ($jugadores as $jugador) {
                $db->query( //Lo hago extraordinariamente con query porque CI no funciona bien con claves compuestas
                    "UPDATE partidas_usuarios 
                    SET ordenTurnos = ? 
                    WHERE idPartida = ? AND idUsuario = ?",
                    [$orden, $jugador['idPartida'], $jugador['idUsuario']]
                );
            $orden++;
            }

            //Se cambia el estado de la partida a "en curso"
            $partidaModel->update($idPartida, ['estado' => 'en curso']);
        }
        
        // Obtenemos los jugadores con sus turnos
        $jugadores = $usuarioPartidaModel
            ->where('idPartida', $idPartida)
            ->findAll();

        $idUsuarios = array_column($jugadores, 'idUsuario');
        $resumen = $this->obtenerResumenEntreJugadores($idUsuarios);
        
        // Obtener nombres
        $jugadoresConNombre = [];
        foreach ($jugadores as $jugador) {
            $usuario = $usuarioModel->find($jugador['idUsuario']);
            $jugadoresConNombre[] = [
                'nombre' => $usuario['nombreUsuario'],
                'turno' => $jugador['ordenTurnos'] ?? null // Si no tiene turno, será null
            ];
        }

        return view('asignarTurnos', [
            'idPartida' => $idPartida,
            'jugadores' => $jugadoresConNombre,
            'resumen' => $resumen, // Resumen de partidas previas entre los jugadores
        ]);
    }

    private function obtenerResumenEntreJugadores(array $idUsuarios)
    {
        $db = \Config\Database::connect();
        sort($idUsuarios); // normalizamos el array
        $jugadoresKey = implode(',', $idUsuarios);
        $inIds = implode(',', $idUsuarios);

        /* ----------  1. ¿Hay historial conjunto?  ---------- */
        $partidas = $db->query("
            SELECT p.idPartida, p.fechaPartida, p.idGanador, u.nombreUsuario AS nombreGanador
            FROM partidas p
            JOIN partidas_usuarios pu ON p.idPartida = pu.idPartida
            JOIN usuarios u ON p.idGanador = u.id
            WHERE pu.idUsuario IN ($inIds) AND p.estado = 'finalizada'
            GROUP BY p.idPartida
            ORDER BY p.fechaPartida DESC
        ")->getResultArray();

        foreach ($partidas as $partida) {
            $usuariosEnPartida = array_column(
                $db->query("SELECT idUsuario FROM partidas_usuarios WHERE idPartida = ?", [$partida['idPartida']])
                ->getResultArray(),
                'idUsuario'
            );
            sort($usuariosEnPartida);
            if (implode(',', $usuariosEnPartida) === $jugadoresKey) {
                // → hay historial entre EXACTAMENTE estos jugadores
                
                $fechaCompleta = new \DateTime($partida['fechaPartida']);
                
                return [
                    'tipo'   => 'historial',
                    'fecha'  => $fechaCompleta->format('d/m/Y'),
                    'hora'   => $fechaCompleta->format('H:i'),
                    'ganador'=> $partida['nombreGanador']
                ];
            }
        }

        /* ----------  2. No hay historial: construir ranking ---------- */
        //  inicializamos victorias en 0
        $victorias = [];
        foreach ($idUsuarios as $uid) {
            $nombre = $db->table('usuarios')->select('nombreUsuario')->where('id', $uid)->get()->getRow('nombreUsuario');
            $victorias[$nombre] = 0;
        }

        //  sumamos las reales si existen
        $ganadas = $db->query("
            SELECT u.nombreUsuario, COUNT(*) AS ganadas
            FROM partidas p
            JOIN usuarios u ON p.idGanador = u.id
            WHERE p.idGanador IN ($inIds)
            GROUP BY p.idGanador
        ")->getResultArray();

        foreach ($ganadas as $fila) {
            $victorias[$fila['nombreUsuario']] = (int)$fila['ganadas'];
        }

        return [
            'tipo'      => 'ranking',
            'victorias' => $victorias // ← siempre contiene a todos los jugadores
        ];
    }

    public function mostrarResultados($idPartida)
    {
        $partidaModel = new \App\Models\PartidaModel();
        $partidaUsuarioModel = new \App\Models\PartidaUsuarioModel();
        $usuarioModel = new \App\Models\UsuarioModel();

        $partida = $partidaModel->find($idPartida);

        if (!$partida) {
            return redirect()->to('/')->with('error', 'Partida no encontrada');
        }

        // Si no está finalizada, redirigir
        if ($partida['estado'] !== 'finalizada') {
            return redirect()->to('/')->with('error', 'La partida aún está en curso');
        }

        // Obtener los jugadores con sus puntajes
        $jugadores = $partidaUsuarioModel->where('idPartida', $idPartida)->findAll();

        // Preparar array con nombre y puntos
        $resultados = [];
        foreach ($jugadores as $jugador) {
            $usuario = $usuarioModel->find($jugador['idUsuario']);
            $resultados[] = [
                'nombre' => $usuario['nombreUsuario'],
                'puntos' => $jugador['puntos'],
                'esGanador' => ($jugador['idUsuario'] == $partida['idGanador']) // Verifica si es el ganador
            ];
        }

        // Ordenar por puntaje descendente
        usort($resultados, fn($a, $b) => $b['puntos'] <=> $a['puntos']);

        return view('resultados', [
            'resultados' => $resultados,
            'idPartida' => $idPartida
        ]);
    }

    public function ranking()
    {
        $db = \Config\Database::connect();

        // Obtener los tamaños únicos de tableros usados en partidas
        $tableros = $db->query("SELECT DISTINCT filas, columnas FROM tableros ORDER BY filas, columnas")->getResultArray();
        $rankingPorTablero = [];

        foreach ($tableros as $t) {
            $filas = $t['filas'];
            $columnas = $t['columnas'];

            // Subconsulta para contar ganadas y jugadas
            $query = $db->query("
                SELECT 
                    u.nombreUsuario,
                    u.id AS idUsuario,
                    COUNT(p.idPartida) AS jugadas,
                    SUM(CASE WHEN p.idGanador = u.id THEN 1 ELSE 0 END) AS ganadas,
                    MAX(p.fechaPartida) AS ultima_partida
                FROM partidas p
                JOIN partidas_usuarios pu ON pu.idPartida = p.idPartida
                JOIN usuarios u ON u.id = pu.idUsuario
                JOIN tableros t ON p.idTablero = t.idTablero
                WHERE t.filas = ? AND t.columnas = ? AND p.estado = 'finalizada'
                GROUP BY u.id
                ORDER BY ganadas DESC, jugadas DESC, idUsuario ASC
                LIMIT 5
            ", [$filas, $columnas]);

            $rankingPorTablero["{$filas}x{$columnas}"] = $query->getResultArray();
        }

        return view('ranking', ['rankingPorTablero' => $rankingPorTablero]);
    }
}