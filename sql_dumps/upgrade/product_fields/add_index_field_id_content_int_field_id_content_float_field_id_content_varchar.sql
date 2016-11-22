
ALTER TABLE `prefix_simplecatalog_product_fields` ADD INDEX `field_id_content_int` (`field_id`, `content_int`);

ALTER TABLE `prefix_simplecatalog_product_fields` ADD INDEX `field_id_content_float` (`field_id`, `content_float`);

ALTER TABLE `prefix_simplecatalog_product_fields` ADD INDEX `field_id_content_varchar` (`field_id`, `content_varchar`);
