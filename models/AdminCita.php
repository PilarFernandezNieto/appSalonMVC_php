<?php
namespace Model;

class AdminCita extends ActiveRecord {
    protected static $tabla = "citas_servicios";
    protected static $columnasDB = [
        "id", 
        "hora", 
        "servicio", 
        "precio", 
        "cliente", 
        "email", 
        "telefono"
    ];

    public $id;
    public $hora;
    public $servicio;
    public $precio;
    public $cliente;
    public $email;
    public $telefono;

    public function __construct($args= []){
        $this->id = $args["id"] ?? null;
        $this->hora = $args["hora"] ?? "";
        $this->servicio = $args["servicio"] ?? "";
        $this->precio = $args["precio"] ?? "";
        $this->cliente = $args["cliente"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->telefono = $args["telefono"] ?? "";
    }
}