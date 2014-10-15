-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Oct 12, 2014 at 11:53 PM
-- Server version: 5.5.38
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hack4dk2014`
--

-- --------------------------------------------------------

--
-- Table structure for table `ce_posts`
--

CREATE TABLE `ce_posts` (
`id` int(11) NOT NULL,
  `post_id` varchar(50) NOT NULL COMMENT 'facebook internal post id',
  `picture` char(250) DEFAULT NULL COMMENT 'Link til billede (Hvis de er noget på posten)',
  `link` char(250) DEFAULT NULL COMMENT 'Link til posten på facebook',
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `message` text
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds posts from Facebook' AUTO_INCREMENT=165 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ce_posts`
--
ALTER TABLE `ce_posts`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `post_id` (`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ce_posts`
--
ALTER TABLE `ce_posts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=165;

DELETE FROM ce_keywords;
DELETE FROM ce_keywords_comments;
DELETE FROM ce_comments;
DELETE FROM ce_posts

