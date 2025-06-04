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
        return view('login', ['mensaje' => 'Sesión cerrada correctamente.']);
    }
}
?>