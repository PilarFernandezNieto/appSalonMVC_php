<?php
namespace Model;

class Cita extends ActiveRecord {
    // Base de datos
    protected static $tabla = "citas";
    protected static $columnasDB = ["id", "fecha", "hora", "idUsuario"];

    public $id;
    public $fecha;
    public $hora;
    public $idUsuario;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->fecha = $args["fecha"] ?? "";
        $this->hora = $args["hora"] ?? "";
        $this->idUsuario = $args["idUsuario"] ?? "";

    }
}