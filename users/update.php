<?php
include("../connect.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.html");
    exit();
}

$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(":id", $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p>ユーザーが見つかりません。</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール更新</title>
    <link rel="stylesheet" href="../static/css/update_user.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
<div class="update_container">
    <h1>プロフィール更新</h1>
    <form action="../users/update_user.php" method="post" class="update-form">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" value="<?php echo htmlspecialchars($user['userID']); ?>" disabled>
        <label for="name">名前: <span class="required">*</span></label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($user['email'])?htmlspecialchars($user['email']):""; ?>">
        <label for="old-password">旧パスワード: <span class="required">*</span></label>
        <input type="password" id="old-password" name="oldPassword" required>
        <label for="new-password">新パスワード:</label>
        <input type="password" id="new-password" name="newPassword">
        <div class="cf-turnstile" 
             data-sitekey="0x4AAAAAAA5RjmcHEOkVY3BE"
             data-theme="light"
             data-language="ja" data-appearance="interaction-only">
        </div>
        <button type="submit">更新</button>
    </form>
</div>
</body>
</html>
