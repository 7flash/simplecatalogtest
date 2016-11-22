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
 * --- Не редактировать ---
 *
 */

/*
 * --- Роутер ---
 * tip: *.url ключи используются только здесь
 */

$config['scheme']['url'] = 'scheme';
$config['$root$']['router']['page'][$config['scheme']['url']] = 'PluginSimplecatalog_ActionScheme';

$config['field']['url'] = 'field';
$config['$root$']['router']['page'][$config['field']['url']] = 'PluginSimplecatalog_ActionSchemeField';

$config['product']['url'] = 'product';
$config['$root$']['router']['page'][$config['product']['url']] = 'PluginSimplecatalog_ActionProduct';

$config['product-comments']['url'] = 'product-comments';
$config['$root$']['router']['page'][$config['product-comments']['url']] = 'PluginSimplecatalog_ActionProductcomments';

$config['product-search']['url'] = 'product-search';
$config['$root$']['router']['page'][$config['product-search']['url']] = 'PluginSimplecatalog_ActionProductsearch';

$config['usergroups']['url'] = 'usergroups';
$config['$root$']['router']['page'][$config['usergroups']['url']] = 'PluginSimplecatalog_ActionUsergroups';

$config['userassign']['url'] = 'userassign';
$config['$root$']['router']['page'][$config['userassign']['url']] = 'PluginSimplecatalog_ActionUserassign';

$config['sccategories']['url'] = 'sccategories';
$config['$root$']['router']['page'][$config['sccategories']['url']] = 'PluginSimplecatalog_ActionCategories';

/*
 * загрузка изображений
 */
$config['sc_images']['url'] = 'sc_images';
$config['$root$']['router']['page'][$config['sc_images']['url']] = 'PluginSimplecatalog_ActionImages';

/*
 * магазин
 */
$config['sc_shop']['url'] = 'shop';
$config['$root$']['router']['page'][$config['sc_shop']['url']] = 'PluginSimplecatalog_ActionShop';

/*
 * настройки связей схем
 */
$config['sc_links']['url'] = 'sc_links';
$config['$root$']['router']['page'][$config['sc_links']['url']] = 'PluginSimplecatalog_ActionSchemeLinks';

/*
 * публичный доступ для работы со схемами
 */
$config['scheme-public']['url'] = 'scheme-public';
$config['$root$']['router']['page'][$config['scheme-public']['url']] = 'PluginSimplecatalog_ActionSchemePublic';

/*
 * --- Список таблиц, используемых ORM ---
 */

$config['$root$']['db']['table']['simplecatalog_scheme'] = '___db.table.prefix___simplecatalog_scheme';
$config['$root$']['db']['table']['simplecatalog_scheme_fields'] = '___db.table.prefix___simplecatalog_scheme_fields';
$config['$root$']['db']['table']['simplecatalog_product'] = '___db.table.prefix___simplecatalog_product';
$config['$root$']['db']['table']['simplecatalog_product_categories'] = '___db.table.prefix___simplecatalog_product_categories';
$config['$root$']['db']['table']['simplecatalog_product_fields'] = '___db.table.prefix___simplecatalog_product_fields';
$config['$root$']['db']['table']['simplecatalog_usergroup'] = '___db.table.prefix___simplecatalog_usergroups';
$config['$root$']['db']['table']['simplecatalog_userassign'] = '___db.table.prefix___simplecatalog_userassign';
$config['$root$']['db']['table']['simplecatalog_category'] = '___db.table.prefix___simplecatalog_categories';
$config['$root$']['db']['table']['simplecatalog_images_image'] = '___db.table.prefix___simplecatalog_images';
$config['$root$']['db']['table']['simplecatalog_shop_order'] = '___db.table.prefix___simplecatalog_shop_orders';
$config['$root$']['db']['table']['simplecatalog_scheme_link'] = '___db.table.prefix___simplecatalog_scheme_links';
$config['$root$']['db']['table']['simplecatalog_links_link'] = '___db.table.prefix___simplecatalog_links';
$config['$root$']['db']['table']['simplecatalog_maps_item'] = '___db.table.prefix___simplecatalog_maps';
$config['$root$']['db']['table']['simplecatalog_counter'] = '___db.table.prefix___simplecatalog_counter';

/*
 * --- Директории для загрузки файлов ---
 */

$config['upload_folder']['content'] = '___path.root.server______path.uploads.root___/simplecatalog/content/';
$config['upload_folder']['categories'] = '___path.root.server______path.uploads.root___/simplecatalog/categories/';
$config['upload_folder']['products'] = '___path.root.server______path.uploads.root___/simplecatalog/products/';

return $config;

?>