<?php
include("../connect.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.html");
    exit();
}

$commentId = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : null;
$articleId = isset($_POST['article_id']) ? intval($_POST['article_id']) : null;

if ($commentId && $articleId) {
    $stmt = $conn->prepare("
        SELECT c.author, a.author AS article_author 
        FROM comment c 
        INNER JOIN articles a ON c.article_id = a.id 
        WHERE c.id = :comment_id
    ");
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment) {
        $userId = $_SESSION['id'];
        $isAuthor = $comment['author'] == $userId; 
        $isArticleAuthor = $comment['article_author'] == $userId; 
        $isAdmin = $_SESSION['role'] == '1'; 

        if ($isAuthor || $isArticleAuthor || $isAdmin) {
            $deleteStmt = $conn->prepare("DELETE FROM comment WHERE id = :comment_id");
            $deleteStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
            $deleteStmt->execute();

            header("Location: detail.php?id=" . $articleId);
            exit();
        } else {
            echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="403">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';

        }
    } else {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="404">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';

    }
} else {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="400">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';
}
?>
