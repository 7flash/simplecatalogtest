CREATE TABLE `prefix_simplecatalog_images` (

	`id`			 																INT UNSIGNED NOT NULL AUTO_INCREMENT,

	`target_id`			 													INT UNSIGNED NOT NULL,
	`target_type`			 												TINYINT UNSIGNED NOT NULL,

	`file_path`			 													VARCHAR (500) NOT NULL DEFAULT '',

	`date_add`			 													DATETIME NOT NULL,
	`user_id`			 														INT(11) UNSIGNED NOT NULL,
	`sorting`			 														INT NOT NULL DEFAULT 1,
	
	PRIMARY KEY			 													(`id`),
	INDEX `target_id_target_type`			 				(`target_id`, `target_type` ASC),
	INDEX `sorting`			 											(`sorting` DESC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
