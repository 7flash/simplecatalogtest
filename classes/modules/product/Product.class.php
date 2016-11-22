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

class PluginSimplecatalog_ModuleProduct extends ModuleORM {

	private $oMapper = null;

	/*
	 * Типы значений поля продукта (тип поля в таблице)
	 */
	const FIELD_TYPE_INT = 1;
	const FIELD_TYPE_FLOAT = 2;
	const FIELD_TYPE_VARCHAR = 4;
	const FIELD_TYPE_TEXT = 8;
	
	/*
	 * Модерация
	 */
	const MODERATION_DONE = 0;
	const MODERATION_NEEDED = 1;
	const MODERATION_DRAFT = 2;
	const MODERATION_DEFERRED = 4;

	/*
	 * Ключ сессии для хранения данных о сравнении
	 */
	const COMPARE_PRODUCTS_SESSION_KEY = 'sc_compare_products';
	/*
	 * Максимальное количество продуктов для сравнения
	 */
	const COMPARE_PRODUCTS_MAX_COUNT = 100;

	/*
	 * Очереди сохранения значений полей продуктов
	 */
	const PRODUCT_FIELDS_SAVE_FIRST_QUEUE = 'first_queue';
	const PRODUCT_FIELDS_SAVE_SECOND_QUEUE = 'second_queue';

	/*
	 *
	 * Типы полей для отображения в фильтре продуктов
	 *
	 */
	/*
	 * Минимальное и максимальное число, слайдер
	 */
	const FILTER_DISPLAY_TYPE_NUMBER = 'number';
	/*
	 * Простое поле ввода для текста
	 */
	const FILTER_DISPLAY_TYPE_STRING = 'string';
	/*
	 * Флажок вкл/выкл
	 */
	const FILTER_DISPLAY_TYPE_CHECKBOX = 'checkbox';
	/*
	 * Нередактируемое поле (заголовок)
	 */
	const FILTER_DISPLAY_TYPE_TITLE = 'title';
	/*
	 * Селект простой или множественный
	 */
	const FILTER_DISPLAY_TYPE_SELECT = 'select';

	/*
	 *
	 * Ссылки загрузки файлов из полей продукта типа "файл"
	 *
	 */
	/*
	 * Массив прав доступа к ссылке на экшен продуктов по хешу
	 */
	const SESSION_PF_FILE_ACLS = 'sc_pf_file_acls';

	
	/*
	 * Сортировка по-умолчанию (если она не задана или некорректна)
	 */
	private $aDefaultSortOrderBy = array('add_date' => 'desc');
	/*
	 * Разрешенные направления сортировки
	 * tip: можно менять порядок
	 */
	private $aSortOrdersDirs = array('asc', 'desc');

	
	/*
	 * Разрешенные первые символы для вывода груп под полем поиска
	 */
	private $sAllowedLettersForProductGroups = '\wа-яА-ЯёЁіІїЇєЄґҐ-';


	public function Init() {
		/*
		 * orm требует этого
		 */
		parent::Init();
		$this->oMapper = Engine::GetMapper(__CLASS__);
		/*
		 * установить сортировку по-умолчанию
		 */
		$this->SetDefaultProductSortingOrder(Config::Get('plugin.simplecatalog.product.default_sort_order'));
	}


	/*
	 *
	 * --- Проверка значения поля продукта по правилам поля схемы ---
	 *
	 */

	/**
	 * Проверить одно значение поля продукта по правилам поля схемы
	 *
	 * @param $sValue		значение
	 * @param $oField		поле схемы
	 * @return bool
	 * @throws Exception
	 */
	public function CheckValueOfProductFieldBySchemeField($sValue, $oField) {
		/*
		 * если поле можно не заполнять
		 */
		if (empty($sValue) and !$oField->getMandatoryEnabled()) {
			return true;
		}

		/*
		 * поле обязательно: проверить есть ли данные
		 */
		if (is_null($sValue) or $sValue == '') {
			return false;
		}

		/*
		 * проверить значение, в зависимости от типа поля, описывающего его
		 */
		switch ($oField->getFieldType()) {
			/*
			 * строка
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXT:
				return $this->CheckOneValueForTextType($sValue, $oField);
			/*
			 * многострочный текст
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXTAREA:
				return $this->CheckOneValueForTextareaType($sValue, $oField);
			/*
			 * файл
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
				return $this->CheckOneValueForFileType($sValue, $oField);
			/*
			 * флажок
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX:
				return $this->CheckOneValueForCheckboxType($sValue, $oField);
			/*
			 * нередактируемое поле
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE:
				return $this->CheckOneValueForNotEditableType($sValue, $oField);
			/*
			 * селект
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT:
				return $this->CheckOneValueForSelectType($sValue, $oField);
			/*
			 * тип не распознан
			 */
			default:
				throw new Exception('SC: Error: unknown field type "' . $oField->getFieldType() . '" in ' . __METHOD__);
		}
	}


	/*
	 *
	 * --- Валидаторы для каждого типа поля ---
	 *
	 */

	/**
	 * Проверить значение поля типа "text" по правилам его поля схемы
	 *
	 * @param $sValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForTextType($sValue, $oField) {
		$iLen = mb_strlen ($sValue, 'utf-8');
		return ($iLen >= $oField->getTextMinLength() and $iLen <= $oField->getTextMaxLength());
	}


	/**
	 * Проверить значение поля типа "textarea" по правилам его поля схемы
	 *
	 * @param $sValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForTextareaType($sValue, $oField) {
		$iLen = mb_strlen ($sValue, 'utf-8');
		return ($iLen >= $oField->getTextareaMinLength() and $iLen <= $oField->getTextareaMaxLength());
	}


	/**
	 * Проверить значение поля типа "file" по правилам его поля схемы
	 *
	 * @param $mValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForFileType($mValue, $oField) {
		/*
		 * если файл должен быть загружен
		 */
		if ((!isset($mValue['tmp_name']) or !$mValue['tmp_name']) and $oField->getMandatoryEnabled()) {
			return false;

		/*
		 * размер файла превышает указанный в директиве MAX_FILE_SIZE отправленной формы
		 */
		} elseif (isset($mValue['error']) and $mValue['error'] == UPLOAD_ERR_FORM_SIZE) {
			return false;

		} elseif (isset($mValue['tmp_name']) and $mValue['tmp_name'] and $mValue['error'] == 0) {
			/*
			 * проверить тип файла, если указаны разрешенные типы файлов в настройках поля схемы
			 */
			if ($aAllowedFileTypes = array_map('trim', explode(',', $oField->getFileTypesAllowed()))) {
				if (!in_array(strtolower(pathinfo($mValue['name'], PATHINFO_EXTENSION)), $aAllowedFileTypes)) {
					return false;
				}
			}
			/*
			 * проверить размер файла (не 0 байт и не больше чем разрешено)
			 */
			if ($mValue['size'] == 0 or ($mValue['size'] != 0 and $mValue['size'] / 1024 > $oField->getFileMaxSize())) {
				return false;
			}
		}
		return true;
	}


	/**
	 * Проверить значение поля типа "checkbox" по правилам его поля схемы
	 *
	 * @param $sValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForCheckboxType($sValue, $oField) {
		return in_array($sValue, array(0, 1));
	}


	/**
	 * Проверить значение поля типа "noteditable" по правилам его поля схемы
	 *
	 * @param $sValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForNotEditableType($sValue, $oField) {
		return !empty($sValue);
	}


	/**
	 * Проверить значение поля типа "select" по правилам его поля схемы
	 *
	 * @param $sValue	значение
	 * @param $oField	его поле схемы
	 * @return bool		результат проверки
	 */
	protected function CheckOneValueForSelectType($sValue, $oField) {
		/*
		 * 1. для обычного селекта - указан индекс одного значения (число)
		 * 2. если включен множественный выбор - числа, разделенные ";;" или просто число (если выбран один элемент) и по бокам строки добавляются ";" для корректного поиска в фильтре
		 */
		$aIndexes = $oField->getArrayOfIndexesForMultipleSelectFromStringValue($sValue);
		/*
		 * сверить количество найденных значений по индексам в описании селекта и количество поданных индексов
		 */
		return count(array_intersect_key($oField->getDefinedSelectValues(), array_flip($aIndexes))) == count($aIndexes);
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Получить тип модерации на основе значений полей продукта, полей схемы, пользователе, схемы и флаге сохранения в черновики
	 *
	 * @param $aProductFields		массив очередей полей продукта
	 * @param $aFields				поля схемы
	 * @param $oUser				сущность пользователя, который заполняет продукт
	 * @param $oScheme				сущность схемы
	 * @param $bSaveAsDraft			флаг сохранения продукта в черновики
	 * @param $bSaveDeferred		флаг отложенной публикации продукта
	 * @return int					тип статуса модерации
	 */
	public function MyGetModerationTypeByParameters($aProductFields, $aFields, $oUser, $oScheme, $bSaveAsDraft, $bSaveDeferred) {
		/*
		 * если установлен флаг сохранения продукта в черновики
		 */
		if ($bSaveAsDraft and $oScheme->getAllowDraftsEnabled()) {
			return self::MODERATION_DRAFT;
		}
		/*
		 * можно возвращать значение модерации на основе $aProductFields для полей схемы $aFields и пользователя $oUser,
		 * можно добавить правила для проверки ссылок в текстах, рейтинга пользователя или даты регистрации
		 */

		/*
		 * нужна ли модерация
		 */
		if ($oUser->getUserProductsNeedModerationBySchemeAndNotAdmin($oScheme)) {
			return self::MODERATION_NEEDED;
		}
		/*
		 * отложенная публикация продукта
		 */
		if ($bSaveDeferred and $oUser->getUserCanDeferProductsBySchemeOrIsAdmin($oScheme)) {
			return self::MODERATION_DEFERRED;
		}
		return self::MODERATION_DONE;
	}


	/*
	 *
	 * --- Сортировка ---
	 *
	 */

	/**
	 * Получить реальное поле в БД для синонима и направление сортировки
	 *
	 * @param $sOrder	тип сортировки (публичный синоним поля в БД, заданный в конфиге)
	 * @param $sDir		направление
	 * @return array
	 */
	public function MyGetValidSortOrder($sOrder = null, $sDir = null) {
		/*
		 * если некорректные значения - использовать сортировку по-умолчанию
		 */
		if (!is_string($sOrder) or !is_string($sDir)) {
			return $this->aDefaultSortOrderBy;
		}
		/*
		 * если тип сортировки не разрешен
		 */
		$aAllowedSortOrderTypes = Config::Get('plugin.simplecatalog.product.allowed_sort_order_types');
		if (!in_array($sOrder, array_keys($aAllowedSortOrderTypes))) {
			return $this->aDefaultSortOrderBy;
		}
		/*
		 * если направление сортировки для данного типа не разрешено
		 */
		if (!in_array($sDir, $this->aSortOrdersDirs)) {
			return $this->aDefaultSortOrderBy;
		}

		return array($aAllowedSortOrderTypes[$sOrder] => $sDir);
	}


	/**
	 * Получить поле сортировки, направление и сортировку наоборот для удобства работы с ними в шаблоне
	 *
	 * @param $aSortOrder		сортировка, полученная от MyGetValidSortOrder
	 * @return array
	 */
	public function MyParseSortOrderForTemplateVars($aSortOrder) {
		/*
		 * получить поле сортировки и направление
		 * tip: php 5.4 требует для array_shift массивы для изменения
		 */
		$aKeys = array_keys($aSortOrder);
		$aValues = array_values($aSortOrder);
		$sOrder = array_shift($aKeys);
		$sWay = array_shift($aValues);

		return array(
			'sOrder' => $sOrder,
			'sWay' => $sWay,
			'sReversedWay' => $this->MyGetReversedOrderDirection($sWay)
		);
	}


	/**
	 * Получить сортировку наоборот
	 *
	 * @param $sDir		направление сортировки
	 * @return string	противоположное направление
	 */
	protected function MyGetReversedOrderDirection($sDir) {
		/*
		 * array('asc', 'desc')
		 * 		asc == asc => true => 1 ===> desc
		 * 		desc == asc => false => 0 ===> asc
		 * array('desc', 'asc')
		 * 		asc == desc => false => 0 ===> desc
		 * 		desc == desc => true => 1 ===> asc
		 */
		return $this->aSortOrdersDirs[(int) ($sDir == $this->aSortOrdersDirs[0])];
	}


	/**
	 * Получить сортировку для продуктов по-умолчанию
	 *
	 * @return array
	 */
	public function GetDefaultProductSortingOrder() {
		return $this->aDefaultSortOrderBy;
	}


	/**
	 * Установить сортировку для продуктов по-умолчанию
	 *
	 * @param $aSortOrder		массив сортировки орм
	 * @return array
	 */
	public function SetDefaultProductSortingOrder($aSortOrder) {
		$this->aDefaultSortOrderBy = $aSortOrder;
	}


	/*
	 *
	 * --- Обертки ORM методов ---
	 *
	 */
	
	/*
	 *
	 * --- Для сущности продукта ---
	 *
	 */

	/**
	 * Получить все продукты указанной модерации, схемы, страницы и количества на ней и сортировкой
	 *
	 * @param array      $aModeration        типы модераций
	 * @param            $oScheme            объект схемы
	 * @param int        $iPage              текущая страница
	 * @param int        $iPerPage           результатов на странице
	 * @param array      $aSortOrder         сортировка
	 * @param array      $aParams            дополнительные параметры орм
	 * @return mixed
	 */
	public function MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder($aModeration, $oScheme, $iPage = 1, $iPerPage = 100, $aSortOrder = array(), $aParams = array()) {
		return $this->GetProductItemsByFilter($aParams + array(
			'scheme_id' => $oScheme->getId(),
			'moderation IN' => $aModeration,
			'#order' => $aSortOrder,
			/*
			 * это меняет формат возвращаемых данных: collection (by filter) и count (всего), #limit не делает этого
			 */
			'#page' => array($iPage, $iPerPage),
			/*
			 * уменьшает количество запросов к бд (загружает данные только типа belongs_to и has_one)
			 */
			'#with' => array('scheme', 'user', 'user_edit_last'),
		));
	}


	/**
	 * Получить все продукты указанной модерации, пользователя, схемы, страницы и количества на ней и сортировкой
	 *
	 * @param array      $aModeration          типы модераций
	 * @param            $oUser                сущность пользователя
	 * @param            $oScheme              сущность схемы
	 * @param int        $iPage                текущая страница
	 * @param int        $iPerPage             результатов на странице
	 * @param array      $aSortOrder           сортировка
	 * @param array      $aParams              дополнительные параметры орм
	 * @return mixed
	 */
	public function MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder($aModeration, $oUser, $oScheme, $iPage = 1, $iPerPage = 100, $aSortOrder = array(), $aParams = array()) {
		return $this->MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
			$aModeration,
			$oScheme,
			$iPage,
			$iPerPage,
			$aSortOrder,
			$aParams + array(
				'user_id' => $oUser->getId(),
			)
		);
	}


	/**
	 * Получить все продукты указанной модерации, схемы, страницы и количества на ней, сортировкой и категории
	 *
	 * @param int   $iModeration        тип модерации
	 * @param       $oScheme            объект схемы
	 * @param int   $iPage              текущая страница
	 * @param int   $iPerPage           результатов на странице
	 * @param array $aSortOrder         сортировка орм
	 * @param       $oCategory          объект категории
	 * @return mixed
	 */
	public function MyGetProductItemsByModerationAndSchemeAndPageAndPerPageAndSortOrderAndCategory($iModeration, $oScheme, $iPage = 1, $iPerPage = 100, $aSortOrder, $oCategory) {
		/*
		 * tip: сериализации всех параметров не используется т.к. сериализация сущностей создает слишком длинный ключ
		 */
		$sCacheKey = 'simplecatalog_' . implode('_', array(__METHOD__, $iModeration, $oScheme->getId(), $iPage, $iPerPage, serialize($aSortOrder), $oCategory->getId()));
		if (($mData = $this->Cache_Get($sCacheKey)) === false) {
			$aCategoriesIdsToSearch = array($oCategory->getId());
			/*
			 * если можно устанавливать только конечную категорию продуктам
			 */
			if (Config::Get('plugin.simplecatalog.categories.product_categories_should_not_have_child_categories')) {
				/*
				 * при отображении продуктов такой категории - отображать продукты её дочерних категорий
				 */
				$aCategoriesIdsToSearch = array_merge($aCategoriesIdsToSearch, $oCategory->getDescendingCategoriesIds());
			}
			/*
			 * выполнить запрос
			 */
			$mData = $this->PluginSimplecatalog_Myorm_GetItemsByJoin(array(
				/*
				 * данные для кастомного myorm модуля
				 */
				PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product_categories'),
				PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'product_id',
				/*
				 * условия орм лс
				 */
				'#where' => array(
					'a.`scheme_id` = ?d AND a.`moderation` = ?d AND b.`category_id` IN (?a)' => array($oScheme->getId(), $iModeration, $aCategoriesIdsToSearch)
				),
				'#order' => $aSortOrder,
				'#page' => array($iPage, $iPerPage),
				'#with' => array('scheme', 'user', 'user_edit_last'),
			), array($this));
			/*
			 * теги кеша
			 */
			$aTags = array(
				/*
				 * имена тегов кеша орм для продуктов
				 */
				'PluginSimplecatalog_ModuleProduct_EntityProduct_save',
				'PluginSimplecatalog_ModuleProduct_EntityProduct_delete',
				/*
				 * имена тегов кеша орм для связей категорий с продуктами
				 */
				'PluginSimplecatalog_ModuleProduct_EntityCategories_save',
				'PluginSimplecatalog_ModuleProduct_EntityCategories_delete',
			);
			$this->Cache_Set($mData, $sCacheKey, $aTags, 60 * 60 * 24 * 7);
		}
		return $mData;
	}


	/**
	 * Получить все продукты указанной категории (используется при замене старой категории у продуктов на новую в модуле категорий)
	 *
	 * @param $oCategory	объект категории
	 * @return mixed
	 */
	public function MyGetProductItemsByCategory($oCategory) {
		return $this->PluginSimplecatalog_Myorm_GetItemsByJoin(array(
			/*
			 * данные для кастомного myorm модуля
			 */
			PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product_categories'),
			PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'product_id',
			/*
			 * условия орм лс
			 */
			'#where' => array(
				'b.`category_id` = ?d' => array($oCategory->getId())
			),
		), array($this));
	}
	

	/**
	 * Получить количество продуктов по модерации и схеме
	 *
	 * @param $iModeration	модерация
	 * @param $oScheme		объект схемы
	 * @return int			количество
	 */
	public function MyGetCountProductItemsByModerationAndScheme($iModeration, $oScheme) {
		$iCount = $this->GetCountItemsByFilter(array(
			'scheme_id' => $oScheme->getId(),
			'moderation' => $iModeration,
		));
		return $iCount;
	}


	/**
	 * Получить продукты по фильтру
	 *
	 * @param array $aFilter	фильтр
	 * @return array			продукты
	 */
	public function MyGetProductItemsByFilter($aFilter) {
		return $this->GetProductItemsByFilter($aFilter + array(
			'#with' => array('scheme', 'user', 'user_edit_last')
		));
	}


	/**
	 * Получить продукт по ид
	 *
	 * @param $iId			ид продукта
	 * @return mixed		продукт
	 */
	public function MyGetProductById($iId) {
		return $this->GetProductById($iId);
	}


	/**
	 * Получить продукт по его ЧПУ
	 *
	 * @param $sProductUrl	ЧПУ продукта
	 * @return mixed		продукт
	 */
	public function MyGetProductByProductUrl($sProductUrl) {
		return $this->GetProductByProductUrl($sProductUrl);
	}
	

	/**
	 * Получить продукты по схеме
	 *
	 * @param $oScheme		схема
	 * @return mixed		список продуктов
	 */
	public function MyGetProductItemsByScheme($oScheme) {
		return $this->GetProductItemsBySchemeId($oScheme->getId());
	}


	/**
	 * Получить количество продуктов по ид схемы, по ид юзера и типам модерации
	 *
	 * @param       $iSchemeId		ид схемы
	 * @param       $iUserId		ид юзера
	 * @param       $aModeration	массив типов модераций
	 * @param array $aParams		дополнительные параметры для ОРМ
	 * @return int					количество
	 */
	public function MyGetCountProductItemsBySchemeIdAndUserIdAndModerationIn($iSchemeId, $iUserId, $aModeration, $aParams = array()) {
		$aData = $this->GetCountItemsByFilter(array_merge(array(
			'scheme_id' => $iSchemeId,
			'user_id' => $iUserId,
			'moderation IN' => $aModeration,
		), $aParams));
		return $aData;
	}


	/**
	 * Получить у активной схемы промодерированный продукт по ид
	 *
	 * @param int $iId				ид продукта
	 * @return object|null
	 */
	public function MyGetActiveSchemeModerationDoneProductById($iId) {
		if ($oProduct = $this->GetProductByIdAndModeration($iId, self::MODERATION_DONE) and $oProduct->getScheme()->getActiveEnabled()) {
			return $oProduct;
		}
		return null;
	}


	/**
	 * Получить все продукты указанной модерации и дата публикации которых меньше или равна текущей (для публикации отложенных продуктов)
	 *
	 * @param int $iModeration	тип модерации
	 * @return array
	 */
	public function MyGetProductItemsByModerationAndAddDateLteCurrentDate($iModeration) {
		return $this->GetProductItemsByModerationAndAddDateLte($iModeration, date('Y-m-d H:i:s'));
	}


	/**
	 * Получить другие продукты из категорий продукта ("ещё из этих категорий")
	 *
	 * @param      $oProduct	объект продукта
	 * @param null $oScheme		схема продукта (если передать схему - экономит запросы к бд)
	 * @return mixed
	 */
	public function MyGetProductItemsByTypeMoreFromProductCategories($oProduct, $oScheme = null) {
		if (is_null($oScheme)) {
			$oScheme = $oProduct->getScheme();
		}
		/*
		 * если у продукта нет категорий
		 */
		if (!$oProduct->getCategoriesIds()) {
			return null;
		}
		return $this->PluginSimplecatalog_Myorm_GetItemsByJoin(array(
			/*
			 * данные для кастомного орм модуля
			 */
			PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product_categories'),
			PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'product_id',
			/*
			 * условия орм лс
			 */
			'#where' => array(
				/*
				 * исключение текущего продукта
				 */
				'a.`scheme_id` = ?d AND a.`moderation` IN (?a) AND a.`id` <> ?d AND b.`category_id` IN (?a)' => array(
					$oScheme->getId(),
					array(self::MODERATION_DONE),
					$oProduct->getId(),
					$oProduct->getCategoriesIds()
				)
			),
			/*
			 * показать максимум 5 продуктов
			 */
			'#limit' => array(0, 5),
			'#order' => $this->aDefaultSortOrderBy,
			'#with' => array('scheme'),
		), array($this));
	}


	/*
	 *
	 * --- Для сущности поля продукта ---
	 *
	 */

	/**
	 * Получить поле продукта по ид
	 *
	 * @param $iId			ид поля продукта
	 * @return mixed		объект поля продукта
	 */
	public function MyGetFieldsById($iId) {
		return $this->GetFieldsById($iId);
	}


	/**
	 * Получить поле продукта по ид продукта и ид поля схемы
	 *
	 * @param $iProductId	ид продукта
	 * @param $iFieldId		ид поля схемы
	 * @return mixed		объект поля продукта
	 */
	public function MyGetFieldsByProductIdAndFieldId($iProductId, $iFieldId) {
		return $this->GetFieldsByProductIdAndFieldId($iProductId, $iFieldId);
	}
	

	/**
	 * Получить поля продукта в отсортированном порядке согласно полям схемы
	 *
	 * @param $oProduct		объект продукта
	 * @return array
	 */
	public function MyGetProductFieldsItemsSortedByProduct($oProduct) {
		/*
		 * tip: в новом orm лс есть сортировка по списку ид (9.10.2013)
		 */
		/*
		 * получить поля продукта как есть, без сортировки
		 */
		$aProductFields = $this->GetFieldsItemsByProductId($oProduct->getId());
		/*
		 * получить поля схемы (они уже отсортированы модулем схемы)
		 */
		$aSchemeFields = $oProduct->getScheme()->getFields();
		/*
		 * выполнить сортировку
		 */
		$aSortedProductFields = array();
		foreach ($aSchemeFields as $oSchemeField) {
			/*
			 * проверка есть ли поле схемы у продукта (может быть что поле добавлено в схему после того как был создан продукт)
			 */
			if ($oProductField = $this->_GetProductFieldByIdFromArray($oSchemeField->getId(), $aProductFields)) {
				/*
				 * добавить поле схемы к полю продукта как связанные данные (для более быстрого доступа к полю схемы без ещё одного запроса к БД)
				 */
				$oProductField->setField($oSchemeField);
				$aSortedProductFields[] = $oProductField;
			}
		}
		return $aSortedProductFields;
	}
	

	/**
	 * Получить поле продукта из массива полей продукта по ид поля схемы (удаляя само поле продукта после его нахождения из массива полей продукта)
	 *
	 * @param $iId					ид поля схемы
	 * @param $aProductFields		массив полей продукта (будет уменьшаться после каждого найденного поля)
	 * @return mixed|bool			объект поля продукта
	 */
	private function _GetProductFieldByIdFromArray($iId, &$aProductFields) {
		foreach ($aProductFields as $iKey => $oProductField) {
			if ($oProductField->getFieldId() == $iId) {
				/*
				 * удалить из массива уже найденные поля продукта для увеличения быстродействия при повторном поиске следующего поля
				 */
				unset($aProductFields[$iKey]);
				return $oProductField;
			}
		}
		return false;
	}


	/**
	 * Получить ОДНО первое попавшееся поле продукта по ид поля схемы
	 * tip: этот метод нужен для получения старого типа контента поля, не нужно больше нигде использовать этот метод
	 *
	 * @param $iFieldId		ид поля схемы
	 * @return mixed
	 */
	public function MyGetFieldsByFieldId($iFieldId) {
		/*
		 * ОДНО ПОЛЕ
		 */
		return $this->GetFieldsByFieldId($iFieldId);
	}


	/**
	 * Получить поля продуктов по ид поля схемы
	 *
	 * @param $iFieldId		ид поля схемы
	 * @return array		сущности полей продуктов
	 */
	public function MyGetFieldsItemsByFieldId($iFieldId) {
		return $this->GetFieldsItemsByFieldId($iFieldId);
	}


	/**
	 * Удалить поле у каждого продукта по полю схемы
	 *
	 * @param $oField		объект поля схемы
	 * @return mixed
	 */
	public function DeleteFieldsFromProductsBySchemeField($oField) {
		foreach($this->MyGetFieldsItemsByFieldId($oField->getId()) as $oProductField) {
			$oProductField->Delete();
		}
	}


	/*
	 *
	 * --- Специальные ---
	 *
	 */

	/**
	 * Удалить все загруженнные файлы продукта
	 *
	 * @param $oProduct		объект продукта
	 */
	public function DeleteAllFilesByProduct($oProduct) {
		foreach ($oProduct->getProductFields() as $oProductField) {
			if ($oProductField->getField()->getFieldType() == PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE and $sContent = $oProductField->getContent()) {
				$this->DeleteFileAndAdditionalDataByPathAndProductField($sContent, $oProductField);
			}
		}
	}


	/**
	 * Удалить файл и связанные данные по полному пути и сущности поля продукта (которому принадлежит данный файл)
	 *
	 * @param string $sPath            полный путь к файлу
	 * @param        $oProductField    сущность поля продукта файла
	 */
	public function DeleteFileAndAdditionalDataByPathAndProductField($sPath, $oProductField) {
		/*
		 * удалить файл
		 */
		$this->PluginSimplecatalog_File_RemoveFile($sPath);
		/*
		 * удалить данные доступа
		 */
		if (Config::Get('plugin.simplecatalog.product.build_safe_and_hashed_links_for_file_downloads')) {
			$this->DeleteHashedFileACLData($sPath);
		}
		$this->Hook_Run('sc_product_field_file_delete', array('sPath' => $sPath, 'oProductField' => $oProductField));
	}


	/**
	 * Получить новый корректный и неиспользуемый урл (для ЧПУ) для продукта
	 *
	 * @param $sProductUrl			указанный пользователем урл
	 * @param $aProductFields		поля продукта
	 * @param $oProduct				продукт
	 * @return string
	 */
	public function MyGetValidAndFreeProductUrl($sProductUrl, $aProductFields, $oProduct) {
		$sUrl = null;
		/*
		 * если урл указал пользователь
		 */
		if ($sProductUrl) {
			$sUrl = $sProductUrl;
		} elseif (isset($aProductFields[0])) {
			/*
			 * если есть контент у первого поля продукта
			 */
			$sUrl = $aProductFields[0]->getDisplayValue();
			/*
			 * возможно это файл, нельзя использовать
			 * tip: передается только первая очередь, которая без файлов, поэтому этого не случится
			 */
			if (is_array($sUrl)) {
				$sUrl = func_generator(16);
			}
		} else {
			/*
			 * использовать случайный урл
			 */
			$sUrl = func_generator(16);
		}
		$sUrl = sc_str_translit($sUrl);
		/*
		 * проверить длину урла
		 */
		if (strlen($sUrl) < Config::Get('plugin.simplecatalog.product.min_product_url_length')) {
			$sUrl .= func_generator(16);
		}
		/*
		 * если другой продукт с таким урлом уже есть - добавлять случайный постфикс к урлу и проверять на занятость
		 */
		while ($oFoundProduct = $this->MyGetProductByProductUrl($sUrl) and $oFoundProduct->getId() != $oProduct->getId()) {
			$sUrl .= '-' . func_generator(16);
		}
		return $sUrl;
	}


	/**
	 * Получить количество заполненных полей продукта
	 *
	 * @param $aProductFields		две очереди объектов полей продукта
	 * @param $aValuesOriginal		оригинальные значения полей
	 * @return int					количество заполненных полей
	 */
	public function MyGetFilledFieldsCount($aProductFields, $aValuesOriginal) {
		$iCount = 0;
		foreach($aProductFields as $sQueueKey => $aQueue) {
			foreach($aQueue as $iKey => $oProductField) {
				$mContent = $oProductField->getContent();
				/*
				 * проверить на пустое значение с проверкой типа
				 */
				if (in_array($mContent, array(null, '', false), true)) {
					continue;
				}
				/*
				 * выполнять подсчет на основе типа поля
				 */
				switch($oProductField->getField()->getFieldType()) {
					/*
					 * для файла проверить загружен ли новый файл или есть ранее загруженный файл
					 * tip: для второй очереди значений в контенте указан массив файла
					 */
					case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
						if (!$this->PluginSimplecatalog_File_IsFileUploaded($mContent) and !$aValuesOriginal[$sQueueKey][$iKey]) {
							continue 2;
						}
						break;
					/*
					 * не установленные флажки не считать
					 */
					case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX:
						if ($mContent === 0) {
							continue 2;
						}
					/*
					 * для все остальных полей достаточно базовой проверки
					 */
					default:
				}
				$iCount ++;
			}
		}
		return $iCount;
	}


	/**
	 * Удалить все изображения продукта
	 *
	 * @param $oProduct			сущность продукта
	 */
	public function DeleteAllProductImagesByProduct($oProduct) {
		$aImages = $this->PluginSimplecatalog_Images_MyGetImageItemsSortedByTargetIdAndTargetType($oProduct->getId(), PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
		foreach($aImages as $oImage) {
			$oImage->Delete();
		}
	}


	/*
	 *
	 * --- Группы первых букв первых полей продуктов (для поиска) ---
	 *
	 */

	/**
	 * Получить уникальные все первые буквы полей продуктов по ид поля схемы и с указанным типом модерации продукта (для групп букв поиска)
	 *
	 * @param int $iFieldId    ид поля схемы у полей продуктов, у которых нужно получать первые буквы
	 * @param int $iModeration тип модерации продуктов
	 * @return array           уникальный массив всех первых букв полей продуктов указанного ид поля схемы
	 */
	protected function MyGetFirstLetterGroupsRawBySchemeFieldIdAndJoinProductModeration($iFieldId, $iModeration) {
		$sCacheKey = 'simplecatalog_product_get_first_letter_groups_' . serialize(func_get_args());
		if (($mData = $this->Cache_Get($sCacheKey)) === false) {
			$aLettersRaw = $this->oMapper->GetFirstLetterGroupsRawBySchemeFieldIdAndJoinProductModeration($iFieldId, $iModeration, $this->GetContentTypesPointingOnTableContentTypeFields());
			/*
			 * проверенные буквы
			 */
			$mData = array();
			foreach($aLettersRaw as $sLetter) {
				/*
				 * нужны только буквы и цифры
				 */
				if (!preg_match('#^[' . $this->sAllowedLettersForProductGroups . ']$#iu', $sLetter)) {
					continue;
				}
				$mData[] = $sLetter;
			}
			/*
			 * теги кеша (продукты и их поля)
			 */
			$aTags = array(
				/*
				 * имена тегов кеша орм для продуктов
				 */
				'PluginSimplecatalog_ModuleProduct_EntityProduct_save',
				'PluginSimplecatalog_ModuleProduct_EntityProduct_delete',
				/*
				 * имена тегов кеша орм для полей продуктов
				 */
				'PluginSimplecatalog_ModuleProduct_EntityFields_save',
				'PluginSimplecatalog_ModuleProduct_EntityFields_delete',
			);
			$this->Cache_Set($mData, $sCacheKey, $aTags, 60 * 60 * 24 * 7);
		}
		return $mData;
	}


	/**
	 * Получить группы первых букв заголовков (первых полей) продуктов схемы (для поиска по ним)
	 * tip: вся обработка перенесена в БД чтобы не получать большое количество сущностей первых полей
	 *
	 * @param $oScheme		объект схемы
	 * @return array
	 */
	public function MyGetProductFirstLetterGroupsByScheme($oScheme) {
		/*
		 * нужно только первое поле, которое является заголовком
		 */
		if (!$oSchemeFirstField = $this->PluginSimplecatalog_Scheme_MyGetFirstFieldByScheme($oScheme)) {
			/*
			 * если у схемы нет ни одного поля - нету и групп букв
			 */
			return array();
		}
		/*
		 * получить все уникальные первые буквы заголовков продуктов
		 */
		return $this->MyGetFirstLetterGroupsRawBySchemeFieldIdAndJoinProductModeration($oSchemeFirstField->getId(), self::MODERATION_DONE);
	}


	/*
	 *
	 * --- Сравнение продуктов ---
	 *
	 */

	/**
	 * Получить данные сравнений для всех схем
	 *
	 * @return array
	 */
	protected function GetCompareProductsData() {
		return (array) $this->Session_Get(self::COMPARE_PRODUCTS_SESSION_KEY);
	}


	/**
	 * Сохранить данные сравнений для всех схем
	 *
	 * @param array $aData	данные
	 */
	protected function SetCompareProductsData($aData) {
		$this->Session_Set(self::COMPARE_PRODUCTS_SESSION_KEY, $aData);
	}


	/**
	 * Добавить запись продукта в сравнение в разрезе схемы продукта
	 *
	 * @param $oProduct		сущность продукта
	 * @param $oScheme		сущность схемы добавляемого продукта
	 */
	public function AddProductToCompareProductsList($oProduct, $oScheme = null) {
		$iSchemeId = $oScheme ? $oScheme->getId() : $oProduct->getScheme()->getId();
		/*
		 * получить все записи
		 */
		$aData = $this->GetCompareProductsData();
		/*
		 * для указанной схемы добавить новую запись в список id продуктов сравнения
		 */
		if (!isset($aData[$iSchemeId]) or !in_array($oProduct->getId(), $aData[$iSchemeId])) {
			$aData[$iSchemeId][] = $oProduct->getId();
		}
		/*
		 * сохранить данные
		 */
		$this->SetCompareProductsData($aData);
	}


	/**
	 * Удалить продукт из списка сравнения его схемы
	 *
	 * @param $oProduct		объект продукта
	 */
	public function RemoveProductFromCompareProductsList($oProduct) {
		/*
		 * получить все записи
		 */
		$aData = $this->GetCompareProductsData();
		/*
		 * удалить id продукта из списка его схемы
		 */
		unset($aData[$oProduct->getScheme()->getId()][array_search($oProduct->getId(), $aData[$oProduct->getScheme()->getId()])]);
		/*
		 * переназначить ключи, хак для аякса: если ключ не начинается с нуля, то json_encode в вьюере передает не массив, а объект.
		 * исправлено в product_compare.js, в CompareAjaxRequestResponseHandler()
		 */
		//$aData[$oProduct->getScheme()->getId()] = array_values($aData[$oProduct->getScheme()->getId()]);
		/*
		 * сохранить данные
		 */
		$this->SetCompareProductsData($aData);
	}


	/**
	 * Получить массив id продуктов для сравнения по схеме
	 *
	 * @param $oScheme		объект схемы
	 * @return array		ид продуктов схемы для сравнения
	 */
	public function GetCompareProductsIdsForScheme($oScheme) {
		$aData = $this->GetCompareProductsData();
		return array_key_exists($oScheme->getId(), $aData) ? $aData[$oScheme->getId()] : array();
	}


	/**
	 * Добавить продукт и получить ид продуктов одной схемы если есть два или больше продукта в сравнении или ложь если это первое добавление
	 *
	 * @param $oProduct			продукт, который нужно добавить в сравнение
	 * @return array|bool		массив ид продуктов или ложь, если продукт добавлен первым
	 */
	public function GetDataForComparingProductsInScheme($oProduct) {
		/*
		 * сохранить запись
		 */
		$this->AddProductToCompareProductsList($oProduct);
		/*
		 * если уже есть продукты для сравнения у данной схемы и их два или больше
		 */
		if ($aCompareProductIds = $this->GetInCompareListAreAtLeastTwoProducts($oProduct)) {
			return $aCompareProductIds;
		}
		/*
		 * нет данных или первая запись о сравнении
		 */
		return false;
	}


	/**
	 * Добавлен ли указанный продукт в список сравнения
	 *
	 * @param $oProduct		объект продукта
	 * @return bool
	 */
	public function GetProductAlreadyInCompareList($oProduct) {
		/*
		 * получить все записи
		 */
		$aData = $this->GetCompareProductsData();
		/*
		 * есть ли ид продукта в списке продуктов сравнения его схемы
		 */
		return isset($aData[$oProduct->getScheme()->getId()]) and in_array($oProduct->getId(), $aData[$oProduct->getScheme()->getId()]);
	}


	/**
	 * Проверить есть ли в списке сравнения как минимум два продукта (одной схемы)
	 *
	 * @param $oProduct		объект продукта
	 * @return array|bool	массив ид продуктов сравнения или ложь если продуктов меньше двух
	 */
	public function GetInCompareListAreAtLeastTwoProducts($oProduct) {
		if ($aCompareProductIds = $this->GetCompareProductsIdsForScheme($oProduct->getScheme()) and count($aCompareProductIds) > 1) {
			return $aCompareProductIds;
		}
		return false;
	}


	/**
	 * Сравнить массив продуктов на совпадение значений в полях продуктов
	 *
	 * @param $aProducts	массив продуктов
	 * @return array		массив, в котором по порядку указано одинаковое ли каждое поле из набора продуктов или нет
	 */
	public function GetBoolArrayWithComparedProductFields($aProducts) {
		$oScheme = reset($aProducts)->getScheme();
		/*
		 * массив с данными о совпадении значений
		 */
		$aData = array();
		/*
		 * по всем полям схемы
		 */
		foreach($oScheme->getFieldsWOFirstField() as $iKey => $oSchemeField) {
			/*
			 * флаг что поля одинаковые
			 */
			$bFieldsAreEqual = true;
			/*
			 * значение поля первого продукта (будет сравниваться с последующими продуктами)
			 */
			$mFirstFieldValue = null;
			/*
			 * сравнить поле схемы у каждого продукта
			 */
			foreach($aProducts as $oProduct) {
				$aFields = $oProduct->getProductFieldsWOFirstField();
				if (is_null($mFirstFieldValue)) {
					$mFirstFieldValue = $aFields[$iKey]->getContent();
				} else {
					if ($mFirstFieldValue != $aFields[$iKey]->getContent()) {
						$bFieldsAreEqual = false;
					}
				}
			}
			/*
			 * добавить запись про это поле
			 */
			$aData[$iKey] = $bFieldsAreEqual;
		}
		return $aData;
	}


	/**
	 * Достигнут ли лимит продуктов для сравнения для схемы
	 *
	 * @param $oScheme			сущность схемы
	 * @return bool
	 */
	public function GetCompareListProductsLimitExceedByScheme($oScheme) {
		return count($this->GetCompareProductsIdsForScheme($oScheme)) >= self::COMPARE_PRODUCTS_MAX_COUNT;
	}


	/*
	 *
	 * --- Получение значения поля из формы ---
	 *
	 */

	/**
	 * Получить значение поля формы на основе поля схемы, описывающего это значение и массивов данных POST и FILES
	 *
	 * @param $oField			поле схемы
	 * @param $aProductRawData	данные продукта из реквеста
	 * @param $aFilesRawData	реверсный массив файлов
	 * @return array			массив со значением, очередью и флагом значения как текста
	 */
	public function GetFieldValueInfoFromRawData($oField, $aProductRawData, $aFilesRawData) {
		/*
		 * тип очереди по-умолчанию (должна обрабатываться сразу после сохранения продукта)
		 */
		$sQueue = self::PRODUCT_FIELDS_SAVE_FIRST_QUEUE;
		/*
		 * получить сырое значение из реквеста
		 */
		$mValue = isset($aProductRawData[$oField->getId()]) ? $aProductRawData[$oField->getId()] : null;
		/*
		 * можно ли обрабатывать данное значение как текст (скалярное ли это значение)
		 */
		$bCanBeParsedAsText = true;

		/*
		 *
		 * произвести дополнительную обработку значения в зависимости от типа поля
		 *
		 */
		switch($oField->getFieldType()) {
			/*
			 * это файл, который должен быть получен из другого массива
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
				if (isset($aFilesRawData[$oField->getId()])) {
					$mValue = $aFilesRawData[$oField->getId()];
					/*
					 * вторая очередь (должен быть сохранен после первой очереди)
					 */
					$sQueue = self::PRODUCT_FIELDS_SAVE_SECOND_QUEUE;
					/*
					 * нельзя обрабатывать как текст т.к. это массив с данными
					 */
					$bCanBeParsedAsText = false;
				}
				break;
			/*
			 * это чекбокс
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX:
				/*
				 * который не был включен на форме
				 */
				if (is_null($mValue)) {
					$mValue = 0;
				}
				break;
			/*
			 * это селект
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT:
				/*
				 * для множественного селекта
				 */
				if ($oField->getSelectMultipleItemsEnabled()) {
					/*
					 * не было выбрано ни одного значения
					 */
					if (is_null($mValue)) {
						$mValue = '';
					} else {
						/*
						 * упаковать массив выбранных элементов в строку для сохранения
						 * tip: разделители в начале и в конце упакованного значения нужны для корректного поиска по фильтру
						 */
						$mValue = $oField->getStringValueForMultipleSelectFromArrayOfIndexes($mValue);
					}
				}
				break;
			/*
			 * это обычный текст
			 */
			default:
				/*
				 * значение должно быть текстом или считать что значения нет
				 */
				if (!is_scalar($mValue)) {
					$mValue = null;
				}
				break;
		}
		/*
		 * вернуть результат
		 */
		return array(
			'value' => $mValue,
			'queue' => $sQueue,
			'can_be_parsed_as_text' => $bCanBeParsedAsText
		);
	}


	/*
	 *
	 * --- Типы контента таблицы полей продукта ---
	 *
	 */

	/**
	 * Получить тип контента для поля продукта (для сохранения значения в соответствующее поле таблицы полей продукта) на основе поля схемы
	 *
	 * @param $oSchemeField			объект поля схемы
	 * @return int
	 * @throws Exception
	 */
	public function GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($oSchemeField) {
		/*
		 * если для поля схемы указан валидатор
		 */
		if ($oSchemeField->getValidator() and $aValidatorData = $this->PluginSimplecatalog_Validator_GetValidatorDataByKey($oSchemeField->getValidator())) {
			/*
			 * получить тип поля продукта на основе указанного типа в валидаторе поля схемы
			 */
			switch ($aValidatorData['value_type']) {
				case 'int':
					return self::FIELD_TYPE_INT;
				case 'float':
					return self::FIELD_TYPE_FLOAT;
				case 'varchar':
					return self::FIELD_TYPE_VARCHAR;
				case 'text':
					return self::FIELD_TYPE_TEXT;
				default:
					throw new Exception('SC: error: unknown validator type "' . $aValidatorData['value_type'] . '" set in config, ' . __METHOD__);
			}
		}
		/*
		 * получить тип поля продукта на основе базового типа поля схемы
		 */
		switch ($oSchemeField->getFieldType()) {
			/*
			 * строка (в схеме строка указана как "text")
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXT:
				return self::FIELD_TYPE_VARCHAR;
			/*
			 * многострочный текст
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXTAREA:
				return self::FIELD_TYPE_TEXT;
			/*
			 * файл
			 * tip: если файл будет переделан на отдельное хранилище - можно использовать целый тип для ид записи файла
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
				return self::FIELD_TYPE_VARCHAR;
			/*
			 * чекбокс
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX:
				return self::FIELD_TYPE_INT;
			/*
			 * нередактируемое поле
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE:
				return self::FIELD_TYPE_VARCHAR;
			/*
			 * селект (индексы значений)
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT:
				return self::FIELD_TYPE_VARCHAR;
			/*
			 * исключение если тип поля схемы неизвестен
			 */
			default:
				throw new Exception('SC: error: unknown scheme field type "' . $oSchemeField->getFieldType() . '" in ' . __METHOD__);
		}
	}


	/**
	 * Добавить поля со значениями по-умолчанию поля схемы в продукты с указанными ид
	 *
	 * @param $aProductsIds		ид продуктов
	 * @param $oField			объект поля схемы
	 */
	public function AddDefaultFieldValueForListedProducts($aProductsIds, $oField) {
		/*
		 * получить тип контента
		 */
		$iContentType = $this->GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($oField);
		/*
		 * получить массив всех типов контента
		 */
		$aContentTypes = $this->GetContentTypesPointingOnTableContentTypeFields();
		$this->oMapper->AddDefaultFieldValueForListedProducts($aProductsIds, $oField, $iContentType, $aContentTypes);
	}


	/**
	 * Получить массив типов контента в качестве ключей, указывающих на соответствующие поля контента таблицы полей продукта
	 *
	 * @return array
	 */
	public function GetContentTypesPointingOnTableContentTypeFields() {
		return array(
			self::FIELD_TYPE_INT => 'content_int',
			self::FIELD_TYPE_FLOAT => 'content_float',
			self::FIELD_TYPE_VARCHAR => 'content_varchar',
			self::FIELD_TYPE_TEXT => 'content_text'
		);
	}


	/**
	 * Получить имя поля контента таблицы полей продукта по типу контента
	 *
	 * @param int $iContentType		тип контента поля продукта
	 * @return null
	 */
	public function GetContentTypeTableFieldNameByContentType($iContentType) {
		$aData = $this->GetContentTypesPointingOnTableContentTypeFields();
		return isset($aData[$iContentType]) ? $aData[$iContentType] : null;
	}


	/**
	 * Получить геттер или сеттер для поля с контентом таблицы полей продукта по типу метода (сеттер или геттер) и типу контента
	 * (для получения прямого доступа к полю таблицы с данными)
	 *
	 * @param string $sMethodType  тип метода (сеттер или геттер)
	 * @param int    $iContentType тип контента
	 * @return string          	   строка с названием метода
	 * @throws Exception
	 */
	public function GetEntityMethodNameToOperateWithContentField($sMethodType, $iContentType) {
		switch ($sMethodType) {
			case 'set':
			case 'get':
				return $sMethodType . func_camelize($this->GetContentTypeTableFieldNameByContentType($iContentType));
			default:
				throw new Exception('SC: error: unknown method type "' . $sMethodType . '" in ' . __METHOD__);
		}
	}


	/*
	 *
	 * --- Миграция контента полей продуктов ---
	 *
	 */

	/**
	 * Получить старый тип контента поля продукта по полю схемы
	 *
	 * @param $oField			объект поля схемы
	 * @return mixed
	 */
	public function GetCurrentContentTypeOfProductFieldBySchemeField($oField) {
		/*
		 * получить одно поле первого попавшегося продукта по ид поля схемы
		 */
		if ($oProductField = $this->MyGetFieldsByFieldId($oField->getId())) {
			/*
			 * получить старый тип контента
			 */
			return $oProductField->getContentType();
		}
		return null;
	}


	/**
	 * Выполнить процесс миграции контента из одного поля таблицы полей продукта в другое по полю схемы, тип которого был изменен
	 *
	 * @param $oField			объект поля схемы
	 */
	public function PerformDataMigrationFromOneTableFieldToAnother($oField) {
		/*
		 * процесс может быть долгим
		 */
		@set_time_limit(0);
		ignore_user_abort(true);
		/*
		 * получить новый тип контента
		 */
		$iNewContentType = $this->GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($oField);
		/*
		 * получить старый тип контента
		 */
		$iOldContentType = $this->GetCurrentContentTypeOfProductFieldBySchemeField($oField);
		/*
		 * получить массив всех типов контента указывающих на соответствующие поля таблицы
		 */
		$aContentTypes = $this->GetContentTypesPointingOnTableContentTypeFields();
		/*
		 * запустить процесс
		 */
		$this->oMapper->PerformDataMigrationFromOneTableFieldToAnother($oField, $iOldContentType, $iNewContentType, $aContentTypes);
		/*
		 * очистить кеш полей продуктов
		 */
		$sEntityFullRoot = 'PluginSimplecatalog_ModuleProduct_EntityFields';
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($sEntityFullRoot . '_save', $sEntityFullRoot . '_delete'));
	}


	/*
	 *
	 * --- Работа с категориями продукта ---
	 *
	 */

	/**
	 * Добавить массив ид категорий продукту
	 *
	 * @param $oProduct				объект продукта
	 * @param $aCategoriesIdsNew	массив ид категорий
	 */
	public function AddCategoriesToProduct($oProduct, $aCategoriesIdsNew) {
		/*
		 * для уменьшения количества продуктов у старых категорий
		 */
		$aCategoriesIdsOld = $oProduct->getCategoriesIds();

		/*
		 * только новые (добавленные) категории, без учета ранее установленных
		 */
		$aCategoriesIdsNeedToAdd = array_diff($aCategoriesIdsNew, $aCategoriesIdsOld);
		/*
		 * категории, которые следует удалить у продукта
		 */
		$aCategoriesIdsNeedToDelete = array_diff($aCategoriesIdsOld, $aCategoriesIdsNew);

		/*
		 * удалить старые записи категорий
		 */
		$this->DeleteCategoriesByArrayIdForProduct($oProduct, $aCategoriesIdsNeedToDelete);
		/*
		 * уменьшить счетчики количества продуктов старых категорий
		 */
		$this->DecreaseCategoriesItemsCount($aCategoriesIdsNeedToDelete);

		/*
		 * добавить список новых категорий
		 */
		$this->AddCategoriesByArrayIdForProduct($oProduct, $aCategoriesIdsNeedToAdd);
		/*
		 * увеличить счетчики количества продуктов новых категорий
		 */
		$this->IncreaseCategoriesItemsCount($aCategoriesIdsNeedToAdd);
	}


	/**
	 * Получить связи категорий с продуктом по ид продукта и массиву ид категорий
	 *
	 * @param $iProductId		ид продукта
	 * @param $aCategoriesIds	массив ид категорий
	 * @return mixed			массив сущностей связей
	 */
	protected function MyGetCategoriesItemsByProductIdAndCategoryIdIn($iProductId, $aCategoriesIds) {
		return $this->GetCategoriesItemsByProductIdAndCategoryIdIn($iProductId, $aCategoriesIds);
	}


	/**
	 * Удалить категории по массиву их ид у продукта
	 *
	 * @param $oProduct			объект продукта
	 * @param $aCategoriesIds	массив ид категорий, которые следует удалить
	 */
	protected function DeleteCategoriesByArrayIdForProduct($oProduct, $aCategoriesIds) {
		if (!empty($aCategoriesIds)) {
			$aCategoriesLinks = $this->MyGetCategoriesItemsByProductIdAndCategoryIdIn($oProduct->getId(), $aCategoriesIds);
			foreach($aCategoriesLinks as $oLink) {
				$oLink->Delete();
			}
		}
	}


	/**
	 * Добавить категории по массиву их ид продукту
	 *
	 * @param $oProduct			объект продукта
	 * @param $aCategoriesIds	массив ид категорий, которые следует добавить
	 * @throws Exception
	 */
	protected function AddCategoriesByArrayIdForProduct($oProduct, $aCategoriesIds) {
		foreach($aCategoriesIds as $iCategoryId) {
			$oEnt = Engine::GetEntity('PluginSimplecatalog_Product_Categories');
			$oEnt->setProductId($oProduct->getId());
			$oEnt->setCategoryId($iCategoryId);
			/*
			 * валидация внесенных данных
			 */
			if (!$oEnt->_Validate()) {
				throw new Exception('SC: error: ' . $oEnt->_getValidateError() . ' in ' . __METHOD__);
			}
			$oEnt->Save();
		}
	}


	/**
	 * Уменьшить счетчик продуктов на 1 для массива ид категорий
	 *
	 * @param $aCategoriesIds	массив ид категорий
	 */
	public function DecreaseCategoriesItemsCount($aCategoriesIds) {
		foreach($aCategoriesIds as $iCategoryId) {
			$this->PluginSimplecatalog_Category_DecreaseItemsCountForCategoryById($iCategoryId);
		}
	}


	/**
	 * Увеличить счетчик продуктов на 1 для массива ид категорий
	 *
	 * @param $aCategoriesIds	массив ид категорий
	 */
	protected function IncreaseCategoriesItemsCount($aCategoriesIds) {
		foreach($aCategoriesIds as $iCategoryId) {
			$this->PluginSimplecatalog_Category_IncreaseItemsCountForCategoryById($iCategoryId);
		}
	}


	/**
	 * Проверка категорий продукта по их ид (на существование; чтобы указанные категории были конечными, без субкатегорий, если это указано в конфиге)
	 *
	 * @param $aCategoriesIds	массив ид категорий
	 * @return bool|string		true или текст ошибки
	 */
	public function CheckCategoriesIdsAreCorrect($aCategoriesIds) {
		/*
		 * проверить существуют ли категории по всем указанным ид
		 */
		if (!$aCategories = $this->PluginSimplecatalog_Category_GetCategoriesArrayIdExists($aCategoriesIds)) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found');
		}
		/*
		 * если указано использовать для продуктов только конечные категории
		 */
		if (Config::Get('plugin.simplecatalog.categories.product_categories_should_not_have_child_categories')) {
			foreach($aCategories as $oCategory) {
				/*
				 * если категория имеет дочерние (вложенные категории)
				 */
				if ($oCategory->getChildren()) {
					return $this->Lang_Get('plugin.simplecatalog.Errors.categories.select_only_with_no_subcategories', array('name' => $oCategory->getName()));
				}
			}
		}
		return true;
	}


	/*
	 *
	 * --- Получение данных для построения фильтра продуктов ---
	 *
	 */

	/**
	 * Подготовить данные для фильтра по продуктам для отображения в форме по схеме
	 *
	 * @param $oScheme		объект схемы
	 * @return array		массив данных с сущностью поля, типом отображаемого поля и его параметрами
	 * @throws Exception	неизвестный тип поля схемы
	 */
	public function PrepareProductFilterToDisplayFieldsInFormForScheme($oScheme) {
		$aFields = $oScheme->getFields();
		/*
		 * массив содержащий настройки отображения фильтра
		 */
		$aFieldsOptions = array();
		foreach($aFields as $oField) {
			/*
			 * нужно получить только те поля, по которым нужно искать и, следовательно, поля для которых нужно отобразить
			 */
			if (!$oField->getAllowedToSearchIn()) continue;
			/*
			 * получить тип контента для поля продукта
			 */
			$iContentType = $oField->getContentTypeForProductField();
			/*
			 * в зависимости от типа поля получить нужное отображение
			 */
			switch($oField->getFieldType()) {
				/*
				 * строка
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXT:
					$aOptions = $this->PrepareProductFilterDataForText($oField, $iContentType);
					break;
				/*
				 * многострочное поле ввода
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXTAREA:
					$aOptions = $this->PrepareProductFilterDataForTextarea($oField, $iContentType);
					break;
				/*
				 * файл
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
					$aOptions = $this->PrepareProductFilterDataForFile($oField, $iContentType);
					break;
				/*
				 * флажок
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_CHECKBOX:
					$aOptions = $this->PrepareProductFilterDataForCheckbox($oField, $iContentType);
					break;
				/*
				 * нередактируемое поле
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE:
					$aOptions = $this->PrepareProductFilterDataForNotEditable($oField, $iContentType);
					break;
				/*
				 * селект
				 */
				case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT:
					$aOptions = $this->PrepareProductFilterDataForSelect($oField, $iContentType);
					break;
				/*
				 * неизвестный тип поля
				 */
				default:
					throw new Exception('SC: error: unknown field type "' . $oField->getFieldType() . '" in ' . __METHOD__);
			}
			/*
			 * нужно ли добавлять это поле (например, если для чисел мин. значение === макс., то слайдер не нужен т.к. выбирать нечего)
			 */
			if ($aOptions === false) {
				continue;
			}
			$aFieldsOptions[$oField->getId()] = array(
				'field' => $oField,
				'options' => $aOptions
			);
		}
		return $aFieldsOptions;
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_TEXT"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 * @throws Exception		если неверный тип контента
	 */
	protected function PrepareProductFilterDataForText($oField, $iContentType) {
		switch ($iContentType) {
			/*
			 * это целое число
			 */
			case self::FIELD_TYPE_INT:
				$iMin = $this->GetMinValueForField($oField);
				$iMax = $this->GetMaxValueForField($oField);
				/*
				 * если мин. значение === макс. - не показывать фильтр для такого поля т.к. нечего выбирать
				 */
				if ($iMin === $iMax) {
					return false;
				}
				return array(
					'type' => self::FILTER_DISPLAY_TYPE_NUMBER,
					'params' => array(
						'min' => $iMin,
						'max' => $iMax,
						'accuracy' => '1',
					),
				);
			/*
			 * это дробное число
			 */
			case self::FIELD_TYPE_FLOAT:
				$iMin = $this->GetMinValueForField($oField);
				$iMax = $this->GetMaxValueForField($oField);
				/*
				 * если мин. значение === макс. - не показывать фильтр для такого поля т.к. нечего выбирать
				 */
				if ($iMin === $iMax) {
					return false;
				}
				return array(
					'type' => self::FILTER_DISPLAY_TYPE_NUMBER,
					'params' => array(
						'min' => $iMin,
						'max' => $iMax,
						'accuracy' => '0.1',
					),
				);

			/*
			 * это текстовые поля
			 */
			case self::FIELD_TYPE_VARCHAR:
			case self::FIELD_TYPE_TEXT:
				/*
				 * обычный ввод
				 */
				return array(
					'type' => self::FILTER_DISPLAY_TYPE_STRING,
					'params' => array(
						'maxlength' => $oField->getTextMaxLength(),
					),
				);
			default:
				throw new Exception('SC: error: unknown content type "' . $iContentType . '" for field type "' . $oField->getFieldType() . '" in ' . __METHOD__);
		}
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_TEXTAREA"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 */
	protected function PrepareProductFilterDataForTextarea($oField, $iContentType) {
		/*
		 * обычный ввод
		 */
		return array(
			'type' => self::FILTER_DISPLAY_TYPE_STRING,
			'params' => array(
				'maxlength' => $oField->getTextareaMaxLength(),
			),
		);
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_FILE"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 */
	protected function PrepareProductFilterDataForFile($oField, $iContentType) {
		/*
		 * обычный ввод
		 */
		return array(
			'type' => self::FILTER_DISPLAY_TYPE_STRING,
			'params' => array(
				'maxlength' => 200,
			),
		);
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_CHECKBOX"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 */
	protected function PrepareProductFilterDataForCheckbox($oField, $iContentType) {
		/*
		 * чекбокс (целое число)
		 */
		return array(
			'type' => self::FILTER_DISPLAY_TYPE_CHECKBOX,
			'params' => array(),
		);
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_NOT_EDITABLE"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 */
	protected function PrepareProductFilterDataForNotEditable($oField, $iContentType) {
		/*
		 * включено ли отображение заголовков в фильтре
		 */
		if (!Config::Get('plugin.simplecatalog.product.show_not_editable_fields_in_product_filter')) {
			return false;
		}
		/*
		 * не редактируемое поле (заголовок)
		 */
		return array(
			'type' => self::FILTER_DISPLAY_TYPE_TITLE,
			'params' => array(),
		);
	}


	/**
	 * Получить данные для отображения фильтра по продуктам для поля схемы типа "FIELD_TYPE_SELECT"
	 *
	 * @param $oField			объект поля схемы
	 * @param $iContentType		тип контента поля схемы для поля продукта
	 * @return array
	 */
	protected function PrepareProductFilterDataForSelect($oField, $iContentType) {
		/*
		 * селект (строка)
		 */
		return array(
			'type' => self::FILTER_DISPLAY_TYPE_SELECT,
			'params' => array(),
		);
	}


	/*
	 *
	 * --- Получение агрегирующих данных полей продуктов ---
	 *
	 */

	/**
	 * Получить данные с помощью агрегирующей фукнции по указанному полю таблицы поля схемы промодерированных продуктов
	 *
	 * @param $sAggrFunction	имя агрегирующей mysql функции
	 * @param $sFieldName		имя поля таблицы для функции
	 * @param $oField			объект поля схемы
	 * @return int|null
	 */
	private function GetAggregateDataFromProductFields($sAggrFunction, $sFieldName, $oField) {
		return $this->oMapper->GetAggregateDataFromProductFields($sAggrFunction, $sFieldName, $oField, self::MODERATION_DONE);
	}


	/**
	 * Получить минимальное число среди полей промодерированных продуктов по полю схемы
	 *
	 * @param $oField			объект поля схемы
	 * @return int|null
	 */
	protected function GetMinValueForField($oField) {
		/*
		 * получить тип контента поля продукта для поля схемы
		 */
		$iContentType = $oField->getContentTypeForProductField();
		/*
		 * получить имя поля в таблице
		 */
		$sContentFieldName = $this->GetContentTypeTableFieldNameByContentType($iContentType);
		/*
		 * вызвать агрегирующую функцию
		 */
		return $this->GetAggregateDataFromProductFields('MIN', $sContentFieldName, $oField);
	}


	/**
	 * Получить максимальное число среди полей промодерированных продуктов по полю схемы
	 *
	 * @param $oField			объект поля схемы
	 * @return int|null
	 */
	protected function GetMaxValueForField($oField) {
		/*
		 * получить тип контента поля продукта для поля схемы
		 */
		$iContentType = $oField->getContentTypeForProductField();
		/*
		 * получить имя поля в таблице
		 */
		$sContentFieldName = $this->GetContentTypeTableFieldNameByContentType($iContentType);
		/*
		 * вызвать агрегирующую функцию
		 */
		return $this->GetAggregateDataFromProductFields('MAX', $sContentFieldName, $oField);
	}


	/*
	 *
	 * --- Получение продуктов по полям фильтра из запроса ---
	 *
	 */

	/**
	 * Получить продукты и общее количество по значениям полей фильтра продуктов
	 *
	 * @param       $aFieldsFromRequest		поля фильтра продукта
	 * @param		$aCategories			массив категорий
	 * @param       $iModeration			тип модерации
	 * @param       $oScheme				объект схемы
	 * @param       $iPage					страница
	 * @param       $iPerPage				количество на страницу
	 * @param array $aSortOrder				сортировка
	 * @return mixed
	 */
	public function MyGetProductItemsByProductFilterFields($aFieldsFromRequest, $aCategories, $iModeration, $oScheme, $iPage, $iPerPage, $aSortOrder = array()) {
		/*
		 * получить ид всех категорий и дерева каждой категории включительно, если указано в конфиге
		 */
		$aCategoriesIds = array();
		foreach($aCategories as $oCategory) {
			$aCategoriesIds[] = $oCategory->getId();
			/*
			 * нужно ли учитывать у категории всю ветку субкатегорий при поиске
			 */
			if (Config::Get('plugin.simplecatalog.product.add_all_category_tree_for_product_filter')) {
				$aCategoriesIds = array_merge($aCategoriesIds, $oCategory->getDescendingCategoriesIds());
			}
		}
		/*
		 * получить уникальные ид всех категорий (т.к. могли выбрать ветки категорий так, что одна входит в другую)
		 */
		$aCategoriesIds = array_unique($aCategoriesIds);
		/*
		 * получить массив условий для построения WHERE запроса
		 */
		$aWhereConditions = $this->GatherRequestProductFilterData($aFieldsFromRequest, $oScheme);
		/*
		 * выполнить запрос
		 *
		 * tip: если есть значения из фильтра для поиска - поиск выполняется без указания ид схемы т.к. ид полей схемы - сквозные и однозначно идентифицируют схему.
		 * 		но если ни одно значение не указано для поиска (пустой фильтр), то подзапрос будет исключен и будет добавлено правило для ид схемы таблицы продуктов
		 */
		return $this->oMapper->GetProductItemsByProductFilterFields($aWhereConditions, $aCategoriesIds, $iModeration, $oScheme, $iPage, $iPerPage, $aSortOrder);
	}


	/**
	 * Получить массив условий для построения WHERE условия sql подзапроса по массиву "сырых" полей фильтра продуктов из реквеста для схемы
	 *
	 * @param $aFieldsFromRequest	массив полей из реквеста фильтра продуктов
	 * @param $oScheme				объект схемы
	 * @return array				массив условий для построения sql запроса WHERE
	 * @throws Exception			при неизвестном типе отображаемого поля на форме
	 */
	protected function GatherRequestProductFilterData($aFieldsFromRequest, $oScheme) {
		/*
		 * получить опции для отображения фильтра
		 */
		$aFieldsOptions = $this->PrepareProductFilterToDisplayFieldsInFormForScheme($oScheme);
		/*
		 * массив условий WHERE для запроса
		 */
		$aWhereConditions = array();
		/*
		 * пройтись по всем полям из реквеста
		 */
		foreach($aFieldsFromRequest as $iKey => $mValueFromRequest) {
			$aCondition = array();
			/*
			 * есть ли поле с таким ид в опциях для отображения фильтра
			 */
			if (!array_key_exists($iKey, $aFieldsOptions)) continue;
			/*
			 * указано ли значение по которому нужно искать
			 * tip: у селекта может быть индекс == 0
			 */
			if ($mValueFromRequest === '') continue;

			/*
			 * сущность поля схемы
			 */
			$oField = $aFieldsOptions[$iKey]['field'];
			/*
			 * опции отображаемого поля
			 */
			$aFieldOptionsCurrent = $aFieldsOptions[$iKey]['options'];
			/*
			 * тип отображаемого поля
			 */
			$sFieldType = $aFieldOptionsCurrent['type'];
			/*
			 * имя поля в таблице
			 */
			$sFieldTableName = $this->GetContentTypeTableFieldNameByContentType($oField->getContentTypeForProductField());

			/*
			 * в зависимости от типа отображаемых данных - собрать данные
			 */
			switch($sFieldType) {
				/*
				 * диапазон чисел (слайдер)
				 */
				case self::FILTER_DISPLAY_TYPE_NUMBER:
					/*
					 * для чисел должны быть границы от и до
					 */
					if (!is_array($mValueFromRequest) or count($mValueFromRequest) != 2) continue 2;
					$aCondition = array(
						'type' => 'between',
						'value' => array(array_shift($mValueFromRequest), array_shift($mValueFromRequest))
					);
					break;
				/*
				 * поле ввода
				 */
				case self::FILTER_DISPLAY_TYPE_STRING:
					if (!is_string($mValueFromRequest)) continue 2;
					$aCondition = array(
						'type' => 'regexp',
						'value' => array($mValueFromRequest)
					);
					break;
				/*
				 * флажок (вкл/выкл)
				 */
				case self::FILTER_DISPLAY_TYPE_CHECKBOX:
					/*
					 * всегда 1 т.к. если чекбокс не установлен - его нету в реквесте
					 */
					$aCondition = array(
						'type' => 'equal',
						'value' => array(1)
					);
					break;
				/*
				 * не редактируемое поле (заголовок)
				 */
				case self::FILTER_DISPLAY_TYPE_TITLE:
					/*
					 * никак не обрабатывать это поле - это заголовок
					 * tip: данное поле не передается в реквесте,
					 * 		данный код нужен чтобы не сработало исключение ниже если кто-то подставит в запрос данные этого поля (скрытый инпут с ид поля-заголовка)
					 */
					continue 2;
				/*
				 * селект/мультиселект
				 */
				case self::FILTER_DISPLAY_TYPE_SELECT:
					/*
					 * для множественного селекта индексы хранятся через ";;" в varchar столбце (и ";" по бокам)
					 * и выполняется проверка на вхождение указанных индексов среди строки индексов селекта в продукте
					 *
					 * т.е. "table_row LIKE '%;1;%;3;%;5;%'" где table_row = ';0;;1;;3;;5;'
					 */
					if ($oField->getSelectMultipleItemsEnabled()) {
						/*
						 * если некорректный формат данных или если выбран пункт "не выбрано" (равен пустой строке) - игнорировать такое условие
						 */
						if (!is_array($mValueFromRequest) or in_array('', $mValueFromRequest)) continue 2;
						$sSelectItemsDelimiter = $oField->getSelectFilterItemsUsingAndLogicEnabled() ? '.*' : '|';
						$aCondition = array(
							'type' => 'regexp',
							/*
							 * tip: если нужно использовать отдельную таблицу для значений селектов, то чтение и запись можно выполнять через методы
							 * getArrayOfIndexesForMultipleSelectFromStringValue и getStringValueForMultipleSelectFromArrayOfIndexes поля схемы напрямую в таблицу значений селектов,
							 * возвращая ид записи, например, а здесь прописать условие IN на выборку ид совпавших записей (ид вернет getStringValueForMultipleSelectFromArrayOfIndexes)
							 */
							'value' => array($oField->getStringValueForMultipleSelectFromArrayOfIndexes($mValueFromRequest, ';' . $sSelectItemsDelimiter . ';'))
						);
					} else {
						/*
						 * у обычного селекта только один индекс
						 */
						$aCondition = array(
							'type' => 'equal',
							'value' => array($mValueFromRequest)
						);
					}
					break;
				/*
				 * неизвестный тип отображения для поля
				 */
				default:
					throw new Exception('SC: error: unknown display type "' . $sFieldType . '" in ' . __METHOD__);
			}
			$aWhereConditions[] = array(
				'field_name' => $sFieldTableName,
				'condition' => $aCondition,
				'field_id' => $oField->getId(),
			);
		}	// /foreach
		return $aWhereConditions;
	}


	/*
	 *
	 * --- Комментарии ---
	 *
	 */

	/**
	 * Получить комментарии продукта
	 *
	 * @param $oProduct		объект продукта
	 */
	public function LoadProductComments($oProduct) {
		if (!Config::Get('module.comment.nested_page_reverse') and Config::Get('module.comment.use_nested') and Config::Get('module.comment.nested_per_page')) {
			$iPageDefault = ceil(
				$this->Comment_GetCountCommentsRootByTargetId($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT) / Config::Get('module.comment.nested_per_page')
			);
		} else {
			$iPageDefault = 1;
		}
		$iPage = getRequest('cmtpage', 0) ? (int) getRequest('cmtpage', 0) : $iPageDefault;
		$aReturn = $this->Comment_GetCommentsByTargetId($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT, $iPage, Config::Get('module.comment.nested_per_page'));
		$iMaxIdComment = $aReturn['iMaxIdComment'];
		$aComments = $aReturn['comments'];
		/*
		 * если постраничная навигация для комментариев включена
		 */
		if (Config::Get('module.comment.use_nested') and Config::Get('module.comment.nested_per_page')) {
			$aPaging = $this->Viewer_MakePaging(
				$aReturn['count'],
				$iPage,
				Config::Get('module.comment.nested_per_page'),
				Config::Get('pagination.pages.count'),
				/*
				 * tip: в лс 1.0.3 забыли про параметр sBaseUrl в постраничной навигации по комментариям, поэтому выставление этого параметра ни на что не влияет для комментариев,
				 * 		урл для страницы комментариев начинается на "?cmtpage=2" без полного пути, указанного здесь
				 */
				$oProduct->getItemShowWebPath()
			);
			if (!Config::Get('module.comment.nested_page_reverse') and $aPaging) {
				/*
				 * перестроение страниц в обратном порядке
				 */
				$aPaging['aPagesLeft'] = array_reverse($aPaging['aPagesLeft']);
				$aPaging['aPagesRight'] = array_reverse($aPaging['aPagesRight']);
			}
			$this->Viewer_Assign('aPagingCmt', $aPaging);
		}

		$this->Viewer_Assign('aComments', $aComments);
		$this->Viewer_Assign('iMaxIdComment', $iMaxIdComment);
	}


	/*
	 *
	 * --- Доступ к файлам ---
	 *
	 */

	/**
	 * Получить зашифрованную ссылку для скачивания файла только для текущего пользователя
	 *
	 * @param $oProductField		сущность поля продукта
	 * @return string				шифрованная ссылка на экшен продуктов
	 */
	public function GetSafeAndHashedFileUrlForCurrentUserAccessOnly($oProductField) {
		/*
		 * если файл не загружен
		 */
		if (!$sFilename = $oProductField->getContent()) {
			return false;
		}
		$sHash = md5($sFilename);
		/*
		 * сохранить ссылку и правила доступа к ней в сессии
		 */
		$_SESSION[self::SESSION_PF_FILE_ACLS][$sHash] = array(
			'ip' => func_getIp(),
			'date' => date('Y-m-d H:i:s'),
			'product_field_id' => $oProductField->getId(),
		);
		/*
		 * создать ссылку на экшен продуктов
		 */
		return Router::GetPath('product') . 'file/get/' . $sHash . '/';
	}


	/**
	 * Получить данные доступа к файлу
	 *
	 * @param string $sHash		хеш файла
	 * @return Entity|null
	 */
	public function GetHashedFileDownloadACLData($sHash) {
		return isset($_SESSION[self::SESSION_PF_FILE_ACLS][$sHash]) ? Engine::GetEntity('PluginSimplecatalog_Product_HashedFile', $_SESSION[self::SESSION_PF_FILE_ACLS][$sHash]) : null;
	}


	/**
	 * Вышло ли время жизни для указанной даты (для файлов)
	 *
	 * @param $sDate		дата из записи файла
	 * @return bool
	 */
	public function GetHashedFileTimeIsUp($sDate) {
		return strtotime('+' . ((int) Config::Get('plugin.simplecatalog.product.safe_and_hashed_links_lifetime_days')) . ' day', strtotime($sDate)) < time();
	}


	/**
	 * Удалить данные доступа к файлу
	 *
	 * @param $sFilename		полный путь и имя файла
	 */
	protected function DeleteHashedFileACLData($sFilename) {
		$sHash = md5($sFilename);
		unset($_SESSION[self::SESSION_PF_FILE_ACLS][$sHash]);
	}


	/*
	 *
	 * --- Связи между продуктами ---
	 *
	 */

	/**
	 * Получить продукты в шаблон по настройкам связей схемы для каждой связи для выбора в селектах
	 *
	 * @param        $oScheme                    сущность схемы
	 * @param        $oUserCurrent               сущность пользователя для выбора "своих продуктов"
	 * @param        $oExcludeProduct            продукт для исключения из получаемых продуктов
	 * @throws Exception
	 */
	public function AssignProductDataByLinksSettings($oScheme, $oUserCurrent, $oExcludeProduct = null) {
		/*
		 * получить настройки связей для схемы
		 */
		if ($aLinkSettings = $oScheme->getActiveLinkSettingsSorted()) {
			/*
			 * все наборы связей схемы с данными для выбора
			 */
			$aSchemeLinksData = array();
			/*
			 * по каждым настройкам связи схемы
			 */
			foreach($aLinkSettings as $oLinkSettings) {
				/*
				 * если указан продукт для исключения - добавить условие для орм
				 */
				$aExcludeParams = $oExcludeProduct ? array('#where' => array('id <> ?d' => array($oExcludeProduct->getId()))) : array();
				/*
				 * возможность выбора всех продуктов
				 */
				if ($oLinkSettings->getSelectTypeAll()) {
					/*
					 * получить список всех продуктов и общее количество
					 */
					$aProductsData = $this->MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
						array(
							self::MODERATION_DONE,
						),
						$oLinkSettings->getTargetScheme(),
						1,
						$oLinkSettings->getProductsCountToSelect(),
						$this->GetDefaultProductSortingOrder(),
						$aExcludeParams
					);

				/*
				 * выбор только своих продуктов
				 */
				} elseif ($oLinkSettings->getSelectTypeSelf()) {
					/*
					 * получить продукты этого пользователя и общее количество
					 */
					$aProductsData = $this->MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
						array(
							self::MODERATION_DONE
						),
						$oUserCurrent,
						$oLinkSettings->getTargetScheme(),
						1,
						$oLinkSettings->getProductsCountToSelect(),
						$this->GetDefaultProductSortingOrder(),
						$aExcludeParams
					);

				/*
				 * тип неизвестен
				 */
				} else {
					throw new Exception('SC: error: unknown select type "' . $oLinkSettings->getSelectType() . '" in ' . __METHOD__);
				}
				/*
				 * если продуктов нет - не добавлять выбор связи (нечего выбирать)
				 */
				if (!$aProductsData['count']) {
					continue;
				}
				$aSchemeLinksData[] = array(
					'aProductsData' => $aProductsData,
					'oLinkSettings' => $oLinkSettings,
				);
			}
			$this->Viewer_Assign('aSchemeLinksData', $aSchemeLinksData);
		}
	}


	/**
	 * Создать связи для продукта по указанному массиву ид для связей для каждой настройки связи
	 *
	 * @param $oScheme												сущность схемы
	 * @param $oProduct												продукт для которого задаются связи
	 * @param $aGroupsOfProductsIdsToLinkRaw						"сырые" ид продуктов для связи по ид каждой настроенной связи схемы
	 */
	public function AddLinksToProductByRawPostData($oScheme, $oProduct, $aGroupsOfProductsIdsToLinkRaw) {
		/*
		 * получить настройки связей для схемы
		 */
		if ($aLinkSettings = $oScheme->getActiveLinkSettingsSorted()) {
			/*
			 * по каждым настройкам связи схемы
			 */
			foreach($aLinkSettings as $oLinkSettings) {
				/*
				 * есть ли данные для этой связи
				 * tip: для обычного селекта (выбор одного продукта) всегда будут данные, для флажков если не выбрано ничего - данных не будет
				 */
				$mProductsIdsToLink = isset($aGroupsOfProductsIdsToLinkRaw[$oLinkSettings->getId()]) ? $aGroupsOfProductsIdsToLinkRaw[$oLinkSettings->getId()] : array();
				/*
				 * проверить количество подаваемых продуктов для связи в зависимости от типа связи
				 */
				if (!$this->GetIsCorrectInputProductCountByLinkSettings($oLinkSettings, $mProductsIdsToLink)) {
					continue;
				}
				/*
				 * для удобства
				 */
				$aProductsIdsToLink = (array) $mProductsIdsToLink;
				/*
				 * убрать пустые значения (не выбранные связи) и дубли (если вручную добавить дубли ид)
				 */
				$aProductsIdsToLink = array_unique(array_filter($aProductsIdsToLink));
				/*
				 * получить существующие связи для данной настройке связи
				 */
				$aLinksCurrent = $this->PluginSimplecatalog_Links_MyGetProductLinkItemsByParentSchemeLinkSettingsAndProduct(
					$oLinkSettings,
					$oProduct,
					array('#index-from' => 'to_target_id')
				);
				$aProductsIdsCurrent = array_keys($aLinksCurrent);
				/*
				 * связи, которые добавились
				 */
				$aIdsToAdd = array_diff($aProductsIdsToLink, $aProductsIdsCurrent);
				/*
				 * связи, которые нужно удалить
				 */
				$aIdsToDelete = array_diff($aProductsIdsCurrent, $aProductsIdsToLink);
				/*
				 * создать новые связи
				 */
				$this->AddProductLinksToProductIdsList($oProduct, $oLinkSettings, $aIdsToAdd);
				/*
				 * удалить старые связи
				 */
				foreach($aIdsToDelete as $iIdToDelete) {
					$aLinksCurrent[$iIdToDelete]->Delete();
				}
			}
		}
	}


	/**
	 * Проверить количество связываемых продуктов (один или несколько) по настройки связи схемы
	 *
	 * @param $oLinkSettings			настройки связи схемы
	 * @param $mLinkedProductsIds		ид продукта (-ов)
	 * @return bool
	 */
	protected function GetIsCorrectInputProductCountByLinkSettings($oLinkSettings, $mLinkedProductsIds) {
		/*
		 * если связь 1 к 1
		 */
		if ($oLinkSettings->getTypeHasOne()) {
			/*
			 * нет ли ошибки
			 */
			if (!is_scalar($mLinkedProductsIds)) {
				return false;
			}

		/*
		 * если связь 1 ко многим
		 */
		} elseif ($oLinkSettings->getTypeHasMany()) {
			/*
			 * нет ли ошибки
			 */
			if (!is_array($mLinkedProductsIds)) {
				return false;
			}
		}
		return true;
	}


	/**
	 * Проверить привязываемый продукт по настройкам связи схемы
	 *
	 * @param $oProductToLink			продукт, который нужно проверить перед привязкой
	 * @param $oLinkSettings			настройки связи схемы
	 * @param $oProduct					продукт, к которому будет добавлен в связи продукт $oProductToLink
	 * @return bool
	 */
	protected function CheckProductToBeLinkedForLinkSettings($oProductToLink, $oLinkSettings, $oProduct) {
		/*
		 * принадлежит ли привязываемый продукт схеме, указанной в настройках связи
		 */
		if ($oProductToLink->getSchemeId() != $oLinkSettings->getTargetSchemeId()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.product_from_another_scheme', array(
				'product_title' => $oProductToLink->getFirstFieldTitle(),
				'link_name' => $oLinkSettings->getDisplayName(),
			)), $this->Lang_Get('error'), true);
			return false;
		}
		/*
		 * если разрешено выбирать для связи только свои продукты - проверить авторов связываемых продуктов
		 */
		if ($oLinkSettings->getSelectTypeSelf()) {
			if ($oProductToLink->getUserId() != $oProduct->getUserId()) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.products_authors_doesnt_match', array(
					'product_title' => $oProductToLink->getFirstFieldTitle(),
					'parent_product_title' => $oProduct->getFirstFieldTitle(),
				)), $this->Lang_Get('error'), true);
				return false;
			}
		}
		/*
		 * проверить привязку продукта самого к себе
		 */
		if ($oProductToLink->getId() == $oProduct->getId()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.product_cant_be_linked_to_itself'), $this->Lang_Get('error'), true);
			return false;
		}
		return true;
	}


	/**
	 * Создать связи для продукта по настройкам связи схемы и списку ид продуктов для привязки
	 *
	 * @param $oProduct							продукт к которому добавляются связи
	 * @param $oLinkSettings					настройки связи схемы
	 * @param $aProductsIdsToLink				ид продуктов, которые нужно привязать
	 */
	protected function AddProductLinksToProductIdsList($oProduct, $oLinkSettings, $aProductsIdsToLink) {
		foreach($aProductsIdsToLink as $iProductIdToLink) {
			if (!$oProductToLink = $this->MyGetActiveSchemeModerationDoneProductById((int) $iProductIdToLink)) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.scheme_links.product_not_found', array('id' => $iProductIdToLink)), $this->Lang_Get('error'), true);
				continue;
			}
			/*
			 * проверить привязываемый продукт по настройкам связи схемы
			 */
			if (!$this->CheckProductToBeLinkedForLinkSettings($oProductToLink, $oLinkSettings, $oProduct)) {
				continue;
			}

			$oEnt = Engine::GetEntity('PluginSimplecatalog_Links_Link');
			$oEnt->setParentType(PluginSimplecatalog_ModuleLinks::PARENT_TYPE_SCHEME_LINKS_SETTINGS);
			$oEnt->setParentId($oLinkSettings->getId());
			$oEnt->setFromTargetType(PluginSimplecatalog_ModuleLinks::TARGET_TYPE_PRODUCTS);
			$oEnt->setFromTargetId($oProduct->getId());
			$oEnt->setToTargetType(PluginSimplecatalog_ModuleLinks::TARGET_TYPE_PRODUCTS);
			$oEnt->setToTargetId($oProductToLink->getId());

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'), true);
				continue;
			}

			$oEnt->Save();
		}
	}


	/**
	 * Получить существующие связи продукта (привязанные другие продукты) и их настройки для каждой настроенной связи схемы
	 * tip: используется в сущности продукта для получения связей и в методе получения связей редактирования продукта
	 *
	 * @param      $oProduct                        сущность продукта
	 * @param bool $bWithProducts                   нужно ли загружать продукты связи через связи сущности одним запросом
	 * @param bool $bFilterForModeratedProductsOnly отфильтровать связи только с промодерированными продуктами
	 * @return array
	 */
	public function GatherProductLinksData($oProduct, $bWithProducts = true, $bFilterForModeratedProductsOnly = true) {
		$oScheme = $oProduct->getScheme();
		/*
		 * получить настройки связей для схемы
		 */
		if ($aLinkSettings = $oScheme->getActiveLinkSettingsSorted()) {
			/*
			 * массив массивов связей продуктов и их настроек с ид настроек связей в качестве ключей
			 */
			$aProductLinksData = array();
			/*
			 * по каждой настройке связи схемы
			 */
			foreach($aLinkSettings as $oLinkSettings) {
				$aProductLinks = $this->PluginSimplecatalog_Links_MyGetProductLinkItemsByParentSchemeLinkSettingsAndProduct(
					$oLinkSettings,
					$oProduct,
					$bWithProducts ? array('#with' => array('product')) : array()
				);
				/*
				 * если нужно получить только промодерированные продукты в связях
				 * tip: только если включена автозагрузка продуктов $bWithProducts (для увеличения быстродействия, не обязательно)
				 */
				if ($bWithProducts and $bFilterForModeratedProductsOnly) {
					$aProductLinks = $this->FilterProductsLinksForModeratedProductsOnly($aProductLinks);
				}
				/*
				 * если связей нет - не добавлять в вывод
				 */
				if (count($aProductLinks) == 0) {
					continue;
				}
				$aProductLinksData[$oLinkSettings->getId()] = array(
					'aProductLinks' => $aProductLinks,
					'oLinkSettings' => $oLinkSettings,
				);
			}
			return $aProductLinksData;
		}
		return array();
	}


	/**
	 * Отфильтровать связи оставив в них только промодерированные продукты
	 *
	 * @param $aProductLinks			массив связей продукта
	 * @return array					новый массив связей продукта
	 */
	private function FilterProductsLinksForModeratedProductsOnly($aProductLinks) {
		/*
		 * связи у которых промодерированные продукты
		 */
		$aProductLinksWithModeratedProducts = array();
		/*
		 * по каждой связи
		 */
		foreach($aProductLinks as $oLink) {
			if ($oLink->getProduct()->getModerationDone()) {
				$aProductLinksWithModeratedProducts[] = $oLink;
			}
		}
		return $aProductLinksWithModeratedProducts;
	}


	/**
	 * Получить массив ид связанных продуктов для продукта по каждой настройке связи схемы
	 * tip: используется для редактирования продукта
	 *
	 * @param $oProduct			продукт, ид продуктов связей которого нужно получить
	 * @return array
	 */
	public function GetProductsLinksIdsGroupedForLinkSettingsByProduct($oProduct) {
		/*
		 * массив связей продукта, где ключ = ид настройки связи схемы, а значения - массив ид продуктов в связи
		 */
		$aLinksIds = array();
		/*
		 * по всем данным связей
		 */
		foreach($this->GatherProductLinksData($oProduct, false, false) as $aProductData) {
			$oLinkSettings = $aProductData['oLinkSettings'];
			$aProductLinks = $aProductData['aProductLinks'];
			/*
			 * по каждой связи
			 */
			foreach($aProductLinks as $oLink) {
				/*
				 * получить ид привязанной цели
				 */
				$aLinksIds[$oLinkSettings->getId()][] = $oLink->getToTargetId();
			}
		}
		return $aLinksIds;
	}


	/*
	 *
	 * --- Заполнение SEO данных ---
	 *
	 */

	/**
	 * Установить для продукта SEO данные
	 *
	 * @param $oProduct				сущность продукта
	 * @param $sTitle				заголовок, заданный пользователем
	 * @param $sDescription			описание, заданное пользователем
	 * @param $sKeywords			ключевые слова, заданные пользователем
	 */
	public function SetSEODataForProduct($oProduct, $sTitle, $sDescription, $sKeywords) {
		$bManualSEO = $oProduct->getScheme()->getAllowEditAdditionalSeoMetaEnabled();

		/*
		 * получить автоматическое описание и ключевые слова
		 */
		list($sAutoDescription, $sAutoKeywords) = $this->GetSeoDescriptionAndKeywordsByProduct($oProduct);

		/*
		 * если не включено ручное заполнение СЕО данных или не указано значение - заполнить автоматическим значением
		 */
		if (!$bManualSEO or !$sTitle) {
			$sTitle = htmlspecialchars_decode($oProduct->getFirstFieldTitle());
		}
		if (!$bManualSEO or !$sDescription) {
			$sDescription = $sAutoDescription;
		}
		if (!$bManualSEO or !$sKeywords) {
			$sKeywords = $sAutoKeywords;
		}

		$oProduct->setSeoTitle(strip_tags($sTitle));
		$oProduct->setSeoDescription(strip_tags($sDescription));
		$oProduct->setSeoKeywords(strip_tags($sKeywords));

		$oProduct->Save();
	}


	/**
	 * Получить автоматически описание и ключевые слова для мета-тегов "description" и "keywords" продукта на основе значений его полей
	 *
	 * @param $oProduct				сущность продукта
	 * @return array				массив, в котором первый элемент - описание, второй - ключевые слова
	 */
	protected function GetSeoDescriptionAndKeywordsByProduct($oProduct) {
		$aDescription = array();
		$aKeywords = array();
		foreach($oProduct->getProductFieldsWOFirstField() as $oProductField) {
			$oField = $oProductField->getField();
			/*
			 * заполнять только значениями из текстовых полей и селектов
			 */
			if (!in_array($oField->getFieldType(), array(
				PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXT,
				PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXTAREA,
				PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT
			))) {
				continue;
			}
			/*
			 * пропускать не заполненные поля
			 */
			if (!$sValue = $oProductField->getDisplayValue()) {
				continue;
			}
			/*
			 * пропускать поля, для просмотра которых нужна авторизация (права доступа)
			 */
			if ($oField->getForAuthUsersOnlyEnabled()) {
				continue;
			}
			/*
			 * описание: нужно ли добавлять заголовок поля перед значением поля
			 */
			$aDescription[] = ($oField->getShowFieldNamesInListEnabled() ? $oField->getTitle() . ': ' : '') . $sValue;
			/*
			 * ключевые слова: нужно выбрать только те поля, для которых нужно выводить заголовок, т.е. это фактически характеристика продукта
			 */
			if ($oField->getShowFieldNamesInListEnabled()) {
				$aKeywords[] = $sValue;
			}
		}
		return array(
			implode('. ', $aDescription),
			implode(', ', $aKeywords),
		);
	}


	/*
	 *
	 * --- Заголовок продукта ---
	 *
	 */

	/**
	 * Значение первого поля продукта (используется как заголовок продукта) или заголовок по-умолчанию
	 *
	 * @param Entity $oProduct      сущность продукта
	 * @param int    $iLengthMax    длина заголовка в символах
	 * @param string $sBreakPostfix добавляемый постфикс на границе превышения длины
	 * @param bool   $bKeepWords    оставлять целыми слова, не разрывая слово посредине
	 * @return string
	 */
	public function MyGetProductTitle($oProduct, $iLengthMax = 150, $sBreakPostfix = '...', $bKeepWords = true) {
		/*
		 * если у продукта есть поля
		 */
		if ($aProductFields = $oProduct->getProductFields()) {
			/*
			 * получить первое поле
			 */
			$oFirstField = reset($aProductFields);
			/*
			 * значение первого поля с максимальной длиной
			 */
			$sTitle = $oFirstField->getDisplayValue(true, $iLengthMax, $sBreakPostfix, $bKeepWords);
			/*
			 * у заголовка не может быть тегов (даже переводов строк, которые разрешены в случае лимитирования длины строки)
			 */
			$sTitle = strip_tags($sTitle);
			/*
			 * заголовок вместе с префиксом и постфиксом
			 */
			if ($sTitle) {
				return $oFirstField->getField()->getValuePrefix() . $sTitle . $oFirstField->getField()->getValuePostfix();
			}
		}
		/*
		 * если ни одного поля не создано и нет, соответственно, заголовка или он не заполнен - показать строку по-умолчанию
		 */
		return $this->Lang_Get('plugin.simplecatalog.Products.Item.no_title');
	}


	/*
	 *
	 * --- Кастомный валидатор ---
	 *
	 */

	/**
	 * Проверить через кастомный валидатор поля схемы значение если: оно задано или поле обязательно к заполнению
	 *
	 * @param $oField			поле схемы
	 * @param $sValue			значение для проверки
	 * @return bool|string		тру или текст ошибки
	 */
	public function CheckValueByCustomFieldValidator($oField, $sValue) {
		/*
		 * если нужно проверить значение кастомным валидатором
		 */
		if ($oField->getValidator()) {
			/*
			 * если поле обязательно к заполнению или есть значение (которое должно быть проверено)
			 */
			if ($oField->getMandatoryEnabled() or $sValue) {
				/*
				 * выполнить проверку
				 */
				if (!$this->PluginSimplecatalog_Validator_IsFieldValidByValidatorIdAndFieldValue($oField->getValidator(), $sValue)) {
					/*
					 * вернуть текст ошибки
					 */
					return $this->Lang_Get('plugin.simplecatalog.validators_list.' . $oField->getValidator() . '.error') . ' "' . $oField->getTitle() . '"';
				}
			}
		}
		/*
		 * значение корректно
		 */
		return true;
	}


	/*
	 *
	 * --- Отложенная публикация ---
	 *
	 */

	/**
	 * Опубликовать все отложенные продукты, у которых настала дата публикации (из всех каталогов)
	 *
	 * @return bool|int		false или количество опубликованных продуктов
	 */
	public function PerformDeferredProductsPublishing() {
		if ($aDeferredProducts = $this->MyGetProductItemsByModerationAndAddDateLteCurrentDate(self::MODERATION_DEFERRED)) {
			foreach($aDeferredProducts as $oDeferredProduct) {
				$oDeferredProduct->setModerationDone();
				$oDeferredProduct->Save();
			}
			return count($aDeferredProducts);
		}
		return false;
	}


	/*
	 *
	 * --- Обертки для удобного вызова ---
	 *
	 */

	/**
	 * Добавить в шаблон список схем и их последних продуктов по типу места отображения последних продуктов (перед контентом топиков или в сайдбаре)
	 *
	 * @param string $sType тип места отображения последних продуктов
	 * @return bool|array	false или массив схем и их продуктов
	 */
	public function AssignSchemesWithLastProductsForActiveSchemesByShowLastProductsType($sType) {
		/*
		 * получить блоки, которые нужно показать
		 */
		if ($aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItemsByBlockShowLastProducts($sType)) {
			/*
			 * список схем и её продуктов
			 */
			$aItems = array();
			/*
			 * получить корректную сортировку
			 */
			$aSortOrder = $this->GetDefaultProductSortingOrder();
			foreach($aSchemes as $oScheme) {
				/*
				 * получить список продуктов и общее количество
				 */
				$aProductsData = $this->MyGetProductItemsByModerationInAndSchemeAndPageAndPerPageAndSortOrder(
					array(
						self::MODERATION_DONE,
					),
					$oScheme,
					1,
					Config::Get('plugin.simplecatalog.product.last_products_items_count'),
					$aSortOrder
				);
				/*
				 * если продуктов нет - не добавлять схему
				 */
				if ($aProductsData['count'] == 0) {
					continue;
				}
				$aItems[] = array('oScheme' => $oScheme, 'aProducts' => $aProductsData['collection']);
			}
			return $aItems;
		}
		return false;
	}


}

?>