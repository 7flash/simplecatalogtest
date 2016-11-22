CREATE TABLE `prefix_simplecatalog_shop_orders` (

	`id`			 																INT UNSIGNED NOT NULL AUTO_INCREMENT,

	`name`			 															VARCHAR (40) NOT NULL,
	`phone`			 															VARCHAR (20) NOT NULL,
	`comment`			 														VARCHAR (500) NOT NULL,

	`delivery_type`														TINYINT UNSIGNED NOT NULL,

	`geo_name`			 													VARCHAR (100) NOT NULL,
	`exact_adress`			 											VARCHAR (100) NOT NULL,
	`receiver_name`			 											VARCHAR (40) NOT NULL,

	`payment_type`														TINYINT UNSIGNED NOT NULL,

	-- serialized
	`cart_data`			 													VARCHAR (1000) NOT NULL,
	`total_price`															FLOAT (12,2) UNSIGNED NOT NULL,

	`new`			 																TINYINT NOT NULL,

	`date_add`			 													DATETIME NOT NULL,
	`user_id`			 														INT(11) UNSIGNED,
	`ip`			 																VARCHAR(15) NOT NULL DEFAULT '',
	
	PRIMARY KEY			 													(`id`),
	INDEX `date_add`			 										(`date_add` DESC)

)

ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
