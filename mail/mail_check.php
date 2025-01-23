<?php
    include "../connect.php";

    if (!isset($_SESSION['id'])) {
        header("Location: ../login/login.html");
        exit();
    } 
    
    $email_auth = isset($_GET['auth'])? $_GET['auth']: null;
    if($email_auth==null){
        header('Location: ../articles/search.php');
    }
    
    if($email_auth==$_SESSION['email_auth']){
        $stmt = $conn->prepare("UPDATE users SET email = :email WHERE id = :userId");
        $stmt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
        $stmt->bindParam(':userId', $_SESSION['id'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['email_auth'] = null;
            header('Location: ../articles/search.php');
        } else {
            header('Location: ../static/error.php?code=Email Auth Error');
        }
    }else{
        header('Location: ../static/error.php?code=404');
    }