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
 * Разрешенные типы полей для схем
 */
$config['scheme']['Allowed_Field_Types'] = array(
	/*
	 * строка
	 */
	'text',
	/*
	 * многострочный текст
	 */
	'textarea',
	/*
	 * файл
	 */
	'file',
	/*
	 * флажок
	 */
	'checkbox',
	/*
	 * нередактируемое поле, которое можно использовать как заголовок раздела, заполнив его значением по-умолчанию при создании поля
	 */
	'noteditable',
	/*
	 * выпадающий список
	 */
	'select'
);

/*
 * Код шаблонов схемы по-умолчанию
 */
$config['scheme']['templates']['default_code'] = 'default';

return $config;

?>