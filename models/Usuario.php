<?php

namespace Model;

use Model\ActiveRecord;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];
    public $id;
    public $nombre;
    public $email;        // Propiedad `email`
    public $password;
    public $password2;
    public $password_actual;
    public $password_nuevo;
    public $token;
    public $confirmado;   // Propiedad `confirmado`

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function validarNuevoUsuario(): array
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre debe estar relleno';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email introducido no es valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if (strlen($this->password) < 8) {
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los password no coinciden';
        }
        return self::$alertas;
    }

    public function validarPerfil(): array
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre debe estar relleno';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email introducido no es valido';
        }
        return self::$alertas;
    }

    public function validarLogin(): array
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email introducido no es valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if (strlen($this->password) < 8) {
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        return self::$alertas;
    }

    public function validarPassword(): array
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if (strlen($this->password) < 8) {
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los password no coinciden';
        }
        return self::$alertas;
    }

    public function generarToken(): void
    {
        $this->token = bin2hex(random_bytes(15));
    }

    public function hashearPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email No Es Válido';
        }
        return self::$alertas;
    }

    public function nuevo_password(): array
    {
        if (!$this->password_actual) {
            self::setAlerta('error', 'El Password Actual es obligatorio');
        }
        if (!$this->password_nuevo) {
            self::setAlerta('error', 'El Password Nuevo es obligatorio');
        }
        if (strlen($this->password_nuevo) < 8) {
            self::setAlerta('error', 'El Password Nuevo debe contener mínimo 8 caracteres');
        }

        return self::$alertas;
    }
    public function comprobar_password(){

        return password_verify($this->password_actual, $this->password);
    }
}