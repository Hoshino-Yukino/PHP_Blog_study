<?php
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    echo json_encode(['success' => false, 'message' => '権限がありません。']);
    exit();
}

$userId = $_POST['id'] ?? null;
$newRole = $_POST['role'] ?? null;

if (!$userId || !in_array($newRole, ['0', '1', '-100'], true)) {
    echo json_encode(['success' => false, 'message' => '無効なデータです。']);
    exit();
}

if ($userId == $_SESSION['id']) {
    echo json_encode(['success' => false, 'message' => '自分のロールを変更することはできません。']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE users SET role = :role, session_valid = 0 WHERE id = :id");
    $stmt->bindParam(':role', $newRole, PDO::PARAM_INT);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'データベースエラー: ' . $e->getMessage()]);
}
?>
