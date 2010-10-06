CREATE TABLE `ctm_test_machine` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `machineName` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `createdAt` bigint(20) unsigned NOT NULL,
  `lastModified` bigint(20) unsigned NOT NULL,
  `isDisabled` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
