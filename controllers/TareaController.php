<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController
{

    public static function index()
    {
        session_start();
        $proyectoId = $_GET['id'];
        if (!$proyectoId) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoId);

        if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belongsto('proyectoId', $proyecto->id);
        echo json_encode([
            'tareas' => $tareas
        ]);
    }

    public static function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $proyectoId = $_POST['proyectoId'];
            $proyecto = Proyecto::where('url', $proyectoId);
            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al Agregar la Tarea'
                ];
                return;
            }


            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();

            $respuesta = [
                'tipo' => 'exito',
                'mensaje' => 'Tarea Creada Correctamente',
                'id' => $resultado['id'],
                'proyectoId' => $proyecto->id
            ];


            echo json_encode($respuesta);
        }
    }

    public static function actualizar()
    {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = Proyecto::where('url',$_POST['url']);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al Agregar la Tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            
            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Tarea Actualizada Correctamente',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id
                ];
                echo json_encode(['respuesta' => $respuesta]);
                return;
            }


            
            $tarea->estado = $_POST['estado'];
            $resultado = $tarea->guardar();
            
            // echo json_encode($tarea);


            echo json_encode($respuesta);
        }
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $proyecto = Proyecto::find($_POST['proyectoId']);
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al eliminar la Tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();
            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Tarea Eliminada Correctamente',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id
                ];
                echo json_encode(['respuesta' => $respuesta]);
                return;
            }

            echo json_encode($_POST);
        }
    }
}
