
ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `scheme_id_moderation`;

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `scheme_id_user_id_moderation`;

ALTER TABLE `prefix_simplecatalog_product` ADD INDEX `scheme_id_moderation_user_id` (`scheme_id`, `moderation`, `user_id` ASC);

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `scheme_id`;

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `id_moderation`;


ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `price`;

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `add_date`;

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `comment_count`;

ALTER TABLE `prefix_simplecatalog_product` DROP INDEX `fields_filled_count`;


ALTER TABLE `prefix_simplecatalog_product` ADD INDEX `scheme_id_moderation_price` (`scheme_id`, `moderation`, `price`);

ALTER TABLE `prefix_simplecatalog_product` ADD INDEX `scheme_id_moderation_add_date` (`scheme_id`, `moderation`, `add_date`);

ALTER TABLE `prefix_simplecatalog_product` ADD INDEX `scheme_id_moderation_comment_count` (`scheme_id`, `moderation`, `comment_count`);

ALTER TABLE `prefix_simplecatalog_product` ADD INDEX `scheme_id_moderation_fields_filled_count` (`scheme_id`, `moderation`, `fields_filled_count`);
