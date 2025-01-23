<?php
    session_start();
    $conn = new PDO(
        "mysql:host=mysql;port=3306;dbname=test;charset=utf8",
        "root",
        "root_password"
    );

    $token = $_POST['cf-turnstile-response'] ?? null;

    if (!$token) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="CF Error">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
    }

    require_once '../cloudflare/verify.php';

    $responseToken = $_POST['cf-turnstile-response'] ?? null;

    $verificationResult = verifyTurnstile($responseToken);

    if ($verificationResult['success']) {
        $u = isset($_POST["userId"])?$_POST["userId"]:"";
        $p = isset($_POST["password"])?$_POST["password"]:"";
        if (empty($u)||empty($p)){
            header("Location: login.html");
        }
        try{
            $result = $conn->prepare(
                "SELECT * FROM users WHERE userID = :id AND role IN (1, 0,-100)"
            );
            $result->execute(array(":id"=>$u));
            $r = $result->fetch();
            if($r && password_verify($p,$r['password'])){
                if($r['role']=="-100"){
                    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
                    <input type="hidden" name="error_code" value="Account BAN">
                    </form>
                    <script>document.getElementById("errorForm").submit();</script>';
                    exit;
                }
                $updateStmt = $conn->prepare("UPDATE users SET session_valid = 1 WHERE id = :id");
                $updateStmt->bindParam(':id', $r['id'], PDO::PARAM_INT);
                $updateStmt->execute();  
                          
                $_SESSION['id'] = $r['id'];
                $_SESSION['name'] = $r['name'];
                $_SESSION['role'] = $r['role'];
                $_SESSION['email'] = isset($r['email'])?$r['email']:null;
                $conn = null;
                header("Location: ../articles/search.php");
            }else{
                header("Location: failure.html");
            }
        }catch(PDOException $e){
            die($e->getMessage());
        }
    } else {
        echo "CF認証失败：";
        if (isset($verificationResult['error-codes'])) {
            echo implode(", ", $verificationResult['error-codes']);
        }
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="CF Error">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
    }
