CREATE TABLE `prefix_simplecatalog_categories` (

	`id` 																	INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`url` 																VARCHAR (50) NOT NULL DEFAULT '',
	`full_url` 														VARCHAR (50) NOT NULL DEFAULT '',
	`name` 																VARCHAR (100) NOT NULL DEFAULT '',
	`parent_id` 													INT UNSIGNED DEFAULT '0',
	`sorting` 														INT NOT NULL DEFAULT 1,
	
	`items_count` 												INT UNSIGNED NOT NULL DEFAULT 0,
	`target_type` 												TINYINT UNSIGNED NOT NULL,
	`target_id` 													INT UNSIGNED NOT NULL,

	`image_url` 													VARCHAR (500) NOT NULL DEFAULT '',

	`description` 												VARCHAR (500) NOT NULL DEFAULT '',
	
	PRIMARY KEY 													(`id`),
	UNIQUE `url_target_id_target_type` 		(`url`, `target_id`, `target_type`),
	INDEX `sorting` 											(`sorting` ASC),
	INDEX `target_type` 									(`target_type` ASC),
	INDEX `target_id_target_type` 				(`target_id`, `target_type` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


--

-- ls orm sets 0 for parent_id for main parent (top-level) but foreign keys need that this should be NULL so dont use there FK
-- ALTER TABLE `prefix_simplecatalog_categories` ADD CONSTRAINT `prefix_simplecatalog_categories_parent_id_fk1` FOREIGN KEY (`parent_id`) REFERENCES `prefix_simplecatalog_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
