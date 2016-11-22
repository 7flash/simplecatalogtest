CREATE TABLE `prefix_simplecatalog_product_fields` (

	`id` 															INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_id` 											INT UNSIGNED NOT NULL,
	`field_id` 												INT UNSIGNED NOT NULL,

	`content_type`										TINYINT NOT NULL,

	`content_int`											INT NOT NULL,
	`content_float`										FLOAT (9,2) NOT NULL,
	`content_varchar`									VARCHAR (2000) NOT NULL,
	`content_text`										TEXT NOT NULL,

	-- original data
	`content_source`									TEXT NOT NULL DEFAULT '',


	PRIMARY KEY 											(`id`),
	UNIQUE `product_id_field_id` 			(`product_id`, `field_id`),

	INDEX `field_id_content_int`			(`field_id`, `content_int`),
	INDEX `field_id_content_float`		(`field_id`, `content_float`),
	INDEX `field_id_content_varchar`	(`field_id`, `content_varchar`(255))
	-- no index for content_text row - it will be too big

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_product_fields` ADD CONSTRAINT `prefix_simplecatalog_product_fields_product_id_fk1` FOREIGN KEY (`product_id`) REFERENCES `prefix_simplecatalog_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_product_fields` ADD CONSTRAINT `prefix_simplecatalog_product_fields_field_id_fk1` FOREIGN KEY (`field_id`) REFERENCES `prefix_simplecatalog_scheme_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
