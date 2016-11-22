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

class PluginSimplecatalog_ModuleSitemap extends PluginSimplecatalog_Inherits_PluginSitemap_ModuleSitemap {

	/*
	 * Префикс урла для ссылок сайтмапа
	 * Нужен чтобы урл не совпадал с другими урлами движка или сторонних плагинов и чтобы можно было "ловить" запросы на данные каталогов в __call
	 */
	const SC_URL_PREFIX = 'simplecatalog';
	/*
	 * Добавочный урл для списка всех схем
	 */
	const SC_SCHEMES_LIST_ALL_URL = 'schemeslistall';
	/*
	 * Сортировка (ORM) продуктов для вывода в сайтмапе
	 */
	protected $aProductsSortingOrder = array();


	/*
	 *
	 * --- Наследуемые методы плагина sitemap ---
	 *
	 */

	public function Init() {
		parent::Init();
		$this->aProductsSortingOrder = Config::Get('plugin.simplecatalog.sitemap.products.sord_order');
	}


	/**
	 * Дополнить счетчики добавляемых в сайтмап данных
	 *
	 * @return array
	 */
	public function getExternalCounters() {
		$aData = $this->GetSimplecatalogCounters();
		/*
		 * добавить к другим счетчикам сторонних плагинов
		 */
		return array_merge(parent::getExternalCounters(), $aData);
	}


	/*
	 *
	 * --- Реализация методов получения данных ---
	 *
	 */

	/**
	 * Получить счетчики плагина SC
	 *
	 * @return array
	 */
	protected function GetSimplecatalogCounters() {
		$iItemsPerPage = Config::Get('plugin.simplecatalog.sitemap.products.items_per_page');
		$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems();
		/*
		 * добавляемые счетчики
		 */
		$aData = array();
		/*
		 *
		 * --- Добавление продуктов схем ---
		 *
		 */
		foreach($aSchemes as $oScheme) {
			/*
			 * количество продуктов каждой схемы
			 */
			$iCount = $this->PluginSimplecatalog_Product_MyGetCountProductItemsByModerationAndScheme(PluginSimplecatalog_ModuleProduct::MODERATION_DONE, $oScheme);
			/*
			 * сколько страниц продуктов у каждой схемы
			 */
			$aData[self::SC_URL_PREFIX . $oScheme->getId()] = (int) ceil($iCount / $iItemsPerPage);
		}
		/*
		 *
		 * --- Добавление списка схем (каталогов продуктов) ---
		 *
		 */
		if (!empty($aSchemes)) {
			/*
			 * список всех схем, запись "1" с учетом, что их не может быть больше 50к
			 */
			$aData[self::SC_URL_PREFIX . self::SC_SCHEMES_LIST_ALL_URL] = 1;
		}
		return $aData;
	}


	/**
	 * Вывод в сайтмап списка схем (каталогов продуктов)
	 *
	 * @param $iPage
	 * @return array
	 */
	protected function GetSchemesListAll($iPage) {
		$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems();

		$aData = array();
		foreach($aSchemes as $oScheme) {
			/*
			 * получить последний отредактированный продукт схемы
			 */
			$sEditDateLast = null;
			$aProducts = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
				),
				$oScheme,
				1,
				1,
				array('edit_date' => 'desc')
			);
			if ($aProducts['count']) {
				$oProductEditLast = reset($aProducts['collection']);
				$sEditDateLast = $oProductEditLast->getEditDate();
			}
			/*
			 * собрать схемы для вывода в сайтмап
			 */
			$aData[] = $this->getDataForSitemapRow(
				$oScheme->getCatalogItemsWebPath(),
				/*
				 * дата последнего отредактированного продукта схемы
				 */
				$sEditDateLast,
				Config::Get('plugin.simplecatalog.sitemap.schemes.priority'),
				Config::Get('plugin.simplecatalog.sitemap.schemes.changefreq')
			);
		}
		return $aData;
	}


	/**
	 * Вывод в сайтмап промодерированных продуктов схемы по её ид и странице
	 *
	 * @param int $iId   			ид схемы
	 * @param int $iPage 			номер страницы
	 * @return array                готовые данные для вывода в сайтмап
	 */
	protected function GetSchemeProductsBySchemeIdAndPage($iId, $iPage) {
		/*
		 * есть ли такая активная схема
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeById((int) $iId)) {
			return array();
		}
		/*
		 * получить продукты схемы на страницу
		 */
		$aProducts = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
			),
			$oScheme,
			$iPage,
			Config::Get('plugin.simplecatalog.sitemap.products.items_per_page'),
			$this->aProductsSortingOrder
		);
		/*
		 * если продуктов нет
		 */
		if (!$aProducts['count']) {
			return array();
		}
		/*
		 * собрать данные продуктов для вывода в сайтмап
		 */
		$aData = array();
		foreach($aProducts['collection'] as $oProduct) {
			$aData[] = $this->getDataForSitemapRow(
				$oProduct->getItemShowWebPath(),
				$oProduct->getEditDate(),
				Config::Get('plugin.simplecatalog.sitemap.products.priority'),
				Config::Get('plugin.simplecatalog.sitemap.products.changefreq')
			);
		}
		return $aData;
	}


	/**
	 * Вызов неизвестного метода для "ловли" получения данных каталогов и списка схем
	 *
	 * @param $sName		имя метода
	 * @param $aArgs		аргументы
	 * @return mixed
	 */
	public function __call($sName, $aArgs) {
		$sNameToCheck = 'GetDataFor' . ucfirst(self::SC_URL_PREFIX);
		/*
		 * если это вызов на получение данных каталогов
		 */
		if (strpos($sName, $sNameToCheck) === 0) {
			/*
			 * получить ид схемы, удалив префикс
			 */
			$mSchemeId = str_replace($sNameToCheck, '', $sName);
			/*
			 * страница
			 */
			if (!$iPage = (int) reset($aArgs)) {
				return array();
			}
			/*
			 * если это список всех схем
			 */
			if ($mSchemeId == self::SC_SCHEMES_LIST_ALL_URL) {
				return $this->GetSchemesListAll($iPage);
			}
			return $this->GetSchemeProductsBySchemeIdAndPage($mSchemeId, $iPage);
		}
		/*
		 * вызов методов движка
		 */
		return parent::__call($sName, $aArgs);
	}

}

?>