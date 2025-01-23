<?php
	header("Content-Type: text/html; charset=UTF-8");
    session_start();
    try{
        $conn = new PDO(
            "mysql:host=mysql;port=3306;dbname=test;charset=utf8",
            "root",
            "root_password"
        );
        if (isset($_SESSION['id'])) {
            $stmt = $conn->prepare("SELECT session_valid FROM users WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($result && $result['session_valid'] == 0) {
                // 如果会话无效，销毁会话并重定向到登录页面
                session_destroy();
                header("Location: ../login/login.html");
                exit();
            }
        }
    }catch(PDOException $e){
        die($e->getMessage());
    }
