<?php
namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','password','token','confirmado'];
    public $id;
    public $nombre;
    public $email;        // Propiedad `email`
    public $password;
    public $password2;
    public $token;
    public $confirmado;   // Propiedad `confirmado`

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;        
    }

    public function validarNuevoUsuario(){
        if (!$this->nombre){
            self::$alertas['error'][] = 'El nombre debe estar relleno';        
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }elseif(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El Email introducido no es valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if(strlen($this->password)<8){
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        if($this->password!==$this->password2){
            self::$alertas['error'][] = 'Los password no coinciden';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }elseif(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El Email introducido no es valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if(strlen($this->password)<8){
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if(strlen($this->password)<8){
            self::$alertas['error'][] = 'El Password debe contener mínimo 8 caracteres';
        }
        if($this->password!==$this->password2){
            self::$alertas['error'][] = 'Los password no coinciden';
        }
        return self::$alertas;
    }

    public function generarToken(){
        $this->token = bin2hex(random_bytes(15));
    }

    public function hashearPassword(){
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }elseif(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El Email No Es Válido';
        }
        return self::$alertas;
    }
}
?>
