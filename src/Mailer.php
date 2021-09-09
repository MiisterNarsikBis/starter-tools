<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class Mailer
 */
class Mailer
{
    /**
     * @return PHPMailer
     */
    public function setConfig()
    {
        $mail = new PHPMailer();
        //$mail->Host = '';
        //$mail->Port = 587;
        //$mail->SMTPAuth = true;
        //$mail->Username = '';
        //$mail->Password = '';
        //$mail->SMTPSecure = 'tls';
        //$mail->CharSet = "utf-8";

        return $mail;
    }

    /**
     * @param string $template
     * @param string $to
     * @param array $params
     * @param string $subject
     * @param array $files
     * @throws Exception
     */
    public function sendEmail(string $template, string  $to, array $params, string $subject, array $files = [])
    {
        ob_start();
        include($template);

        $MESSAGE = ob_get_clean();

        $mail = $this->setConfig();
        $mail->CharSet = "utf-8";
        $mail->setFrom(EMAIL_SENDER, NOM_CLIENT); // pw sstarter kit config
        $mail->addAddress($to);

        $mail->isHTML(true);  // Set email format to HTML

        $mail->Subject = $subject;
        if(!empty($files)){
            foreach ($files as $file) {
                $mail->AddAttachment($file, basename($file));
            }
        }

        $mail->Body = $MESSAGE;
        $mail->AltBody = strip_tags($MESSAGE);

        if (!$mail->send()) {
            echo '#1 - Mail not send - Erreur : '.$mail->ErrorInfo;
            die();
        }

    }

}