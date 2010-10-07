CREATE TABLE `ctm_test_run_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_run_browser_id` bigint(20) unsigned NOT NULL,
  `selenium_log` text,
  `run_log` text NOT NULL,
  `duration` int(10) unsigned DEFAULT NULL,
  `createdAt` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
