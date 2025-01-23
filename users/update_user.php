<?php
include("../connect.php");


if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.html");
    exit();
}

$token = $_POST['cf-turnstile-response'] ?? null;

if (!$token) {
    die("CF_TOKEN_NULL");
}

require_once '../cloudflare/verify.php';

$responseToken = $_POST['cf-turnstile-response'] ?? null;

$verificationResult = verifyTurnstile($responseToken);

if (!$verificationResult['success']) {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="CF Error">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';
    exit;
}

$userId = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $oldPassword = isset($_POST['oldPassword']) ?$_POST['oldPassword']: null;
    $newPassword = isset($_POST['newPassword']) ?$_POST['newPassword']: null;
    $oldEmail = isset($_SESSION['email']) ?$_SESSION['email']: null;
    $email = $_POST['email'];

    if (!$name || !$oldPassword) {
        echo "必須項目が入力されていません。";
        echo $userId.$name.$oldPassword;
        exit();
    }


    try {
        $result = $conn->prepare("SELECT password FROM users WHERE id = :userId");
        $result->execute(array(":userId"=>$userId));
        $user = $result->fetch();
        
        if (!$user || !password_verify($oldPassword,$user['password'])) {
            echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="PW Error">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
            exit;
        }

        if ($newPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = :name, password = :password,session_valid = 0 WHERE id = :userId");
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = :name WHERE id = :userId");
        }
        if ($email!=$oldEmail){
            include "../mail/mail_core.php";   
            $randomKey = uniqid();         
            try {
                $mail->Subject = 'Email Check';
                $mail->addAddress($email, $name); // 收件人
                include("../config.php");
                $mail->Body    = '
                <!DOCTYPE html>
                    <html lang="ja">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Email Verification</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 0;
                            }
                            .email-container {
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                border: 1px solid #ddd;
                                border-radius: 10px;
                                overflow: hidden;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            }
                            .email-header {
                                background-color: #007bff;
                                color: #ffffff;
                                text-align: center;
                                padding: 20px;
                            }
                            .email-header h1 {
                                margin: 0;
                                font-size: 24px;
                            }
                            .email-body {
                                padding: 20px;
                                color: #333;
                            }
                            .email-body p {
                                margin: 15px 0;
                                line-height: 1.6;
                            }
                            .email-body a {
                                display: inline-block;
                                padding: 10px 20px;
                                color: #ffffff;
                                background-color: #007bff;
                                text-decoration: none;
                                border-radius: 5px;
                                font-size: 16px;
                                margin-top: 15px;
                            }
                            .email-footer {
                                background-color: #f4f4f4;
                                text-align: center;
                                padding: 10px;
                                font-size: 12px;
                                color: #777;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <!-- Header -->
                            <div class="email-header">
                                <h1>簡易ブログ - メールアドレスの確認</h1>
                            </div>
            
                            <!-- Body -->
                            <div class="email-body">
                                <p>こんにちは！</p>
                                <p>簡易ブログをご利用いただきありがとうございます。</p>
                                <p>以下のボタンをクリックして、メールアドレスを確認してください：</p>
            
                                <!-- Verification Link -->
                                <a href="' .$emailCheckUrl. 'mail_check.php?auth='. md5(strtolower(trim($email.$randomKey))) .'" target="_blank">メールアドレスを確認する</a>
            
                                <p>このリンクは<strong>次回ログイン</strong>で期限切れとなります。期限内にメールアドレスを確認してください。</p>
            
                                <p>もしこのメールに覚えがない場合は、無視してください。</p>
                            </div>
            
                            <!-- Footer -->
                            <div class="email-footer">
                                &copy; 2025 簡易ブログ. All rights reserved.
                            </div>
                        </div>
                    </body>
                    </html>
            
                ';
                $mail->AltBody = 'This is a plain text version of the email content.';
                $mail->send();
                $_SESSION['email'] = $email;
                $_SESSION['email_auth'] = md5(strtolower(trim($email.$randomKey)));  
            } catch (Exception $e) {
                echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
                <input type="hidden" name="error_code" value="500">
                </form>
                <script>document.getElementById("errorForm").submit();</script>';
    
            }   
        }

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            header('Location: ../articles/search.php');
        } else {
            echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="500">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';

        }
    } catch (Exception $e) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
        <input type="hidden" name="error_code" value="500">
        </form>
        <script>document.getElementById("errorForm").submit();</script>';

    }
} else {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="400">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';

}
