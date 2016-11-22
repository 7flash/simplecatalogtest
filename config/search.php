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
 * --- Валидация поискового запроса ---
 *
 */

/*
 * Регулярка для чистки строки запроса от ненужных символов.
 * tip: Для поддержки других языков нужно добавить набор символов языка
 * tip: литерал "\w" не используется т.к. он содержит "_", что означает "любой символ" для LIKE запроса sql
 */
$config['search']['products']['validate_regexp'] = '#[^\da-zа-яА-ЯёЁіІїЇєЄґҐ ,.:()"\'&«»№\/—-]#iu';

/*
 * Минимальная длина поискового запроса (символов всего)
 */
$config['search']['products']['query_length']['min'] = 3;

/*
 * Максимальная длина поискового запроса (символов всего)
 */
$config['search']['products']['query_length']['max'] = 500;

/*
 * Выводить ли ссылки для поиска в других ПС по этому же сайту если ничего не найдено средствами встроенного поиска
 */
$config['search']['products']['show_links_for_other_se'] = true;

return $config;

?>