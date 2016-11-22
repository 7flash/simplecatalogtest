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

if (!isset($config)) {
	die('SC: no config for urls setup');
}

/*
 *
 * --- Системный редирект урлов (часть конфига urls.php) ---
 *
 */
if ($config['urls']['catalog']['enable_short_urls']) {
	$sDatFile = Config::Get('sys.cache.dir') . 'sc_catalog_urls.dat';
	if ($aShortUrls = @file($sDatFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
		$aRouterUri = array();
		foreach($aShortUrls as $sUrl) {
			/*
			 * Правило для списка продуктов (items)
			 *
			 * tip: дополнительная проверка на слеш "/" в последних скобках нужна т.к. в 1.0.3 версии есть баг в роутере в GetRequestUri,
			 * который не отсекает крайние слеши если есть строка запроса "?" т.е. при "?" слеш будет, а если нет строки запроса, то слеши по бокам будут убраны
			 */
			$aRouterUri['#^(' . $sUrl . ')(/page\d+/?)?$#'] = 'product/items/$1$2';
			/*
			 * Правило для страницы продукта (item)
			 */
			$aRouterUri['#^' . $sUrl . '/(.*)$#'] = 'product/item/$1';
		}
		/*
		 * добавить правила в роутер
		 */
		$config['$root$']['router']['uri'] = array_merge(Config::Get('router.uri'), $aRouterUri);
	}
}
