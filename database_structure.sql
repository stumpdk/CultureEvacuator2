-- phpMyAdmin SQL Dump
-- version 4.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2014 at 12:55 PM
-- Server version: 5.6.17-65.0-587.wheezy
-- PHP Version: 5.4.4-14+deb7u10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kbharkiv`
--

-- --------------------------------------------------------

--
-- Table structure for table `ce_comments`
--

CREATE TABLE IF NOT EXISTS `ce_comments` (
`id` int(11) NOT NULL,
  `post_id` varchar(45) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `message` varchar(45) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `like_count` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds comments to posts' AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords`
--

CREATE TABLE IF NOT EXISTS `ce_keywords` (
`id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds keywords for comments.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords_comments`
--

CREATE TABLE IF NOT EXISTS `ce_keywords_comments` (
`id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about keywords in comments.' AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_likes`
--

CREATE TABLE IF NOT EXISTS `ce_likes` (
`id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds data about likes.' AUTO_INCREMENT=69 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_posts`
--

CREATE TABLE IF NOT EXISTS `ce_posts` (
`id` int(11) NOT NULL,
  `picture` char(250) DEFAULT NULL,
  `link` char(250) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds posts from Facebook' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_users`
--

CREATE TABLE IF NOT EXISTS `ce_users` (
`id` int(11) NOT NULL,
  `fb_id` int(11) NOT NULL,
  `level` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about users' AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ce_comments`
--
ALTER TABLE `ce_comments`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ce_keywords`
--
ALTER TABLE `ce_keywords`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ce_keywords_comments`
--
ALTER TABLE `ce_keywords_comments`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ce_likes`
--
ALTER TABLE `ce_likes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ce_posts`
--
ALTER TABLE `ce_posts`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ce_users`
--
ALTER TABLE `ce_users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ce_comments`
--
ALTER TABLE `ce_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `ce_keywords`
--
ALTER TABLE `ce_keywords`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ce_keywords_comments`
--
ALTER TABLE `ce_keywords_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=77;
--
-- AUTO_INCREMENT for table `ce_likes`
--
ALTER TABLE `ce_likes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `ce_posts`
--
ALTER TABLE `ce_posts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ce_users`
--
ALTER TABLE `ce_users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
