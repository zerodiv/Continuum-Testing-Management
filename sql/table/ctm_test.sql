CREATE TABLE `ctm_test` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testFolderId` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `testStatusId` bigint(20) unsigned NOT NULL,
  `createdAt` bigint(20) unsigned NOT NULL,
  `createdBy` bigint(20) unsigned NOT NULL,
  `modifiedAt` bigint(20) unsigned NOT NULL,
  `modifiedBy` bigint(20) unsigned NOT NULL,
  `revisionCount` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`createdBy`),
  KEY `testFolderId` (`testFolderId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
