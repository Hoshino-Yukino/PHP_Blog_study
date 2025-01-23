<?php include('../connect.php');?>
<?php
$currentDateTime = date('Y-m-d\TH:i');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規作成</title>
    <link rel="stylesheet" href="../static/css/create.css">
    <script src="../static/js/create.js" defer></script>
</head>
<body>
    <div class="create-container">
        <h2>新規記事作成</h2>

        <form method="POST" class="create-form" action="create_article.php">
            <div class="form-group">
                <label for="author">著者：</label>
                <?php 
                    if ($_SESSION['role'] == '1') {
                        echo "<input type='radio' name='author' value='{$_SESSION['id']}'>$_SESSION[name]";
                        echo "<input type='radio' name='author' value='-1'>管理員";
                    } else {
                        echo "<span id='author'>{$_SESSION['name']}</span>";
                        echo "<input type='hidden' name='author' value='{$_SESSION['id']}'>";
                    }
                ?>
            </div>

            <div class="form-group">
                <label for="subject">件名：</label>
                <input type="text" id="subject" name="subject" required>
            </div>

            <div class="form-group">
                <label for="body">本文：</label>
                <textarea id="body" name="body" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="modified">投稿時間：</label>
                <input type="radio" name="modified" value="now">現在
                <input type="radio" name="modified" value="yoyaku">予約
            </div>

            <div id="timePicker" class="form-group">
                <label for="scheduledTime">予約時間：</label>
                <input type="datetime-local" id="scheduledTime" name="time" min="<?php echo $currentDateTime; ?>">            
            </div>
            <div class="cf-turnstile" 
                data-sitekey="0x4AAAAAAA5RjmcHEOkVY3BE" 
                data-theme="light" data-language="ja" data-appearance="interaction-only"></div>
            <button type="submit" class="submit-btn">投稿</button>
        </form>
    </div>

</body>
</html>
