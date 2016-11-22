
ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `target_type_target_id`;

ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `target_id`;

ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `target_type`;

ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `lat`;

ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `lng`;

ALTER TABLE `prefix_simplecatalog_maps` DROP INDEX `lat_lng`;

ALTER TABLE `prefix_simplecatalog_maps` ADD INDEX `target_id_target_type_lat_lng` (`target_id`, `target_type`, `lat`, `lng`);
