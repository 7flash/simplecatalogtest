CREATE TABLE `prefix_simplecatalog_counter` (

	`id` 																									INT UNSIGNED NOT NULL AUTO_INCREMENT,

	`target_type` 																				TINYINT NOT NULL,
	`target_id` 																					INT UNSIGNED NOT NULL,

	`count` 																							INT UNSIGNED NOT NULL,

	PRIMARY KEY 																					(`id`),
	UNIQUE `target_id_target_type` 												(`target_id`, `target_type`)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
