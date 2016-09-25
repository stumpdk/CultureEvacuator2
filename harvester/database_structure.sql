-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2016 at 10:53 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `c9`
--

-- --------------------------------------------------------

--
-- Table structure for table `ce_comments`
--

CREATE TABLE IF NOT EXISTS `ce_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` varchar(45) DEFAULT NULL,
  `fb_comment_id` varchar(45) DEFAULT NULL,
  `message` char(250) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `like_count` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fb_comment_id` (`fb_comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds comments to posts' AUTO_INCREMENT=275 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords`
--

CREATE TABLE IF NOT EXISTS `ce_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`),
  KEY `keyword_2` (`keyword`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds keywords for comments.' AUTO_INCREMENT=112 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords_comments`
--

CREATE TABLE IF NOT EXISTS `ce_keywords_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` varchar(100) NOT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  `comment_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `post_id_2` (`post_id`),
  KEY `keyword_id` (`keyword_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about keywords in comments.' AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_likes`
--

CREATE TABLE IF NOT EXISTS `ce_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds data about likes.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_posts`
--

CREATE TABLE IF NOT EXISTS `ce_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` varchar(50) NOT NULL COMMENT 'facebook internal post id',
  `picture` text COMMENT 'Link til billede (Hvis de er noget på posten)',
  `picture_large` text COMMENT 'Linkt til stort billede (med laaaange URLer)',
  `picture_large_height` INT(11) DEFAULT NULL,
  `picture_large_width` INT(11) DEFAULT NULL,
  `link` char(250) DEFAULT NULL COMMENT 'Link til posten på facebook',
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id` (`post_id`),
  KEY `post_id_2` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds posts from Facebook' AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_users`
--

CREATE TABLE IF NOT EXISTS `ce_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_id` int(11) NOT NULL,
  `level` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about users' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
