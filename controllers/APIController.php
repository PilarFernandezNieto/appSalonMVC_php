<?php
namespace Controllers;

use Model\Cita;
use MVC\Router;
use Model\Servicio;
use Model\CitaServicio;

class APIController {
    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar(){

        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $id = $resultado["id"];

        // Almacena citas y servicios
        $idServicios = explode(",", $_POST["servicios"] );
        foreach($idServicios as $idServicio){
            $args = [
                "citaId" => $id,
                "servicioId" => (int)$idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }
  
        echo json_encode(["resultado" => $resultado]);
    }

    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $id = $_POST["id"];
            $cita = Cita::find($id);
            $cita->eliminar();
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            
        }
    }
    public static function actualizar(Router $router){
        $id = $_GET["id"];
        $cita = Cita::find($id);

        $consulta = "SELECT servicios.id, servicios.nombre, servicios.precio ";
        $consulta .= "FROM servicios ";
        $consulta .= "JOIN citas_servicios ON servicios.id = citas_servicios.servicioId ";
        $consulta .= "JOIN citas ON citas_servicios.citaId = citas.id ";
        $consulta .= "WHERE citas.id = " . $id;
        $servicios = Servicio::SQL($consulta);
        
        $router->render("admin/actualizar", [
            "cita" => $cita,
            "servicios" => $servicios
        ]);
    }
    

}