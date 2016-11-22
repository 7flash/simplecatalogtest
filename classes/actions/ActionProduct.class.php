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

class PluginSimplecatalog_ActionProduct extends ActionPlugin {

	/*
	 * Сущность текущего пользователя
	 */
	protected $oUserCurrent = null;
	/*
	 * Для меню
	 */
	public $sMenuHeadItemSelect = null;
	public $sMenuItemSelect = null;
	public $sMenuSubItemSelect = null;
	/*
	 * Для постраничности
	 */
	protected $iCurrentPage = 1;
	protected $iPerPage = 20;
	/*
	 * Список параметров экшена без номера страницы (нужен для списка продуктов категории), заполняется из PreparePagingByScheme
	 */
	protected $aParamsWOPage = array();

	
	public function Init() {
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->SetDefaultEvent('index');
		if (!SCRootStorage::IsInit()) {
			return Router::Action('error');
		}
	}

	
	protected function RegisterEvent() {
		/*
		 * только для админов
		 */
		$this->AddEventPreg('#^index$#', 'EventIndex');
		
		/*
		 * общие эвенты
		 */
		$this->AddEventPreg('#^add$#', 'EventAdd');
		$this->AddEventPreg('#^edit$#', '#^\d+$#', 'EventEdit');
		$this->AddEventPreg('#^edit$#', '#^ajax-clean-file-field$#', 'EventAjaxCleanFileField');
		$this->AddEventPreg('#^delete$#', 'EventDelete');
		
		/*
		 * пользовательские эвенты
		 */
		$this->AddEventPreg('#^items$#', 'EventItems');
		$this->AddEventPreg('#^item$#', 'EventItem');
		$this->AddEventPreg('#^my$#', 'EventMyProducts');
		$this->AddEventPreg('#^drafts$#', 'EventDrafts');
		$this->AddEventPreg('#^map$#', 'EventMapItems');
		$this->AddEventPreg('#^ajax-map-items-loader$#', 'EventAjaxMapItemsLoader');
		
		/*
		 * модерация
		 */
		$this->AddEventPreg('#^moderation$#', '#^approve$#', 'EventModerationApprove');
		$this->AddEventPreg('#^moderation$#', '#^disapprove$#', 'EventModerationDisapprove');
		$this->AddEventPreg('#^moderation$#', 'EventModeration');

		/*
		 * категории
		 */
		$this->AddEventPreg('#^category$#', 'EventCategoryProducts');

		/*
		 * сравнение продуктов
		 */
		$this->AddEventPreg('#^compare$#', '#^add$#', 'EventCompareProductAdd');
		$this->AddEventPreg('#^compare$#', '#^delete$#', 'EventCompareProductDelete');
		$this->AddEventPreg('#^compare$#', 'EventCompareProducts');

		/*
		 * фильтр продуктов
		 */
		$this->AddEventPreg('#^filter$#', 'EventProductFilter');

		/*
		 * доступ к файлам
		 */
		$this->AddEventPreg('#^file$#', '#^get$#', 'EventDownloadFile');

		/*
		 * вывод карточки продукта (через ембед код)
		 */
		$this->AddEventPreg('#^api$#', '#^embed$#', 'EventShowEmbedProduct');
	}
	

	/**
	 * Специальный детализированный вид продуктов схемы (только для админов)
	 *
	 * @return string
	 */
	public function EventIndex() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}

		/*
		 * если нужно показать данные
		 */
		if ($this->GetParam(0)) {
			$this->GetLastProductsForAdminIndex();
		} else {
			/*
			 * показать страницу информации
			 */
			$this->SetTemplateAction('index_info');
			$this->sMenuSubItemSelect = 'index_info';
		}
		$this->sMenuItemSelect = 'index';
		/*
		 * добавить список схем для субменю
		 */
		$this->Viewer_Assign('aSchemesMenuItems', $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems());
		$this->Viewer_AddHtmlTitle($this->Lang('Title'));
	}


	/**
	 * Получить последние продукты чтобы отобразить на странице index для админов
	 *
	 * @return bool
	 */
	protected function GetLastProductsForAdminIndex() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuSubItemSelect = $oScheme->getSchemeUrl();

		$this->PreparePagingByScheme($oScheme);

		/*
		 * получить список продуктов и общее количество
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
				PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED,
				PluginSimplecatalog_ModuleProduct::MODERATION_DRAFT,
				PluginSimplecatalog_ModuleProduct::MODERATION_DEFERRED,
			),
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$this->PluginSimplecatalog_Product_GetDefaultProductSortingOrder()
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogItemsAdminIndexWebPath()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
	}


	/**
	 * Главная страница каталога схемы (доступ для всех)
	 */
	public function EventItems() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * что нужно показывать на главной странице каталога
		 */
		if ($oScheme->getNeedToShowLastProductsOnProductItemsPage()) {
			/*
			 * получить последние продукты схемы
			 */
			$this->GetLastProductsByScheme($oScheme);

		} elseif ($oScheme->getNeedToShowCategoriesOnProductItemsPage()) {
			/*
			 * показать категории схемы
			 */
			$this->GetCategoriesTreeByScheme($oScheme);
			$this->SetTemplateAction('items_categories');
		} elseif ($oScheme->getNeedToShowMapOnProductItemsPage()) {
			/*
			 * показать карту меток схемы
			 */
			$this->GetMapItemsCountByScheme($oScheme);
			$this->SetTemplateAction('items_mapitems');
		}

		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$this->sMenuItemSelect = 'index';

		$this->Viewer_Assign('oScheme', $oScheme);

		/*
		 * добавить seo данные
		 */
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
		$this->Viewer_SetHtmlDescription(func_text_words(strip_tags($oScheme->getDescription()), Config::Get('plugin.simplecatalog.product.seo.description_words_count')));
		$this->Viewer_SetHtmlKeywords($oScheme->getKeywords());
	}


	/**
	 * Получить последние продукты схемы
	 *
	 * @param $oScheme		объект схемы
	 */
	protected function GetLastProductsByScheme($oScheme) {
		$this->PreparePagingByScheme($oScheme);

		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));

		/*
		 * получить список продуктов и общее количество
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
			),
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogItemsWebPath(),
			$this->GetDataFromFilter() ? array('filter' => $this->GetDataFromFilter()) : array()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
	}


	/**
	 * Получить дерево категорий схемы
	 *
	 * @param $oScheme		объект схемы
	 */
	protected function GetCategoriesTreeByScheme($oScheme) {
		$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));
	}


	/**
	 * Получить количество меток на карте схемы
	 *
	 * @param $oScheme		объект схемы
	 */
	protected function GetMapItemsCountByScheme($oScheme) {
		$this->Viewer_Assign('iTotalMapItemsCount', $this->PluginSimplecatalog_Maps_MyGetSchemeMapItemsCountByScheme($oScheme));
	}


	/**
	 * Показать страницу продукта (доступ для всех)
	 *
	 * @return bool
	 */
	public function EventItem() {
		/*
		 * получить продукт из урла
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductByProductUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * если продукт не промодерирован - разрешить просмотр для: автора продукта, админов или модераторов схемы
		 */
		if (!$oProduct->getModerationDone() and (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct))) {
			$this->Message_AddError($this->Lang('Errors.Access_Denied'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * активна ли схема
		 */
		$oScheme = $oProduct->getScheme();
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		
		/*
		 * получить комментарии продукта
		 */
		if ($oProduct->getCommentsEnabled()) {
			$this->PluginSimplecatalog_Product_LoadProductComments($oProduct);
		}

		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('oProduct', $oProduct);
		/*
		 * если продукт на модерации - добавить меню для модераторов (права уже проверены выше)
		 */
		if ($oProduct->getModerationNeeded()) {
			$this->Viewer_Assign('bShowModeratorControls', true);
		}
		/*
		 * добавить seo данные
		 */
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
		$this->Viewer_AddHtmlTitle($oProduct->getSeoTitle());
		$this->Viewer_SetHtmlDescription(func_text_words($oProduct->getSeoDescription(), Config::Get('plugin.simplecatalog.product.seo.description_words_count')));
		$this->Viewer_SetHtmlKeywords($oProduct->getSeoKeywords());
		$this->Viewer_SetHtmlCanonical($oProduct->getItemShowWebPath());
		$this->Hook_Run('sc_product_item_view', array('oProduct' => $oProduct, 'oScheme' => $oScheme));
	}


	/**
	 * Список продуктов, созданных пользователем
	 *
	 * @return bool|string
	 */
	public function EventMyProducts() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$this->sMenuItemSelect = 'my';

		$this->PreparePagingByScheme($oScheme);
		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));
		
		/*
		 * проверить права пользователя на создание продуктов схемы
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanAddNewProductsInScheme($oScheme)) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}
		
		/*
		 * получить продукты этого пользователя
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
				PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED,
				PluginSimplecatalog_ModuleProduct::MODERATION_DEFERRED,
			),
			$this->oUserCurrent,
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogMyItemsWebPath(),
			$this->GetDataFromFilter() ? array('filter' => $this->GetDataFromFilter()) : array()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
		$this->Viewer_Assign('iMyProducts', $aProductsData['count']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->SetTemplateAction('items');
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Список черновиков, созданных пользователем
	 *
	 * @return bool|string
	 */
	public function EventDrafts() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$this->sMenuItemSelect = 'drafts';

		$this->PreparePagingByScheme($oScheme);
		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));

		/*
		 * проверить права пользователя на создание продуктов схемы и разрешение на использование черновиков
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanAddNewProductsInScheme($oScheme) or !$oScheme->getAllowDraftsEnabled()) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}

		/*
		 * получить продукты этого пользователя
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DRAFT
			),
			$this->oUserCurrent,
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogDraftsItemsWebPath(),
			$this->GetDataFromFilter() ? array('filter' => $this->GetDataFromFilter()) : array()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
		$this->Viewer_Assign('iDraftsProducts', $aProductsData['count']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->SetTemplateAction('items');
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Список продуктов на модерации
	 *
	 * @return bool|string
	 */
	public function EventModeration() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$this->sMenuItemSelect = 'moderation';

		$this->PreparePagingByScheme($oScheme);
		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));
		
		/*
		 * проверить права на модерацию
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}

		/*
		 * получить продукты, которые нуждаются в модерации и общее количество
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED,
			),
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogModerationNeededItemsWebPath(),
			$this->GetDataFromFilter() ? array('filter' => $this->GetDataFromFilter()) : array()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('iModerationNeededProducts', $aProductsData['count']);
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
		$this->Viewer_Assign('bShowModeratorControls', true);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->SetTemplateAction('items');
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Ссылка подтвеждения прохождения модерации продукта
	 *
	 * @return bool|string
	 */
	public function EventModerationApprove() {
		$this->Security_ValidateSendForm();
		/*
		 * получить ид продукта из урла
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * проверить активна ли схема
		 */
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить права пользователя на модерацию
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}
		/*
		 * подтвердить модерацию
		 */
		$oProduct->setModerationDone();
		$oProduct->Save();
		
		$this->Message_AddNotice('Ok', '', true);
		Router::Location($oScheme->getCatalogModerationNeededItemsWebPath());
	}


	/**
	 * Ссылка НЕ прохождения модерации продукта (полного удаления)
	 *
	 * @return bool|string
	 */
	public function EventModerationDisapprove() {
		$this->Security_ValidateSendForm();
		/*
		 * получить ид продукта из урла
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * проверить активна ли схема
		 */
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить права пользователя на модерацию
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)) {
			$this->ShowError($this->Lang('Errors.Access_Denied'));
			return Router::Action('error');
		}
		/*
		 * удалить продукт
		 */
		$oProduct->Delete();
		
		$this->Message_AddNotice('Ok', '', true);
		Router::Location($oScheme->getCatalogModerationNeededItemsWebPath());
	}


	/**
	 * Добавление нового продукта
	 *
	 * @return bool
	 */
	public function EventAdd() {
		/*
		 * получить схему из урла
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить авторизацию пользователя
		 */
		if (!$this->oUserCurrent) {
			$this->Message_AddError($this->Lang('Errors.Access_Denied'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		if ($this->oUserCurrent->getUserType() != 'provider') {
			$this->Message_AddError('Добавлять продукты в наш каталог могут только поставщики', $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_AddHtmlTitle($this->Lang('Products.Add.titles.new', array('scheme' => $oScheme->getSchemeName())));

		/*
		 * получить категории
		 */
		$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));

		/*
		 * получить данные по настройкам связей схемы
		 */
		$this->PluginSimplecatalog_Product_AssignProductDataByLinksSettings($oScheme, $this->oUserCurrent);
		
		/*
		 * если была отправлена форма
		 */
		if (isPost('submit_add') or isPost('submit_save_draft')) {
			$this->Security_ValidateSendForm();
			return $this->SubmitAdd($oScheme);
		}
	}


	/**
	 * Обработчик добавления нового продукта
	 * tip: может вызываться программно сторонними плагинами, необходимо заполнить oUserCurrent перед вызовом для установки автора
	 *
	 * @param $oScheme		объект схемы
	 * @return bool
	 * @throws Exception
	 */
	private function SubmitAdd($oScheme) {

		/*
		 * валидные поля продукта для сохранения (две очереди)
		 */
		$aProductFields = array(
			PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_FIRST_QUEUE => array(),
			PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_SECOND_QUEUE => array()
		);
		/*
		 * сохранить оригинальные значения полей для возврата правок (например, для файлов)
		 */
		$aValuesOriginal = array(
			PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_FIRST_QUEUE => array(),
			PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_SECOND_QUEUE => array()
		);
		
		$aFields = $oScheme->getFields();
		$aProductRawData = getRequest('product_data');
		/*
		 * если на форме есть хотя бы одно файловое поле
		 * array_diverse - для легкого доступа ко всем полям по имени
		 */
		$aFilesRawData = isset($_FILES['product_data']) ? array_diverse($_FILES['product_data']) : array();

		/*
		 *
		 * есть ли такой продукт
		 *
		 */
		if ($iProductIdRaw = (int) getRequest('id')) {
			if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById($iProductIdRaw)) {
				return $this->ShowError($this->Lang('Errors.Product_Not_Found'));
			}
			/*
			 * проверить права пользователя для редактирования существуюшего продукта
			 */
			if (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct)) {
				return $this->ShowError($this->Lang('Errors.You_Cant_Edit_This_Product'));
			}
			
		} else {
			$oProduct = Engine::GetEntity('PluginSimplecatalog_Product');
			/*
			 * проверить права пользователя для создания нового продукта
			 */
			if (!$this->oUserCurrent or !$this->oUserCurrent->getCanAddNewProductsInScheme($oScheme)) {
				return $this->ShowError($this->Lang('Errors.Access_Denied'));
			}
		}

		/*
		 * получить данные по настройкам связей схемы только если продукт не новый (редактировался)
		 * tip: данный метод при добавлении продукта вызывается дважды: первый раз в EventAdd для текущего пользователя,
		 * 		второй - здесь, для автора продукта на случай если продукт не пройдет валидацию
		 */
		if (!$oProduct->_isNew()) {
			$this->PluginSimplecatalog_Product_AssignProductDataByLinksSettings($oScheme, $oProduct->getUser(), $oProduct);
		}

		/*
		 *
		 * пройтись по всем полям схемы для получения значений полей продукта
		 *
		 */
		foreach($aFields as $oField) {

			/*
			 *
			 * получить значение, очередь и текст ли это (скалярное значение) для поля по сырым входным данным
			 *
			 */
			$aValueInfo = $this->PluginSimplecatalog_Product_GetFieldValueInfoFromRawData($oField, $aProductRawData, $aFilesRawData);
			$mValue = $mValueSource = $aValueInfo['value'];
			$sQueue = $aValueInfo['queue'];
			$bCanBeParsedAsText = $aValueInfo['can_be_parsed_as_text'];
			/*
			 * если это поле не редактируемое через форму
			 */
			if (!$oField->getProductFieldDataAreAvailableOnForm()) {
				/*
				 * если продукт новый
				 */
				if ($oProduct->_isNew()) {
					/*
					 * заполнить значением по-умолчанию
					 */
					$mValue = $mValueSource = $oField->getDefaultValue();
				} else {
					/*
					 * для существующих продуктов не обновлять нередактируемые поля (в них могут быть данные, обновляемые другими плагинами)
					 */
					continue;
				}
			}
			/*
			 * если значение не было получено
			 */
			if (is_null($mValue)) {
				return $this->ShowError($this->Lang('Errors.Wrong_Product_Request_Data', array('field' => $oField->getTitle())));
			}
			
			/*
			 *
			 * можно ли это значение парсить как текст
			 *
			 */
			if ($bCanBeParsedAsText) {
				$mValue = trim($mValue);
				/*
				 * если нужно парсить значение через Jevix
				 */
				if ($oField->getRunParserEnabled()) {
					$mValue = $this->Text_Parser($mValue);
				} elseif (Config::Get('plugin.simplecatalog.product.add.strip_tags_when_default_parser_disabled')) {
					$mValue = strip_tags($mValue, '<br>');
				}
				/*
				 * если нужно проверить значение кастомным валидатором
				 */
				if (($mCustomValidatorCheck = $this->PluginSimplecatalog_Product_CheckValueByCustomFieldValidator($oField, $mValue)) !== true) {
					return $this->ShowError($mCustomValidatorCheck);
				}
			}
			
			/*
			 *
			 * проверить значение согласно внутренним правилам поля схемы
			 *
			 */
			if (!$this->PluginSimplecatalog_Product_CheckValueOfProductFieldBySchemeField($mValue, $oField)) {
				return $this->ShowError($this->Lang('Errors.Field_Not_Correct', array('field' => $oField->getTitle())));
			}
			
			/*
			 *
			 * есть ли такое поле продукта
			 *
			 */
			if ($oProduct->getId()) {
				/*
				 * tip: права на продукт уже проверены
				 */
				if (!$oEnt = $this->PluginSimplecatalog_Product_MyGetFieldsByProductIdAndFieldId($oProduct->getId(), $oField->getId())) {
					//return $this->ShowError($this->Lang('Errors.Product_Field_Not_Found'));
					/*
					 * это поле схемы было добавлено в схему после того как был впервые создан этот продукт
					 * tip: это не произойдет т.к. теперь при создании нового поля в схеме, оно автоматически добавляется ко всем продуктам схемы со значением по-умолчанию
					 */
					$oEnt = Engine::GetEntity('PluginSimplecatalog_Product_Fields');
				}
			} else {
				$oEnt = Engine::GetEntity('PluginSimplecatalog_Product_Fields');
			}
			
			$oEnt->setFieldId($oField->getId());
			/*
			 * установить сущность поля схемы чтобы setContent ниже не делал ещё один запрос за ней
			 */
			$oEnt->setField($oField);
			/*
			 * сохранить старое значение для отмены изменений (например, для файлов)
			 */
			$sContentOriginal = $oEnt->getContent();
			$oEnt->setContent($mValue);
			if ($bCanBeParsedAsText) {
				$oEnt->setContentSource($mValueSource);
			}
			
			if (!$oEnt->_Validate()) {
				return $this->ShowError($oEnt->_getValidateError());
			}

			/*
			 * добавить поле в очередь для будущего сохранения
			 */
			$aProductFields[$sQueue][] = $oEnt;
			/*
			 * сохранить оригинальное старое значение (используется для файлов, но создано универсальным)
			 */
			$aValuesOriginal[$sQueue][] = $sContentOriginal;
		}	// /конец цикла по полям

		/*
		 *
		 * добавить запись продукта ПЕРЕД сохранением его полей (проверка внешних ключей)
		 *
		 */
		$oProduct->setSchemeId($oScheme->getId());
		$oProduct->setScheme($oScheme);
		/*
		 * модерация
		 */
		$oProduct->setModeration($this->PluginSimplecatalog_Product_MyGetModerationTypeByParameters(
			$aProductFields,
			$aFields,
			$this->oUserCurrent,
			$oScheme,
			getRequest('submit_save_draft'),
			getRequest('save_deferred')
		));
		/*
		 * ЧПУ
		 */
		$oProduct->setProductUrl($this->PluginSimplecatalog_Product_MyGetValidAndFreeProductUrl(
			/*
			 * урл, созданный пользователем
			 */
			getRequest('product_url'),
			/*
			 * или для получения его на основе данных полей
			 */
			$aProductFields[PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_FIRST_QUEUE],
			/*
			 * или из продукта
			 */
			$oProduct
		));
		/*
		 * количество заполненных полей
		 */
		$oProduct->setFieldsFilledCount($this->PluginSimplecatalog_Product_MyGetFilledFieldsCount($aProductFields, $aValuesOriginal));
		/*
		 * установить разрешение комментирования ("user_allow_comments") на основе управляющего правила комментирования схемы
		 */
		$oProduct->setUserAllowCommentsAccordingSchemeOptions(getRequest('user_allow_comments'));


		$sEditDate = date('Y-m-d H:i:s');
		/*
		 * если это отложенная публикация - получить заданную дату
		 */
		if ($oProduct->getModerationDeferred()) {
			$sDateDeferred = getRequest('deferred_date') . ' ' . getRequest('deferred_hour') . ':' . getRequest('deferred_minute') . ':0';
			if ($this->Validate_Validate('date', $sDateDeferred, array('format' => array('yyyy-MM-dd h:m:s'), 'allowEmpty' => false))) {
				$sEditDate = $sDateDeferred;
			}
		}
		/*
		 * всегда устанавливать пользователя, который последним редактировал продукт и дату последнего редактирования
		 */
		$oProduct->setUserIdEditLast($this->oUserCurrent->getId());
		$oProduct->setEditDate($sEditDate);
		/*
		 * если новый продукт (или отложенный) - поставить дату и автора (обновить)
		 */
		if ($oProduct->_isNew() or $oProduct->getModerationDeferred()) {
			$oProduct->setAddDate($oProduct->getEditDate());
			$oProduct->setUserId($oProduct->getUserIdEditLast());
			$oProduct->setCity($this->oUserCurrent->getProviderCity());


		}


		/*
		 * если включен магазин - установить цены
		 */
		if ($oScheme->getShopEnabled()) {
			$oProduct->setPrice(getRequest('price'));
			$oProduct->setPriceNew(getRequest('price_new'));
		}


		/*
		 * валидация внесенных основных данных для продукта
		 */
		if (!$oProduct->_Validate()) {
			return $this->ShowError($oProduct->_getValidateError());
		}


		/*
		 * получение массива ид категорий и их проверка на существование и корректность согласно правилам (на наличие дочерних категорий и т.п.)
		 */
		if ($aCategoriesIds = (array) getRequest('categories_ids') and ($mCategoriesValidation = $this->PluginSimplecatalog_Product_CheckCategoriesIdsAreCorrect($aCategoriesIds)) !== true) {
			return $this->ShowError($mCategoriesValidation);
		}


		/*
		 * вызвать хук, который может валидировать внешние данные и контроллировать возврат формы
		 */
		$mHookSuccess = true;
		$this->Hook_Run('sc_product_add_before_save', array('oProduct' => $oProduct, 'oScheme' => $oScheme, 'mSuccess' => &$mHookSuccess));

		if ($mHookSuccess !== true) {
			return $this->ShowError($mHookSuccess);
		}


		/*
		 * если продукт редактировался - обновить комментарии в зависимости от статуса модерации
		 */
		if (!$oProduct->_isNew()) {
			/*
			 * удалить комментарий из прямого эфира если не промодерирован
			 */
			if (!$oProduct->getModerationDone()) {
				$this->Comment_DeleteCommentOnlineByTargetId($oProduct->getId(), $this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($oScheme->getSchemeUrl()));
			}
			/*
			 * обновить видимость комментариев (опубликованность)
			 */
			$this->Comment_SetCommentsPublish($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT, $oProduct->getModerationDone() ? 1 : 0);
		}


		/*
		 * теперь в $oProduct уже есть новый id (орм устанавливает для примари ключей с автоинкрементом)
		 */
		if (!$oProduct->Save()) {
			return $this->ShowError($this->Lang('Errors.system_error_when_save_product'));
		}
		/*
		 * чтобы при повторном сохранении сущности продукта (например, при установке СЕО полей) был апдейт вместо инсерта
		 * tip: можно вместо этой строки просто получить продукт по ид, но такой подход экономит запрос и быстрее
		 */
		$oProduct->_SetIsNew(false);


		/*
		 *
		 * сохранить все поля продукта
		 *
		 */
		foreach($aProductFields[PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_FIRST_QUEUE] as $oProductField) {
			$oProductField->setProductId($oProduct->getId());
			$oProductField->Save();
		}
		
		foreach($aProductFields[PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_SECOND_QUEUE] as $iKey => $oProductField) {
			$mValue = $sValueOld = $aValuesOriginal[PluginSimplecatalog_ModuleProduct::PRODUCT_FIELDS_SAVE_SECOND_QUEUE][$iKey];
			/*
			 * защита от нулл значения (если до этого не было значения у поля)
			 */
			$mValue = $mValue ? $mValue : '';
			/*
			 * если был выбран файл
			 */
			if ($this->PluginSimplecatalog_File_IsFileUploaded($oProductField->getContent())) {
				/*
				 * загрузить файл
				 */
				if (!$mValue = $this->PluginSimplecatalog_File_UploadContentFile($oProductField->getContent())) {
					return $this->ShowError($this->Lang('Errors.System_Error_With_File_Upload', array('title' => $oProductField->getField()->getTitle())));
				}
				/*
				 * удалить старый файл и все связанные данные (доступа и количества загрузок)
				 */
				if ($sValueOld) {
					$this->PluginSimplecatalog_Product_DeleteFileAndAdditionalDataByPathAndProductField($sValueOld, $oProductField);
				}
			}
			$oProductField->setProductId($oProduct->getId());
			/*
			 * сохранить значение в любом случае
			 */
			$oProductField->setContent($mValue);
			/*
			 * заполнить оригинальное значение текущим т.к. в цикле по полям оно заполняется только для текстовых значений
			 */
			$oProductField->setContentSource($mValue);
			$oProductField->Save();
		}

		/*
		 * установка категорий (проверка выполнена выше)
		 */
		$this->PluginSimplecatalog_Product_AddCategoriesToProduct($oProduct, $aCategoriesIds);

		/*
		 * создать связи
		 */
		$this->PluginSimplecatalog_Product_AddLinksToProductByRawPostData($oScheme, $oProduct, getRequest('product_links'));

		/*
		 * заполнить сео поля
		 */
		$this->PluginSimplecatalog_Product_SetSEODataForProduct($oProduct, getRequest('seo_title'), getRequest('seo_description'), getRequest('seo_keywords'));

		/*
		 * сохранение меток карты
		 */
		if ($oScheme->getMapItemsEnabled()) {
			$this->PluginSimplecatalog_Maps_AddMapItemsToProductByRawPostData($oScheme, $oProduct, getRequest('product_map_items'));
		}

		/*
		 * добавить автора продукта в подписчики на новые комментарии
		 */
		$this->Subscribe_AddSubscribeSimple(PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT . '_new_comment', $oProduct->getId(),$this->oUserCurrent->getMail());

		$this->Hook_Run('sc_product_add_after', array('oProduct' => $oProduct, 'oScheme' => $oScheme));

		/*
		 * если нужна загрузка фото к продукту - вернуться на страницу редактирования продукта
		 */
		if (isPost('upload_photo')) {
			$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.products.edit.product_saved_and_adding_photos_can_be_processed'), '', true);
			Router::Location($oProduct->getItemEditWebPath());
			return true;
		}
		
		/*
		 * показать сообщение основанное на типе модерации продукта
		 */
		$this->Message_AddNotice($this->Lang('notices.products.moderation.' . $oProduct->getModeration()), '', true);
		Router::Location($oProduct->getItemShowWebPath());
		return true;
	}


	/**
	 * Редактирование продукта
	 *
	 * @return bool
	 */
	public function EventEdit() {
		/*
		 * получить продукт из урла
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * активна ли схема
		 */
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить права пользователя для существующих продуктов
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct)) {
			$this->Message_AddError($this->Lang('Errors.You_Cant_Edit_This_Product'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * для меню
		 */
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_AddHtmlTitle($this->Lang('Products.Add.titles.edit', array('scheme' => $oScheme->getSchemeName())));
		/*
		 * дерево категорий
		 */
		$this->Viewer_Assign('aCategoryTree', $this->PluginSimplecatalog_Category_MyBuildCategoryTreeForScheme($oScheme));

		/*
		 * общие поля продукта
		 */
		$_REQUEST['id'] = $oProduct->getId();
		$_REQUEST['product_url'] = $oProduct->getProductUrl();
		$_REQUEST['user_allow_comments'] = $oProduct->getUserAllowComments();
		$_REQUEST['categories_ids'] = $oProduct->getCategoriesIds();
		$_REQUEST['item_show_web_path'] = $oProduct->getItemShowWebPath();
		/*
		 * для отображения иконок
		 */
		$_REQUEST['moderation_needed'] = $oProduct->getModerationNeeded();
		$_REQUEST['moderation_saved_as_draft'] = $oProduct->getModerationDraft();
		$_REQUEST['moderation_saved_as_deferred'] = $oProduct->getModerationDeferred();

		/*
		 * если включен магазин - получить цены
		 */
		if ($oScheme->getShopEnabled()) {
			$_REQUEST['price'] = $oProduct->getPrice();
			$_REQUEST['price_new'] = $oProduct->getPriceNew();
		}
		/*
		 * если включено заполнение сео данных
		 */
		if ($oScheme->getAllowEditAdditionalSeoMetaEnabled()) {
			$_REQUEST['seo_title'] = $oProduct->getSeoTitle();
			$_REQUEST['seo_description'] = $oProduct->getSeoDescription();
			$_REQUEST['seo_keywords'] = $oProduct->getSeoKeywords();
		}
		/*
		 * если пользователь может использовать отложенную публикацию и продукт отложенный
		 */
		if ($this->oUserCurrent->getUserCanDeferProductsBySchemeOrIsAdmin($oScheme) and $oProduct->getModerationDeferred()) {
			$_REQUEST['save_deferred'] = true;
			$iAddDate = strtotime($oProduct->getAddDate());
			$_REQUEST['deferred_date'] = date('Y-m-d', $iAddDate);
			$_REQUEST['deferred_hour'] = date('H', $iAddDate);
			$_REQUEST['deferred_minute'] = date('i', $iAddDate);
		}

		/*
		 * ручные поля продукта
		 *
		 * структура:
		 *		array('product_data' => array('field_id_in_scheme' => 'value_of_product'))
		 */
		foreach($oProduct->getProductFields() as $oProductField) {
			$_REQUEST['product_data'][$oProductField->getFieldId()] = $oProductField->getContentSource();
		}

		/*
		 * получить данные по настройкам связей схемы и продукты для выбора
		 */
		$this->PluginSimplecatalog_Product_AssignProductDataByLinksSettings($oScheme, $oProduct->getUser(), $oProduct);
		/*
		 * получить связи продукта
		 */
		$_REQUEST['product_links'] = $this->PluginSimplecatalog_Product_GetProductsLinksIdsGroupedForLinkSettingsByProduct($oProduct);

		/*
		 * получить метки на карте
		 */
		if ($oScheme->getMapItemsEnabled()) {
			$_REQUEST['product_map_items'] = $this->PluginSimplecatalog_Maps_GetProductMapItemsForEditing($oProduct);
		}

		$this->SetTemplateAction('add');
	}


	/**
	 * Удаление продукта
	 *
	 * @return bool
	 */
	public function EventDelete() {
		$this->Security_ValidateSendForm();
		/*
		 * получить продукт из урла
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * активна ли схема
		 */
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить права пользователя на существующий продукт
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct)) {
			$this->Message_AddError($this->Lang('Errors.You_Cant_Edit_This_Product'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		
		/*
		 * удалить продукт (все связанные данные будут удалены через автоматически вызываемый обработчик в сущности)
		 */
		$oProduct->Delete();

		$this->Message_AddNotice('Ok', '', true);
		Router::Location($oScheme->getCatalogItemsWebPath());
	}


	/**
	 * Показать продукты категории
	 *
	 * @return bool
	 */
	public function EventCategoryProducts() {
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->PreparePagingByScheme($oScheme);

		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));

		/*
		 * получить категорию
		 */
		$aParams = $this->aParamsWOPage;
		if (!$sCategoryUrl = array_pop($aParams) or !$oCategory = $this->PluginSimplecatalog_Category_MyGetCategoryByUrlAndScheme($sCategoryUrl, $oScheme)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		/*
		 * получить продукты и общее количество
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationAndSchemeAndPageAndPerPageAndSortOrderAndCategory(
			PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder,
			$oCategory
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oCategory->getCategoryUrl($oScheme),
			$this->GetDataFromFilter() ? array('filter' => $this->GetDataFromFilter()) : array()
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
		$this->Viewer_Assign('oCategory', $oCategory);

		/*
		 * добавить seo данные
		 */
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
		$this->Viewer_AddHtmlTitle($oCategory->getName());
		$this->Viewer_SetHtmlDescription(func_text_words($oCategory->getDescription(), Config::Get('plugin.simplecatalog.product.seo.description_words_count')));
		$this->Viewer_SetHtmlKeywords(implode(', ', array($oCategory->getName(), $oCategory->getUrl())));
	}


	/*
	 *
	 * --- Сравнение продуктов ---
	 *
	 */

	/**
	 * Добавление продукта к сравнению
	 *
	 * @return bool
	 */
	public function EventCompareProductAdd() {
		$this->Viewer_SetResponseAjax('json');

		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById((int) getRequest('id'))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'));
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * проверка количества продуктов в сравнении
		 */
		if ($this->PluginSimplecatalog_Product_GetCompareListProductsLimitExceedByScheme($oScheme)) {
			$this->Message_AddError($this->Lang('Errors.compare.to_much_products_in_comparing', array('max' => PluginSimplecatalog_ModuleProduct::COMPARE_PRODUCTS_MAX_COUNT)));
			return false;
		}

		if ($aData = $this->PluginSimplecatalog_Product_GetDataForComparingProductsInScheme($oProduct)) {
			/*
			 * есть два или больше продуктов для сравнения - нужно построить ссылку
			 */
			$this->Viewer_AssignAjax('aProductsIds', $aData);
			$this->Viewer_AssignAjax('sText', $this->Lang('Products.Items.Comparing.comparing_ready'));
		} else {
			/*
			 * это первое добавление продукта
			 */
			$this->Viewer_AssignAjax('bFirst', true);
			$this->Viewer_AssignAjax('sText', $this->Lang('Products.Items.Comparing.added_first'));
		}
	}


	/**
	 * Страница сравнения продуктов
	 *
	 * @return bool
	 */
	public function EventCompareProducts() {
		/*
		 * получить список id продуктов
		 */
		$aProductIdsRaw = $this->GetParams();
		/*
		 * id схемы первого продукта в урле, по которому будут проверяться одинаковые ли id схем остальных продуктов
		 * чтобы исключить возможность ручной подстановки в урл id продуктов из других схем
		 */
		$iSchemeId = null;
		/*
		 * получить сущность каждого продукта
		 */
		$aProducts = array();
		foreach($aProductIdsRaw as $iId) {
			/*
			 * фикс возможных некорректных данных из урла
			 */
			$iId = (int) $iId;
			/*
			 * проверка количества продуктов в сравнении
			 */
			if (count($aProducts) >= PluginSimplecatalog_ModuleProduct::COMPARE_PRODUCTS_MAX_COUNT) {
				$this->Message_AddError(
					$this->Lang('Errors.compare.to_much_products_in_comparing', array('max' => PluginSimplecatalog_ModuleProduct::COMPARE_PRODUCTS_MAX_COUNT)),
					$this->Lang_Get('error')
				);
				/*
				 * при превышении количества сравниваемых продуктов не проверять другие из запроса
				 */
				break;
			}
			/*
			 * есть ли такой промодерированный продукт у активной схемы
			 */
			if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById($iId)) {
				$this->Message_AddError($this->Lang('Errors.compare.product_not_found', array('id' => $iId)), $this->Lang_Get('error'));
				/*
				 * продолжить в случае отсутствия продукта с таким id
				 */
				continue;
			}
			/*
			 * сравнение id схем продуктов (все продукты должны иметь одну и ту же схему)
			 */
			if (!$iSchemeId) {
				/*
				 * id схемы первого продукта будет сравниваться с другими
				 */
				$iSchemeId = $oProduct->getSchemeId();
			} else {
				if ($iSchemeId != $oProduct->getSchemeId()) {
					$this->Message_AddError(
						$this->Lang('Errors.compare.product_has_different_scheme', array('title' => $oProduct->getFirstFieldTitle(), 'id' => $iId)),
						$this->Lang_Get('error')
					);
					/*
					 * продолжить в случае несовпадения схемы
					 */
					continue;
				}
			}
			/*
			 * добавление в список продуктов для сравнения
			 */
			$aProducts[] = $oProduct;
		}
		/*
		 * если после проверки и получения продуктов нету ни одного (нужен хотя бы один)
		 */
		if (count($aProducts) < 1) {
			$this->Message_AddError($this->Lang('Errors.compare.no_products_are_found'), $this->Lang_Get('error'));
			return false;
		}
		$oScheme = reset($aProducts)->getScheme();
		/*
		 * если использовали ссылку сравнения, а в сессии нет продуктов для сравнения - добавить их чтобы можно было удалять продукты из списка сравнения
		 */
		if (!$this->PluginSimplecatalog_Product_GetCompareProductsIdsForScheme($oScheme)) {
			foreach($aProducts as $oProduct) {
				$this->PluginSimplecatalog_Product_AddProductToCompareProductsList($oProduct, $oScheme);
			}
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->Viewer_Assign('aProducts', $aProducts);
		$this->Viewer_Assign('aComparedProductFields', $this->PluginSimplecatalog_Product_GetBoolArrayWithComparedProductFields($aProducts));
		$this->Viewer_Assign('oScheme', $oScheme);

		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Удаление продукта из списка сравнения
	 *
	 * @return bool
	 */
	public function EventCompareProductDelete() {
		$this->Security_ValidateSendForm();

		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById((int) $this->GetParam(1))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * удалить продукт из списка
		 */
		if ($oProduct->getProductInCompareList()) {
			$this->PluginSimplecatalog_Product_RemoveProductFromCompareProductsList($oProduct);
		}
		$this->Message_AddNotice('Ok', '', true);
		/*
		 * если больше нет продуктов для сравнения - сделать редирект на страницу каталога
		 */
		if (!$this->PluginSimplecatalog_Product_GetCompareProductsIdsForScheme($oProduct->getScheme())) {
			Router::Location($oProduct->getScheme()->getCatalogItemsWebPath());
			return false;
		}
		/*
		 * сделать редирект на страницу сравнения
		 */
		Router::Location($oProduct->getCompareProductsUrl());
	}


	/**
	 * Фильтр продуктов
	 *
	 * @return bool
	 */
	public function EventProductFilter() {
		$this->SetTemplateAction('items');
		/*
		 * получить активную схему
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		/*
		 * получить поля фильтра
		 */


		$this->PreparePagingByScheme($oScheme);

		/*
		 * получить корректную сортировку
		 */
		$aSortOrder = $this->PluginSimplecatalog_Product_MyGetValidSortOrder($this->GetDataFromFilter('sort'), $this->GetDataFromFilter('dir'));

		/*
		 * получить список продуктов по фильтру и общее количество
		 */
		/*$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByProductFilterFields(
			$aFieldsRequestData,
			$aCategories,
			PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
			$oScheme,
			$this->iCurrentPage,
			$this->iPerPage,
			$aSortOrder
		);*/
		/*
		 * получить ид всех категорий и дерева каждой категории включительно, если указано в конфиге
		 */

		//foreach($aCategories as $oCategory) {
			//$aCategoriesIds[] = $oCategory->getId();
			/*
			 * нужно ли учитывать у категории всю ветку субкатегорий при поиске
			 */
			//if (Config::Get('plugin.simplecatalog.product.add_all_category_tree_for_product_filter')) {
			//	$aCategoriesIds = array_merge($aCategoriesIds, $oCategory->getDescendingCategoriesIds());
			//}
		//}
		/*
		 * получить уникальные ид всех категорий (т.к. могли выбрать ветки категорий так, что одна входит в другую)
		 */

		$min = $this->GetDataFromFilter('price_min');
		$max = $this->GetDataFromFilter('price_max');

		$aFilter = array(
				'#page' => array($this->iCurrentPage, $this->iPerPage),
				'price >=' => $min,
				'price <=' => $max,
				'#order' => $aSortOrder

		);

		if($aProviderIds = $this->GetDataFromFilter('provider')  and is_array($aProviderIds) ){
			$aFilter['user_id IN'] = $aProviderIds;
		}else{
			$_REQUEST['filter']['provider'] = null;


		}


		/*
		 * были ли выбраны категории
		 */
		if ($aCategoriesIdsRaw = $this->GetDataFromFilter('categories_ids') and is_array($aCategoriesIdsRaw)) {
			/*
			 * получить существующие категории по массиву ид
			 */
			//if (!$aCategories = $this->PluginSimplecatalog_Category_GetCategoriesArrayIdExists($aCategoriesIdsRaw, false)) {
			//	$this->Message_AddError($this->Lang('Errors.Category_Not_Found'), $this->Lang_Get('error'), true);
			//	$this->RedirectToReferer();
			//	return false;
			//}
			$aFilter['category_id IN'] = array_unique($aCategoriesIdsRaw);

		} else {
			$aCategories = array();
			/*
			 * очистить реквест чтобы и визуально фильтр выглядел как и работает
			 */
			$_REQUEST['filter']['categories_ids'] = null;
		}

		if ($aCityIds = $this->GetDataFromFilter('city') and is_array($aCityIds)){
			$aFilter['city IN'] = $aCityIds;

		}else{
			$_REQUEST['filter']['city'] = null;

		}

		/*if (!$aFieldsRequestData = $this->GetDataFromFilter('fields') or !is_array($aFieldsRequestData)) {
			$aFieldsRequestData =array();
			//$this->Message_AddError($this->Lang('Errors.Incorrect_Filter'), $this->Lang_Get('error'), true);
			//$this->RedirectToReferer();
			//return false;
		}*/

		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByFilter($aFilter);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData['count'],
			$this->iCurrentPage,
			$this->iPerPage,
			Config::Get('pagination.pages.count'),
			$oScheme->getCatalogItemsFilterWebPath(),
			array('filter' => $this->GetDataFromFilter())
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('aProducts', $aProductsData['collection']);
		$this->Viewer_Assign('aSortOrderData', $this->PluginSimplecatalog_Product_MyParseSortOrderForTemplateVars($aSortOrder));
		$this->Viewer_Assign('iTotalProductCount', $aProductsData['count']);
		$this->Viewer_Assign('oScheme', $oScheme);

		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();

		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Аякс очистка поля файла продукта при редактировании
	 *
	 * @return bool
	 */
	public function EventAjaxCleanFileField() {
		$this->Viewer_SetResponseAjax('json');
		/*
		 * есть ли такой продукт
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) getRequest('product_id'))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * активна ли схема
		 */
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить права у пользователя на редактирование продукта
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct)) {
			$this->Message_AddError($this->Lang('Errors.You_Cant_Edit_This_Product'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * есть ли такое поле у продукта
		 */
		if (!$oProductField = $this->PluginSimplecatalog_Product_MyGetFieldsByProductIdAndFieldId((int) getRequest('product_id'), (int) getRequest('scheme_field_id'))) {
			$this->Message_AddError($this->Lang('Errors.Field_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * удалить файл и все связанные данные (доступа и количества загрузок)
		 */
		$this->PluginSimplecatalog_Product_DeleteFileAndAdditionalDataByPathAndProductField($oProductField->getContent(), $oProductField);
		/*
		 * очистить поле продукта
		 */
		$oProductField->setContent('');
		$oProductField->setContentSource('');
		$oProductField->Save();

		$this->Message_AddNotice($this->Lang('notices.products.edit.clean_file_field_done'));
	}


	/**
	 * Получить файл по его хешу (механизм защищенных ссылок)
	 *
	 * @return bool
	 */
	public function EventDownloadFile() {
		/*
		 * включен ли режим защищенных ссылок
		 */
		if (!Config::Get('plugin.simplecatalog.product.build_safe_and_hashed_links_for_file_downloads')) {
			$this->Message_AddError($this->Lang('Errors.files.safe_links_are_not_enabled'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * получить хеш файла и данные доступа
		 */
		if (!$sHash = $this->GetParam(1) or !$oACL = $this->PluginSimplecatalog_Product_GetHashedFileDownloadACLData($sHash)) {
			$this->Message_AddError($this->Lang('Errors.files.incorrect_hash'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить айпи, которому разрешен доступ
		 */
		if (!$oACL->getIpIsTheSame()) {
			$this->Message_AddError($this->Lang('Errors.files.hash_has_another_ip'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить время доступа
		 * tip: "записанные в блокнот" ссылки через некоторое время работать не будут - необходимо будет зайти на страницу продукта снова
		 */
		if ($oACL->getAccessTimeIsUp()) {
			$this->Message_AddError($this->Lang('Errors.files.url_time_is_up'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * есть ли такое поле продукта
		 */
		if (!$oProductField = $oACL->getProductField()) {
			$this->Message_AddError($this->Lang('Errors.product_field_not_found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$oProduct = $oProductField->getProduct();
		/*
		 * проверить модерацию
		 */
		if (!$oProduct->getModerationDone() and (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct))) {
			$this->Message_AddError($this->Lang('Errors.Product_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * проверить наличие файла
		 */
		$sFilename = $this->PluginSimplecatalog_Tools_GetServerPath($oProductField->getContent());
		if (!file_exists($sFilename)) {
			$this->Message_AddError($this->Lang('Errors.files.file_not_found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->Hook_Run('sc_product_field_file_download', array('oProduct' => $oProduct, 'oProductField' => $oProductField));
		/*
		 * выслать файл пользователю
		 */
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');
		header('Content-Disposition: attachment; filename="' . basename($sFilename) . '";');
		header('Last-Modified: ' . gmdate('r', time()));
		header('Connection: close');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Content-Length: ' . filesize($sFilename));
		readfile($sFilename);
		/*
		 * выключить движок
		 */
		Engine::getInstance()->Shutdown();
		exit();
	}


	/**
	 * Вывод карточки продукта (через ембед код)
	 *
	 * @return bool
	 */
	public function EventShowEmbedProduct() {
		$this->SetTemplateAction('embed');
		/*
		 * включено ли получение ембед кода
		 */
		if (!Config::Get('plugin.simplecatalog.product.allow_embed_code')) {
			return $this->ShowError($this->Lang('Errors.embed.disabled'));
		}
		/*
		 * есть ли такой продукт
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById((int) $this->GetParam(1))) {
			return $this->ShowError($this->Lang('Errors.Product_Not_Found'));
		}
		$this->Viewer_Assign('oScheme', $oProduct->getScheme());
		$this->Viewer_Assign('oProduct', $oProduct);
	}


	/*
	 *
	 * --- Работа с картой ---
	 *
	 */

	/**
	 * Отобразить страницу меток на карте для всех продуктов схемы
	 *
	 * @return bool
	 */
	public function EventMapItems() {
		/*
		 * есть ли такая схема
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl((string) $this->GetParam(0))) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		/*
		 * включена ли карта для схемы
		 */
		if (!$oScheme->getMapItemsEnabled()) {
			$this->Message_AddError($this->Lang('Errors.map_items.map_disabled'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}
		$this->sMenuHeadItemSelect = $oScheme->getSchemeUrl();
		$this->sMenuItemSelect = 'mapitems';

		$this->Viewer_Assign('oScheme', $oScheme);
		$this->GetMapItemsCountByScheme($oScheme);

		$this->SetTemplateAction('mapitems');
		$this->Viewer_AddHtmlTitle($oScheme->getSchemeName());
	}


	/**
	 * Аякс загрузчик меток карты при изменении масштаба
	 */
	public function EventAjaxMapItemsLoader() {
		$this->Viewer_SetResponseAjax('jsonp');
		/*
		 * для jsonp не выполняется валидация csfr-ключа автоматически для аякс запросов
		 * tip: данная проверка не обязательна т.к. это получение открытых данных, но это сделано в целях защиты от использования данных извне
		 */
		$this->Security_ValidateSendForm();

		$mData = null;
		$sError = null;
		/*
		 * есть ли такая схема
		 */
		if (!is_string(getRequest('scheme_id')) or !$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeById(getRequest('scheme_id'))) {
			$sError = $this->Lang('Errors.Scheme_Not_Found');
		}

		if (!$sError) {
			/*
			 * включена ли карта для схемы
			 */
			if (!$oScheme->getMapItemsEnabled()) {
				$sError = $this->Lang('Errors.map_items.map_disabled');
			}
		}

		if (!$sError) {
			/*
			 * координаты передаются строкой с запятыми
			 */
			if (!is_string(getRequest('coords'))) {
				$sError = $this->Lang('Errors.map_items.unknown_coords');
			}
		}

		if (!$sError) {
			/*
			 * проверить координаты
			 */
			$aCoords = explode(',', getRequest('coords'));
			if (!$this->Validate_Validate('sc_array', $aCoords, array(
				'allowEmpty' => false,
				'max_items' => 4,
				'min_items' => 4,
				'item_validator' => array(
					'type' => 'number',
					'params' => array(
						'allowEmpty' => false,
						'max' => 180,
						'min' => -180,
					),
				),
			))) {
				$sError = $this->Validate_GetErrorLast();
			}
		}

		if (!$sError) {
			$mData = $this->GetMapItemsByCoordsAndScheme($aCoords, $oScheme);
		}

		$this->Viewer_AssignAjax('error', $sError);
		$this->Viewer_AssignAjax('data', $mData);
	}


	/**
	 * Получить в указанной области координат метки продуктов схемы
	 *
	 * @param array $aCoords        массив прямоугольных координат области меток для отображения (широта 1, долгота 1, широта 2, долгота 2)
	 * @param       $oScheme        сущность схемы
	 * @return array
	 */
	protected function GetMapItemsByCoordsAndScheme($aCoords, $oScheme) {
		$aItems = $this->PluginSimplecatalog_Maps_MyGetItemsByFilterAndSchemeAndProductModerationDone(array(
			/*
			 * широта
			 */
			'lat >=' => $aCoords[0],
			'lat <=' => $aCoords[2],
			/*
			 * долгота
			 */
			'lng >=' => $aCoords[1],
			'lng <=' => $aCoords[3],
		), $oScheme);

		$aFeatures = array();
		/*
		 * для построения верстки содержимого метки
		 */
		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('oScheme', $oScheme);
		/*
		 * получить шаблон схемы
		 */
		$sTemplatePath = $this->PluginSimplecatalog_Scheme_GetTemplatePathByScheme($oScheme, 'maps/all_items_loader/item_elements/content.tpl');

		foreach($aItems as $oItem) {
			$oProduct = $oItem->getProduct();

			$oViewer->Assign('oProduct', $oProduct);
			$oViewer->Assign('oItem', $oItem);

			$aFeatures[] = array(
				'type' => 'Feature',
				'id' => $oItem->getId(),
				'geometry' => array(
					'type' => 'Point',
					'coordinates' => array($oItem->getLat(), $oItem->getLng()),
				),
				'properties' => array(
					'balloonContent' => $oViewer->Fetch($sTemplatePath),
					'hintContent' => $oProduct->getFirstFieldTitle() . ($oItem->getExtraHint() ? ': ' . $oItem->getExtraHint() : ''),
					'iconContent' => $oItem->getTitle(),
					'clusterCaption' => HtmlTagReady::A($oProduct->getItemShowWebPath(), $oProduct->getFirstFieldTitle()) . ($oItem->getTitle() ? ': ' . $oItem->getTitle() : ''),
				),
				'options' => array(
					'preset' => $oItem->getExtraPreset(),
				),
			);
		}
		/*
		 * дебаг: границы загружаемых областей
		 */
/*		$aFeatures[] = array(
			'type'       => 'Feature',
			'id'         => mt_rand(100200300, 5100200300),
			'geometry'   => array(
				'type'        => 'Point',
				'coordinates' => array($aCoords[0], $aCoords[1]),
			),
			'properties' => array(
				'iconContent' => '1',
			),
		);
		$aFeatures[] = array(
			'type'       => 'Feature',
			'id'         => mt_rand(100200300, 5100200300),
			'geometry'   => array(
				'type'        => 'Point',
				'coordinates' => array($aCoords[2], $aCoords[3]),
			),
			'properties' => array(
				'iconContent' => '2',
			),
		);*/

		/*
		 * дебаг: все метки в кластере для remote менеджера
		 */
/*		$mData = array(
			'type' => 'FeatureCollection',
			'features' => array(
				array(
					'type' => 'Cluster',
					'id' => PHP_INT_MAX,
					'bbox' => array(array($aCoords[0], $aCoords[1]), array($aCoords[2], $aCoords[3])),
					'number' => count($aFeatures),
					'features' => $aFeatures,
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array(($aCoords[0] + $aCoords[2]) / 2, ($aCoords[1] + $aCoords[3]) / 2),
					),
				),
			),
		);*/
		return array(
			'type' => 'FeatureCollection',
			'features' => $aFeatures,
		);
	}
	

	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Показать ошибку
	 *
	 * @param $sMsg		текст ошибки
	 * @return bool
	 */
	private function ShowError($sMsg) {
		$this->Message_AddError($sMsg, $this->Lang_Get('error'));
		return false;
	}


	/**
	 * Получение текстовки без указания префикса плагина
	 *
	 * @param       $sLangKey		ключ без префикса плагина
	 * @param array $aParams		параметры, передаваемые методу Lang_Get
	 * @return mixed
	 */
	private function Lang($sLangKey, $aParams = array()) {
		return $this->Lang_Get('plugin.simplecatalog.' . $sLangKey, $aParams);
	}


	/**
	 * Выполнить редирект на реферер
	 */
	private function RedirectToReferer() {
		$this->PluginSimplecatalog_Tools_RedirectToReferer();
	}


	/**
	 * Установить страницу и количество элементов на странице на основе данных схемы
	 *
	 * @param Entity $oScheme           объект схемы (для количества элементов на странице)
	 */
	public function PreparePagingByScheme($oScheme) {
		$this->aParamsWOPage = $this->GetParams();
		/*
		 * убрать пустые параметры
		 * tip: фикс бага роутера, когда в параметры экшена добавляется пустое значение последним параметром (из-за наличия строки запроса "?" в урле)
		 * 		в новой версии лс этот баг исправлен
		 */
		$this->aParamsWOPage = array_filter($this->aParamsWOPage);
		/*
		 * параметров должно быть как минимум два (первый - урл схемы, второй - страница)
		 */
		if (count($this->aParamsWOPage) < 2) {
			/*
			 * страница не указана
			 */
			$sPageNumRaw = '';
		} else {
			/*
			 * номер страницы нужно получить из конца параметров
			 */
			$sPageNumRaw = end($this->aParamsWOPage);
		}
		/*
		 * дополнительная проверка нужна т.к. если последний параметр ($sPageNumRaw) начинается с числа ("250cc"), то тогда такое значение intval принимает как реальное число - страницу
		 */
		if (!$sPageNumRaw or !preg_match('#^page(\d+)$#', $sPageNumRaw) or !$this->iCurrentPage = intval(preg_replace('#^page(\d+)$#', '\1', $sPageNumRaw))) {
			$this->iCurrentPage = 1;
		} else {
			/*
			 * удалить страницу из параметров
			 */
			array_pop($this->aParamsWOPage);
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
		/*
		 * для меню
		 */
		$this->Viewer_AddMenu('simplecatalog_menu', Plugin::GetTemplatePath(__CLASS__) . 'navs/menu.simplecatalog.tpl');
		$this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
		$this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
	}


}

?>