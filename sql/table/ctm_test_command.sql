CREATE TABLE `ctm_test_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testId` bigint(20) unsigned NOT NULL,
  `testSeleniumCommandId` bigint(20) unsigned NOT NULL,
  `testParamLibraryId` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `testId` (`testId`),
  KEY `testSeleniumCommandId` (`testSeleniumCommandId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
