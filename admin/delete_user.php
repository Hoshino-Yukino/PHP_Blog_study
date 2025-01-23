<?php
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    echo json_encode(['success' => false, 'message' => '権限がありません。']);
    exit();
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ユーザー ID が指定されていません。']);
    exit();
}

$userId = $_POST['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'ユーザーが見つかりません。']);
        exit();
    }

    // 削除ユーザーロール check
    if ($user['role'] == '-1' || $user['id'] == $_SESSION['id']) {
        echo json_encode(['success' => false, 'message' => 'このユーザーは削除できません。']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'データベースエラー: ' . $e->getMessage()]);
}
