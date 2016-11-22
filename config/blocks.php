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
 * --- Блоки ---
 *
 */

/*
 * Блок "ещё от этого автора"
 */
$config['$root$']['block']['rule_more_products_from_this_author'] = array(
	'action' => array('product' => array('item')),
	'blocks' => array(
		'right' => array(
			'sameuserproducts' => array(
				'params' => array('plugin' => 'simplecatalog'),
				'priority' => 800,
			),
		)
	),
);

/*
 * Блок "категории схемы"
 */
$config['$root$']['block']['rule_product_scheme_categories'] = array(
	'action' => array(
		'product' => array('items', 'item', 'category', 'moderation', 'my', 'drafts'),
		'product-search'
	),
	'blocks' => array(
		'right' => array(
			'schemecategories' => array(
				'params' => array('plugin' => 'simplecatalog'),
				'priority' => 700,
			),
		)
	),
);

/*
 * Блок "ещё из этих категорий"
 */
$config['$root$']['block']['rule_more_products_in_these_categories'] = array(
	'action' => array('product' => array('item')),
	'blocks' => array(
		'right' => array(
			'inthesecategories' => array(
				'params' => array('plugin' => 'simplecatalog'),
				'priority' => 750,
			),
		)
	),
);

/*
 * Блок "выбор по фильтру продуктов схемы"
 */
$config['$root$']['block']['rule_product_filter'] = array(
	'action' => array('product' => array('items', 'item', 'category', 'my', 'filter'), 'product-search'),
	'blocks' => array(
		'right' => array(
			'productfilter' => array(
				'params' => array('plugin' => 'simplecatalog'),
				'priority' => 900,
			),
		)
	),
);

/*
 * Блок "последние продукты в сайдбаре"
 */
$config['$root$']['block']['rule_last_products_in_sidebar'] = array(
	'action' => array('index', 'blogs'),
	'blocks' => array(
		'right' => array(
			'lastproducts' => array(
				'params' => array('plugin' => 'simplecatalog'),
				'priority' => 800,
			),
		)
	),
);

return $config;

?>