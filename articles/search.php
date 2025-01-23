<?php include('../connect.php');?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索ページ</title>
    <link rel="stylesheet" href="../static/css/search.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../static/js/search.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="new-entry">
                <?php
                    if(isset($_SESSION['id'])){
                ?>
                    [<a href="create.php" class="button-link" id="create-link" data-url="create.php">新規作成</a>]
                <?php
                    }
                ?>
            </div>
            <div class="user-info">
                <?php
                    if (isset($_SESSION['name'])) {
                        echo '<form action="../login/logout.php" class="logout-form" >';
                        if(isset($_SESSION['email_auth'])){
                            echo"<span class='welcome'>メールアドレス確認中</span>";
                        }
                        if(!isset($_SESSION['email_auth'])&&isset($_SESSION['email'])){
                            echo "<img src='https://www.gravatar.com/avatar/" . md5($_SESSION['email']) . "?s=50' alt='icon' class='icon'>";
                        }
                        if($_SESSION['role']=='1'){
                            echo "<span class='welcome'>" . 
                                "<a href='../users/update.php' id='user-link' class='user-link action-link'>" .
                                htmlspecialchars($_SESSION['name']) .
                                "</a> " .
                                "<a href='../admin/panel.php' class='admin-badge-link'>" .
                                '<img src="../static/img/admin-badge.webp" alt="管理員" class="admin-badge">' .
                                "</a>" .
                                "</span>";

                        }else{
                            echo "<span class='welcome'>" . "<a href='../users/update_user.php' id='user-link' class='user-link action-link'>" .htmlspecialchars($_SESSION['name']) ."</a>" ."</span>";
                        }
                        
                ?>
                
                    <button type="submit" class="logout-btn">ログアウト</button>
                </form>
                <?php 
                    } else {
                        echo "<span class='welcome'>ゲストさん、こんにちは。<a href='../login/login.html'>ログイン</a></span>";
                    }
                ?>
            </div>
        </div>

        <h3>ホーム</h3>
        <form method="POST" class="search-form">
            <input type="text" name="search" placeholder="検索ワードを入力">
            <button type="submit" class="search-btn">検索</button>
        </form>

        <?php
            $itemsPerPage = 5; 
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
            $offset = ($page - 1) * $itemsPerPage; 

            $search = isset($_POST["search"]) ? $_POST["search"] : null;

            $totalQuery = $conn->prepare(
                "SELECT COUNT(*) AS total 
                FROM users, articles 
                WHERE articles.author = users.id 
                AND (:search IS NULL OR articles.subject LIKE :search) 
                AND articles.modified<NOW()"
            );
            $totalQuery->execute(array(":search" => $search ? "%" . $search . "%" : null));
            $totalCount = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalCount / $itemsPerPage);

            $result = $conn->prepare(
                "SELECT articles.*, users.name, users.email
                FROM users, articles 
                WHERE articles.author = users.id 
                AND (:search IS NULL OR articles.subject LIKE :search) 
                AND articles.modified<NOW()
                ORDER BY 
                    CASE WHEN articles.author = -1 THEN 0 ELSE 1 END, 
                    articles.modified DESC
                LIMIT :offset, :itemsPerPage"
            );
            $result->bindValue(':search', $search ? "%" . $search . "%" : null, PDO::PARAM_STR);
            $result->bindValue(':offset', $offset, PDO::PARAM_INT);
            $result->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
            $result->execute();

            // 显示结果
            if ($result->rowCount() > 0) {
                foreach ($result as $r) {
                    $isImportant = $r['author'] == -1;
                    $cardClass = $isImportant ? "blog-card important" : "blog-card";
                    echo "<div class='$cardClass' data-id='" . htmlspecialchars($r['id']) . "' onclick=\"window.location.href='/blog/read.php?id=' + \" . htmlspecialchars($r[id]) >";
                    echo "<div class='blog-header'>";
                    echo "<h4 class='blog-title'>" . ($isImportant ? "<お知らせ> " : "") . htmlspecialchars($r['subject']) . "</h4>";
                    echo "</div>";
                    echo "<div class='blog-body'>";
                    echo "<p class='blog-author'>著者: " .htmlspecialchars($r['name']) . "</p>";
                    echo "<p class='blog-time'>更新時間: " . htmlspecialchars($r['modified']) . "</p>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='no-results'>該当する記事がありません。</p>";
            }

            // 分页导航
            if ($totalPages > 1) {
                echo "<div class='pagination'>";
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo "<span class='current-page'>$i</span>";
                    } else {
                        echo "<a href='?page=$i' class='page-link'>$i</a>";
                    }
                }
                echo "</div>";
            }
        ?>

    </div>
    
    <div class="modal">
        <div class="modal-content">
            <div class="modal-body" id="modalContent">

            </div>
        </div>
    </div>
</body>
</html>
