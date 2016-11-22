CREATE TABLE `prefix_simplecatalog_scheme_links` (

	`id` 																										INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`active` 																								TINYINT NOT NULL DEFAULT 1,

	-- link name to display
	`name` 																									VARCHAR (50) NOT NULL DEFAULT '',
	`description` 																					VARCHAR (200) NOT NULL DEFAULT '',

	-- for scheme
	`scheme_id` 																						INT UNSIGNED NOT NULL,
	-- target scheme
	`target_scheme_id` 																			INT UNSIGNED NOT NULL,

	-- relation type (has one, has many)
	`type` 																									TINYINT NOT NULL,

	-- display format
	`show_type` 																						TINYINT NOT NULL,
	-- selecting products: all or created by user only
	`select_type` 																					TINYINT NOT NULL,

	-- for ordering scheme link settings
	`sorting` 																							INT NOT NULL DEFAULT 1,

	-- products count to select for adding links (limit)
	`products_count_to_select` 															INT UNSIGNED NOT NULL DEFAULT 100,


	PRIMARY KEY 																						(`id`),
	INDEX `scheme_id_sorting` 															(`scheme_id`, `sorting` ASC),
	INDEX `target_scheme_id` 																(`target_scheme_id` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_scheme_links` ADD CONSTRAINT `prefix_simplecatalog_scheme_links_scheme_id_fk1` FOREIGN KEY (`scheme_id`) REFERENCES `prefix_simplecatalog_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_scheme_links` ADD CONSTRAINT `prefix_simplecatalog_scheme_links_target_scheme_id_fk1` FOREIGN KEY (`target_scheme_id`) REFERENCES `prefix_simplecatalog_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
