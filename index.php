<?php
// Redirect to /articles/search.php if accessing root
if ($_SERVER['REQUEST_URI'] === '/') {
    header("Location: https://blog.swqh.online/articles/search.php", true, 301);
    exit;
}