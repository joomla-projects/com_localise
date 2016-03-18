CREATE TABLE IF NOT EXISTS `#__localise_revised_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client` varchar(255) NOT NULL,
  `reftag` varchar(6) NOT NULL,
  `tag` varchar(6) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `revised` tinyint(1) NOT NULL DEFAULT 0,
  `key` varchar(255) NOT NULL,
  `target_text` mediumtext NOT NULL,
  `source_text` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
