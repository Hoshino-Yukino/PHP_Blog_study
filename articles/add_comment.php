<?php

include("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id'])) {
        header("Location: ../login.php");
        exit;
    }

    $comment = trim($_POST['comment']); 
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $user_id = $_SESSION['id'];

    if (empty($comment) || $article_id <= 0) {
        $_SESSION['error'] = 'コメントを追加するには有効な内容が必要です。';
        header("Location: detail.php?id=$article_id");
        exit;
    }

    try {
        $stmt = $conn->prepare(
            "INSERT INTO comment (article_id, author, comment, modified) 
             VALUES (:article_id, :author, :comment, NOW())"
        );
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':author', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        $stmt->execute();

        $_SESSION['success'] = 'コメントが正常に投稿されました。';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'コメントを投稿中にエラーが発生しました。';
    }

    header("Location: detail.php?id=$article_id");
    exit;
} else {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="400">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';

}
?>
