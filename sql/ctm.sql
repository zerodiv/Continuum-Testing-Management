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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
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
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_folder_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `test_status_id` bigint(20) unsigned NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  `modified_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_baseurl`
--

DROP TABLE IF EXISTS `test_baseurl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_baseurl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) unsigned NOT NULL,
  `baseurl` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_baseurl`
--

LOCK TABLES `test_baseurl` WRITE;
/*!40000 ALTER TABLE `test_baseurl` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_baseurl` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_browser`
--

LOCK TABLES `test_browser` WRITE;
/*!40000 ALTER TABLE `test_browser` DISABLE KEYS */;
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
  `test_id` bigint(20) unsigned NOT NULL,
  `test_selenium_command_id` bigint(20) unsigned NOT NULL,
  `test_param_library_id` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `test_selenium_command_id` (`test_selenium_command_id`)
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
-- Table structure for table `test_command_target`
--

DROP TABLE IF EXISTS `test_command_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_command_target` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_command_id` bigint(20) unsigned NOT NULL,
  `target` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_command_id` (`test_command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command_target`
--

LOCK TABLES `test_command_target` WRITE;
/*!40000 ALTER TABLE `test_command_target` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_command_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_command_value`
--

DROP TABLE IF EXISTS `test_command_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_command_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_command_id` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_command_id` (`test_command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command_value`
--

LOCK TABLES `test_command_value` WRITE;
/*!40000 ALTER TABLE `test_command_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_command_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_description`
--

DROP TABLE IF EXISTS `test_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_description`
--

LOCK TABLES `test_description` WRITE;
/*!40000 ALTER TABLE `test_description` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_description` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_folder`
--

LOCK TABLES `test_folder` WRITE;
/*!40000 ALTER TABLE `test_folder` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_html_source`
--

DROP TABLE IF EXISTS `test_html_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_html_source` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) unsigned NOT NULL,
  `html_source` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_html_source`
--

LOCK TABLES `test_html_source` WRITE;
/*!40000 ALTER TABLE `test_html_source` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_html_source` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine`
--

LOCK TABLES `test_machine` WRITE;
/*!40000 ALTER TABLE `test_machine` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine_browser`
--

LOCK TABLES `test_machine_browser` WRITE;
/*!40000 ALTER TABLE `test_machine_browser` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_machine_browser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_param_library`
--

DROP TABLE IF EXISTS `test_param_library`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_param_library` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library`
--

LOCK TABLES `test_param_library` WRITE;
/*!40000 ALTER TABLE `test_param_library` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_param_library` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_param_library_default_value`
--

DROP TABLE IF EXISTS `test_param_library_default_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_param_library_default_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_param_library_id` bigint(20) unsigned NOT NULL,
  `default_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_param_library_id` (`test_param_library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library_default_value`
--

LOCK TABLES `test_param_library_default_value` WRITE;
/*!40000 ALTER TABLE `test_param_library_default_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_param_library_default_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_param_library_description`
--

DROP TABLE IF EXISTS `test_param_library_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_param_library_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_param_library_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_param_library_id` (`test_param_library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library_description`
--

LOCK TABLES `test_param_library_description` WRITE;
/*!40000 ALTER TABLE `test_param_library_description` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_param_library_description` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run`
--

DROP TABLE IF EXISTS `test_run`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_suite_id` bigint(20) unsigned NOT NULL,
  `test_run_state_id` bigint(20) unsigned NOT NULL,
  `iterations` bigint(20) unsigned NOT NULL DEFAULT '1',
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_suite_id` (`test_suite_id`),
  KEY `test_run_state_id` (`test_run_state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run`
--

LOCK TABLES `test_run` WRITE;
/*!40000 ALTER TABLE `test_run` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_run` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_baseurl`
--

DROP TABLE IF EXISTS `test_run_baseurl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_baseurl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_id` bigint(20) unsigned NOT NULL,
  `test_suite_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `test_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `baseurl` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_run_id` (`test_run_id`),
  KEY `test_suite_id` (`test_suite_id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_baseurl`
--

LOCK TABLES `test_run_baseurl` WRITE;
/*!40000 ALTER TABLE `test_run_baseurl` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_run_baseurl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_command`
--

DROP TABLE IF EXISTS `test_run_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_id` bigint(20) unsigned NOT NULL,
  `test_suite_id` bigint(20) unsigned NOT NULL,
  `test_id` bigint(20) unsigned NOT NULL,
  `test_selenium_command_id` bigint(20) unsigned NOT NULL,
  `test_param_library_id` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `test_selenium_command_id` (`test_selenium_command_id`),
  KEY `test_run_id` (`test_run_id`),
  KEY `test_param_library_id` (`test_param_library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command`
--

LOCK TABLES `test_run_command` WRITE;
/*!40000 ALTER TABLE `test_run_command` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_run_command` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_command_target`
--

DROP TABLE IF EXISTS `test_run_command_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_command_target` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_command_id` bigint(20) unsigned NOT NULL,
  `target` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_run_command_id` (`test_run_command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command_target`
--

LOCK TABLES `test_run_command_target` WRITE;
/*!40000 ALTER TABLE `test_run_command_target` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_run_command_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_command_value`
--

DROP TABLE IF EXISTS `test_run_command_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_command_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_command_id` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_run_command_id` (`test_run_command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command_value`
--

LOCK TABLES `test_run_command_value` WRITE;
/*!40000 ALTER TABLE `test_run_command_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_run_command_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_state`
--

DROP TABLE IF EXISTS `test_run_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_state` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_state`
--

LOCK TABLES `test_run_state` WRITE;
/*!40000 ALTER TABLE `test_run_state` DISABLE KEYS */;
INSERT INTO `test_run_state` VALUES (1,'queued'),(2,'executing'),(3,'completed'),(4,'archived');
/*!40000 ALTER TABLE `test_run_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_selenium_command`
--

DROP TABLE IF EXISTS `test_selenium_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_selenium_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_selenium_command`
--

LOCK TABLES `test_selenium_command` WRITE;
/*!40000 ALTER TABLE `test_selenium_command` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_selenium_command` ENABLE KEYS */;
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
  `created_at` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  `modified_by` bigint(20) unsigned NOT NULL,
  `test_status_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_folder_id` (`test_folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite`
--

LOCK TABLES `test_suite` WRITE;
/*!40000 ALTER TABLE `test_suite` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_suite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_suite_baseurl`
--

DROP TABLE IF EXISTS `test_suite_baseurl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_suite_baseurl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_suite_id` bigint(20) unsigned NOT NULL,
  `baseurl` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_baseurl`
--

LOCK TABLES `test_suite_baseurl` WRITE;
/*!40000 ALTER TABLE `test_suite_baseurl` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_suite_baseurl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_suite_description`
--

DROP TABLE IF EXISTS `test_suite_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_suite_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_suite_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_suite_id` (`test_suite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_description`
--

LOCK TABLES `test_suite_description` WRITE;
/*!40000 ALTER TABLE `test_suite_description` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_suite_description` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_plan`
--

LOCK TABLES `test_suite_plan` WRITE;
/*!40000 ALTER TABLE `test_suite_plan` DISABLE KEYS */;
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

-- Dump completed on 2010-04-08  0:10:08
