CREATE TABLE `ctm_test_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testId` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`testId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
