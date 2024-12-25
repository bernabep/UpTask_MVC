<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;


class LoginController

{

    public static function login(Router $router)
    {
        session_start();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if(empty($alertas)){
                $usuario = Usuario::where('email',$usuario->email);
                
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error','El usuario no está registrado o no está confirmado');
                }else{
                    if(password_verify($_POST['password'],$usuario->password)){
                        Usuario::setAlerta('exito','Logado correctamente');
                        // Autenticar
                        
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //Redireccionar
                        header('Location: /dashboard');
                        
                        
                    }else {
                        Usuario::setAlerta('error','Password Incorrecto');
                    }
                    
                }
                
            }
            
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }
    public static function logout(Router $router)
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
        // $router->render('auth/index',[]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevoUsuario();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'Usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Crear un nuevo Usuario
                    //eliminamos password2 porque ya no lo necesitamos
                    unset($usuario->password2);

                    $usuario->hashearPassword();
                    $usuario->generarToken();
                    $resultado = $usuario->guardar();
                    if ($resultado) {

                        //Enviar email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $respuesta = $email->enviar_confirmacion();
                        header('Location: /mensaje');
                    }
                }
            }
        }
        $router->render('auth/crear', [
            'titulo' => 'Crear tu Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function olvide(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)){
                $usuario = Usuario::where('email',$usuario->email);
                
                if($usuario && $usuario->confirmado === "1"){
                    //Generar nuevo token
                    $usuario->generarToken();

                    //Actualizar el usuario
                    $usuario->guardar();
                    
                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $respuesta = $email->recuperar_cuenta();

                    

                    //Imprimir la alerta
                    if($respuesta){
                        Usuario::setAlerta("exito",'Hemos enviado las instrucciones a tu email');
                        $alertas = Usuario::getAlertas();
                        
                    }

                } else{
                    Usuario::setAlerta('error','El Usuario no Exite o no está confirmado');
                    $alertas = Usuario::getAlertas();

                }
            }
        }
        
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }
    public static function reestablecer(Router $router)
    {
        $mostrar = true;
        $alertas = [];
        $token = s($_GET['token']);
        if(!$token){
            header('Location: /');
        }

        $usuario = Usuario::where('token',$token);

        if(!$usuario){
            Usuario::setAlerta('error','El Token no es Válido');
            $mostrar = false;
            $alertas = Usuario::getAlertas();
            
        }
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                $usuario->hashearPassword();
                $usuario->token = null;
                $respuesta = $usuario->guardar();
                
                if($respuesta){
                    header('Location: /');
                }
    
            }
        }
        


        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
    public static function mensaje(Router $router)
    {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }
    public static function confirmar(Router $router)
    {
        $usuario = new Usuario();
        $alertas = [];
        $token = $_GET['token'];
        if (!$token) header('Location: /');

        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);
        //Si devuelve mas de un usuario, me quedo con el primero, no debería pasar nunca
        if (is_array($usuario) && !empty($usuario)) {
            debuguear($usuario);
            $usuario = array_shift($usuario);
        }
        
        if (empty($usuario)) {
            Usuario::setAlerta("error", "El token no es válido");
            $alertas = Usuario::getAlertas();
        } else {
            
            $usuario->confirmado = '1';
            $usuario->token = null;
            unset($usuario->password2);
            //Guardar en la BD
            $respuesta = $usuario->guardar();
            if ($respuesta) {
                Usuario::setAlerta('exito', "Usuario confirmado correctamente");
                $alertas = Usuario::getAlertas();
            }
        }


        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}
