-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-01-22 16:18:02
-- 服务器版本： 5.6.49-log
-- PHP 版本： 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `web2021`
--

-- --------------------------------------------------------

--
-- 表的结构 `actroom`
--

CREATE TABLE `actroom` (
  `roomid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `username1` varchar(50) NOT NULL,
  `username2` varchar(50) NOT NULL,
  `occupied` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `actroom`
--

INSERT INTO `actroom` (`roomid`, `time`, `username1`, `username2`, `occupied`) VALUES
(1, 1, 'zb', 'Ally', 1),
(1, 2, '', '', 0),
(1, 3, '', '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `building`
--

CREATE TABLE `building` (
  `building` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `occupied` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `building`
--

INSERT INTO `building` (`building`, `floor`, `room`, `occupied`) VALUES
(1, 1, 1101, 0),
(13, 1, 3101, 2),
(5, 2, 5217, 4),
(5, 2, 5218, 0),
(5, 2, 5219, 4),
(8, 1, 8101, 2);

-- --------------------------------------------------------

--
-- 表的结构 `error`
--

CREATE TABLE `error` (
  `room` int(11) NOT NULL,
  `error` text NOT NULL,
  `people` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `error`
--

INSERT INTO `error` (`room`, `error`, `people`) VALUES
(5219, 'm', 'chen');

-- --------------------------------------------------------

--
-- 表的结构 `roleaccess`
--

CREATE TABLE `roleaccess` (
  `access` varchar(40) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `superadmin` int(11) NOT NULL DEFAULT '0',
  `student` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `roleaccess`
--

INSERT INTO `roleaccess` (`access`, `admin`, `superadmin`, `student`) VALUES
('baoxiuliebiao', 1, 1, 0),
('diaohuansushe', 1, 1, 0),
('fenpeisushe', 1, 1, 0),
('gerenxinxi', 1, 1, 1),
('guzhangbaoxiu', 1, 1, 1),
('shanchufangjian', 0, 1, 0),
('sushetongjichaxun', 1, 1, 0),
('sushexinxi', 0, 1, 0),
('tianjiafangjian', 0, 1, 0),
('tuisu', 1, 1, 0),
('xiugaimima', 1, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `userinfo`
--

CREATE TABLE `userinfo` (
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `userrole` varchar(20) NOT NULL DEFAULT 'student',
  `room` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `userinfo`
--

INSERT INTO `userinfo` (`username`, `password`, `userrole`, `room`) VALUES
('AA', '81dc9bdb52d04dc20036dbd8313ed055', 'superadmin', 0),
('abc', '900150983cd24fb0d6963f7d28e17f72', 'student', 0),
('Ally', '202cb962ac59075b964b07152d234b70', 'student', 5219),
('chen', '202cb962ac59075b964b07152d234b70', 'admin', 0),
('eqw', '202cb962ac59075b964b07152d234b70', 'student', 0),
('fan', '202cb962ac59075b964b07152d234b70', 'student', 5219),
('gong', '202cb962ac59075b964b07152d234b70', 'student', 5219),
('ll', '202cb962ac59075b964b07152d234b70', 'student', 8101),
('Sun', '202cb962ac59075b964b07152d234b70', 'admin', 8101),
('zb', '202cb962ac59075b964b07152d234b70', 'student', 0),
('zsy', '202cb962ac59075b964b07152d234b70', 'student', 5219);

--
-- 转储表的索引
--

--
-- 表的索引 `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`room`);

--
-- 表的索引 `roleaccess`
--
ALTER TABLE `roleaccess`
  ADD PRIMARY KEY (`access`);

--
-- 表的索引 `userinfo`
--
ALTER TABLE `userinfo`
  ADD PRIMARY KEY (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
