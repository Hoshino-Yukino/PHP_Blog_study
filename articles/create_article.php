<?php 
    include("../connect.php");

    if (!isset($_SESSION['id'])) {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "未ログインです。"]);
        exit();
    }

        $token = $_POST['cf-turnstile-response'] ?? null;

        if (!$token) {
            die("CF_TOKEN_NULL");
        }

        require_once '../cloudflare/verify.php';

        $responseToken = $_POST['cf-turnstile-response'] ?? null;
    
        $verificationResult = verifyTurnstile($responseToken);

        if ($verificationResult['success']) {
            if (isset($_POST['author']) && isset($_POST['subject']) && isset($_POST['body'])) {
                $author = $_POST['author'];
                $subject = $_POST['subject'];
                $body = $_POST['body'];
                $modified = isset($_POST['time'])?$_POST['time']:null;
                if($modified){
                    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $modified);
                    
                    if ($dateTime) {
                        $modified = $dateTime->format('Y-m-d H:i:s'); // html->mysql
                    }
                    $statement = $conn->prepare(
                        "INSERT INTO `articles` (`id`, `subject`, `body`, `author`, `modified`) 
                        VALUES (NULL, :subject, :body, :author, :modified)"
                    );
                    if ($statement->execute([":subject" => $subject, ":body" => $body, ":author" => $author,":modified" => $modified])) {
                        $id = $conn->lastInsertId();
                        echo json_encode(["success" => true, "id" => $id]);
                    } else {
                        echo json_encode(["success" => false, "message" => "投稿に失敗しました。"]);
                    }
                    }else{
                    $statement = $conn->prepare(
                        "INSERT INTO `articles` (`id`, `subject`, `body`, `author`) 
                        VALUES (NULL, :subject, :body, :author)"
                    );
        
                    if ($statement->execute([":subject" => $subject, ":body" => $body, ":author" => $author])) {
                        $id = $conn->lastInsertId();
                        echo json_encode(["success" => true, "id" => $id]);
                    } else {
                        echo json_encode(["success" => false, "message" => "投稿に失敗しました。"]);
                    }
                }
        
            } else {
                echo json_encode(["success" => false, "message" => "全ての項目を入力してください。"]);
            }        
        } else {
            echo json_encode(["success" => false, "message" => "投稿に失敗しました。"]);
            if (isset($verificationResult['error-codes'])) {
                echo json_encode(["success" => false, "message" => "投稿に失敗しました。" .implode(", ", $verificationResult['error-codes'])]); 
            }
        }
    
