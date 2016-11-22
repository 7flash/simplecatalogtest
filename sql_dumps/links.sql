CREATE TABLE `prefix_simplecatalog_links` (

	`id` 																																					INT UNSIGNED NOT NULL AUTO_INCREMENT,

	-- owner
	`parent_type` 																																TINYINT NOT NULL,
	`parent_id` 																																	INT UNSIGNED NOT NULL,

	-- from
	`from_target_type` 																														TINYINT NOT NULL,
	`from_target_id` 																															INT UNSIGNED NOT NULL,

	-- to
	`to_target_type` 																															TINYINT NOT NULL,
	`to_target_id` 																																INT UNSIGNED NOT NULL,


	PRIMARY KEY 																																	(`id`),
	INDEX `parent_pair_from_pair_to_target_type`																	(`parent_id`, `parent_type`, `from_target_id`, `from_target_type`, `to_target_type`),
	INDEX `from_target_id_from_target_type` 																			(`from_target_id`, `from_target_type` ASC),
	INDEX `to_target_id_to_target_type` 																					(`to_target_id`, `to_target_type` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
