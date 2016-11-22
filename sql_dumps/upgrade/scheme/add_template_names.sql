
ALTER TABLE `prefix_simplecatalog_scheme` ADD `template_name_first` VARCHAR (50) NOT NULL DEFAULT 'default';

ALTER TABLE `prefix_simplecatalog_scheme` ADD `template_name_second` VARCHAR (50) NOT NULL DEFAULT 'tiles';

ALTER TABLE `prefix_simplecatalog_scheme` ADD `use_first_template_as_default` TINYINT NOT NULL DEFAULT 2;
