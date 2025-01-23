<?php
include("../connect.php");
$login_id = isset($_SESSION['id']) ? $_SESSION['id'] : "";
$id = isset($_GET['id']) ?$_GET['id']: "";
if ($login_id == "") {
    header("Location:../login/login.html");
    exit;
}


if(isset($_POST['id'])){
    $id = $_POST['id'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
	$modified = null;
    if ($_SESSION['role']=='1'){
        $statement = $conn->prepare(
            "UPDATE articles 
                        SET subject=:subject, body=:body, modified=NOW()
                        WHERE id=:id"
        );
    }else{
        $statement = $conn->prepare(
            "UPDATE articles 
                        SET subject=:subject, body=:body, modified=NOW()
                        WHERE id=:id AND author={$login_id}"
        );
    }
    $statement->execute(array(
    ":subject" => $subject,
    ":body" => $body,
    ":id" => $id
	));
    $count = $statement->rowCount();
    if($count==1){
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
        <input type="hidden" name="error_code" value="500">
        </form>
        <script>document.getElementById("errorForm").submit();</script>';
		
        header("Location: search.php");
    }else{
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
        <input type="hidden" name="error_code" value="500">
        </form>
        <script>document.getElementById("errorForm").submit();</script>';

    }
}
if ($id) {
    if($_SESSION['role']=='1'){
        $statement = $conn->prepare(
            "SELECT articles.*, users.name
            FROM articles, users
            WHERE articles.author = users.id AND articles.id = :id AND :login_id = :login_id"
        );
    }else{
        $statement = $conn->prepare(
            "SELECT articles.*, users.name
            FROM articles, users
            WHERE articles.author = users.id AND articles.author = :login_id AND articles.id = :id"
        );
        
    }
    $statement->execute(array(":login_id" => $login_id, ":id" => $id));

    $r = $statement->fetch();
    if (!$r) {
        echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
        <input type="hidden" name="error_code" value="500">
        </form>
        <script>document.getElementById("errorForm").submit();</script>';
        exit;
    }
} else {
    echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
    <input type="hidden" name="error_code" value="403">
    </form>
    <script>document.getElementById("errorForm").submit();</script>';
    exit;
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../static/js/update.js" defer></script>
    <title>記事更新</title>
    <link rel="stylesheet" href="../static/css/update.css">
</head>
<body>
    <div id="updateModalContent">
        <h3>記事更新</h3>
        <form id="updateForm" method="POST" action="update.php">
            <input type="hidden" name="id" value="<?php echo($id); ?>">
            <label for="subject">件名：</label>
            <input type="text" id="subject" name="subject" value="<?php echo($r['subject']); ?>" required>

            <label for="body">本文：</label>
            <textarea id="body" name="body" required><?php echo($r['body']); ?></textarea>
            <div class="cf-turnstile" 
                data-sitekey="0x4AAAAAAA5RjmcHEOkVY3BE" 
                data-theme="light" data-language="ja" data-appearance="interaction-only"></div>
            <button type="button" id="submitUpdateButton">変更</button>
        </form>
    </div>
</body>
</html>

