<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear una nueva instancia
            $auth = new Usuario($_POST);
           
            $alertas = $auth->validarLogin();

            // Validar que el usuario exista
            if (empty($alertas)) {
                //comprobar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    // verficar password]
                    if ($usuario->comprobarPasswordAndVerificado($auth->password) ) {
                        // autenticar al usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamiento
                        if ($usuario->admin) {
                            $_SESSION['admin'] = $usuario->admin ?? NULL;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }

                        debuguear($_SESSION);
                    }
                } else {
                    // El usuario no existe
                    Usuario::setAlerta('error', 'El usuario no existe');
                }
            }  
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    public static function logout()
    {
        session_start();
        
        $_SESSION = [];

        header('Location: /');
    }
    public static function forget(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear una nueva instancia
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                // verificar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado == 1) {
                    // generar un token
                    $usuario->generarToken();
                    
                    // guardar el token en la base de datos
                    $usuario->guardar();

                    // enviar un email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Se ha enviado instrucciones a tu email para reestablecer tu contraseña');

                    // // redireccionar
                    // header('Location: /mensaje');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no ha confirmado su cuenta');
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/forget', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = new Usuario($_POST);
            $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-contraseña', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function register(Router $router)
    {   
        // Crear una nueva instancia
        $usuario = new Usuario($_POST);

        // alertas vacias
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            
            // Revisar que alertas este vacio
            if (empty($alertas)) {
                // verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                    
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Generar un token
                    $usuario->generarToken();
                    
                    // Enviar email
                    $email = new Email( $usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }

                
            }
        }

        $router->render('auth/register', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];

        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // modificar a usuario confirmado
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar(); 
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }
        
        //obtener alertas 
        $alertas = Usuario::getAlertas();

        //renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}