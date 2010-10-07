CREATE TABLE `ctm_test_suite_plan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testSuiteId` bigint(20) unsigned NOT NULL,
  `linkedId` bigint(20) unsigned NOT NULL,
  `testOrder` bigint(20) unsigned NOT NULL,
  `testSuitePlanTypeId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testSuiteId` (`testSuiteId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
