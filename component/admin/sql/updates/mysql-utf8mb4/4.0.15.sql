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

-- Step 1 of the UTF-8 Multibyte (utf8mb4) conversion for MySQL.
-- Drop indexes which will be added again in step 2.
ALTER TABLE `#__localise` DROP KEY `idx_path`;

-- Step 2 of the UTF-8 Multibyte (utf8mb4) conversion for MySQL.
-- Step 2.1: Limit indexes so their max allowed lengths would not get exceeded with utf8mb4.
ALTER TABLE `#__localise` ADD KEY `idx_path` (`path`(191));

-- Step 2.2: Enlarge columns to avoid data loss on later conversion to utf8mb4.
ALTER TABLE `#__localise` MODIFY `path` varchar(400) NOT NULL DEFAULT '';

-- Step 2.3: Convert all tables to utf8mb4 chracter set with utf8mb4_unicode_ci collation.
ALTER TABLE `#__localise` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__localise_revised_values` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Step 2.4: Set collation to utf8mb4_bin for formerly utf8_bin collated columns.
ALTER TABLE `#__localise` MODIFY `path` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '';

-- Step 2.5: Set default character set and collation for all tables.
ALTER TABLE `#__localise` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__localise_revised_values` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
