
ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP INDEX `mandatory`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP INDEX `field_type`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP INDEX `sorting`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` ADD INDEX `scheme_id_sorting` (`scheme_id`, `sorting` ASC);

ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP INDEX `scheme_id`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP INDEX `show_field_names_in_list`;
