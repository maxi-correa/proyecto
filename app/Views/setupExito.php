<!-- PAGINA DE SETUP ÉxITO -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Éxito de Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <header>
        <h1 class="h1-exito">Base de Datos - Éxito</h1>
    </header>
    
    <table class="tabla-exito">
    <tr>
        <td rowspan="2">
        <img src="<?= base_url('assets/img/exito.png') ?>" alt="Éxito" class="imagen-central" aria-placeholder="Éxito">
        </td>
        <td>
        <div class="mensaje-exito">
            <?= esc($mensaje) ?>
        </div>
        </td>
    </tr>
    <tr>
        <td>
            Serás redirigido en <span id="cuenta">5</span> segundos...
        </td>
    </tr>
    </table>
    </main>

<script>
    let segundos = 5;
    const cuenta = document.getElementById('cuenta');
    
    const intervalo = setInterval(() => {
    segundos--;
    
    // Aplica animación (escala + color)
    cuenta.textContent = segundos;
    cuenta.classList.remove('alerta'); // reinicia animación
    void cuenta.offsetWidth; // truco para reiniciar animación CSS
    if (segundos <= 1) {
        cuenta.classList.add('alerta');
    }

    if (segundos <= 0) {
        clearInterval(intervalo);
        window.location.href = "<?= base_url('/') ?>";
    }
    }, 1000);
</script>

</body>
</html>