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
 * Включить ли поддержку коротких урлов для каталогов
 * (ссылки на каталог будут вида "сайт/урл_каталога")
 */
$config['urls']['catalog']['enable_short_urls'] = false;


/*
 *
 * --- Системный редирект урлов (не редактировать) ---
 *
 */
include_once(dirname(__FILE__) . '/urls/loader.php');

return $config;

?>