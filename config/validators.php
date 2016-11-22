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
 * --- Список валидаторов для полей схемы (проверяет поля продукта при публикации) ---
 *
 */
$config['validators']['list'] = array(
	/*
	 * Ключ - уникальное системное имя, которое также формирует название валидатора для показа в форме создания поля схемы и текст ошибки, если поле не прошло проверку на валидацию.
	 * Указывает на значение из языкового файла: $aLang.plugin.simplecatalog.validators_list.ЭТОТ_КЛЮЧ.*
	 */
	'url' => array(
		/*
		 * Опционально. Все валидаторы должны иметь либо регулярное выражение (ключ "regexp") либо колбек (ключ "callback"),
		 * если указана и регулярка и колбек, то будет использовано регулярное выражение, колбек будет проигнорирован.
		 * Регулярное выражение для проверки значения поля, будет подставлено в preg_match () и если совпадений не будет, то такое поле будет считаться не корректным
		 */
		'regexp' => '#^(?:<a\s+[^>]*href=["\'])?https?://(?:www\.)?[\w\.-]+(?:[\w/?=&;\[\]\.-]+)?(?:["\'][^>]*>.*</a>)?$#iuU',
		/*
		 * Опционально. Колбек php, позволяет задать функцию для проверки значения поля, подставляется в call_user_func ().
		 * Передается один параметр - значение поля. Колбек должен вернуть true, если проверка успешна или false в случае неверного значения
		 */
		'callback' => '',
		/*
		 * задать тип для значения (можно установить один из 4 базовых типов полей: int, float, varchar или text), для хранения значения в соответствующем столбце
		 */
		'value_type' => 'varchar',

	),
	'mail' => array(
		'regexp' => '#^[\w\+\.-]+@[\w\.-]+$#',
		'callback' => '',
		'value_type' => 'varchar',
	),
	'integer' => array(
		'regexp' => '#^\d+$#u',
		'callback' => '',
		'value_type' => 'int',
	),
	'float' => array(
		'regexp' => '#^\d+(?:\.\d++)?$#u',
		'callback' => '',
		'value_type' => 'float',
	),
	'date' => array(
		'regexp' => '#^\d{4}-\d{2}-\d{2}$#u',
		'callback' => '',
		'value_type' => 'varchar',
	),
	'phone' => array(
		'regexp' => '#^\+?[0-9() -]++$#u',
		'callback' => '',
		'value_type' => 'varchar',
	),

);

return $config;

?>