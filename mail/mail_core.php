<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';
            $mail = new PHPMailer(true);
            
            try {
                $mail->isSMTP();                                
                $mail->Host       = '';         
                $mail->SMTPAuth   = true;                      
                $mail->Username   = '';   
                $mail->Password   = '';           
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port       = 587;                  
            
                $mail->setFrom('', 'noreply');

                $mail->isHTML(true);                           
                $mail->Subject = 'Email Check';
                
            } catch (Exception $e) {
                echo "{$mail->ErrorInfo}";
            }
        
?>
