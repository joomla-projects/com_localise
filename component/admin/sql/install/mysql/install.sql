CREATE TABLE IF NOT EXISTS `#__localise` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `path` varchar(255) NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_path` (`path`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
