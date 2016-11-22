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

class PluginSimplecatalog_ActionProductsearch extends ActionPlugin {

	/*
	 * Для постраничности значения по-умолчанию (в т.ч. для аякс автокомплитера)
	 */
	protected $iCurrentPage = 1;
	protected $iPerPage = 20;

	/*
	 * Флаг полного поиска или только по первой букве (алфавитный поиск)
	 */
	protected $bFullSearch = true;

	/*
	 * Для меню
	 */
	public $sMenuHeadItemSelect = null;
	

	public function Init() {
		
	}


	protected function RegisterEvent() {
		$this->AddEventPreg('#^product$#', 'EventProductSearch');
		$this->AddEventPreg('#^letter$#', 'EventAlphabeticalSearch');
		$this->AddEventPreg('#^ajax-title-search$#', 'EventAjaxTitleSearch');
	}


	/**
	 * Выполнить обычный поиск
	 *
	 * @return bool
	 */
	public function EventProductSearch() {
		return $this->FindProducts('q');
	}


	/**
	 * Выполнить алфавитный поиск
	 *
	 * @return bool
	 */
	public function EventAlphabeticalSearch() {
		$this->bFullSearch = false;
		return $this->FindProducts('letter');
	}


	/**
	 * Выполнить поиск
	 *
	 * @param string $sFilterKeyName	имя ключа фильтра в котором получать данные
	 * @return bool
	 */
	protected function FindProducts($sFilterKeyName) {
		/*
		 * поиск только по указанной схеме
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$mRawQuery = $this->GetDataFromFilter($sFilterKeyName);
		if (!is_string($mRawQuery)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Wrong_Search_Query'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->PreparePagingByScheme($oScheme);
		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));

		/*
		 * получить продукты
		 */
		$aResult = $this->PluginSimplecatalog_Search_MyGetProductsBySchemeAndSearchQuery(
			$oScheme,
			(string) $mRawQuery,
			$this->iCurrentPage,
			$this->iPerPage,
			$this->bFullSearch,
			$aSortOrder
		);
		if ($aResult['is_error']) {
			$this->Message_AddError($aResult['msg'], $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$_REQUEST['filter'][$sFilterKeyName] = $aResult['q'];

		/*
		 * добавить пагинацию
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aResult['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			Router::GetPath('product-search') . $this->sCurrentEvent . '/' . $this->GetParam(0),
			array('filter' => $this->GetDataFromFilter())
		);

		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aResult['collection']);
		$this->Viewer_Assign('iTotalProductCount', $aResult['count']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));

		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Аякс автокомплитер для поиска
	 *
	 * @return bool
	 */
	public function EventAjaxTitleSearch() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * поиск только по указанной схеме
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
			return false;
		}

		if (!is_string(getRequest('value'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Wrong_Search_Query'), $this->Lang_Get('error'));
			return false;
		}

		$this->PreparePagingByScheme($oScheme);

		/*
		 * получить продукты
		 */
		$aResult = $this->PluginSimplecatalog_Search_MyGetProductsBySchemeAndSearchQuery(
			$oScheme,
			getRequest('value'),
			$this->iCurrentPage,
			$this->iPerPage,
			$this->bFullSearch,
			$this->PluginSimplecatalog_Product_GetDefaultProductSortingOrder()
		);
		if ($aResult['is_error']) {
			$this->Message_AddError($aResult['msg'], $this->Lang_Get('error'));
			return false;
		}

		/*
		 * получить заголовки и изображения продуктов
		 */
		$aItems = array();
		foreach($aResult['collection'] as $oProduct) {
			$aItems[] = array(
				'value' => $oProduct->getFirstFieldTitle(),
				'image' => $oProduct->getFirstImageOrDefaultPlaceholderPath(),
			);
		}

		$this->Viewer_AssignAjax('aItems', $aItems);
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Выполнить редирект на реферер
	 */
	private function RedirectToReferer() {
		$this->PluginSimplecatalog_Tools_RedirectToReferer();
	}
	

	/**
	 * Установить страницу и количество элементов на странице на основе данных схемы
	 *
	 * @param $oScheme				объект схемы (для количества элементов на странице)
	 */
	public function PreparePagingByScheme($oScheme) {
		if (!$this->iCurrentPage = intval(preg_replace('#^page(\d+)$#', '\1', $this->getParam(1)))) {
			$this->iCurrentPage = 1;
		}
		$this->iPerPage = $oScheme->getItemsPerPageDefinedByUserOrDefault();
	}


	/**
	 * Получить значение из фильтра (массива-переменной "filter" из реквеста) или весь фильтр
	 *
	 * @param string $sName			имя ключа из массива фильтра или null для получения всего фильтра
	 * @return mixed|array|null		значение
	 */
	protected function GetDataFromFilter($sName = null) {
		return $this->PluginSimplecatalog_Tools_GetDataFromFilter($sName);
	}


	public function EventShutdown() {
		$this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
	}

}
