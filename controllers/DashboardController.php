<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;


class DashboardController
{
    public static function index(Router $router)
    {
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsto('propietarioId',$id);

        $router->render('/dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos

        ]);
    }
    public static function crear_proyecto(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            $alertas = $proyecto->validadProyecto();
            if (empty($alertas)) {
                $hash = md5(uniqid());
                $proyecto->url = $hash;
                $proyecto->propietarioId = $_SESSION['id'];

                $proyecto->guardar();

                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('/dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();
        $proyecto = new Proyecto();
        $token = $_GET['id'];
        if(!$token){
            header('Location: /dashboard');
        }
        $proyecto = Proyecto::where('url',$token);

        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto',[
            'titulo'=>$proyecto->proyecto,
            'proyecto'=>$proyecto
        ]);



    }
    public static function perfil(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD']=='POST'){
            $usuario->sincronizar($_POST);
            $usuario->validarPerfil();
            $alertas = Usuario::getAlertas();
            if(empty($alertas)){
                $usuario->nombre = $usuario->nombre;
                $usuario->email = $usuario->email;

                $existeUsuario = Usuario::where('email',$usuario->email);
                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    Usuario::setAlerta('error','El Email ya pertenece a otra cuenta');
                    $alertas = $usuario->getAlertas();
                }else{
                    $resultado = $usuario->guardar();
                    if($resultado){
                        Usuario::setAlerta('exito','Usuario Actualizado Correctamente');
                        $alertas = $usuario->getAlertas();
                        $_SESSION['nombre']=$usuario->nombre;
                        $_SESSION['email']=$usuario->email;
                        // header('Location: /perfil');
                    }

                }
            }
        }

        $router->render('/dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas'=> $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        $alertas = [];
        session_start();
        isAuth();
        $usuario = Usuario::find($_SESSION['id']);
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();
                if($resultado){
                    $usuario->password = $usuario->password_nuevo;
                    $usuario->hashearPassword();
                    $resultado = $usuario->guardar();
                    if($resultado){
                        Usuario::setAlerta('exito','Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                }else{
                    Usuario::setAlerta('error','La contraseña actual no coincide');
                    $alertas = $usuario::getAlertas();
                }
            }
        }

        $router->render('/dashboard/cambiar-password',[
            'titulo' => 'Cambiar Password',
            'alertas'=> $alertas,
            'usuario' => $usuario
        ]);

    }
}
