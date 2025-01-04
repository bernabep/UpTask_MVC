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
            $usuarioFormulario = new Usuario($_POST);
            $usuarioFormulario->validarModificacionUsuario();
            $alertas = Usuario::getAlertas();
            if(empty($alertas)){
                $usuario->nombre = $usuarioFormulario->nombre;
                $usuario->email = $usuarioFormulario->email;
                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /perfil');
                }
                debuguear($resultado);
            }
            debuguear($alertas);


        }

        $router->render('/dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas'=> $alertas,
            'usuario' => $usuario
        ]);
    }
}
