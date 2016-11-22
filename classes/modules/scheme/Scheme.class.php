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

class PluginSimplecatalog_ModuleScheme extends ModuleORM {

	private $oMapper = null;

	/*
	 * Статус активированности элемента
	 */
	const COMPONENT_ENABLED = 2;
	const COMPONENT_DISABLED = 1;
	
	/*
	 * Места, где нужно показывать поля продуктов
	 */
	const FIELD_SHOW_ANYWHERE = 'anywhere';
	const FIELD_SHOW_IN_PRODUCT_LIST = 'product_list';
	const FIELD_SHOW_ON_PRODUCT_PAGE = 'product_page';
	const FIELD_SHOW_NOWHERE = 'nowhere';
	
	/*
	 * Разрешить комментирование в продуктах
	 */
	const ALLOW_COMMENTS_FORCED_TO_ALLOW = 'forced_to_allow';
	const ALLOW_COMMENTS_DENY = 'deny';
	const ALLOW_COMMENTS_USER_DEFINED = 'user_defined';
	
	/*
	 * Кто может добавлять и редактировать продукты (базовые настройки)
	 */
	const CAN_ADD_PRODUCTS_ADMINS = 'admins';
	const CAN_ADD_PRODUCTS_ANY_USER = 'any_user';

	/*
	 * Что нужно показывать на главной странице продуктов (items)
	 */
	const SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS = 'products';
	const SHOW_ON_ITEMS_PAGE_CATEGORIES = 'categories';
	const SHOW_ON_ITEMS_PAGE_MAP = 'map';

	/*
	 * Места, где можно выводить инфо блоки продуктов
	 */
	const SHOW_PRODUCTS_BLOCK_PLACE_TYPE_NONE = 'none';
	const SHOW_PRODUCTS_BLOCK_PLACE_TYPE_BEFORE_CONTENT = 'content';
	const SHOW_PRODUCTS_BLOCK_PLACE_TYPE_IN_SIDEBAR = 'sidebar';

	/*
	 * Тип поля - текст
	 */
	const FIELD_TYPE_TEXT = 'text';
	/*
	 * Тип поля - поле ввода
	 */
	const FIELD_TYPE_TEXTAREA = 'textarea';
	/*
	 * Тип поля - файл
	 */
	const FIELD_TYPE_FILE = 'file';
	/*
	 * Тип поля - чекбокс
	 */
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	/*
	 * Тип поля - не редактируемое поле со значением по-умолчанию
	 */
	const FIELD_TYPE_NOT_EDITABLE = 'noteditable';
	/*
	 * Тип поля - выпадающий список
	 */
	const FIELD_TYPE_SELECT = 'select';

	/*
	 * Ключ сессии где хранится в разрезе ид схемы выбранный пользователем шаблон
	 */
	const TEMPLATE_NAME_SESSION_KEY = 'sc_scheme_template_name';

	/*
	 * Сортировка по-умолчанию для схем
	 */
	private $aDefaultOrder = array('sorting' => 'asc');
	/*
	 * Сортировка по-умолчанию для полей схем
	 * tip: не заменять на сортировку по-умолчанию т.к. здесь она всегда должна быть такой для полей
	 */
	private $aDefaultSortingOrderForSchemeFields = array('sorting' => 'asc');
	/*
	 * Сортировка по-умолчанию для настроек связей схемы
	 */
	private $aDefaultLinkSettingsSortingOrder = array('sorting' => 'asc');


	public function Init() {
		/*
		 * orm требует этого
		 */
		parent::Init();
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}


	/*
	 *
	 * --- Обертки ORM методов ---
	 *
	 */

	/*
	 *
	 * --- Схема ---
	 *
	 */

	/**
	 * Получить схему по ид
	 *
	 * @param $iId		ид схемы
	 * @return mixed	объект схемы
	 */
	public function MyGetSchemeById($iId) {
		return $this->GetSchemeById($iId);
	}


	/**
	 * Получить схему по ид и активности
	 *
	 * @param $iId		ид схемы
	 * @param $iActive	активность
	 * @return mixed	объект схемы
	 */
	public function MyGetSchemeByIdAndActive($iId, $iActive) {
		return $this->GetSchemeByIdAndActive($iId, $iActive);
	}


	/**
	 * Получить активную схему по её ид
	 *
	 * @param $iId			ид схемы
	 * @return mixed		объект схемы
	 */
	public function MyGetActiveSchemeById($iId) {
		return $this->MyGetSchemeByIdAndActive($iId, self::COMPONENT_ENABLED);
	}


	/**
	 * Получить схему по её урлу и статусу её активности (включена ли)
	 *
	 * @param $sSchemeUrl	урл схемы
	 * @param $iActive		статус включенности
	 * @return mixed		объект схемы
	 */
	public function MyGetSchemeBySchemeUrlAndActive($sSchemeUrl, $iActive) {
		return $this->GetSchemeBySchemeUrlAndActive($sSchemeUrl, $iActive);
	}


	/**
	 * Получить активную схему по её урлу
	 *
	 * @param $sSchemeUrl	урл схемы
	 * @return mixed		объект схемы
	 */
	public function MyGetActiveSchemeBySchemeUrl($sSchemeUrl) {
		return $this->MyGetSchemeBySchemeUrlAndActive($sSchemeUrl, self::COMPONENT_ENABLED);
	}


	/**
	 * Получить схему по её урлу
	 *
	 * @param $sSchemeUrl	урл схемы
	 * @return mixed
	 */
	public function MyGetSchemeBySchemeUrl($sSchemeUrl) {
		return $this->GetSchemeBySchemeUrl($sSchemeUrl);
	}


	/**
	 * Получить все схемы отсортированные по значению их поля сортировки
	 *
	 * @return mixed
	 */
	public function MyGetSchemeItemsAll() {
		return $this->GetSchemeItemsAll(array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить все активные схемы
	 *
	 * @return mixed
	 */
	public function MyGetActiveSchemeItems() {
		return $this->GetSchemeItemsByActive(self::COMPONENT_ENABLED, array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить все схемы по активности, массиву разрешенности комментариев и показа онлайн комментариев (в прямом эфире)
	 *
	 * @param $iActive						активность схем
	 * @param $aAllowComments				массив типов разрешенности комментариев
	 * @param $iShowOnlineComments			нужно ли показывать комментарии в блоке "прямой эфир"
	 * @return mixed
	 */
	public function MyGetSchemeItemsByActiveAndAllowCommentsInAndShowOnlineComments($iActive, $aAllowComments, $iShowOnlineComments) {
		return $this->GetSchemeItemsByActiveAndAllowCommentsInAndShowOnlineComments($iActive, $aAllowComments, $iShowOnlineComments, array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить все схемы по добавлению пункта в меню создания нового топика и активности
	 *
	 * @param $iMenuCreate					флаг добавления пункта в меню создания нового топика
	 * @param $iActive						активность схемы
	 * @return mixed
	 */
	public function MyGetSchemeItemsByMenuAddTopicCreateAndActive($iMenuCreate, $iActive) {
		return $this->GetSchemeItemsByMenuAddTopicCreateAndActive($iMenuCreate, $iActive, array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить все схемы по добавлению пункта в главное меню и активности
	 *
	 * @param $iMenuCreate					флаг добавления пункта в главное меню
	 * @param $iActive						активность схемы
	 * @return mixed
	 */
	public function MyGetSchemeItemsByMenuMainAddLinkAndActive($iMenuCreate, $iActive) {
		return $this->GetSchemeItemsByMenuMainAddLinkAndActive($iMenuCreate, $iActive, array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить активные схемы по типу отображения последних продуктов
	 *
	 * @param string $sType		тип
	 * @return mixed
	 */
	public function MyGetActiveSchemeItemsByBlockShowLastProducts($sType) {
		return $this->GetSchemeItemsByActiveAndBlockShowLastProducts(self::COMPONENT_ENABLED, $sType, array('#order' => $this->aDefaultOrder));
	}


	/**
	 * Получить схемы по массиву ид и указанным параметрам
	 *
	 * @param       $aIds			массив ид
	 * @param array $aParams		параметры
	 * @return mixed
	 */
	public function MyGetSchemeItemsByIdIn($aIds, $aParams = array()) {
		return $this->GetSchemeItemsByIdIn($aIds, $aParams);
	}


	/*
	 *
	 * --- Поля ---
	 *
	 */

	/**
	 * Получить поле по ид
	 *
	 * @param $iId		ид поля
	 * @return mixed
	 */
	public function MyGetFieldsById($iId) {
		return $this->GetFieldsById($iId);
	}
	

	/**
	 * Получить отсортированный список полей схемы
	 *
	 * @param $oScheme		объект схемы
	 * @return mixed
	 */
	public function MyGetFieldsItemsSortedByScheme($oScheme) {
		$aData = $this->GetFieldsItemsBySchemeId($oScheme->getId(), array(
			/*
			 * tip: не заменять на сортировку по-умолчанию т.к. здесь она всегда должна быть такой для полей
			 */
			'#order' => $this->aDefaultSortingOrderForSchemeFields
		));
		return $aData;
	}


	/**
	 * Получить первое поле схемы (с учетом сортировки)
	 *
	 * @param $oScheme	объект схемы
	 * @return bool
	 */
	public function MyGetFirstFieldByScheme($oScheme) {
		if ($aFields = $oScheme->getFields()) {
			return reset($aFields);
		}
		return false;
	}


	/**
	 * Получить поля схемы по ид схемы и массиву ид полей и указанным параметрам
	 *
	 * @param       $iSchemeId		ид схемы
	 * @param       $aIds			массив ид полей схемы
	 * @param array $aParams		дополнительные параметры
	 * @return mixed
	 */
	public function MyGetFieldsItemsBySchemeIdAndIdIn($iSchemeId, $aIds, $aParams = array()) {
		return $this->GetFieldsItemsBySchemeIdAndIdIn($iSchemeId, $aIds, $aParams);
	}


	/**
	 * Добавить новое поле со значением по-умолчанию в каждый продукт
	 *
	 * @param $oField		объект поля
	 */
	public function AddFieldWithDefaultValueForAllProducts($oField) {
		/*
		 * получить все продукты схемы с новым полем
		 */
		if ($aProducts = $this->PluginsimpleCatalog_Product_MyGetProductItemsByScheme($oField->getScheme())) {
			/*
			 * получить ид продуктов
			 */
			$aProductsIds = array();
			foreach($aProducts as $oProduct) {
				$aProductsIds[] = $oProduct->getId();
			}
			$this->PluginSimplecatalog_Product_AddDefaultFieldValueForListedProducts($aProductsIds, $oField);
		}
	}


	/**
	 * Получить максимальное значение сортировки полей для схемы
	 *
	 * @param $iSchemeId	ид схемы
	 * @return int			максимальная текущая сортировка
	 */
	protected function GetMaxSortingValueForSchemeField($iSchemeId) {
		return $this->oMapper->GetMaxSortingValueForSchemeField($iSchemeId);
	}


	/**
	 * Получить значение сортировки по-умолчанию для поля схемы
	 *
	 * @param $iSchemeId	ид схемы
	 * @return int			новое значение сортировки
	 */
	public function GetNextFreeSortingValueForSchemeField($iSchemeId) {
		return $this->GetMaxSortingValueForSchemeField($iSchemeId) + 1;
	}


	/**
	 * Получить максимальное значение сортировки схем
	 *
	 * @return int			максимальная текущая сортировка
	 */
	protected function GetMaxSortingValueForScheme() {
		return $this->oMapper->GetMaxSortingValueForScheme();
	}


	/**
	 * Получить значение сортировки по-умолчанию для схемы
	 *
	 * @return int			новое значение сортировки
	 */
	public function GetNextFreeSortingValueForScheme() {
		return $this->GetMaxSortingValueForScheme() + 1;
	}


	/*
	 *
	 * --- Проверка необходимости миграции контента ---
	 *
	 */

	/**
	 * Запуск обработчика миграции данных полей продуктов из одного поля в другое
	 *
	 * @param $oField		объект поля
	 */
	public function RunDataMigrationForProductFields($oField) {
		/*
		 * нужно ли делать миграцию или тип контента поля не изменился
		 */
		if ($this->CheckIfDataMigrationForProductFieldsNeeded($oField)) {
			/*
			 * запустить миграцию
			 */
			$this->PluginSimplecatalog_Product_PerformDataMigrationFromOneTableFieldToAnother($oField);
		}
	}


	/**
	 * Нужна ли миграция данных для указанного поля
	 *
	 * @param $oField		объект поля
	 * @return bool
	 */
	public function CheckIfDataMigrationForProductFieldsNeeded($oField) {
		/*
		 * получить новый тип контента поля продукта на основе типа поля схемы, описывающего это поле или валидатора поля схемы
		 */
		$iNewContentType = $this->PluginSimplecatalog_Product_GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($oField);
		/*
		 * получить старый тип контента
		 */
		$iOldContentType = $this->PluginSimplecatalog_Product_GetCurrentContentTypeOfProductFieldBySchemeField($oField);
		/*
		 * сравнить новый тип редактируемого поля и существующий тип поля (первого попавшегося поля продукта)
		 * tip: проверка на нулл нужна т.к. может не быть старого типа контента т.к. не создано ещё ни одного продукта и, соответственно, поля продукта
		 * 		в этом случае будет возвращен нулл
		 */
		return !is_null($iOldContentType) and $iNewContentType != $iOldContentType;
	}


	/*
	 *
	 * --- Общие ---
	 *
	 */

	/**
	 * Получить массив орм сортировки для схем по-умолчанию
	 *
	 * @return array
	 */
	public function GetDefaultSortingOrder() {
		return $this->aDefaultOrder;
	}


	/**
	 * Получить массив орм сортировки для полей схем по-умолчанию
	 *
	 * @return array
	 */
	public function GetDefaultSortingOrderForSchemeFields() {
		return $this->aDefaultSortingOrderForSchemeFields;
	}


	/**
	 * Получить массив орм сортировки для настроек связи схем по-умолчанию
	 *
	 * @return array
	 */
	public function GetDefaultSortingOrderForSchemeLinkSettings() {
		return $this->aDefaultLinkSettingsSortingOrder;
	}


	/*
	 *
	 * --- Настройки связей для схем ---
	 *
	 */

	/**
	 * Получить настройку связи схемы по ид
	 *
	 * @param $iId						ид связи схемы
	 * @return mixed
	 */
	public function MyGetSchemeLinkById($iId) {
		return $this->GetLinkById($iId);
	}


	/**
	 * Получить список настроек связей для схемы
	 *
	 * @param 			$oScheme		сущность схемы
	 * @param array 	$aParams		дополнительные параметры орм
	 * @return mixed
	 */
	public function MyGetSchemeLinksForScheme($oScheme, $aParams = array()) {
		return $this->GetLinkItemsBySchemeId($oScheme->getId(), $aParams);
	}


	/**
	 * Получить отсортированный список настроек связей для схемы
	 *
	 * @param 			$oScheme		сущность схемы
	 * @return mixed
	 */
	public function MyGetSortedSchemeLinksForScheme($oScheme) {
		return $this->MyGetSchemeLinksForScheme($oScheme, array('#order' => $this->GetDefaultSortingOrderForSchemeLinkSettings()));
	}


	/**
	 * Получить список настроек связей по связанной схеме
	 *
	 * @param $oScheme					сущность схемы
	 * @return mixed
	 */
	public function MyGetSchemeLinksForTargetScheme($oScheme) {
		return $this->GetLinkItemsByTargetSchemeId($oScheme->getId());
	}


	/**
	 * Удалить все настройки связей для схемы, где она указана как основаня или связанная (а настройки связей удалят и сами связи)
	 *
	 * @param $oScheme					сущность схемы
	 */
	public function MyDeleteAllSchemeLinkSettingsByScheme($oScheme) {
		/*
		 * удалить все настройки связей для схемы
		 */
		if ($aLinkSettings = $this->MyGetSchemeLinksForScheme($oScheme)) {
			foreach($aLinkSettings as $oLinkSettings) {
				$oLinkSettings->Delete();
			}
		}
		/*
		 * удалить все настройки связей где схема указанная как привязанная
		 */
		if ($aLinkSettings = $this->MyGetSchemeLinksForTargetScheme($oScheme)) {
			foreach($aLinkSettings as $oLinkSettings) {
				$oLinkSettings->Delete();
			}
		}
	}


	/**
	 * Получить все настройки связей схем по массиву ид и указанным параметрам
	 *
	 * @param       $aIds			массив ид
	 * @param array $aParams		параметры
	 * @return mixed
	 */
	public function MyGetLinkItemsByIdIn($aIds, $aParams = array()) {
		return $this->GetLinkItemsByIdIn($aIds, $aParams);
	}


	/*
	 *
	 * --- Шаблоны ---
	 *
	 */

	/**
	 * Получить массив всех доступных для схемы шаблонов
	 *
	 * @return array
	 */
	public function GetTemplatesAll() {
		/*
		 * шаблон по-умолчанию должен идти первым в списке чтобы быть выбранным при создании схемы
		 */
		$sDefaultTemplateCode = Config::Get('plugin.simplecatalog.scheme.templates.default_code');
		$aTemplates = array(
			$sDefaultTemplateCode => $this->Lang_Get('plugin.simplecatalog.scheme_template_names.admin.' . $sDefaultTemplateCode),
		);
		/*
		 * поиск директорий шаблонов схем
		 */
		foreach(glob($this->GetTemplatesFolder() . '*', GLOB_ONLYDIR) as $sDir) {
			$sCode = basename($sDir);
			if ($sCode == $sDefaultTemplateCode) {
				continue;
			}
			$sNameKey = 'plugin.simplecatalog.scheme_template_names.admin.' . $sCode;
			$sName = $this->Lang_Get($sNameKey);
			/*
			 * если нету имени в языковом файле (константа или вместо текста получен исходный ключ текстовки (в лс 2.0)), то отображать имя директории с шаблонами
			 */
			$aTemplates[$sCode] = in_array($sName, array('NOT_FOUND_LANG_TEXT', $sNameKey)) ? $sCode : $sName;
		}
		/*
		 * В лс 1* версии есть баг некорректного определения orm-сущности (и проблемы с некорректными тегами кеша) если орм модуля плагина наследуется другим плагином
		 * поэтому чтобы отказаться от наследования был добавлен хук
		 */
		$this->Hook_Run('sc_scheme_get_templates_all', array('aTemplates' => &$aTemplates));
		return $aTemplates;
	}


	/**
	 * Получить полный путь к шаблону схемы по указанной схеме и относительного пути к подключаемому шаблону
	 *
	 * @param $oScheme			сущность схемы
	 * @param $sTemplate		относительный путь к шаблону
	 * @return string
	 */
	public function GetTemplatePathByScheme($oScheme, $sTemplate) {
		/*
		 * есть ли заданный схемой или пользователем шаблон
		 */
		if ($this->Viewer_TemplateExists($sPath = $this->GetTemplatesFolder() . $oScheme->getTemplateName() . '/' . $sTemplate)) {
			return $sPath;
		}
		/*
		 * есть ли стандартный шаблон
		 */
		if ($this->Viewer_TemplateExists($sPath = $this->GetTemplatesFolder() . Config::Get('plugin.simplecatalog.scheme.templates.default_code') . '/' . $sTemplate)) {
			return $sPath;
		}
		trigger_error('SC: template file not found for "' . $sPath . '"', E_USER_ERROR);
	}


	/**
	 * Директория с шаблонами схем плагина
	 *
	 * @return string
	 */
	private function GetTemplatesFolder() {
		return Plugin::GetTemplatePath(__CLASS__) . 'scheme_templates/';
	}


}

?>