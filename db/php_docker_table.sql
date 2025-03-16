-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 主機： db:3306
-- 產生時間： 2025 年 03 月 16 日 06:13
-- 伺服器版本： 9.2.0
-- PHP 版本： 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `php_docker`
--

-- --------------------------------------------------------

--
-- 資料表結構 `php_docker_table`
--

CREATE TABLE `php_docker_table` (
  `ID` int NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Body` text NOT NULL,
  `Date_Created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `php_docker_table`
--

INSERT INTO `php_docker_table` (`ID`, `Title`, `Body`, `Date_Created`) VALUES
(1, 'First post', 'First Message body', '2025-03-16'),
(2, 'Second post', 'Second Message body ', '2025-03-16');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `php_docker_table`
--
ALTER TABLE `php_docker_table`
  ADD PRIMARY KEY (`ID`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `php_docker_table`
--
ALTER TABLE `php_docker_table`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
