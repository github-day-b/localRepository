-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成日時: 2015 年 1 月 05 日 09:41
-- サーバのバージョン: 5.5.33
-- PHP のバージョン: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `sample_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `emp_info`
--

CREATE TABLE IF NOT EXISTS `emp_info` (
`emp_id` int(11) NOT NULL,
  `emp_name` varchar(30) NOT NULL,
  `group_id` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `std_start_time` time DEFAULT NULL,
  `std_end_time` time DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

ALTER TABLE `emp_info`
 ADD PRIMARY KEY (`emp_id`), ADD UNIQUE KEY `group_id` (`group_id`,`email`);

ALTER TABLE `emp_info`
MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;

--
-- テーブルのデータのダンプ `emp_info`
--

INSERT INTO `emp_info` (`emp_id`, `emp_name`, `group_id`, `email`, `password`, `std_start_time`, `std_end_time`, `created`, `modified`, `delete_flg`, `admin`) VALUES
(1, 'admin', 'test', 'admin@admin.jp', 'admin', NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `group_info`
--

CREATE TABLE IF NOT EXISTS `group_info` (
  `group_id` varchar(30) CHARACTER SET utf8 NOT NULL,
  `group_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `location_flg` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `group_info`
 ADD PRIMARY KEY (`group_id`);

--
-- テーブルのデータのダンプ `group_info`
--

INSERT INTO `group_info` (`group_id`, `group_name`, `created`, `modified`) VALUES
('admin', 'test', '2015-01-05 16:05:28', '2015-01-05 16:05:28');

-- --------------------------------------------------------

--
-- テーブルの構造 `time_sheet`
--

CREATE TABLE `time_sheet` (
  `emp_id` int(30) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `start_stamp` time DEFAULT NULL,
  `start_location` varchar(255) DEFAULT NULL,
  `interval_time` time DEFAULT NULL,
  `interval_stamp` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `end_stamp` time DEFAULT NULL,
  `end_location` varchar(255) DEFAULT NULL,
  `remark` text,
  `record_seq` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`emp_id`,`work_date`),
  UNIQUE KEY `record_seq` (`record_seq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;
