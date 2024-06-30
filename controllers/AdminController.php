<?php
namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router){
       if(!isset($_SESSION)){
        session_start();
       }
     
        isAdmin();
      
       $fecha = $_GET["fecha"] ?? date("Y-m-d");
       $fechas = explode("-", $fecha);
       if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
        header("Location: /404");
       }

       // Consultar la base de datos
       $consulta = "SELECT citas.id, citas.hora, servicios.nombre AS servicio, servicios.precio,";
       $consulta .= "CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS cliente,";
       $consulta .= "usuarios.email, usuarios.telefono ";
       $consulta .= "FROM citas ";
       $consulta .= "LEFT OUTER JOIN usuarios ON usuarios.id = citas.idUsuario ";
       $consulta .= "LEFT OUTER JOIN citas_servicios ON citas_servicios.citaId = citas.id ";
       $consulta .= "LEFT OUTER JOIN servicios ON servicios.id = citas_servicios.servicioId ";
       $consulta .= " WHERE fecha =  '". $fecha ."' ORDER BY citas.hora";

      $citas = AdminCita::SQL($consulta);
        $router->render("admin/index", [
            "nombre"=> $_SESSION["nombre"],
            "citas" =>$citas,
            "fecha" => $fecha,
        ]);
    }
}