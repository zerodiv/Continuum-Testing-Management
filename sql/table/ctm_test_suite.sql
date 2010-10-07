CREATE TABLE `ctm_test_suite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testFolderId` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `createdAt` bigint(20) unsigned NOT NULL,
  `createdBy` bigint(20) unsigned NOT NULL,
  `modifiedAt` bigint(20) unsigned NOT NULL,
  `modifiedBy` bigint(20) unsigned NOT NULL,
  `testStatusId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_folder_id` (`testFolderId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
