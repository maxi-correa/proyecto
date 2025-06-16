<?php
namespace App\Controllers;

class PasswordController extends BaseController
{
    public function cambiarContrasena()
    {
        // Verificar si el usuario está autenticado
        if (!session()->get('logueado')) {
            return redirect()->to('login');
        }
        // Cargar la vista para cambiar la contraseña
        return view('cambiarContrasena');
    }

    public function procesarCambiarContrasena()
    {
        // Verificar si el usuario está autenticado
        if (!session()->get('logueado')) {
            return redirect()->to('login');
        }

        $request = \Config\Services::request();
        $passwordActual = $request->getPost('password_actual');
        $passwordNueva = $request->getPost('password_nueva');
        $passwordConfirmar = $request->getPost('password_confirmar');

        // Validar los datos recibidos
        if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
            session()->setFlashdata('error', 'Todos los campos son obligatorios.');
            return redirect()->back();
        }
        
        // Verificar que la contraseña actual sea correcta
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->find(session()->get('id'));
        if (!$usuario || !password_verify($passwordActual, $usuario['password'])) {
            session()->setFlashdata('error', 'La contraseña actual es incorrecta.');
            return redirect()->back();
        }
        
        if (strlen($passwordNueva) !== 6 || !ctype_digit($passwordNueva)) {
            session()->setFlashdata('error', 'La nueva contraseña debe ser de 6 caracteres numéricos.');
            return redirect()->back();
        }

        if ($passwordNueva !== $passwordConfirmar) {
            session()->setFlashdata('error', 'Las contraseñas no coinciden.');
            return redirect()->back();
        }

        if (password_verify($passwordNueva, $usuario['password'])) {
            session()->setFlashdata('error', 'La nueva contraseña no puede ser igual a la actual.');
            return redirect()->back();
        }

        // Aquí iría la lógica para cambiar la contraseña en la base de datos
        $datos = ['password' => password_hash($passwordNueva, PASSWORD_DEFAULT)];
        $usuarioModel->update(session()->get('id'), $datos);
        // Actualizar la sesión con la nueva contraseña
        session()->set('password', $datos['password']);
        // Redirigir con un mensaje de éxito

        session()->setFlashdata('exito', 'Contraseña cambiada exitosamente.');
        return redirect()->to('cambiar_contrasena');
    }
}
?>