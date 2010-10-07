CREATE TABLE `ctm_test_run_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testRunId` bigint(20) unsigned NOT NULL,
  `testSuiteId` bigint(20) unsigned NOT NULL,
  `testId` bigint(20) unsigned NOT NULL,
  `testSeleniumCommandId` bigint(20) unsigned NOT NULL,
  `testParamLibraryId` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `testSeleniumCommandId` (`testSeleniumCommandId`),
  KEY `testRunId` (`testRunId`),
  KEY `testParamLibraryId` (`testParamLibraryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
