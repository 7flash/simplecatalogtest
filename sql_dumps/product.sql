CREATE TABLE `prefix_simplecatalog_product` (

	`id` 																								INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`scheme_id` 																				INT UNSIGNED NOT NULL,
	
	`add_date` 																					DATETIME NOT NULL,
	`edit_date` 																				DATETIME NOT NULL,

	`user_id` 																					INT(11) UNSIGNED NOT NULL,
	`user_id_edit_last` 																INT(11) UNSIGNED NOT NULL,

	`moderation` 																				TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`product_url` 																			VARCHAR(2000) NOT NULL,

	`comment_count` 																		INT UNSIGNED NOT NULL DEFAULT 0,
	`fields_filled_count` 															INT UNSIGNED NOT NULL DEFAULT 0,

	`user_allow_comments` 															TINYINT UNSIGNED NOT NULL DEFAULT 1,

	-- for shop
	`price`																							FLOAT (9,2) UNSIGNED DEFAULT NULL,
	-- can be: price (ex. 10), discount (ex. -5%), markup (ex. 5%)
	`price_new`																					VARCHAR(12) DEFAULT NULL,

	-- seo
	`seo_title` 																				VARCHAR(100) NOT NULL,
	`seo_description` 																	VARCHAR(200) NOT NULL,
	`seo_keywords` 																			VARCHAR(200) NOT NULL,


	PRIMARY KEY 																				(`id`),
	INDEX `user_id` 																		(`user_id` ASC),
	INDEX `user_id_edit_last` 													(`user_id_edit_last` ASC),
	INDEX `moderation` 																	(`moderation` ASC),
	INDEX `product_url` 																(`product_url`(255) ASC),

	INDEX `scheme_id_moderation_user_id` 								(`scheme_id`, `moderation`, `user_id` ASC),
	INDEX `scheme_id_moderation_price` 									(`scheme_id`, `moderation`, `price`),
	INDEX `scheme_id_moderation_add_date` 							(`scheme_id`, `moderation`, `add_date`),
	INDEX `scheme_id_moderation_comment_count` 					(`scheme_id`, `moderation`, `comment_count`),
	INDEX `scheme_id_moderation_fields_filled_count` 		(`scheme_id`, `moderation`, `fields_filled_count`)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_product` ADD CONSTRAINT `prefix_simplecatalog_product_scheme_id_fk1` FOREIGN KEY (`scheme_id`) REFERENCES `prefix_simplecatalog_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_product` ADD CONSTRAINT `prefix_simplecatalog_product_user_id_fk1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_product` ADD CONSTRAINT `prefix_simplecatalog_product_user_id_edit_last_fk1` FOREIGN KEY (`user_id_edit_last`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
