<?php
/**
 * Simplecatalog plugin
 *
 * @copyright Serge Pustovit (PSNet), 2008 - 2015
 * @author    Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 * @link      http://psnet.lookformp3.net
 * @link      http://livestreet.ru/profile/PSNet/
 * @link      https://catalog.livestreetcms.com/profile/PSNet/
 * @link      http://livestreetguide.com/developer/PSNet/
 */

$config = array();

/*
 *
 * --- Категории ---
 *
 */

/*
 * Использовать ли в категориях полный путь (parent1/parent2/category) или короткий (category)
 * (для всех категорий)
 */
$config['categories']['use_full_path'] = true;

/*
 * Разрешить назначать продуктам только конечные категории у которых нет дочерних субкатегорий
 * (для всех схем)
 */
$config['categories']['product_categories_should_not_have_child_categories'] = true;

/*
 * Размер изображения для категории (ШхВ)
 */
$config['categories']['image_size'] = array(120, 120);

/*
 * Нужно ли показывать блок "Категории" в сайдбаре и при создании топика, если категории для схемы не заданы
 */
$config['categories']['show_block_when_no_categories'] = true;

return $config;

?>