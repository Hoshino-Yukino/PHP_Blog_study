-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-01-16 03:01:21
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `test`
--
CREATE DATABASE IF NOT EXISTS test;
GRANT ALL PRIVILEGES ON test.* TO 'test_user'@'%' IDENTIFIED BY 'test_password';
FLUSH PRIVILEGES;

-- --------------------------------------------------------

--
-- 表的结构 `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `subject` varchar(128) NOT NULL,
  `body` text NOT NULL,
  `author` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 转存表中的数据 `articles`
--

INSERT INTO `articles` (`id`, `subject`, `body`, `author`, `modified`) VALUES
(1, '[User] User-Test', 'test', 1, '2025-01-16 01:09:28'),
(2, '[Admin] User-Test', 'test', 1, '2025-01-16 01:10:26'),
(3, '[Admin] Admin-Test', 'test', -1, '2025-01-16 01:10:48'),
(4, '[Admin] User-Test', 'test', 1, '2025-01-16 01:10:26'),
(5, '[Admin] User-Test', 'test', 1, '2025-01-16 01:10:26'),
(6, '[Admin] User-Test', 'test', 1, '2025-01-16 01:10:26');

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `author` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 转存表中的数据 `comment`
--

INSERT INTO `comment` (`id`, `article_id`, `comment`, `author`, `modified`) VALUES
(2, 1, 'test', 1, '2025-01-16 01:11:12');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userID` varchar(30) NOT NULL,
  `email` text DEFAULT NULL,
  `password` varchar(120) NOT NULL,
  `name` varchar(30) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `session_valid` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `userID`, `email`, `password`, `name`, `role`, `session_valid`) VALUES
(-1, 'ADMIN_GROUP', NULL, 'LOGIN_IN_ILLEGAL', '管理員', -1, 1),
(1, 'HoshinoYukino', NULL, '$2y$10$kzHpaMnqEps8ObQqbAkiMey9cztCBZixOIBfBlgZcvUBSXHMtb7Z6', '喬禾', 1, 1);

--
-- 转储表的索引
--

--
-- 表的索引 `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
