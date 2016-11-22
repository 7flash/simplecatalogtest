CREATE TABLE `prefix_simplecatalog_maps` (

	`id` 																								INT UNSIGNED NOT NULL AUTO_INCREMENT,

	`target_type`			 																	TINYINT UNSIGNED NOT NULL,
	`target_id`			 																		INT UNSIGNED NOT NULL,

	`lat`																								FLOAT(10,6) NOT NULL,
	`lng`																								FLOAT(10,6) NOT NULL,

	`title` 																						VARCHAR(500) NOT NULL,
	`description` 																			VARCHAR(500) NOT NULL,

	`extra_data` 																				VARCHAR(4000) NOT NULL,


	PRIMARY KEY 																				(`id`),
	INDEX `target_id_target_type_lat_lng` 							(`target_id`, `target_type`, `lat`, `lng`)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
