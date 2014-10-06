-- phpMyAdmin SQL Dump
-- version 4.2.2
-- http://www.phpmyadmin.net
--
-- Vært: localhost
-- Genereringstid: 06. 10 2014 kl. 19:37:50
-- Serverversion: 5.6.17-65.0-587.wheezy
-- PHP-version: 5.4.4-14+deb7u10

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
-- Struktur-dump for tabellen `ce_comments`
--

CREATE TABLE IF NOT EXISTS `ce_comments` (
`id` int(11) NOT NULL,
  `post_id` varchar(45) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `message` char(250) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `like_count` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds comments to posts' AUTO_INCREMENT=1369 ;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ce_keywords`
--

CREATE TABLE IF NOT EXISTS `ce_keywords` (
`id` int(11) NOT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds keywords for comments.' AUTO_INCREMENT=2195 ;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ce_keywords_comments`
--

CREATE TABLE IF NOT EXISTS `ce_keywords_comments` (
`id` int(11) NOT NULL,
  `post_id` char(100) NOT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  `comment_id` char(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about keywords in comments.' AUTO_INCREMENT=2195 ;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ce_likes`
--

CREATE TABLE IF NOT EXISTS `ce_likes` (
`id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds data about likes.' AUTO_INCREMENT=2505 ;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ce_posts`
--

CREATE TABLE IF NOT EXISTS `ce_posts` (
`id` int(11) NOT NULL,
  `picture` char(250) DEFAULT NULL,
  `link` char(250) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds posts from Facebook' AUTO_INCREMENT=154 ;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ce_users`
--

CREATE TABLE IF NOT EXISTS `ce_users` (
`id` int(11) NOT NULL,
  `fb_id` int(11) NOT NULL,
  `level` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='#Hack4DK. Holds informations about users' AUTO_INCREMENT=1 ;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `ce_comments`
--
ALTER TABLE `ce_comments`
 ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ce_keywords`
--
ALTER TABLE `ce_keywords`
 ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ce_keywords_comments`
--
ALTER TABLE `ce_keywords_comments`
 ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ce_likes`
--
ALTER TABLE `ce_likes`
 ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ce_posts`
--
ALTER TABLE `ce_posts`
 ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ce_users`
--
ALTER TABLE `ce_users`
 ADD PRIMARY KEY (`id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `ce_comments`
--
ALTER TABLE `ce_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1369;
--
-- Tilføj AUTO_INCREMENT i tabel `ce_keywords`
--
ALTER TABLE `ce_keywords`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2195;
--
-- Tilføj AUTO_INCREMENT i tabel `ce_keywords_comments`
--
ALTER TABLE `ce_keywords_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2195;
--
-- Tilføj AUTO_INCREMENT i tabel `ce_likes`
--
ALTER TABLE `ce_likes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2505;
--
-- Tilføj AUTO_INCREMENT i tabel `ce_posts`
--
ALTER TABLE `ce_posts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=154;
--
-- Tilføj AUTO_INCREMENT i tabel `ce_users`
--
ALTER TABLE `ce_users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
