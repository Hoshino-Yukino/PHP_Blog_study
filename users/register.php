<?php

include("../connect.php");

require_once("../config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['registerUserId'] ?? null;
    $name = $_POST['registerName'] ?? null;
    $password = $_POST['registerPassword'] ?? null;

    $hCaptchaResponse = $_POST['h-captcha-response'] ?? null;

    if (!$userID || !$name || !$password || !$hCaptchaResponse) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="Invalid Item">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
        exit;
    }

    $hCaptchaVerifyUrl = "https://hcaptcha.com/siteverify";
    $data = [
        'secret' => $hCaptchaSecret,
        'response' => $hCaptchaResponse,
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($hCaptchaVerifyUrl, false, $context);
    $resultData = json_decode($result, true);

    if (!$resultData['success']) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="Hcaptcha Error">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE userID = :userID");
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->execute();
    $existingUserCount = $stmt->fetchColumn();

    if ($existingUserCount > 0) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="UserID重複">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (userID, password, name, role) VALUES (:userID, :password, :name, :role)");
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':role', 0, PDO::PARAM_INT); 

    if ($stmt->execute()) {
        header("Location:../login/login.html");
        
    } else {
        echo "<script>alert('登録中にエラーが発生しました。');</script>";
        echo "登録中にエラーが発生しました。";
    }
} else {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="400">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';

}
