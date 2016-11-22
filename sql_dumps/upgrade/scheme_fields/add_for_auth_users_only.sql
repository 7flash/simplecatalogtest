
ALTER TABLE `prefix_simplecatalog_scheme_fields` DROP `file_for_auth_users_only`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` ADD `for_auth_users_only` TINYINT NOT NULL DEFAULT 1 AFTER `allow_search_in_this_field`;

ALTER TABLE `prefix_simplecatalog_scheme_fields` ADD `min_user_rating_to_view` FLOAT(9,2) NOT NULL DEFAULT 0 AFTER `for_auth_users_only`;
