CREATE TABLE `ctm_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ctmUserRoleId` bigint(20) unsigned NOT NULL DEFAULT '1',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0',
  `isVerified` tinyint(1) NOT NULL DEFAULT '0',
  `verifiedWhen` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createdOn` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tempPassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `is_disabled` (`isDisabled`),
  KEY `ctmUserRoleId` (`ctmUserRoleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;
