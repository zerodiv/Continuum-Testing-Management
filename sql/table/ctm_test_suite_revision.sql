CREATE TABLE `ctm_test_suite_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testSuiteId` bigint(20) unsigned NOT NULL,
  `modifiedAt` bigint(20) unsigned NOT NULL,
  `modifiedBy` bigint(20) unsigned NOT NULL,
  `revision_id` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
