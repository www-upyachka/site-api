-- MySQL dump 10.13  Distrib 5.6.38, for Win32 (AMD64)
--
-- Host: localhost    Database: tmp_dump
-- ------------------------------------------------------
-- Server version	5.6.38-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `otake_access_tokens`
--

DROP TABLE IF EXISTS `otake_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_access_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `for_user` varchar(250) NOT NULL,
  `expires_in` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `aborted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`(191)),
  KEY `for_user` (`for_user`(191))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_access_tokens`
--

LOCK TABLES `otake_access_tokens` WRITE;
/*!40000 ALTER TABLE `otake_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_bans`
--

DROP TABLE IF EXISTS `otake_bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moderator` varchar(250) NOT NULL,
  `banned_user` varchar(250) NOT NULL,
  `time` varchar(50) NOT NULL,
  `reason` mediumtext NOT NULL,
  `sub` varchar(250) NOT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `discontinued_in_date` int(11) NOT NULL,
  `discontinued_by` varchar(250) DEFAULT NULL,
  `ban_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `moderator` (`moderator`),
  KEY `sub` (`sub`),
  KEY `discontinued_by` (`discontinued_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_bans`
--

LOCK TABLES `otake_bans` WRITE;
/*!40000 ALTER TABLE `otake_bans` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_bans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_comments`
--

DROP TABLE IF EXISTS `otake_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `comment_text` mediumtext NOT NULL,
  `sub` varchar(250) NOT NULL,
  `parent_post` int(11) NOT NULL,
  `parent_comment` varchar(250) DEFAULT '0',
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `sub` (`sub`),
  KEY `parent_comment` (`parent_comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_comments`
--

LOCK TABLES `otake_comments` WRITE;
/*!40000 ALTER TABLE `otake_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_comments_versions`
--

DROP TABLE IF EXISTS `otake_comments_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_comments_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(250) NOT NULL,
  `editor` varchar(250) NOT NULL,
  `ver_time` int(11) NOT NULL,
  `text` mediumtext NOT NULL,
  `post_time` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `editor` (`editor`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_comments_versions`
--

LOCK TABLES `otake_comments_versions` WRITE;
/*!40000 ALTER TABLE `otake_comments_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_comments_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_invites`
--

DROP TABLE IF EXISTS `otake_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `parent_user` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_used` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_user` (`parent_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_invites`
--

LOCK TABLES `otake_invites` WRITE;
/*!40000 ALTER TABLE `otake_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_invites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_karma`
--

DROP TABLE IF EXISTS `otake_karma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_karma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_type` enum('psto','comment') NOT NULL,
  `type` enum('plus','minus') NOT NULL,
  `mass` int(11) NOT NULL,
  `voting_user` varchar(250) NOT NULL,
  `date` int(11) NOT NULL,
  `to_user` varchar(250) NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `voting_user` (`voting_user`(191)),
  KEY `to_user` (`to_user`(191))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_karma`
--

LOCK TABLES `otake_karma` WRITE;
/*!40000 ALTER TABLE `otake_karma` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_karma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_moderators`
--

DROP TABLE IF EXISTS `otake_moderators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_moderators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `date` int(11) NOT NULL,
  `king` varchar(250) NOT NULL,
  `sub` varchar(250) NOT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `discontinued_in_date` int(11) NOT NULL DEFAULT '0',
  `discontinued_by` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `king` (`king`),
  KEY `sub` (`sub`),
  KEY `discontinued_by` (`discontinued_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_moderators`
--

LOCK TABLES `otake_moderators` WRITE;
/*!40000 ALTER TABLE `otake_moderators` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_moderators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_modlog`
--

DROP TABLE IF EXISTS `otake_modlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_modlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moderator` varchar(250) NOT NULL,
  `type` enum('delete_psto','edit_psto','delete_comment','edit_comment','recovery_psto','recovery_comment','edit_sub','add_mod','remove_mod','pin','unpin') NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `sub` varchar(250) NOT NULL,
  `user_moderated` varchar(250) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `moderator` (`moderator`),
  KEY `sub` (`sub`),
  KEY `comment_id` (`comment_id`),
  KEY `post_id` (`post_id`),
  KEY `user_moderated` (`user_moderated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_modlog`
--

LOCK TABLES `otake_modlog` WRITE;
/*!40000 ALTER TABLE `otake_modlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_modlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_posts`
--

DROP TABLE IF EXISTS `otake_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `post_text` mediumtext NOT NULL,
  `sub` varchar(250) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `bumped` int(11) NOT NULL,
  `is_invited` tinyint(1) NOT NULL DEFAULT '0',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `sub` (`sub`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_posts`
--

LOCK TABLES `otake_posts` WRITE;
/*!40000 ALTER TABLE `otake_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_posts_versions`
--

DROP TABLE IF EXISTS `otake_posts_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_posts_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(250) NOT NULL,
  `editor` varchar(250) NOT NULL,
  `ver_time` int(11) NOT NULL,
  `text` mediumtext NOT NULL,
  `post_time` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `editor` (`editor`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_posts_versions`
--

LOCK TABLES `otake_posts_versions` WRITE;
/*!40000 ALTER TABLE `otake_posts_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_posts_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_reports`
--

DROP TABLE IF EXISTS `otake_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` enum('post','comment') NOT NULL,
  `content_id` int(11) NOT NULL,
  `user` varchar(250) NOT NULL,
  `reason` varchar(250) NOT NULL,
  `sub` varchar(250) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `sub` (`sub`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_reports`
--

LOCK TABLES `otake_reports` WRITE;
/*!40000 ALTER TABLE `otake_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_subpages`
--

DROP TABLE IF EXISTS `otake_subpages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_subpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` mediumtext NOT NULL,
  `admin` varchar(250) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address_2` (`address`),
  KEY `address` (`address`),
  KEY `admin` (`admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_subpages`
--

LOCK TABLES `otake_subpages` WRITE;
/*!40000 ALTER TABLE `otake_subpages` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_subpages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otake_users`
--

DROP TABLE IF EXISTS `otake_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otake_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(250) NOT NULL,
  `passwd` varchar(250) NOT NULL,
  `joindate` int(11) NOT NULL,
  `join_ip` varchar(250) NOT NULL,
  `ugroup` enum('user','admin') NOT NULL,
  `email` varchar(250) NOT NULL,
  `parent_user` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `parent_user` (`parent_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otake_users`
--

LOCK TABLES `otake_users` WRITE;
/*!40000 ALTER TABLE `otake_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `otake_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-02 18:39:12
