
ALTER TABLE `prefix_simplecatalog_categories` DROP INDEX `url`;

ALTER TABLE `prefix_simplecatalog_categories` DROP INDEX `target_type_target_id`;

ALTER TABLE `prefix_simplecatalog_categories` ADD INDEX `target_id_target_type` (`target_id`, `target_type` ASC);

ALTER TABLE `prefix_simplecatalog_categories` DROP INDEX `target_id`;

ALTER TABLE `prefix_simplecatalog_categories` DROP INDEX `parent_id`;
