<?php
// エラーリスト
$errorMessages = [
    'UserID重複' => '既に登録されたユーザーIDです。',
    '400' => 'リクエストが無効です。',
    '401' => '認証に失敗しました。ログインしてください。',
    '403' => 'アクセス権限がありません。',
    '404' => 'ページが見つかりません。',
    '500' => 'サーバーエラーが発生しました。',
    'Invalid Item' => '必須項目が入力されていません。',
    'Hcaptcha Error' => 'hCaptcha 検証に失敗しました。もう一度お試しください。',
    'CF Error' => 'CloudFlare 検証に失敗しました。もう一度お試しください。',
    'PW Error' => 'パスワードが正しくありません。',
    'Email Auth Error' => 'メールアドレス認証失敗、やり直してください。',
    'Account BAN' => "申し訳ございませんが、あなたのアカウントは現在封鎖されています。" 
];

$errorCode = $_POST['error_code'] ?? null;

if ($errorCode==null){
    $errorCode = $_GET['code'] ?? null;
}

$errorMessage = $errorMessages[$errorCode] ?? '不明なエラーが発生しました。';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エラーページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
        }
        .error-container {
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #f5c6cb;
            background-color: #f8d7da;
            border-radius: 10px;
            max-width: 400px;
        }
        .error-code {
            font-size: 48px;
            font-weight: bold;
        }
        .error-message {
            font-size: 20px;
            margin-top: 10px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f5c6cb;
            color: #721c24;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #f1b0b7;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">
            <?php echo htmlspecialchars($errorCode ?: 'Error'); ?>
        </div>
        <div class="error-message">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <a href="javascript:history.back()">戻る</a>
    </div>
</body>
</html>
