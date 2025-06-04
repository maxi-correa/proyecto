<?php
namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        // Cargar la vista de inicio de sesión
        return view('login');
    }

    public function procesarLogin()
    {
        $request = \Config\Services::request(); // Obtener el servicio de solicitud

        $email = $request->getPost('email');
        $password = $request->getPost('password');

        //Verificar que los campos no estén vacíos
        if (empty($email) || empty($password)) {
            return redirect()->to('/login')->with('error', 'El correo electrónico y la contraseña son obligatorios.');
        }
        // Validar que el email tenga un formato correcto
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/login')->with('error', 'El correo electrónico no es válido.');
        }
        
        // Si el password no es numérico o no tiene 6 caracteres, redirigir con error
        if (!is_numeric($password) || strlen($password) !== 6) {
            return redirect()->to('/login')->with('error', 'La contraseña debe ser numérica y tener exactamente 6 caracteres.');
        }

        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->where('email', $email)->first();
        // Verificar si el usuario existe y si la contraseña es correcta
        if (!$usuario) {
            return redirect()->to('/login')->with('error', 'El usuario no existe.');
        }

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Iniciar sesión
            session()->set('logueado', true);
            session()->set('id', $usuario['id']);
            session()->set('nombre', $usuario['nombre']);
            session()->set('apellido', $usuario['apellido']);
            return redirect()->to('/');
        } else {
            return redirect()->to('/login')->with('error', 'Credenciales incorrectas.');
        }
    }

    public function registro()
    {
        // Cargar la vista de registro
        return view('registro');
    }

    public function registrar()
    {
        $request = \Config\Services::request(); // Obtener el servicio de solicitud
        
        $datos = [
            'nombre' => $request->getPost('nombre'),
            'apellido' => $request->getPost('apellido'),
            'email' => $request->getPost('email'),
            'password' => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
            'creado_en' => date('Y-m-d H:i:s'),
        ];
        
        $password = $request->getPost('password');
        $password_verificado = $request->getPost('confirmar_password');
        
        // Validar que los campos no estén vacíos
        if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || empty($password) || empty($password_verificado)) {
            return redirect()->to('/registro')->withInput()->with('error', 'Todos los campos son obligatorios.');
        }

        // Validar que el nombre y el apellido solo contengan letras y espacios
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['nombre']) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['apellido'])) {
            return redirect()->to('/registro')->withInput()->with('error', 'El nombre y el apellido solo pueden contener letras y espacios.');
        }
        
        // Validar que el email tenga un formato correcto
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/registro')->withInput()->with('error', 'El correo electrónico no es válido.');
        }
        // Validar que el email no esté ya registrado
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->where('email', $datos['email'])->first();
        if ($usuario) {
            return redirect()->to('/registro')->withInput()->with('error', 'El correo electrónico ya está registrado.');
        }

        // Si el password no es numérico o no tiene 6 caracteres, redirigir con error
        if (!is_numeric($password) || strlen($password) !== 6) {
            return redirect()->to('/registro')->withInput()->with('error', 'La contraseña debe ser numérica y tener exactamente 6 caracteres.');
        }

        // Verificar que las contraseñas coincidan
        if ($password !== $password_verificado) {
            return redirect()->to('/registro')->withInput()->with('error', 'Las contraseñas no coinciden.');
        }

        // Insertar el usuario en la base de datos
        if ($usuarioModel->insert($datos)) {
            return redirect()->to('/login')->with('exito', 'Usuario registrado con éxito.');
        } else {
            return redirect()->to('/registro')->withInput()->with('error', 'No se pudo registrar el usuario.');
        }
    }
}
?>