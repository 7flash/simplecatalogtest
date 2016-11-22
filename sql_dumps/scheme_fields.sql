CREATE TABLE `prefix_simplecatalog_scheme_fields` (

	`id` 																		INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`scheme_id` 														INT UNSIGNED NOT NULL,
	
	`title` 																VARCHAR (500) NOT NULL DEFAULT '',
	`description` 													VARCHAR (1000) NOT NULL DEFAULT '',

	`mandatory` 														TINYINT NOT NULL DEFAULT 1,

	-- for direct access of product field by code
	`code` 																	VARCHAR (30) NOT NULL,

	`field_type` 														VARCHAR (50) NOT NULL,

	-- text before and after value
	`value_prefix` 													VARCHAR (50) NOT NULL,
	`value_postfix` 												VARCHAR (50) NOT NULL,

	-- for ordering fields
	`sorting` 															INT NOT NULL DEFAULT 1,

	-- need to parse value
	`run_parser` 														TINYINT NOT NULL DEFAULT 1,

	-- field validator
	`validator` 														VARCHAR (20) NOT NULL,

	-- default value for this field
	`default_value` 												VARCHAR (1000) NOT NULL,

	-- where need to show field
	`places_to_show_field` 									VARCHAR (14) NOT NULL DEFAULT 'anywhere',

	`show_field_names_in_list` 							TINYINT NOT NULL DEFAULT 1,

	-- for parametric search
	`allow_search_in_this_field` 						TINYINT NOT NULL DEFAULT 1,

	-- user rules
	`for_auth_users_only` 									TINYINT NOT NULL DEFAULT 1,
	`min_user_rating_to_view`								FLOAT(9,2) NOT NULL DEFAULT 0,

	-- field can be edited in form
	`editable_by_user` 											TINYINT NOT NULL DEFAULT 2,

	
	-- field_type: for text field
	`text_min_length` 											INT NOT NULL DEFAULT 2,
	`text_max_length` 											INT NOT NULL DEFAULT 2000,
	
	-- field_type: for textarea field
	`textarea_min_length` 									INT NOT NULL DEFAULT 2,
	`textarea_max_length` 									INT NOT NULL DEFAULT 5000,

	-- field_type: for file field
	`file_max_size` 												INT UNSIGNED NOT NULL DEFAULT 500,
	`file_types_allowed` 										VARCHAR (100) NOT NULL DEFAULT '',

	-- field_type: select
	`select_items` 													VARCHAR (2000) NOT NULL DEFAULT '',
	`select_multiple_items` 								TINYINT NOT NULL DEFAULT 1,
	`select_filter_items_using_and_logic` 	TINYINT NOT NULL DEFAULT 2,


	PRIMARY KEY 														(`id`),
	INDEX `scheme_id_sorting` 							(`scheme_id`, `sorting` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--

ALTER TABLE `prefix_simplecatalog_scheme_fields` ADD CONSTRAINT `prefix_simplecatalog_scheme_fields_scheme_id_fk1` FOREIGN KEY (`scheme_id`) REFERENCES `prefix_simplecatalog_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
