CREATE TABLE `ctm_test_machine_browser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testMachineId` bigint(20) unsigned NOT NULL,
  `testBrowserId` bigint(20) unsigned NOT NULL,
  `isAvailable` int(1) NOT NULL DEFAULT '0',
  `lastSeen` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `test_machine_id` (`testMachineId`),
  KEY `test_browser_id` (`testBrowserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
