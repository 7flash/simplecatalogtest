CREATE TABLE `prefix_simplecatalog_userassign` (

	`id` 										INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` 							INT(11) UNSIGNED NOT NULL,
	`group_id` 							INT UNSIGNED NOT NULL,
	
	PRIMARY KEY 						(`id`),
	INDEX `user_id` 				(`user_id` ASC),
	INDEX `group_id` 				(`group_id` ASC)
	
)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_userassign` ADD CONSTRAINT `prefix_simplecatalog_userassign_group_id_fk1` FOREIGN KEY (`group_id`) REFERENCES `prefix_simplecatalog_usergroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_userassign` ADD CONSTRAINT `prefix_simplecatalog_userassign_user_id_fk1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
