CREATE TABLE `ctm_test_param_library_description` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_param_library_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testParamLibraryId` (`test_param_library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
