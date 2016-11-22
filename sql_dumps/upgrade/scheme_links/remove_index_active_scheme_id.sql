
ALTER TABLE `prefix_simplecatalog_scheme_links` DROP INDEX `active_scheme_id`;

-- first add new index for scheme_id field that used in foreign keys
ALTER TABLE `prefix_simplecatalog_scheme_links` ADD INDEX `scheme_id_sorting` (`scheme_id`, `sorting` ASC);

-- and after that can be removed old index
ALTER TABLE `prefix_simplecatalog_scheme_links` DROP INDEX `scheme_id`;

ALTER TABLE `prefix_simplecatalog_scheme_links` DROP INDEX `active`;

ALTER TABLE `prefix_simplecatalog_scheme_links` DROP INDEX `sorting`;
