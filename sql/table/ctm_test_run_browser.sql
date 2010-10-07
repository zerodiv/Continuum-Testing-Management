CREATE TABLE `ctm_test_run_browser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testRunId` bigint(20) unsigned NOT NULL,
  `testBrowserId` bigint(20) unsigned NOT NULL,
  `testMachineId` bigint(20) unsigned NOT NULL,
  `testRunStateId` bigint(20) unsigned NOT NULL,
  `hasLog` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
