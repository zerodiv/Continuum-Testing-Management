CREATE TABLE `ctm_test_param_library_default_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `testParamLibraryId` bigint(20) unsigned NOT NULL,
  `defaultValue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_param_library_id` (`testParamLibraryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
