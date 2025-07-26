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
            return redirect()->to('/partida');
        }
    }

    /** -----------------------------------------------
     * FUNCIONES QUE PERMITEN EL ACCESO A LA APLICACIÓN 
     * -----------------------------------------------*/
    
    //Se muestra la vista de inicio de sesión
    public function login()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado
        
        // Cargar la vista de inicio de sesión
        return view('login');
    }
    
    //Se procesa el incio de sesión
    public function procesarLogin()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado

        $request = \Config\Services::request(); // Obtener el servicio de solicitud

        $nombre = $request->getPost('nombre');
        $password = $request->getPost('password');

        //Verificar que los campos no estén vacíos
        if (empty($nombre) || empty($password)) {
            return redirect()->to('/login')->with('error', 'El nombre de usuario y la contraseña son obligatorios.');
        }
        // Validar que el nombre de usuario tenga un formato correcto
        if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ_]{4,}$/', $nombre)) {
            return redirect()->to('/login')->with('error', 'El nombre de usuario no es válido.');
        }
        
        // Si el password no es numérico o no tiene 6 caracteres, redirigir con error
        if (!is_numeric($password) || strlen($password) !== 6) {
            return redirect()->to('/login')->with('error', 'La contraseña debe ser numérica y tener exactamente 6 caracteres.');
        }

        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->where('nombreUsuario', $nombre)->first();
        // Verificar si el usuario existe y si la contraseña es correcta
        if (!$usuario) {
            return redirect()->to('/login')->with('error', 'El usuario no existe.');
        }

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Iniciar sesión
            session()->set('logueado', true);
            session()->set('id', $usuario['id']);
            session()->set('nombre', $usuario['nombreUsuario']);
            return redirect()->to('/partida')->with('exito', 'Inicio de sesión exitoso.');
        } else {
            return redirect()->to('/login')->withInput()->with('error', 'Credenciales incorrectas.');
        }
    }
    
    // Se muestra la vista de registro
    public function registro()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado
        
        $paisModel = new \App\Models\PaisModel();
        $data['paises'] = $paisModel->obtenerTodos();

        // Cargar la vista de registro
        return view('registro', $data);
    }
    
    // Se procesa el registro de un nuevo usuario
    public function procesarRegistro()
    {
        $this->verificar_logueo(); // Verificar si el usuario ya está logueado

        $request = \Config\Services::request(); // Obtener el servicio de solicitud
        
        $datos = [
            'nombreUsuario' => $request->getPost('nombre'),
            'email' => $request->getPost('email'),
            'fechaNacimiento' => $request->getPost('nacimiento'),
            'idPais' => $request->getPost('pais'),
            'password' => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
            'creado_en' => date('Y-m-d H:i:s'),
        ];
        
        $password = $request->getPost('password');
        $password_verificado = $request->getPost('confirmar_password');
        
        // Validar que los campos no estén vacíos
        if (empty($datos['nombreUsuario']) || empty($datos['email']) || empty($datos['fechaNacimiento']) || empty($datos['idPais']) || empty($password) || empty($password_verificado)) {
            return redirect()->to('/registro')->withInput()->with('error', 'Todos los campos son obligatorios.');
        }

        // Validar que el nombre contenga letras y números, guiones bajos sin espacios y un mínimo de 4 caracteres
        if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ_]{4,}$/', $datos['nombreUsuario'])) {
            return redirect()->to('/registro')->withInput()->with('error', 'El nombre solo puede contener letras, números y guiones bajos sin espacios y debe tener al menos 4 caracteres.');
        }
        
        //Validar que el nombre de usuario no se repita
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuarioExistente = $usuarioModel->where('nombreUsuario', $datos['nombreUsuario'])->first();
        if ($usuarioExistente) {
            return redirect()->to('/registro')->withInput()->with('error', 'El nombre de usuario ya está registrado.');
        }
        
        // Validar que el email tenga un formato correcto
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/registro')->withInput()->with('error', 'El correo electrónico no es válido.');
        }

        // Validar que el email no esté ya registrado
        $mailExistente = $usuarioModel->where('email', $datos['email'])->first();
        if ($mailExistente) {
            return redirect()->to('/registro')->withInput()->with('error', 'El correo electrónico ya está registrado.');
        }

        // Validar que la fecha de nacimiento este hasta 100 años en el pasado pero al menos 6 años atrás
        $fechaNacimiento = $datos['fechaNacimiento'];
        $fechaActual = date('Y-m-d');
        $fechaLimite = date('Y-m-d', strtotime('-6 years', strtotime($fechaActual)));
        $fechaLimiteMax = date('Y-m-d', strtotime('-100 years', strtotime($fechaActual)));
        if ($fechaNacimiento > $fechaLimite || $fechaNacimiento < $fechaLimiteMax) {
            return redirect()->to('/registro')->withInput()->with('error', 'La fecha de nacimiento debe ser al menos 6 años atrás y no más de 100 años atrás.');
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

    // Se devuelve la vista de recuperar contraseña
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

    // Se procesa la recuperación de contraseña
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