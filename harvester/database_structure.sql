-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Oct 22, 2014 at 06:57 PM
-- Server version: 5.5.38
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hack4dk2014`
--

-- --------------------------------------------------------

--
-- Table structure for table `ce_comments`
--

CREATE TABLE `ce_comments` (
`id` int(11) NOT NULL,
  `post_id` varchar(45) DEFAULT NULL,
  `fb_comment_id` varchar(45) DEFAULT NULL,
  `message` char(250) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `like_count` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds comments to posts' AUTO_INCREMENT=6025 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords`
--

CREATE TABLE `ce_keywords` (
`id` int(11) NOT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds keywords for comments.' AUTO_INCREMENT=2401 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_keywords_comments`
--

CREATE TABLE `ce_keywords_comments` (
`id` int(11) NOT NULL,
  `post_id` varchar(100) NOT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  `comment_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about keywords in comments.' AUTO_INCREMENT=5904 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_likes`
--

CREATE TABLE `ce_likes` (
`id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds data about likes.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_posts`
--

CREATE TABLE `ce_posts` (
`id` int(11) NOT NULL,
  `post_id` varchar(50) NOT NULL COMMENT 'facebook internal post id',
  `picture` text DEFAULT NULL COMMENT 'Link til billede (Hvis de er noget på posten)',
  `picture_large` text DEFAULT NULL COMMENT 'Linkt til stort billede (med laaaange URLer)',
  `link` char(250) DEFAULT NULL COMMENT 'Link til posten på facebook',
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `message` text
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds posts from Facebook' AUTO_INCREMENT=318 ;

-- --------------------------------------------------------

--
-- Table structure for table `ce_users`
--

CREATE TABLE `ce_users` (
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
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `fb_comment_id` (`fb_comment_id`);

--
-- Indexes for table `ce_keywords`
--
ALTER TABLE `ce_keywords`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `keyword` (`keyword`);

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
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `post_id` (`post_id`);

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6025;
--
-- AUTO_INCREMENT for table `ce_keywords`
--
ALTER TABLE `ce_keywords`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2401;
--
-- AUTO_INCREMENT for table `ce_keywords_comments`
--
ALTER TABLE `ce_keywords_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5904;
--
-- AUTO_INCREMENT for table `ce_likes`
--
ALTER TABLE `ce_likes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ce_posts`
--
ALTER TABLE `ce_posts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=318;
--
-- AUTO_INCREMENT for table `ce_users`
--
ALTER TABLE `ce_users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- DELETE FROM ce_keywords;
-- DELETE FROM ce_keywords_comments;
-- DELETE FROM ce_comments;
-- DELETE FROM ce_posts;

