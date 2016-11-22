CREATE TABLE `prefix_simplecatalog_product_categories` (

	`id` 																	INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_id` 													INT UNSIGNED NOT NULL,
	`category_id` 												INT UNSIGNED NOT NULL,

	PRIMARY KEY 													(`id`),
	UNIQUE `product_id_category_id` 			(`product_id`, `category_id` ASC),
	INDEX `category_id` 									(`category_id` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_product_categories` ADD CONSTRAINT `prefix_simplecatalog_product_categories_product_id_fk1` FOREIGN KEY (`product_id`) REFERENCES `prefix_simplecatalog_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_simplecatalog_product_categories` ADD CONSTRAINT `prefix_simplecatalog_product_categories_category_id_fk1` FOREIGN KEY (`category_id`) REFERENCES `prefix_simplecatalog_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
