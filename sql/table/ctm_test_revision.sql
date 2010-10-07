CREATE TABLE `ctm_test_revision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testId` bigint(20) unsigned NOT NULL,
  `modifiedAt` bigint(20) unsigned NOT NULL,
  `modifiedBy` bigint(20) unsigned NOT NULL,
  `revisionId` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testId` (`testId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
