<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Base de Datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'celular', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $celular;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->celular = $args['celular'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validacion par ala creacion de la cuenta

    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][]= 'El nombre es obligatorio';
        }

        if(!$this->apellido) {
            self::$alertas['error'][]= 'El apellido es obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][]= 'El email es obligatorio';
        }

        if(!$this->celular) {
            self::$alertas['error'][]= 'El celular es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][]= 'El password es obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][]= 'El password debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][]= 'El Email es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][]= 'La Contraseña es obligatoria';
        }

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][]= 'El Email es obligatorio';
        }

        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][]= 'La Contraseña es obligatoria';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][]= 'La Contraseña debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function existeUsuario() {
        // Revisar si el usuario existe
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado->num_rows > 0) {
            self::$alertas['error'][] = 'El usuario ya esta registrado';
        }

        return $resultado;
    }

    public function hashPassword() {
        // Hashear el password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function generarToken() {
        // Generar un token
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);

        if(!$resultado ||!$this->confirmado) {
            self::$alertas['error'][] = 'Contraseña incorrecta o no has confirmado tu cuenta';
        }
        return true;
    }
}