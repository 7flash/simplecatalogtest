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
 * Общие настройки плагина
 *
 */

/*
 * Разрешить подключение кастомных css и js файлов:
 * 		/templates/skin/_custom/style.css
 * 		/templates/skin/_custom/script.js
 * tip: нужны для того, чтобы вносить правки в дизайн и они не затирались при обновлении плагина
 */
$config['general']['assets']['enable_custom_css_and_js_files'] = false;

/*
 * Коды шаблонов, для которых нужно дополнительно подгружать специальные css файлы (адаптацию)
 * Подробнее об адаптациях стилей см. /templates/skin/_adaptations/readme.txt
 */
$config['general']['assets']['load_special_assets_for_skins'] = array(
	'developer-kit',
	'maxone',
	'jupiter',
);


/*
 *
 * --- Элементов на страницу ---
 *
 */

/*
 * Значения для выбора количества элементов на страницу
 */
$config['general']['pagination']['values_for_per_page'] = array(
	10,
	20,
	30,
	50,
	100,
);

return $config;

?>