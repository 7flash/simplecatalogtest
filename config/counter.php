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
 * Универсальный счетчик
 *
 */

/*
 * Исключить из подсчета просмотров продукта админов, автора и пользователей, у которых есть права на редактирование продукта
 */
$config['count']['product']['views']['exclude_managers'] = true;

return $config;

?>