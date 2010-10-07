CREATE TABLE `ctm_test_run_baseurl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testRunId` bigint(20) unsigned NOT NULL,
  `testSuiteId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `testId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `baseurl` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testRunId` (`testRunId`),
  KEY `testSuiteId` (`testSuiteId`),
  KEY `testId` (`testId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
