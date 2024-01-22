<?php

namespace Controllers;

use Classes\Email;
use MVC\Router;
use Model\Usuario;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();
            if (empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where("email", $auth->email);
                if ($usuario) {
                    // Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar al ususario
                        if (!isset($_SESSION)) {
                            session_start();
                        };
                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre . " " . $usuario->apellido ?? null;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        // Redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION["admin"] = $usuario->admin ?? null;
                            header("Location: /admin");
                        } else {
                            header("Location: /cita");
                        }
                    }
                } else {
                    Usuario::setAlerta("error", "Usuario no encontrado");
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render("auth/login", [
            "alertas" => $alertas,
            "nombre" => $_SESSION["nombre"] ?? null
        ]);
    }

    public static function logout() {
        if(!isset($_SESSION)){
            session_start();
        }
        $_SESSION = [];
        header("Location: /");
    }

    public static function crear(Router $router) {
        $usuario = new Usuario();
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
        }
        if (empty($alertas)) {

            $resultado = $usuario->existeUsuario();
            //debuguear($usuario);
            if ($resultado->num_rows) {
                $alertas = Usuario::getAlertas();
            } else {
                // Hashear password
                $usuario->hashPassword();

                // Generar un token único
                $usuario->crearToken();

                // Enviar el email
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarConfirmacion();

                // Crear el usuario
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header("Location: /mensaje");
                }
            }
        }

        $router->render("auth/crear_cuenta", [
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }
    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET["token"]);
        $usuario = Usuario::where("token", $token);

        if (empty($usuario)) {
            Usuario::setAlerta("error", "Token no válido");
        } else {
            $usuario->confirmado = "1";
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta("exito", "Cuenta confirmada correctamente");
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }
    public static function mensaje(Router $router) {
        $router->render("auth/mensaje");
    }



    public static function olvide(Router $router) {
        $alertas = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);
                if ($usuario && $usuario->confirmado === "1") {
                    // Generar un token de un solo uso
                    $usuario->crearToken();
                    $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de éxito
                    Usuario::setAlerta("exito", "Revisa tu email");
                } else {
                    Usuario::setAlerta("error", "El usuario no existe o no está cnfirmado");
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/olvide_password", [
            "alertas" => $alertas
        ]);
    }
   
    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET["token"]);

        $usuario = Usuario::where("token", $token);

        if (empty($usuario)) {
            Usuario::setAlerta("error", "Token no válido");
            $error = true;
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();


            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;

                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header("Location: /");
                }
                // debuguear($usuario);
            }
        }
        $alertas = Usuario::getAlertas();
        
        $router->render("auth/recuperar-password", [
            "alertas" => $alertas,
            "error" => $error

        ]);
    }
}
