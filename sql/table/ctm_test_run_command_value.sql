CREATE TABLE `ctm_test_run_command_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testRunCommandId` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testRunCommandId` (`testRunCommandId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
