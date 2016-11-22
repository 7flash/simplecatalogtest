CREATE TABLE `prefix_simplecatalog_usergroups` (

	`id` 																				INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`group_name` 																VARCHAR (100) NOT NULL DEFAULT '',
	`active` 																		TINYINT NOT NULL DEFAULT 1,
	`scheme_id` 																INT UNSIGNED NOT NULL,
	
	`can_user_edit_products` 										TINYINT NOT NULL DEFAULT 1,
	`user_products_need_moderation` 						TINYINT NOT NULL DEFAULT 1,
	`user_can_moderate_products` 								TINYINT NOT NULL DEFAULT 1,
	`user_can_defer_products` 									TINYINT NOT NULL DEFAULT 1,
	`user_can_create_new_products` 							TINYINT NOT NULL DEFAULT 1,
	
	PRIMARY KEY 																(`id`),
	INDEX `active` 															(`active` ASC),
	INDEX `scheme_id_active` 										(`scheme_id`, `active` ASC)
	
)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_usergroups` ADD CONSTRAINT `prefix_simplecatalog_usergroups_scheme_id_fk1` FOREIGN KEY (`scheme_id`) REFERENCES `prefix_simplecatalog_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
