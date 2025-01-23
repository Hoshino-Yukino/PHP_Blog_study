<?php

include("../connect.php");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事詳細 - read</title>
    <link rel="stylesheet" href="../static/css/read.css">
    <script src="../static/js/read.js" defer></script>
</head>
<body>
<div class="read_container">
    <?php
    $isFromSearch = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'search.php') !== false;

    if (!$isFromSearch && isset($_SESSION['name'])) {
        echo "<div class='header'>";
        echo "<span class='welcome'>" . htmlspecialchars($_SESSION['name']) . "さん使用中</span>";
        ?>
        <form action="../login/logout.php" method="post">
            <button type="submit" class="logout-btn">ログアウト</button>
        </form>
        <?php
        echo "</div>";
    }
    ?>

    <div class="article-details">
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : '99999999';
        $stmt = $conn->prepare(
            "SELECT articles.*, users.name 
             FROM users 
             INNER JOIN articles ON articles.author = users.id 
             WHERE articles.id = :id"
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            echo "<div class='article-card'>";
            echo "<h2>表題</h2>";
            echo "<p>" . htmlspecialchars($article['subject']) . "</p>";
            echo "</div>";

            echo "<div class='article-card'>";
            echo "<h2>本文</h2>";
            echo "<p>" . nl2br(htmlspecialchars($article['body'])) . "</p>";
            echo "</div>";

            echo "<div class='article-card'>";
            echo "<h2>著者</h2>";
            echo "<p>" . htmlspecialchars($article['name']) . "</p>";
            echo "</div>";

            echo "<div class='article-card'>";
            echo "<h2>更新日</h2>";
            echo "<p>" . htmlspecialchars($article['modified']) . "</p>";
            echo "</div>";
            echo "<div class='actions'>";
            echo "<a href='detail.php?id=" . $article['id'] . "' class='action-link' target='_blank'>詳細</a>";
            if (isset($_SESSION['id']) && ($_SESSION['id'] == $article['author'] || $_SESSION['role'] == '1')) {
                
                echo "<a href='update.php?id={$article['id']}' class='action-link update-link' id='update-link' data-url=\"update.php?id={$article['id']}\">更新</a>";
                echo "<a href='delete.php?id=" . $article['id'] . "' class='action-link delete-link'>削除</a>";
                
            }
            echo "</div>";
        } else {
            echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="404">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';

        }
        ?>
    </div>
</div>
</body>
</html>
