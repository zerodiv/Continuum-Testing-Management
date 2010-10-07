CREATE TABLE `ctm_test_folder` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentId` (`parentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100;
