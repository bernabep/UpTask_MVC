<?php

namespace Classes;

use Model\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Email
{
    protected $email;
    protected $usuario;
    protected $token;

    public function __construct($email, $usuario, $token)
    {
        $this->email = $email;
        $this->usuario = $usuario;
        $this->token = $token;
    }

    public function enviar_confirmacion()
    { 
        $respuesta = false;
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['MAIL_PORT'];
            $mail->Username = $_ENV['MAIL_USER'];
            $mail->Password = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            // Configuración de la codificación (CLAVE)
            $mail->CharSet = 'UTF-8'; // Establece la codificación a UTF-8
            $mail->Encoding = 'base64'; // O '8bit' si tu servidor lo soporta, base64 es más compatible.

            //Recipients
            $mail->setFrom('cuentas@uptask.com', 'Mailer');
            $mail->addAddress($this->email, $this->usuario);     //Add a recipient


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Confirme su cuenta en UpTask';
            $contenido = '<p><strong>Hola ' . $this->usuario . '</strong></p>';
            $contenido .= 'Has Creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlaces:';
            $contenido .= '<p>Presiona aquí: <a href="'. $_ENV['APP_URL'] . '/confirmar?token=' . $this->token . '">Confirmar cuenta</a></p>';
            $contenido .= '<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>';
            $mail->Body = $contenido;
            $mail->AltBody = 'Por favor, copie el siguiente enlace en su navegador para confirmar su cuenta: '. $_ENV['APP_URL'] . '/confirmar?token=' . $this->token;
            $respuesta = $mail->send();
            
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        return $respuesta;
    }

    public function recuperar_cuenta(){ 
        $respuesta = false;
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '6e25219db0710b';
            $mail->Password = 'd4fab631faec9e';//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            // Configuración de la codificación (CLAVE)
            $mail->CharSet = 'UTF-8'; // Establece la codificación a UTF-8
            $mail->Encoding = 'base64'; // O '8bit' si tu servidor lo soporta, base64 es más compatible.

            //Recipients
            $mail->setFrom('bpandofdesarrollador@gmail.com', 'Mailer');
            $mail->addAddress($this->email, $this->usuario);     //Add a recipient


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Recuperar Contraseña UpTask';
            $contenido = '<p><strong>Hola ' . $this->usuario . '</strong></p>';
            $contenido .= 'Has solicitado recuperar tu contraseña en UpTask, solo debes cambiarla en el siguiente enlaces:';
            $contenido .= '<p>Presiona aquí: <a href="'. $_ENV['APP_URL'] . '/reestablecer?token=' . $this->token . '">Cambiar Contraseña</a></p>';
            $contenido .= '<p>Si tu no has solicitado recuperar la contraseña puedes ignorar este mensaje</p>';
            $mail->Body = $contenido;
            $mail->AltBody = 'Por favor, copie el siguiente enlace en su navegador para recuperar la contarseña de su cuenta: '. $_ENV['APP_URL'] . '/confirmar?token=' . $this->token;
            $respuesta = $mail->send();
            
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        return $respuesta;
    }
}