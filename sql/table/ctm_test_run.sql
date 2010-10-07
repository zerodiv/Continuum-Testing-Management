CREATE TABLE `ctm_test_run` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testSuiteId` bigint(20) unsigned NOT NULL,
  `testRunStateId` bigint(20) unsigned NOT NULL,
  `iterations` bigint(20) unsigned NOT NULL DEFAULT '1',
  `createdAt` bigint(20) unsigned NOT NULL,
  `createdBy` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testSuiteId` (`testSuiteId`),
  KEY `testRunStateId` (`testRunStateId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
