<?php
include "../connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    header("Location: ../login/login.html");
    exit();
}

// データー統計
$stats = [
    'users' => 0,
    'articles' => 0,
    'comments' => 0,
];

try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
    $stmt->execute();
    $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM articles");
    $stmt->execute();
    $stats['articles'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM comment");
    $stmt->execute();
    $stats['comments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    die("データベースエラー: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者パネル</title>
    <link rel="stylesheet" href="../static/css/admin_panel.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="admin-panel">
    <aside class="sidebar">
        <h2>管理パネル</h2>
        <ul>
            <li><a href="../articles/search.php" class="nav-link">ブログに戻る</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <section id="dashboard" class="dashboard">
            <h1>ダッシュボード</h1>
            <div class="stats">
                <div class="stat-item">
                    <h3>ユーザー数</h3>
                    <p><?php echo htmlspecialchars($stats['users']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>記事数</h3>
                    <p><?php echo htmlspecialchars($stats['articles']); ?></p>
                </div>
                <div class="stat-item">
                    <h3>コメント数</h3>
                    <p><?php echo htmlspecialchars($stats['comments']); ?></p>
                </div>
            </div>
        </section>

        <section id="user-management" class="user-management">
            <h1>ユーザー管理</h1>
            <div class="search-box">
                <input type="text" id="user-search" class="search-input" placeholder="ユーザーID、氏名、メールアドレスで検索">
                <button id="search-button" class="search-button">検索</button>
            </div>
            <table class="user-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>UserId</th>
                    <th>氏名</th>
                    <th>メールアドレス</th>
                    <th>ロール</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="user-table-body">
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT * FROM users");
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['userID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . (isset($row['email']) ? htmlspecialchars($row['email']) : "NULL") . "</td>";
                            echo "<td>";
                            if ($row['role'] == '-1') {
                                echo "<span>ロール組</span>";
                                echo "</td>";
                                echo"<td></td>";
                            } elseif ($row['role'] == '1') {
                                echo "<span>管理員</span>";
                                echo "</td>";
                                echo"<td></td>";
                            } else {
                                echo "<select class='role-select' data-id='" . htmlspecialchars($row['id']) . "'>";
                                echo "<option value='0'" . ($row['role'] == '0' ? " selected" : "") . ">ユーザー</option>";
                                echo "<option value='1'" . ($row['role'] == '1' ? " selected" : "") . ">管理員</option>";
                                echo "<option value='-100'" . ($row['role'] == '-100' ? " selected" : "") . ">封鎖されたユーザー</option>";
                                echo "</select>";
                                echo "</td>";
                                echo "<td><button class='delete-user' data-id='" . htmlspecialchars($row['id']) . "'>削除</button></td>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>             
            </table>
        </section>

    </main>
</div>

<script>
    $(document).ready(function () {
        // ロール変更
        $('.role-select').on('change', function () {
            const userId = $(this).data('id');
            const newRole = $(this).val();

            if (confirm('ロールを変更しますか？')) {
                $.post('../admin/role_edit.php', { id: userId, role: newRole }, function (response) {
                    if (response.success) {
                        alert('ロールが正常に変更されました。');
                        location.reload();
                    } else {
                        alert('ロール変更に失敗しました: ' + response.message);
                    }
                }, 'json').fail(function () {
                    alert('サーバーエラーが発生しました。');
                });
            }
        });

    });
</script>
<script>
    $(document).ready(function () {
        // 検索機能
        $('#search-button').on('click', function () {
            const query = $('#user-search').val().trim();

            $.post('../admin/search_user.php', { query: query }, function (response) {
                if (response.success) {
                    const rows = response.data.map(user => {
                        return `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.userID}</td>
                                <td>${user.name}</td>
                                <td>${user.email ? user.email : 'NULL'}</td>
                                <td>
                                    ${user.role == '-1' ? 
                                        '<span>ロール組</span></td><td>' : 
                                        user.id == <?php echo $_SESSION['id']; ?> ? 
                                        '<span>管理員</span></td><td>' : 
                                        `<select class='role-select' data-id='${user.id}'>
                                            <option value='0' ${user.role == '0' ? 'selected' : ''}>ユーザー</option>
                                            <option value='1' ${user.role == '1' ? 'selected' : ''}>管理員</option>
                                            <option value='-100' ${user.role == '-100' ? 'selected' : ''}>封鎖されたユーザー</option>
                                        </select</td><td>
                                        <button class='delete-user' data-id='${user.id}'>削除</button>`}
                                </td>
                            </tr>
                        `;
                    }).join('');

                    $('#user-table-body').html(rows);
                } else {
                    alert('検索結果を取得できませんでした: ' + response.message);
                }
            }, 'json').fail(function () {
                alert('サーバーエラーが発生しました。');
            });
        });

        $('#user-table-body').on('click', '.delete-user', function () {
            const userId = $(this).data('id');

            if (confirm('本当にこのユーザーを削除しますか？')) {
                $.post('../admin/delete_user.php', { id: userId }, function (response) {
                    if (response.success) {
                        alert('ユーザーが正常に削除されました。');
                        location.reload(); 
                    } else {
                        alert('削除に失敗しました: ' + response.message);
                    }
                }, 'json').fail(function () {
                    alert('サーバーエラーが発生しました。');
                });
            }
        });

        $('#user-table-body').on('change', '.role-select', function () {
            const userId = $(this).data('id');
            const newRole = $(this).val();

            if (confirm('ロールを変更しますか？')) {
                $.post('../admin/role_edit.php', { id: userId, role: newRole }, function (response) {
                    if (response.success) {
                        alert('ロールが正常に変更されました。');
                    } else {
                        alert('ロール変更に失敗しました: ' + response.message);
                    }
                }, 'json').fail(function () {
                    alert('サーバーエラーが発生しました。');
                });
            }
        });
    });
</script>
</body>
</html>
