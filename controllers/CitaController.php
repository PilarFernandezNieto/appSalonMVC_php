<?php

namespace Controllers;

use Model\Cita;
use MVC\Router;
use Model\Servicio;

class CitaController {
    public static function index(Router $router) {
        if (!$_SESSION["nombre"]) {
            session_start();
        }
        isAuth();
        //debuguear($_SESSION);
        $router->render("cita/index", [
            "nombre"=>$_SESSION["nombre"],
            "id" => $_SESSION["id"]
        ]);
    }
    public static function actualizar(Router $router) {
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
