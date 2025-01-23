<?php
    include("../connect.php");
    $login_id = isset($_SESSION['id'])? $_SESSION['id']:"";
    if($login_id==""){
        header("Location:../login/login.html");
        exit(0);
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete</title>
</head>
<body>
<?php
    if(isset($_GET["id"])){
        $id = $_GET["id"];
        if($_SESSION['role']=='1'){
            $statement = $conn->prepare(
                "DELETE FROM articles 
                            WHERE id=:id AND :login_id=:login_id" 
            );
        }else{
            $statement = $conn->prepare(
                "DELETE FROM articles 
                            WHERE id=:id AND author=:login_id" 
            );
        }
        $statement->execute(array(":id"=>$id,":login_id"=>$login_id));
        $count = $statement->rowCount();

        if($count==1){
            header("Location: articles/search.php");
			exit;
        }else{
            echo '<form id="errorForm" action="../static/error.php" method="post" style="display:none;">
            <input type="hidden" name="error_code" value="500">
            </form>
            <script>document.getElementById("errorForm").submit();</script>';

        }

    }
?>
</body>
</html>

