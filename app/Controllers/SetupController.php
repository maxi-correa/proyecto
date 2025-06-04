<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class SetupController extends Controller
{
    public function index()
    {
        try{ //try sirve para manejo de excepciones, si algo sale mal, se ejecuta el catch
            
            //Crear la base de datos si no existe
            $mysqli = new \mysqli('localhost', 'root', '', '');
            if ($mysqli->connect_errno)
            {
                echo 'Error de conexión a MySQL: ' . $mysqli->connect_error . '<br>';
            }

            $dbName = 'juego'; //Nombre de la base de datos
            $consulta = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($mysqli->query($consulta)===true)
            {
                echo "Base de datos $dbName creada correctamente.<br>";
            }
            else
            {
                echo 'Error al crear la base de datos: ' . $mysqli->error .'<br>';
            }

            $mysqli->close();

            //Ejecutar migraciones
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            //echo 'Migraciones ejecutadas correctamente. <br>';

            //Ejecutar seeders
            $seeder = \Config\Database::seeder();
            $seeder->call('UsuarioSeeder');
            //echo 'Base de datos inicialiazada correctamente con usuario demo. <br>';

            session()->setFlashdata('setup_estatus', 'exito');
            return redirect()->to('/setup/exito');
            
        } catch (\Throwable $e) {
            // Si ocurre un error, se captura la excepción y redirige a la página de error
            $mensaje = $e->getMessage();
            session()->setFlashdata([
                'error' => $mensaje,
                'setup_estatus' => 'error',
            ]);
            return redirect()->to('/setup/error');
        }
    }

    public function exito()
    {
        if (session()->getFlashdata('setup_estatus') !== 'exito') {
            // Si no hay mensaje de éxito, redirigir a inicio
            return redirect()->to('/login')->with('exito', 'La base de datos ya está configurada.');
        }
        //Mostrar en vista el mensaje de éxito
        return view('setupExito', ['mensaje' => 'Base de datos creada, migraciones y seeders ejecutados correctamente.']);
    }

    public function error()
    {
        if (session()->getFlashdata('setup_estatus') !== 'error') {
            // Si no hay mensaje de error, redirigir a inicio
            return redirect()->to('/login')->with('error', 'No se pudo completar la configuración de la base de datos.');
        }
        //Mostrar en vista el mensaje de error
        $mensaje = session()->getFlashdata('error');
        return view('errors/setupError', ['mensaje' => $mensaje]);
    }
}
?>