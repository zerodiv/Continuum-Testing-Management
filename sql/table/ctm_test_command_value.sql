CREATE TABLE `ctm_test_command_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testCommandId` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_command_id` (`testCommandId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
