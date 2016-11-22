
ALTER TABLE `prefix_simplecatalog_product_fields` ADD `content_source` TEXT NOT NULL DEFAULT '';

-- set current data as source input data
UPDATE
	`prefix_simplecatalog_product_fields`
SET
	`content_source` = CASE
			WHEN `content_type`=1 THEN `content_int`
			WHEN `content_type`=2 THEN `content_float`
			WHEN `content_type`=4 THEN `content_varchar`
			WHEN `content_type`=8 THEN `content_text`
	END;
