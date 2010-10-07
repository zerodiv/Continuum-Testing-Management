CREATE TABLE `ctm_test_suite_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testSuiteId` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testSuiteId` (`testSuiteId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
