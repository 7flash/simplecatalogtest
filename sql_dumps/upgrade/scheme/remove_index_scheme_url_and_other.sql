
ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `scheme_url_active`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `menu_add_topic_create`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `menu_main_add_link`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `active_allow_comments_show_online_comments`;

ALTER TABLE `prefix_simplecatalog_scheme` ADD INDEX `show_online_comments_active_allow_comments` (`show_online_comments`, `active`, `allow_comments` ASC);

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `active`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `allow_comments`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `moderation_needed`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `block_show_last_products`;

ALTER TABLE `prefix_simplecatalog_scheme` DROP INDEX `id_active`;
