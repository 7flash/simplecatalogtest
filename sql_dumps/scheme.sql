CREATE TABLE `prefix_simplecatalog_scheme` (

	`id` 																																INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`scheme_url` 																												VARCHAR (50) NOT NULL DEFAULT '',
	`scheme_name` 																											VARCHAR (100) NOT NULL DEFAULT '',
	`description` 																											VARCHAR (2000) NOT NULL DEFAULT '',
	`keywords` 																													VARCHAR (1000) NOT NULL DEFAULT '',
	`active` 																														TINYINT NOT NULL DEFAULT 1,
	
	`menu_add_topic_create` 																						TINYINT NOT NULL DEFAULT 1,
	`menu_main_add_link` 																								TINYINT NOT NULL DEFAULT 1,
	
	`short_view_fields_count` 																					SMALLINT UNSIGNED NOT NULL DEFAULT 2,
	
	`allow_comments` 																										VARCHAR (16) NOT NULL DEFAULT 'user_defined',
	`allow_user_friendly_url` 																					TINYINT NOT NULL DEFAULT 1,
	`allow_edit_additional_seo_meta` 																		TINYINT NOT NULL DEFAULT 2,
	-- who can add/edit products (base settings)
	`can_add_products` 																									VARCHAR (16) NOT NULL DEFAULT 'admins',
	-- does moderation needed by default
	`moderation_needed` 																								TINYINT NOT NULL DEFAULT 1,
	`show_first_letter_groups` 																					TINYINT NOT NULL DEFAULT 1,
	-- show last products in profile whois page
	`profile_show_last_products` 																				TINYINT NOT NULL DEFAULT 1,
	-- show created products in profile publications
	`profile_show_created_products` 																		TINYINT NOT NULL DEFAULT 1,
	-- show online comments tab
	`show_online_comments` 																							TINYINT NOT NULL DEFAULT 1,
	-- min user rating to create product
	`min_user_rating_to_create_products` 																FLOAT(9,2) NOT NULL DEFAULT 0,
	-- days author can manage products after last editing
	`days_author_can_manage_products_after_last_editing`								SMALLINT UNSIGNED NOT NULL DEFAULT 0,

	-- for ordering schemes
	`sorting` 																													INT NOT NULL DEFAULT 1,
	-- products per page
	`items_per_page` 																										SMALLINT UNSIGNED NOT NULL DEFAULT 15,
	-- what to show on product items page (products, categories or map)
	`what_to_show_on_items_page` 																				VARCHAR (16) NOT NULL DEFAULT 'products',

	-- for product images
	`max_images_count` 																									INT NOT NULL DEFAULT 5,
	`image_width` 																											INT NOT NULL DEFAULT 600,
	`image_height` 																											INT NOT NULL DEFAULT 400,
	`exact_image_proportions` 																					TINYINT NOT NULL DEFAULT 1,

	-- add shop functionality
	`shop` 																															TINYINT NOT NULL DEFAULT 1,

	`block_show_last_products` 																					VARCHAR (10) NOT NULL DEFAULT 'none',

	`allow_drafts` 																											TINYINT NOT NULL DEFAULT 1,

	-- maps
	`map_items` 																												TINYINT NOT NULL DEFAULT 1,
	`select_preset_for_map_items` 																			TINYINT NOT NULL DEFAULT 1,
	`map_items_max` 																										SMALLINT UNSIGNED NOT NULL DEFAULT 15,

	-- allow deferred products for admins
	`allow_deferred_products` 																					TINYINT NOT NULL DEFAULT 1,

	`allow_count_views` 																								TINYINT NOT NULL DEFAULT 1,

	`template_name_first` 																							VARCHAR (50) NOT NULL DEFAULT 'default',
	`template_name_second` 																							VARCHAR (50) NOT NULL DEFAULT 'tiles',
	`use_first_template_as_default` 																		TINYINT NOT NULL DEFAULT 2,

	
	PRIMARY KEY 																												(`id`),
	UNIQUE `scheme_url` 																								(`scheme_url` ASC),
	INDEX `sorting` 																										(`sorting` ASC),

	INDEX `menu_add_topic_create_active` 																(`menu_add_topic_create`, `active` ASC),
	INDEX `menu_main_add_link_active` 																	(`menu_main_add_link`, `active` ASC),
	INDEX `show_online_comments_active_allow_comments` 									(`show_online_comments`, `active`, `allow_comments` ASC),
	INDEX `active_block_show_last_products` 														(`active`, `block_show_last_products` ASC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
