CREATE TABLE `ctm_test_baseurl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testId` bigint(20) unsigned NOT NULL,
  `baseurl` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testId` (`testId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
