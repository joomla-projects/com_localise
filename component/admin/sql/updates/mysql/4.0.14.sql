--
-- Step 1 of the UTF-8 Multibyte (utf8mb4) conversion for MySQL
--
-- Drop indexes which will be added again in step 2.
--
ALTER TABLE `#__localise` DROP KEY `idx_path`;

--
-- Step 2 of the UTF-8 Multibyte (utf8mb4) conversion for MySQL
--
-- Add back indexes previosly dropped with step 1,
-- but with limited lenghts of columns, and then perform the conversions
-- for utf8mb4.
--
-- Step 2.1: Limit indexes to first 100 so their max allowed lengths would not get exceeded with utf8mb4.
-- 
ALTER TABLE `#__localise` ADD UNIQUE KEY `idx_path` (`path`(100));

-- 
-- Step 2.2: Enlarge columns to avoid data loss on later conversion to utf8mb4
-- 
ALTER TABLE `#__localise` MODIFY `path` varchar(400) NOT NULL DEFAULT '';

-- 
-- Step 2.3: Convert all tables to utf8mb4 chracter set with utf8mb4_unicode_ci collation.
-- 
ALTER TABLE `#__localise` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__localise_revised_values` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Step 2.4: Set collation to utf8mb4_bin for formerly utf8_bin collated columns.
--
ALTER TABLE `#__localise` MODIFY `path` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '';

-- 
-- Step 2.5: Set default character set and collation for all tables.
-- 
ALTER TABLE `#__localise` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__localise_revised_values` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
