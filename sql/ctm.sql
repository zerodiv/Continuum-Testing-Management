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
INSERT INTO `account` VALUES (1,1,'jorcutt@adicio.com','40676ea8edbaf48007422d4eac7608dc','jorcutt@adicio.com',0,1,0,1270710882,''),(2,1,'bseydioglu','622d0ed3f9b06b2e83851f7859c76c6c','bseydioglu@adicio.com',0,1,0,1270710882,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
INSERT INTO `test` VALUES (1,0,'Sample Test',1,1270843947,1,1270844241,1),(2,1,'Burak\'s Sub Test',1,1273079175,2,1273079320,2),(3,3,'Rally Test',1,1273080277,2,1273080277,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_baseurl`
--

LOCK TABLES `test_baseurl` WRITE;
/*!40000 ALTER TABLE `test_baseurl` DISABLE KEYS */;
INSERT INTO `test_baseurl` VALUES (1,1,'http://jorcutt-desktop/'),(2,2,'http://adicio.ning.com'),(3,3,'https://rally1.rallydev.com/');
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
  `is_available` int(1) NOT NULL DEFAULT '0',
  `last_seen` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `major_version` (`major_version`),
  KEY `minor_version` (`minor_version`),
  KEY `patch_version` (`patch_version`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_browser`
--

LOCK TABLES `test_browser` WRITE;
/*!40000 ALTER TABLE `test_browser` DISABLE KEYS */;
INSERT INTO `test_browser` VALUES (7,'firefox',3,0,15,1,1273594028),(8,'iexplore',8,0,6001,1,1274306124),(9,'chrome',4,1,249,1,1274227091),(10,'firefox',3,6,3,1,1274306124),(11,'safari',5,31,22,1,1274306124),(12,'googlechrome',4,1,2,1,1274306124);
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command`
--

LOCK TABLES `test_command` WRITE;
/*!40000 ALTER TABLE `test_command` DISABLE KEYS */;
INSERT INTO `test_command` VALUES (22,1,1,1),(23,1,1,2),(24,1,2,0),(25,1,3,0),(26,1,3,0),(27,1,4,0),(28,1,4,0),(34,2,2,0),(35,2,3,0),(36,2,3,0),(37,2,4,0),(38,3,2,0),(39,3,1,0),(40,3,1,0),(41,3,4,0),(42,3,5,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command_target`
--

LOCK TABLES `test_command_target` WRITE;
/*!40000 ALTER TABLE `test_command_target` DISABLE KEYS */;
INSERT INTO `test_command_target` VALUES (22,22,'jorcutt@adicio.com'),(23,23,'11pass'),(24,24,'/user/login/'),(25,25,'username'),(26,26,'password'),(27,27,'//input[@value=\'Login!\']'),(28,28,'link=Logout : ${ctm_input_username}'),(34,34,'/main/authorization/signIn?target=http%3A%2F%2Fadicio.ning.com%2F'),(35,35,'signin_email'),(36,36,'signin_password'),(37,37,'//input[@value=\'Sign In\']'),(38,38,'/slm/login.op'),(39,39,'j_username'),(40,40,'j_password'),(41,41,'ext-gen18'),(42,42,'link=Logout');
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_command_value`
--

LOCK TABLES `test_command_value` WRITE;
/*!40000 ALTER TABLE `test_command_value` DISABLE KEYS */;
INSERT INTO `test_command_value` VALUES (22,22,'ctm_var_username'),(23,23,'ctm_var_password'),(24,24,''),(25,25,'${ctm_input_username}'),(26,26,'${ctm_input_password}'),(27,27,''),(28,28,''),(34,34,''),(35,35,'bseydioglu@adicio.com'),(36,36,'userbs78'),(37,37,''),(38,38,''),(39,39,'value'),(40,40,'userbs78'),(41,41,''),(42,42,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_description`
--

LOCK TABLES `test_description` WRITE;
/*!40000 ALTER TABLE `test_description` DISABLE KEYS */;
INSERT INTO `test_description` VALUES (1,1,''),(2,2,'Ning test'),(3,3,'Rally login test');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_folder`
--

LOCK TABLES `test_folder` WRITE;
/*!40000 ALTER TABLE `test_folder` DISABLE KEYS */;
INSERT INTO `test_folder` VALUES (1,0,'Burak\'s Ning Test'),(2,0,'Test2'),(3,0,'Burak\'s Rally Tests');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_html_source`
--

LOCK TABLES `test_html_source` WRITE;
/*!40000 ALTER TABLE `test_html_source` DISABLE KEYS */;
INSERT INTO `test_html_source` VALUES (1,1,'<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head profile=\"http://selenium-ide.openqa.org/profiles/test-case\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<link rel=\"selenium.base\" href=\"http://jorcutt-desktop/\" />\n<title>sampleTestLogin</title>\n</head>\n<body>\n<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\">\n<thead>\n<tr><td rowspan=\"1\" colspan=\"3\">sampleTestLogin</td></tr>\n</thead><tbody>\n<tr>\n        <td>store</td>\n        <td>jorcutt@adicio.com</td>\n        <td>ctm_var_username</td>\n</tr>\n<tr>\n        <td>store</td>\n        <td>11pass</td>\n        <td>ctm_var_password</td>\n</tr>\n<tr>\n        <td>open</td>\n        <td>/user/login/</td>\n        <td></td>\n</tr>\n<tr>\n        <td>type</td>\n        <td>username</td>\n        <td>${ctm_input_username}</td>\n</tr>\n<tr>\n        <td>type</td>\n        <td>password</td>\n        <td>${ctm_input_password}</td>\n</tr>\n<tr>\n        <td>clickAndWait</td>\n        <td>//input[@value=\'Login!\']</td>\n        <td></td>\n</tr>\n<tr>\n        <td>clickAndWait</td>\n        <td>link=Logout : ${ctm_input_username}</td>\n        <td></td>\n</tr>\n\n</tbody></table>\n</body>\n</html>\n'),(2,2,'<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head profile=\"http://selenium-ide.openqa.org/profiles/test-case\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<link rel=\"selenium.base\" href=\"\" />\n<title>continuum</title>\n</head>\n<body>\n<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\">\n<thead>\n<tr><td rowspan=\"1\" colspan=\"3\">continuum</td></tr>\n</thead><tbody>\n<tr>\n	<td>open</td>\n	<td>/main/authorization/signIn?target=http%3A%2F%2Fadicio.ning.com%2F</td>\n	<td></td>\n</tr>\n<tr>\n	<td>type</td>\n	<td>signin_email</td>\n	<td>bseydioglu@adicio.com</td>\n</tr>\n<tr>\n	<td>type</td>\n	<td>signin_password</td>\n	<td>userbs78</td>\n</tr>\n<tr>\n	<td>clickAndWait</td>\n	<td>//input[@value=\'Sign In\']</td>\n	<td></td>\n</tr>\n\n</tbody></table>\n</body>\n</html>\n'),(3,3,'<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head profile=\"http://selenium-ide.openqa.org/profiles/test-case\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<link rel=\"selenium.base\" href=\"https://rally1.rallydev.com/\" />\n<title>rally</title>\n</head>\n<body>\n<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\">\n<thead>\n<tr><td rowspan=\"1\" colspan=\"3\">rally</td></tr>\n</thead><tbody>\n<tr>\n	<td>open</td>\n	<td>/slm/login.op</td>\n	<td></td>\n</tr>\n<tr>\n	<td>store</td>\n	<td>j_username</td>\n	<td>value</td>\n</tr>\n<tr>\n	<td>store</td>\n	<td>j_password</td>\n	<td>userbs78</td>\n</tr>\n<tr>\n	<td>clickAndWait</td>\n	<td>ext-gen18</td>\n	<td></td>\n</tr>\n<tr>\n	<td>click</td>\n	<td>link=Logout</td>\n	<td></td>\n</tr>\n\n</tbody></table>\n</body>\n</html>\n');
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
  `guid` varchar(255) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `os` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `last_modified` bigint(20) unsigned NOT NULL,
  `is_disabled` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine`
--

LOCK TABLES `test_machine` WRITE;
/*!40000 ALTER TABLE `test_machine` DISABLE KEYS */;
INSERT INTO `test_machine` VALUES (1,'e94b7d45-7e51-4027-bc51-484eb4b4edaa','192.168.100.115','Windows XP - Professional - Service Pack 3',1271723482,1274306124,0),(3,'007f0100','192.168.100.26','Linux x86_64',1273177970,1273594028,0);
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
  `is_available` int(1) NOT NULL DEFAULT '0',
  `last_seen` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `test_machine_id` (`test_machine_id`),
  KEY `test_browser_id` (`test_browser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1268 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_machine_browser`
--

LOCK TABLES `test_machine_browser` WRITE;
/*!40000 ALTER TABLE `test_machine_browser` DISABLE KEYS */;
INSERT INTO `test_machine_browser` VALUES (1111,3,7,0,0),(1264,1,8,1,1274306124),(1265,1,12,1,1274306124),(1266,1,10,1,1274306124),(1267,1,11,1,1274306124);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library`
--

LOCK TABLES `test_param_library` WRITE;
/*!40000 ALTER TABLE `test_param_library` DISABLE KEYS */;
INSERT INTO `test_param_library` VALUES (1,'ctm_var_username',1270844160,1,1270844160,1),(2,'ctm_var_password',1270844160,1,1270844160,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library_default_value`
--

LOCK TABLES `test_param_library_default_value` WRITE;
/*!40000 ALTER TABLE `test_param_library_default_value` DISABLE KEYS */;
INSERT INTO `test_param_library_default_value` VALUES (1,1,'jorcutt@adicio.com'),(2,2,'11pass');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_param_library_description`
--

LOCK TABLES `test_param_library_description` WRITE;
/*!40000 ALTER TABLE `test_param_library_description` DISABLE KEYS */;
INSERT INTO `test_param_library_description` VALUES (1,1,''),(2,2,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run`
--

LOCK TABLES `test_run` WRITE;
/*!40000 ALTER TABLE `test_run` DISABLE KEYS */;
INSERT INTO `test_run` VALUES (49,1,3,1,1273535589,2),(51,4,1,1,1274290274,1),(52,4,1,1,1274306111,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_baseurl`
--

LOCK TABLES `test_run_baseurl` WRITE;
/*!40000 ALTER TABLE `test_run_baseurl` DISABLE KEYS */;
INSERT INTO `test_run_baseurl` VALUES (104,43,1,0,'http://jorcutt-laptop/'),(105,43,0,1,'http://jorcutt-desktop/'),(106,43,2,0,'http://continuum.localhost'),(107,43,0,3,'https://rally1.rallydev.com/'),(108,43,0,2,'http://adicio.ning.com'),(109,43,3,0,'http://continuum.localhost'),(110,44,1,0,'http://jorcutt-laptop/'),(111,44,0,1,'http://jorcutt-desktop/'),(112,44,2,0,'http://continuum.localhost'),(113,44,0,3,'https://rally1.rallydev.com/'),(114,44,0,2,'http://adicio.ning.com'),(115,44,3,0,'http://continuum.localhost'),(140,49,1,0,'http://jorcutt-laptop/'),(141,49,0,1,'http://jorcutt-desktop/'),(142,49,2,0,'http://continuum.localhost'),(143,49,0,3,'https://rally1.rallydev.com/'),(144,49,0,2,'http://adicio.ning.com'),(145,49,3,0,'http://continuum.localhost'),(147,51,4,0,'http://jorcutt-desktop/'),(148,51,0,1,'http://jorcutt-desktop/'),(149,52,4,0,'http://jorcutt-desktop/'),(150,52,0,1,'http://jorcutt-desktop/');
/*!40000 ALTER TABLE `test_run_baseurl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_browser`
--

DROP TABLE IF EXISTS `test_run_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_browser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_id` bigint(20) unsigned NOT NULL,
  `test_browser_id` bigint(20) unsigned NOT NULL,
  `test_machine_id` bigint(20) unsigned NOT NULL,
  `test_run_state_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_browser`
--

LOCK TABLES `test_run_browser` WRITE;
/*!40000 ALTER TABLE `test_run_browser` DISABLE KEYS */;
INSERT INTO `test_run_browser` VALUES (19,49,7,3,3),(20,52,8,1,1),(21,52,8,1,1);
/*!40000 ALTER TABLE `test_run_browser` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command`
--

LOCK TABLES `test_run_command` WRITE;
/*!40000 ALTER TABLE `test_run_command` DISABLE KEYS */;
INSERT INTO `test_run_command` VALUES (157,43,1,1,1,1),(158,43,1,1,1,2),(159,44,1,1,1,1),(160,44,1,1,1,2),(169,49,1,1,1,1),(170,49,1,1,1,2),(171,51,4,1,1,1),(172,51,4,1,1,2),(173,52,4,1,1,1),(174,52,4,1,1,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command_target`
--

LOCK TABLES `test_run_command_target` WRITE;
/*!40000 ALTER TABLE `test_run_command_target` DISABLE KEYS */;
INSERT INTO `test_run_command_target` VALUES (152,157,'jorcutt@adicio.com'),(153,158,'11pass'),(154,159,'jorcutt@adicio.com'),(155,160,'11pass'),(164,169,'jorcutt@adicio.com'),(165,170,'11pass'),(166,171,'jorcutt@adicio.com'),(167,172,'11pass'),(168,173,'jorcutt@adicio.com'),(169,174,'11pass');
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
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_command_value`
--

LOCK TABLES `test_run_command_value` WRITE;
/*!40000 ALTER TABLE `test_run_command_value` DISABLE KEYS */;
INSERT INTO `test_run_command_value` VALUES (152,157,'ctm_var_username'),(153,158,'ctm_var_password'),(154,159,'ctm_var_username'),(155,160,'ctm_var_password'),(164,169,'ctm_var_username'),(165,170,'ctm_var_password'),(166,171,'ctm_var_username'),(167,172,'ctm_var_password'),(168,173,'ctm_var_username'),(169,174,'ctm_var_password');
/*!40000 ALTER TABLE `test_run_command_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_run_log`
--

DROP TABLE IF EXISTS `test_run_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_run_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_browser_id` bigint(20) unsigned NOT NULL,
  `data` text,
  `duration` int(10) unsigned DEFAULT NULL,
  `created_at` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_log`
--

LOCK TABLES `test_run_log` WRITE;
/*!40000 ALTER TABLE `test_run_log` DISABLE KEYS */;
INSERT INTO `test_run_log` VALUES (1,49,'text',10,1273599477);
/*!40000 ALTER TABLE `test_run_log` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_run_state`
--

LOCK TABLES `test_run_state` WRITE;
/*!40000 ALTER TABLE `test_run_state` DISABLE KEYS */;
INSERT INTO `test_run_state` VALUES (1,'queued'),(2,'executing'),(3,'completed'),(4,'archived'),(5,'failed');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_selenium_command`
--

LOCK TABLES `test_selenium_command` WRITE;
/*!40000 ALTER TABLE `test_selenium_command` DISABLE KEYS */;
INSERT INTO `test_selenium_command` VALUES (1,'store'),(2,'open'),(3,'type'),(4,'clickAndWait'),(5,'click');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite`
--

LOCK TABLES `test_suite` WRITE;
/*!40000 ALTER TABLE `test_suite` DISABLE KEYS */;
INSERT INTO `test_suite` VALUES (1,0,'Sample Test Suite',1270843966,1,1270843992,1,1),(2,0,'Burak\'s Test Suite',1273078667,2,1273176063,2,1),(3,1,'Burak\'s Ning suite',1273078682,2,1273079384,2,1),(4,0,'jorcutt\\\'s test for the holy grail',1274290251,1,1274290251,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_baseurl`
--

LOCK TABLES `test_suite_baseurl` WRITE;
/*!40000 ALTER TABLE `test_suite_baseurl` DISABLE KEYS */;
INSERT INTO `test_suite_baseurl` VALUES (1,1,'http://jorcutt-laptop/'),(2,2,'http://continuum.localhost'),(3,3,'http://continuum.localhost'),(4,4,'http://jorcutt-desktop/');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_description`
--

LOCK TABLES `test_suite_description` WRITE;
/*!40000 ALTER TABLE `test_suite_description` DISABLE KEYS */;
INSERT INTO `test_suite_description` VALUES (1,1,''),(2,2,'My sh*t'),(3,3,''),(4,4,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_suite_plan`
--

LOCK TABLES `test_suite_plan` WRITE;
/*!40000 ALTER TABLE `test_suite_plan` DISABLE KEYS */;
INSERT INTO `test_suite_plan` VALUES (4,1,1,1,2),(13,2,3,1,2),(14,2,2,2,2),(15,1,2,2,1),(16,1,3,3,1),(17,1,2,4,2),(18,1,3,5,2),(19,1,3,6,1),(20,1,3,7,1),(21,4,1,1,2);
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

-- Dump completed on 2010-05-20  9:14:54
