CREATE TABLE ctm_test_browser (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  majorVersion int(11) NOT NULL,
  minorVersion int(11) NOT NULL,
  patchVersion int(11) NOT NULL,
  isAvailable int(1) NOT NULL DEFAULT '0',
  lastSeen bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY `name` (`name`),
  KEY majorVersion (majorVersion),
  KEY minorVersion (minorVersion),
  KEY patchVersion (patchVersion),
  KEY isAvailable (isAvailable)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
