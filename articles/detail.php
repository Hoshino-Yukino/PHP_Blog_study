<?php

include("../connect.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事詳細 - detail</title>
    <link rel="stylesheet" href="../static/css/detail.css">
    <script src="../static/js/detail.js" defer></script>

</head>
<body>

    <div class="container">
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : '99999999';
        $stmt = $conn->prepare(
            "SELECT articles.*, users.name, users.email FROM articles
            INNER JOIN users ON articles.author = users.id
            WHERE articles.id = :id"
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            echo "<div class='article'>";
            echo "<div class='article-icon'>";
            if (isset($article['email'])) {
                echo "<img src='https://www.gravatar.com/avatar/" . md5($article['email']) . "?s=60' alt='author-icon'>";
            } else {
                echo "<span>👤</span>"; // デフォルトアイコン
            }
            echo "</div>";
            echo "<div class='article-content'>";
            echo "<h1>" . htmlspecialchars($article['subject']) . "</h1>";
            echo "<p>" . nl2br(htmlspecialchars($article['body'])) . "</p>";
            echo "<small><strong>著者:</strong> " . htmlspecialchars($article['name']) . " | <strong>投稿日:</strong> " . htmlspecialchars($article['modified']) . "</small>";
            echo "</div>";
            echo "</div>";

            if ($article['author']!='-1'){
                echo "<div class='comments'>";
                echo "<h2>コメント</h2>";
            }

            $limit = 5; 
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            $commentCountStmt = $conn->prepare(
                "SELECT COUNT(*) FROM comment WHERE article_id = :article_id"
            );
            $commentCountStmt->bindParam(':article_id', $id, PDO::PARAM_INT);
            $commentCountStmt->execute();
            $totalComments = $commentCountStmt->fetchColumn();

            $totalPages = ceil($totalComments / $limit);

            $commentStmt = $conn->prepare(
                "SELECT comment.*, users.name, users.email FROM comment
                INNER JOIN users ON comment.author = users.id
                WHERE comment.article_id = :article_id
                ORDER BY comment.modified ASC
                LIMIT :limit OFFSET :offset"
            );
            $commentStmt->bindParam(':article_id', $id, PDO::PARAM_INT);
            $commentStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $commentStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $commentStmt->execute();
            $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

            $floorNumber = $offset + 1;

            if ($comments) {
                foreach ($comments as $comment) {
                    echo "<div class='comment'>";
                    echo "<div class='comment-icon'>";
                    if (isset($comment['email'])) {
                        echo "<img src='https://www.gravatar.com/avatar/" . md5($comment['email']) . "?s=50' alt='icon'>";
                    } else {
                        echo "<span>👤</span>";
                    }
                    echo "</div>";
                    echo "<div class='comment-content'>";
                    echo "<div class='comment-author'>";
                    echo "<span class='floor-number'>#{$floorNumber}</span> "; 
                    echo htmlspecialchars($comment['name']);
                    echo "</div>";
                    echo "<div class='comment-body'>" . nl2br(htmlspecialchars($comment['comment'])) . "</div>";
                    echo "<small>" . htmlspecialchars($comment['modified']) . "</small>";

                    if (
                        isset($_SESSION['id']) &&
                        (
                            $_SESSION['id'] == $comment['author'] || 
                            $_SESSION['id'] == $article['author'] ||
                            $_SESSION['role'] == '1' 
                        )
                    ) {
                        echo "<form action='delete_comment.php' method='post' style='display:inline;'>";
                        echo "<input type='hidden' name='comment_id' value='" . htmlspecialchars($comment['id']) . "'>";
                        echo "<input type='hidden' name='article_id' value='" . htmlspecialchars($id) . "'>";
                        echo "<button type='submit' class='delete-btn'>削除</button>";
                        echo "</form>";
                    }
            
                    echo "</div>";
                    echo "</div>";
            
                    $floorNumber++; 
                }
            } elseif ($article['author']=='-1'){
            
            } else{
                echo "<p>コメントはまだありません。</p>";
            }

            echo "<div class='pagination'>";
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i == $page ? 'active' : '';
                echo "<a href='?id=$id&page=$i' class='$active'>$i</a>";
            }
            echo "</div>";

            if (isset($_SESSION['id'])&&$article['author']!='-1') {
                echo'<div class="add-comment">
                <form action="add_comment.php" method="post">
                    <textarea name="comment" rows="4" placeholder="コメントを追加..." required></textarea>
                    <input type="hidden" name="article_id" value='.htmlspecialchars($id).'>
                        <div class="cf-turnstile" 
                            data-sitekey="0x4AAAAAAA5RjmcHEOkVY3BE" data-theme="light"
                            data-language="ja" data-appearance="interaction-only"></div>
                    <button type="submit">コメントを投稿</button>
                </form>
                </div>';
            } elseif ($article['author']=='-1'){

            } else {
                echo "<p>コメントを投稿するにはログインしてください。</p>";
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
</body>
</html>
