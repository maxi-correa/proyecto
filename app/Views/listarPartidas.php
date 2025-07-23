<!-- app/Views/partida/listaPartidas.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Unirse a una partida</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <?= view('capas/barra', ['cambiar_contrasena' => false, 'sonido' => false, 'mostrar_logout' => true]); ?>
<div class="body-formulario">
    <main class="main-content">
        <div class="formulario formulario-menu">
            <h1>Partidas Disponibles</h1>
            <?php if (empty($partidas)): ?>
                <p>No hay partidas disponibles por ahora.</p>
            <?php else: ?>
                <?php foreach ($partidas as $partida): ?>
                    <div class="partida-card">
                        <div class="partida-info">
                            <strong>Partida #<?= esc($partida['idPartida']) ?></strong><br>
                            Límite: <?= esc($partida['cantidad_jugadores']) ?> jugadores<br>
                            Jugadores conectados: <?= esc($partida['jugadores_conectados']) ?>
                        </div>
                        <div>
                            <img 
                                src="<?= base_url('assets/img/tablero' . $partida['filas'] . 'x' . $partida['columnas'] . '.png') ?>" 
                                alt="Tablero <?= $partida['filas'] ?>x<?= $partida['columnas'] ?>" 
                                class="tablero-miniatura"
                            ><br>
                            <?= $partida['filas'] . 'x' . $partida['columnas'] ?>
                        </div>
                        <div class="partida-acciones">
                            <button onclick="confirmarIngreso(<?= $partida['idPartida'] ?>)">Unirse</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <button class="boton-volver" onclick="location.href='<?= base_url('partida') ?>'">Volver al menú principal</button>
        </div>
    </main>
    <?= view('capas/pie') ?>
</div>

<!-- Modal básico -->
<div id="modalConfirmar" class="modal">
    <div class="formulario">
        <h2>¿Deseás unirte a esta partida?</h2>
        <button class="boton-confirmar" id="botonConfirmar">Sí, unirme</button>
        <br><br>
        <button class="boton-volver"  onclick="cerrarModal()">Cancelar</button>
    </div>
</div>
<script src="<?= base_url('assets/js/barra.js') ?>"></script>
<script>
    let partidaSeleccionada = null;

    function confirmarIngreso(idPartida) {
        partidaSeleccionada = idPartida;
        document.getElementById('modalConfirmar').classList.add('activo');
    }

    function cerrarModal() {
        document.getElementById('modalConfirmar').classList.remove('activo');
    }

    document.getElementById('botonConfirmar').addEventListener('click', function () {
        window.location.href = "<?= base_url('partida/verificar_espera/') ?>" + partidaSeleccionada;
    });
</script>
</body>
</html>