CREATE TABLE `ctm_test_command_target` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testCommandId` bigint(20) unsigned NOT NULL,
  `target` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_command_id` (`testCommandId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
