-- MySQL dump 10.13  Distrib 5.1.37, for apple-darwin8.11.1 (i386)
--
-- Host: localhost    Database: ctm
-- ------------------------------------------------------
-- Server version	5.1.37

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
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_role_id` bigint(20) unsigned NOT NULL DEFAULT '1',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_when` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created_on` bigint(20) unsigned NOT NULL DEFAULT '0',
  `temp_password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `is_disabled` (`is_disabled`),
  KEY `account_role_id` (`account_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (2,1,'jorcutt@adicio.com','40676ea8edbaf48007422d4eac7608dc','jorcutt@adicio.com',0,1,1262065668,1262065668,NULL);
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_role`
--

DROP TABLE IF EXISTS `account_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_role`
--

LOCK TABLES `account_role` WRITE;
/*!40000 ALTER TABLE `account_role` DISABLE KEYS */;
INSERT INTO `account_role` VALUES (4,'admin'),(1,'default'),(3,'qa'),(2,'user');
/*!40000 ALTER TABLE `account_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `selenium_command`
--

DROP TABLE IF EXISTS `selenium_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `selenium_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `selenium_command`
--

LOCK TABLES `selenium_command` WRITE;
/*!40000 ALTER TABLE `selenium_command` DISABLE KEYS */;
/*!40000 ALTER TABLE `selenium_command` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_folder_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `html_source` text NOT NULL,
  `test_status_id` bigint(20) unsigned NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  `modified_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
INSERT INTO `test` VALUES (1,0,'A Sample Test','This is my pretty test entered in because Duane likes testing the test that has testing requirements written by testing the requirements within the test.','<?xml version=\\\"1.0\\\" encoding=\\\"UTF-8\\\"?>\r\n<!DOCTYPE html PUBLIC \\\"-//W3C//DTD XHTML 1.0 Strict//EN\\\" \\\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\\\">\r\n<html xmlns=\\\"http://www.w3.org/1999/xhtml\\\" xml:lang=\\\"en\\\" lang=\\\"en\\\">\r\n<head profile=\\\"http://selenium-ide.openqa.org/profiles/test-case\\\">\r\n<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=UTF-8\\\" />\r\n<link rel=\\\"selenium.base\\\" href=\\\"http://jorcutt-desktop/\\\" />\r\n<title>sampleTestLogin</title>\r\n</head>\r\n<body>\r\n<table cellpadding=\\\"1\\\" cellspacing=\\\"1\\\" border=\\\"1\\\">\r\n<thead>\r\n<tr><td rowspan=\\\"1\\\" colspan=\\\"3\\\">sampleTestLogin</td></tr>\r\n</thead><tbody>\r\n<tr>\r\n	<td>store</td>\r\n	<td>jorcutt@adicio.com</td>\r\n	<td>ctm_input_username</td>\r\n</tr>\r\n<tr>\r\n	<td>open</td>\r\n	<td>/user/login/</td>\r\n	<td></td>\r\n</tr>\r\n<tr>\r\n	<td>type</td>\r\n	<td>username</td>\r\n	<td>${ctm_input_username}</td>\r\n</tr>\r\n<tr>\r\n	<td>type</td>\r\n	<td>password</td>\r\n	<td>11pass</td>\r\n</tr>\r\n<tr>\r\n	<td>clickAndWait</td>\r\n	<td>//input[@value=\\\'Login!\\\']</td>\r\n	<td></td>\r\n</tr>\r\n<tr>\r\n	<td>clickAndWait</td>\r\n	<td>link=Logout : ${ctm_input_username}</td>\r\n	<td></td>\r\n</tr>\r\n\r\n</tbody></table>\r\n</body>\r\n</html>\r\n',1,1268963301,2,1269288133,0);
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_browser`
--

DROP TABLE IF EXISTS `test_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_browser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `major_version` int(11) NOT NULL,
  `minor_version` int(11) NOT NULL,
  `patch_version` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `major_version` (`major_version`),
  KEY `minor_version` (`minor_version`),
  KEY `patch_version` (`patch_version`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_browser`
--

LOCK TABLES `test_browser` WRITE;
/*!40000 ALTER TABLE `test_browser` DISABLE KEYS */;
INSERT INTO `test_browser` VALUES (3,'Safari',0,0,0),(4,'Safari',4,0,5),(5,'Firefox',4,0,5),(6,'Firefox',3,6,2),(7,'Chrome',5,0,307);
/*!40000 ALTER TABLE `test_browser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_command`
--

DROP TABLE IF EXISTS `test_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `selenium_command_id` bigint(20) unsigned NOT NULL,
  `target` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command`
--

LOCK TABLES `test_command` WRITE;
/*!40000 ALTER TABLE `test_command` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_command` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_folder`
--

DROP TABLE IF EXISTS `test_folder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_folder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_folder`
--

LOCK TABLES `test_folder` WRITE;
/*!40000 ALTER TABLE `test_folder` DISABLE KEYS */;
INSERT INTO `test_folder` VALUES (4,0,'PFL'),(7,4,'Sample Child'),(9,7,'Grandchild'),(10,0,'Another sample tree'),(11,0,'Careers'),(12,11,'Login'),(13,0,'Real Estate'),(14,13,'Broker');
/*!40000 ALTER TABLE `test_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_machine`
--

DROP TABLE IF EXISTS `test_machine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_machine` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(2048) NOT NULL,
  `os` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `last_modified` bigint(20) unsigned NOT NULL,
  `is_disabled` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine`
--

LOCK TABLES `test_machine` WRITE;
/*!40000 ALTER TABLE `test_machine` DISABLE KEYS */;
INSERT INTO `test_machine` VALUES (4,'jorcutt-laptop','Darwin',1269411891,1269413024,0);
/*!40000 ALTER TABLE `test_machine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_machine_browser`
--

DROP TABLE IF EXISTS `test_machine_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_machine_browser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_machine_id` bigint(20) unsigned NOT NULL,
  `test_browser_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_machine_id` (`test_machine_id`),
  KEY `test_browser_id` (`test_browser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine_browser`
--

LOCK TABLES `test_machine_browser` WRITE;
/*!40000 ALTER TABLE `test_machine_browser` DISABLE KEYS */;
INSERT INTO `test_machine_browser` VALUES (1,4,7),(2,4,6),(3,4,4);
/*!40000 ALTER TABLE `test_machine_browser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_status`
--

DROP TABLE IF EXISTS `test_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_status`
--

LOCK TABLES `test_status` WRITE;
/*!40000 ALTER TABLE `test_status` DISABLE KEYS */;
INSERT INTO `test_status` VALUES (1,'pending'),(2,'live'),(3,'disabled');
/*!40000 ALTER TABLE `test_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_suite`
--

DROP TABLE IF EXISTS `test_suite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_suite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_folder_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  `modified_by` bigint(20) unsigned NOT NULL,
  `test_status_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_folder_id` (`test_folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite`
--

LOCK TABLES `test_suite` WRITE;
/*!40000 ALTER TABLE `test_suite` DISABLE KEYS */;
INSERT INTO `test_suite` VALUES (1,0,'testing create','fasdfasdf\r\n\r\nasdfasdf\r\n\r\nasfdfasd\r\n\r\nasdfasdf',1268779744,0,1268779744,2,1),(2,0,'jorcutt\\\'s test for the holy grail','The GRAIL! but beware of the jaberwocky!',1268933499,2,1268944976,2,1),(3,10,'This is a sample Test for Association','This test shows off the association capability of CTM',1268965552,2,1268965552,2,1);
/*!40000 ALTER TABLE `test_suite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_suite_plan`
--

DROP TABLE IF EXISTS `test_suite_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_suite_plan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_suite_id` bigint(20) unsigned NOT NULL,
  `linked_id` bigint(20) unsigned NOT NULL,
  `test_order` bigint(20) unsigned NOT NULL,
  `test_suite_plan_type_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_suite_id` (`test_suite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_plan`
--

LOCK TABLES `test_suite_plan` WRITE;
/*!40000 ALTER TABLE `test_suite_plan` DISABLE KEYS */;
INSERT INTO `test_suite_plan` VALUES (1,3,1,1,1),(2,3,2,2,1),(3,3,1,4,2),(4,3,2,5,1),(5,3,1,3,1),(6,3,1,6,2);
/*!40000 ALTER TABLE `test_suite_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_suite_plan_type`
--

DROP TABLE IF EXISTS `test_suite_plan_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_suite_plan_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_plan_type`
--

LOCK TABLES `test_suite_plan_type` WRITE;
/*!40000 ALTER TABLE `test_suite_plan_type` DISABLE KEYS */;
INSERT INTO `test_suite_plan_type` VALUES (1,'suite'),(2,'test');
/*!40000 ALTER TABLE `test_suite_plan_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-03-24 12:47:22
