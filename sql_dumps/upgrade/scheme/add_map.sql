
ALTER TABLE `prefix_simplecatalog_scheme` ADD `map_items` TINYINT NOT NULL DEFAULT 1;

ALTER TABLE `prefix_simplecatalog_scheme` ADD `select_preset_for_map_items` TINYINT NOT NULL DEFAULT 1;

ALTER TABLE `prefix_simplecatalog_scheme` ADD `map_items_max` SMALLINT UNSIGNED NOT NULL DEFAULT 15;
