<?php
include "../connect.php";

if (!isset($_POST['query'])) {
    echo json_encode(['success' => false, 'message' => '検索クエリが指定されていません。']);
    exit();
}

$query = "%" . $_POST['query'] . "%";

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE userID LIKE :query OR name LIKE :query OR email LIKE :query");
    $stmt->bindParam(':query', $query, PDO::PARAM_STR);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $users]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'データベースエラー: ' . $e->getMessage()]);
}
