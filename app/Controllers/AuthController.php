<?php
namespace App\Controllers;

class AuthController extends BaseController
{
    /** ---------------------------------------
     * FUNCIONES QUE AYUDAN A LAS SIGUIENTES 
     * ---------------------------------------*/
    
    public function comprobar_bd()
    {
        $db = db_connect();
        
        try{
            // Verificar si la base de datos existe
            $dbName = env('database.default.database'); // Nombre de la base de datos
            $consulta = $db->query("SHOW DATABASES LIKE '$dbName'");

        } catch (\Exception $e) {
            return false; // Si hay un error al conectarse, la base de datos no existe
        }

        if ($consulta->getNumRows() > 0) {
            return true; // La base de datos existe
        } else {
            return false; // La base de datos no existe
        }
    }

    public function verificar_logueo()
    {
        // Verificar si el usuario ya está autenticado
        if (session()->get('logueado')) {
            // Redirigir a la página principal si ya está autenticado
            return redirect()->to('/');
        }
    }

    /** -----------------------------------------------
     * FUNCIONES QUE PERMITEN EL ACCESO A LA APLICACIÓN 
     * -----------------------------------------------*/

    public function login()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado
        
        $existe_bd = $this->comprobar_bd();
        
        // Cargar la vista de inicio de sesión
        return view('login', ['existe_bd' => $existe_bd]);
    }

    public function procesarLogin()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado

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
            return redirect()->to('/login')->withInput()->with('error', 'Credenciales incorrectas.');
        }
    }

    public function registro()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado
        
        // Cargar la vista de registro
        return view('registro');
    }

    public function procesarRegistro()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado

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


    public function recuperarContrasena()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado

        $existe_bd = $this->comprobar_bd();
        if (!$existe_bd) {
            return redirect()->to('/login')->with('error', 'La base de datos no existe.');
        }

        // Cargar la vista de recuperar contraseña
        return view('recuperarContrasena');
    }

    public function procesarRecuperarContrasena()
    {
        // Procesar el formulario de recuperación de contraseña
        $email = $this->request->getPost('email');
        if (empty($email)) {
            return redirect()->to('/recuperar')->with('error', 'El correo electrónico es obligatorio.');
        }
        // Validar que el email tenga un formato correcto
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/recuperar')->with('error', 'El correo electrónico no es válido.');
        }
        
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->where('email', $email)->first();
        
        if (!$usuario) {
            return redirect()->to('/recuperar')->withInput()->with('error', 'El usuario no existe.');
        }

        if($usuario)
        {
            $nuevaContrasena = random_int(100000, 999999); // Generar una nueva contraseña aleatoria de 6 dígitos
            $usuarioModel->update($usuario['id'], ['password' => password_hash($nuevaContrasena, PASSWORD_DEFAULT)]);

            // Enviar un correo electrónico con la nueva contraseña al usuario
            $servicioEmail = \Config\Services::email();
            $servicioEmail->setTo($email);
            $servicioEmail->setSubject('Nueva contraseña');
            $servicioEmail->setMessage("Su nueva contraseña es: $nuevaContrasena");
            if ($servicioEmail->send()) {
                return redirect()->to('/login')->with('exito', 'Se ha enviado un correo electrónico con la nueva contraseña.');
            } else {
                return redirect()->to('/recuperar')->withInput()->with('error', 'No se pudo enviar el correo electrónico.');
            }
        }
    }
}

?>