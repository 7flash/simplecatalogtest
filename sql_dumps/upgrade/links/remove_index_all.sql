
ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `parent_type_parent_id_from_target_type_from_target_id`;

ALTER TABLE `prefix_simplecatalog_links` ADD INDEX `parent_pair_from_pair_to_target_type` (`parent_id`, `parent_type`, `from_target_id`, `from_target_type`, `to_target_type`);


ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `parent_type`;

ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `parent_id`;

ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `from_target_type`;

ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `from_target_id`;

ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `to_target_type`;

ALTER TABLE `prefix_simplecatalog_links` DROP INDEX `to_target_id`;


ALTER TABLE `prefix_simplecatalog_links` ADD INDEX `from_target_id_from_target_type` (`from_target_id`, `from_target_type` ASC);

ALTER TABLE `prefix_simplecatalog_links` ADD INDEX `to_target_id_to_target_type` (`to_target_id`, `to_target_type` ASC);
