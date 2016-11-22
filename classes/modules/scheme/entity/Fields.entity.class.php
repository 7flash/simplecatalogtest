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

class PluginSimplecatalog_ModuleScheme_EntityFields extends EntityORM {

	/*
	 * Разделитель новой строки (часть регулярки)
	 */
	const NEW_LINE_DELIMITER = '\r\n|\r|\n';

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		/*
		 * Основные правила будут добавлены в ExtendValidateRulesWithDefaultFields()
		 */
		
		// text
		array('text_min_length', 'number', 'min' => 0, 'max' => 2000, 'allowEmpty' => false, 'integerOnly' => true, 'on' => array('text')),
		array('text_max_length', 'number', 'min' => 1, 'max' => 2000, 'allowEmpty' => false, 'integerOnly' => true, 'on' => array('text')),
		
		// textarea
		array('textarea_min_length', 'number', 'min' => 0, 'max' => 65535, 'allowEmpty' => false, 'integerOnly' => true, 'on' => array('textarea')),
		array('textarea_max_length', 'number', 'min' => 1, 'max' => 65535, 'allowEmpty' => false, 'integerOnly' => true, 'on' => array('textarea')),
		
		// file
		array('file_max_size', 'number', 'min' => 1, 'max' => 1000000, 'allowEmpty' => false, 'integerOnly' => true, 'on' => array('file')),
		array('file_types_allowed', 'string', 'min' => 1, 'max' => 100, 'allowEmpty' => false, 'on' => array('file')),

		// select
		array('select_items', 'string', 'min' => 1, 'max' => 2000, 'allowEmpty' => false, 'on' => array('select')),
		array('select_multiple_items', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => array('select')),
		array('select_filter_items_using_and_logic', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => array('select')),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		'scheme' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityScheme', 'scheme_id'),
	);


	public function Init() {
		parent::Init();
		$this->ExtendValidateRulesWithDefaultFields();
	}


	/**
	 * Расширить правила валидации поля схемы общими полями таблицы
	 */
	protected function ExtendValidateRulesWithDefaultFields() {
		/*
		 * добавить пустой сценарий и список разрешенных полей
		 */
		$aAllowedFieldTypes = array_merge(array(''), Config::Get('plugin.simplecatalog.scheme.Allowed_Field_Types'));

		/*
		 * Добавление основных правил
		 * Прописаны здесь для легкого добавления типов (из конфига) в автоматическом режиме
		 */
		$this->aValidateRules[] = array('scheme_id', 'sc_method', 'method' => 'PluginSimplecatalog_Scheme_MyGetSchemeById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Scheme_Not_Found', 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('title', 'string', 'min' => 1, 'max' => 500, 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('description', 'string', 'min' => 0, 'max' => 1000, 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('mandatory', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
		/*
		 * разрешить не заполнять код поля (нужен только для прямого доступа к полю продукта по коду в шаблоне)
		 */
		$this->aValidateRules[] = array('code', 'regexp', 'pattern' => '#^[\w]{1,30}$#', 'allowEmpty' => true, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('field_type', 'string', 'min' => 1, 'max' => 50, 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('field_type', 'sc_enum', 'allowed' => Config::Get('plugin.simplecatalog.scheme.Allowed_Field_Types'), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		// -- текст до и после значения
		$this->aValidateRules[] = array('value_prefix', 'string', 'min' => 0, 'max' => 50, 'allowEmpty' => true, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('value_postfix', 'string', 'min' => 0, 'max' => 50, 'allowEmpty' => true, 'on' => $aAllowedFieldTypes);
		// -- для сортировки полей
		$this->aValidateRules[] = array('sorting', 'check_sorting', 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('run_parser', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('validator', 'sc_enum', 'allowed' => array_keys(
			Config::Get('plugin.simplecatalog.validators.list')
		), 'allowEmpty' => true, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('default_value', 'string', 'min' => 1, 'max' => 1000, 'allowEmpty' => true, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('places_to_show_field', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::FIELD_SHOW_ANYWHERE,
			PluginSimplecatalog_ModuleScheme::FIELD_SHOW_IN_PRODUCT_LIST,
			PluginSimplecatalog_ModuleScheme::FIELD_SHOW_ON_PRODUCT_PAGE,
			PluginSimplecatalog_ModuleScheme::FIELD_SHOW_NOWHERE,
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('show_field_names_in_list', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('allow_search_in_this_field', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('for_auth_users_only', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
		$this->aValidateRules[] = array('min_user_rating_to_view', 'number', 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);

		$this->aValidateRules[] = array('editable_by_user', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false, 'on' => $aAllowedFieldTypes);
	}


	/**
	 * Вызывается перед удалением поля схемы
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		/*
		 * процесс может занять некоторое время
		 */
		@set_time_limit(0);
		ignore_user_abort(true);
		/*
		 * удалить данные полей продуктов для этого поля
		 */
		$this->PluginSimplecatalog_Product_DeleteFieldsFromProductsBySchemeField($this);

		return parent::beforeDelete();
	}


	/**
	 * Вызывается после сохранения поля схемы
	 */
	protected function afterSave() {
		/*
		 * если это новое поле
		 */
		if ($this->_isNew()) {
			/*
			 * добавить значение по-умолчанию поля схемы ко всем продуктам
			 */
			$this->PluginSimplecatalog_Scheme_AddFieldWithDefaultValueForAllProducts($this);
		} else {
			/*
			 * это редактирование существующего поля
			 */

			/*
			 * запустить миграцию контента таблицы полей продуктов из одного типа в другой
			 */
			$this->PluginSimplecatalog_Scheme_RunDataMigrationForProductFields($this);
		}
	}


	/*
	 *
	 * --- Валидация ---
	 *
	 */

	/**
	 * Проверить чтобы для поля схемы была указана сортировка
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateCheckSorting($mValue, $aParams) {
		/*
		 * если сортировка не была указана вручную - установить максимальное новое значение (добавлять поле схемы в конец формы)
		 */
		if (!$this->getSorting()) {
			$this->setSorting($this->PluginSimplecatalog_Scheme_GetNextFreeSortingValueForSchemeField($this->getSchemeId()));
		}
		return true;
	}


	/*
	 *
	 * --- Тип контента поля продукта ---
	 *
	 */

	/**
	 * Получить тип контента для поля продукта (константа FIELD_TYPE_* из модуля продуктов)
	 *
	 * @return mixed
	 */
	public function getContentTypeForProductField() {
		return $this->PluginSimplecatalog_Product_GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($this);
	}


	/**
	 * Получить имя поля таблицы полей продукта на основе типа контента поля продукта
	 *
	 * @return string
	 */
	public function getFieldNameOfProductFieldTableByContentType() {
		return $this->PluginSimplecatalog_Product_GetContentTypeTableFieldNameByContentType($this->getContentTypeForProductField());
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Нужно ли показывать поле в указанном месте
	 *
	 * @param $sPlace	тип места
	 * @return bool
	 */
	public function getFieldNeedToBeShownByPlace($sPlace) {
		if (in_array($this->getPlacesToShowField(), array($sPlace, PluginSimplecatalog_ModuleScheme::FIELD_SHOW_ANYWHERE))) {
			return true;
		}
		return false;
	}


	/**
	 * Включен ли поиск по этому полю
	 *
	 * @return bool
	 */
	public function getAllowedToSearchIn() {
		return $this->getAllowSearchInThisField() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нужно ли заполнять обязательно это поле при создании продукта
	 *
	 * @return bool
	 */
	public function getMandatoryEnabled() {
		return $this->getMandatory() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нужно ли отображать заголовок поля при выводе значения
	 *
	 * @return bool
	 */
	public function getShowFieldNamesInListEnabled() {
		return $this->getShowFieldNamesInList() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нужно ли парсить значение поля через парсер Jevix
	 *
	 * @return bool
	 */
	public function getRunParserEnabled() {
		return $this->getRunParser() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Просмотр содержимого поля только для авторизированных пользователей сайта
	 *
	 * @return bool
	 */
	public function getForAuthUsersOnlyEnabled() {
		return $this->getForAuthUsersOnly() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Разрешено ли заполнять/редактировать поле при создании продукта (для создания полей, доступных только программно)
	 *
	 * @return bool
	 */
	public function getEditableByUserEnabled() {
		return $this->getEditableByUser() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Есть ли данные для поля продукта на форме создания продукта (или это скрытое поле или заголовок, которое должно быть заполнено значением по-умолчанию)
	 *
	 * @return bool
	 */
	public function getProductFieldDataAreAvailableOnForm() {
		return $this->getEditableByUserEnabled() and $this->getFieldType() != PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE;
	}


	/*
	 *
	 * --- Для типа поля "select" ---
	 *
	 */

	/**
	 * Получить массив всех значений для типа поля "select"
	 *
	 * @return array
	 */
	public function getDefinedSelectValues() {
		if ($this->getFieldType() == PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT) {
			return preg_split('#' . self::NEW_LINE_DELIMITER . '#uU', $this->getSelectItems(), null, PREG_SPLIT_NO_EMPTY);
		}
		return array();
	}


	/**
	 * Получить значение для типа поля "select" по индексу
	 *
	 * @param int $iIndex		индекс
	 * @return string|null
	 */
	public function getDefinedSelectValueForIndex($iIndex) {
		$aData = $this->getDefinedSelectValues();
		return isset($aData[$iIndex]) ? $aData[$iIndex] : null;
	}


	/**
	 * Получить значения через разделитель для типа поля "select" по массиву индексов
	 *
	 * @param        $aIndexes	массив индексов
	 * @param string $sGlue		строка для склейки значений
	 * @return string
	 */
	public function getDefinedSelectValuesForIndexArray($aIndexes, $sGlue = ', ') {
		return implode($sGlue, array_intersect_key($this->getDefinedSelectValues(), array_flip($aIndexes)));
	}


	/**
	 * Включен ли режим множественного выбора в селекте
	 *
	 * @return bool
	 */
	public function getSelectMultipleItemsEnabled() {
		return $this->getSelectMultipleItems() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Использовать ли для фильтра по продуктам логику поиска "И" для опций мультиселекта или "ИЛИ" в противном случае
	 *
	 * @return bool
	 */
	public function getSelectFilterItemsUsingAndLogicEnabled() {
		return $this->getSelectFilterItemsUsingAndLogic() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Получить массив индексов из строкового представления для множественного селекта
	 *
	 * @param $sValue					сырое строковое представления (с разделителями)
	 * @param string $sDelimiter		строковый разделитель индексов
	 * @param string $sWrapper			оберточный символ (по бокам строки)
	 * @return array
	 */
	public function getArrayOfIndexesForMultipleSelectFromStringValue($sValue, $sDelimiter = ';;', $sWrapper = ';') {
		return explode($sDelimiter, trim($sValue, $sWrapper));
	}


	/**
	 * Получить строковое представление из массива индексов для множественного селекта
	 *
	 * @param $aIndexes					массив индексов
	 * @param string $sDelimiter		строковый разделитель индексов
	 * @param string $sWrapper			оберточный символ (по бокам строки)
	 * @return string					строковое представление (с разделителями)
	 */
	public function getStringValueForMultipleSelectFromArrayOfIndexes($aIndexes, $sDelimiter = ';;', $sWrapper = ';') {
		return $sWrapper . implode($sDelimiter, $aIndexes) . $sWrapper;
	}


	/**
	 * Получить отображаемое значение для любого селекта на основе строки индексов
	 *
	 * @param string $sIndexes 			индексы (из getContent поля продукта)
	 * @param string $sGlue    			разделитель значений для множественного селекта
	 * @return string
	 */
	public function getDisplayValueForSelectFromStringValueIndexes($sIndexes, $sGlue = ', ') {
		/*
		 * если включен множественный выбор
		 */
		if ($this->getSelectMultipleItemsEnabled()) {
			/*
			 * получить массив индексов из строки с индексами
			 */
			$aIndexes = $this->getArrayOfIndexesForMultipleSelectFromStringValue($sIndexes);
			return $this->getDefinedSelectValuesForIndexArray($aIndexes, $this->getValuePostfix() . $sGlue . $this->getValuePrefix());
		}
		return $this->getDefinedSelectValueForIndex($sIndexes);
	}


	/*
	 *
	 * --- Для типа поля "file" ---
	 *
	 */

	/**
	 * Получить (зашифрованную) ссылку для скачивания файла (только для текущего пользователя)
	 *
	 * @param $oProductField		сущность поля продукта
	 * @return string				зашифрованная ссылка или входящее значение
	 */
	public function getFileUrlForCurrentUserAccess($oProductField) {
		$sData = $sContent = $oProductField->getContent();
		/*
		 * нужно ли шифровать ссылки
		 */
		if (Config::Get('plugin.simplecatalog.product.build_safe_and_hashed_links_for_file_downloads')) {
			$sData = $this->PluginSimplecatalog_Product_GetSafeAndHashedFileUrlForCurrentUserAccessOnly($oProductField);
		}
		/*
		 * в хеш тег добавлять имя файла для отображения в имени ссылки
		 */
		return $sData ? $sData . '#' . basename($sContent) : '';
	}


	/**
	 * Получить количество скачиваний файла поля продукта
	 *
	 * @param $oProductField		сущность поля продукта
	 * @return bool|int
	 */
	public function getFileDownloadsCount($oProductField) {
		if (Config::Get('plugin.simplecatalog.product.count_file_downloads') and $oCounter = $this->PluginSimplecatalog_Counter_MyGetCounterByProductField($oProductField)) {
			return $oCounter->getCount();
		}
		return false;
	}


	/*
	 *
	 * --- Для типа поля "text" ---
	 *
	 */

	/**
	 * Включен ли для типа поля "строка" валидатор типа "дата"
	 *
	 * @return bool
	 */
	public function getValidatorTypeIsDate() {
		return $this->getFieldType() == PluginSimplecatalog_ModuleScheme::FIELD_TYPE_TEXT and $this->getValidator() == 'date';
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить урл редактирования поля
	 *
	 * @return string
	 */
	public function getEditWebPath() {
		return Router::GetPath('field') . 'edit/' . $this->getId();
	}


	/**
	 * Получить урл удаления поля
	 *
	 * @return string
	 */
	public function getDeleteWebPath() {
		return Router::GetPath('field') . 'delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}

}

?>