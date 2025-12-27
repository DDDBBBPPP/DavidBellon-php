<?php

namespace Controladores;

use Clases\Request;
use Clases\Sesion;
use Clases\Auth;
use Modelos\Administrador;
use Modelos\Cliente;
use Modelos\UsuarioModelo;

class AuthController extends BaseController
{
    /**
     * Login (GET muestra vista / POST procesa)
     */
    public function index(): void
    {
        // GET  mostrar login
        if (Request::esMetodo("get")) {
            $this->render("auth/login.twig");
            return;
        }

        // POST
        $email = Request::obtener("email") ?? "";
        $password = Request::obtener("password") ?? "";

        $usuario = Auth::login($email, $password);

        // LOGIN OK  REDIRECCIÓN (NO RENDER)
        if ($usuario instanceof \Modelos\Administrador) {
            Request::redirigirARuta("admin.inicio");
            exit;
        }

        if ($usuario instanceof \Modelos\Cliente) {
            Request::redirigirARuta("cliente.inicio");
            exit;
        }

        // LOGIN KO → REDIRECCIÓN (NO RENDER)
        Request::redirigir("/login?error=1");

    }




    /**
     * Registro de nuevo cliente (GET muestra vista / POST procesa)
     */
    public function registro(): void
    {
        if (Request::esMetodo("get")) {
            $this->render("auth/registro.twig");
            return;
        }

        // Recoger y limpiar datos
        $nombre     = trim(Request::obtener("nombre") ?? "");
        $apellidos  = trim(Request::obtener("apellidos") ?? "");
        $email      = trim(Request::obtener("email") ?? "");
        $password   = Request::obtener("password") ?? "";
        $telefono   = trim(Request::obtener("telefono") ?? "");
        $direccion  = trim(Request::obtener("direccion") ?? "");

        // Validaciones
        if ($nombre === "" || $email === "" || $password === "") {
            $this->render("auth/registro.twig", [
                "error" => "Nombre, email y contraseña son obligatorios"
            ]);
            return;
        }
        // Validamos con uso de filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render("auth/registro.twig", [
                "error" => "El email no tiene un formato válido"
            ]);
            return;
        }

        if (strlen($password) < 6) {
            $this->render("auth/registro.twig", [
                "error" => "La contraseña debe tener al menos 6 caracteres"
            ]);
            return;
        }

        // base de datos
        $ok = UsuarioModelo::registrarCliente(
            $nombre,
            $apellidos ?: null,
            $email,
            $password,
            $telefono ?: null,
            $direccion ?: null
        );

        if (!$ok) {
            $this->render("auth/registro.twig", [
                "error" => "El email ya está registrado"
            ]);
            return;
        }

        // login
        Request::redirigir("/login?registrado");
    }



    /**
     * Logout
     */
    public function logout(): void
    {
        Auth::logout();
        Request::redirigir("/login");
    }
}
