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

class PluginSimplecatalog_ModuleScheme_EntityScheme extends EntityORM {

	/*
	 * Ручное кеширование полей схемы, т.к. вызов не через связи, а через обычный метод
	 */
	private $aSchemeFields = array();

	/*
	 * Старый урл схемы, заполняется всегда перед сохранением схемы чтобы потом обновить урлы в прямом эфире (после сохранения)
	 */
	private $sSchemeUrlOld = null;


	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),
		array('id', 'sc_method', 'method' => 'PluginSimplecatalog_Scheme_MyGetSchemeById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Scheme_Not_Found', 'allowEmpty' => true),

		array('scheme_url', 'regexp', 'pattern' => '#^[\w]{1,50}$#', 'allowEmpty' => false),
		array('scheme_url', 'scheme_url'),

		array('scheme_name', 'string', 'min' => 2, 'max' => 100, 'allowEmpty' => false),

		array('description', 'string', 'min' => 0, 'max' => 2000, 'allowEmpty' => false),
		array('keywords', 'string', 'min' => 0, 'max' => 1000, 'allowEmpty' => false),

		array('active', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('menu_add_topic_create', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('menu_main_add_link', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('short_view_fields_count', 'number', 'min' => 0, 'max' => 1000, 'allowEmpty' => false, 'integerOnly' => true),

		array('allow_comments', 'check_allow_comments'),
		array('allow_user_friendly_url', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('allow_edit_additional_seo_meta', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('can_add_products', 'check_can_add_products'),
		array('moderation_needed', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),
		array('show_first_letter_groups', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),
		array('profile_show_last_products', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),
		array('profile_show_created_products', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),
		array('show_online_comments', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),
		array('min_user_rating_to_create_products', 'number', 'allowEmpty' => false),
		array('days_author_can_manage_products_after_last_editing', 'number', 'min' => 0, 'max' => 65535, 'allowEmpty' => false, 'integerOnly' => true),

		/*
		 * для сортировки схем (порядка их вывода)
		 */
		array('sorting', 'check_sorting'),

		array('items_per_page', 'number', 'min' => 0, 'max' => 500, 'allowEmpty' => false, 'integerOnly' => true),
		array('what_to_show_on_items_page', 'check_what_to_show_on_items_page'),
		/*
		 * изображения
		 */
		array('max_images_count', 'number', 'min' => 0, 'max' => 50, 'allowEmpty' => false, 'integerOnly' => true),
		array('image_width', 'number', 'min' => 100, 'max' => 2000, 'allowEmpty' => false, 'integerOnly' => true),
		array('image_height', 'number', 'min' => 100, 'max' => 2000, 'allowEmpty' => false, 'integerOnly' => true),
		array('exact_image_proportions', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('shop', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),

		array('block_show_last_products', 'block_show_last_products'),

		array('allow_drafts', 'number', 'min' => 1, 'max' => 2, 'allowEmpty' => false),

		/*
		 * карты
		 */
		array('map_items', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('select_preset_for_map_items', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('map_items_max', 'number', 'min' => 1, 'max' => 65535, 'allowEmpty' => false, 'integerOnly' => true),

		array('allow_deferred_products', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('allow_count_views', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		/*
		 * шаблоны
		 */
		array('template_name_first', 'template_name'),
		array('template_name_second', 'template_name'),
		array('use_first_template_as_default', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		/*
		 * заменён на вариант с сортировкой
		 */
		//'fields' => array(EntityORM::RELATION_TYPE_HAS_MANY, 'PluginSimplecatalog_ModuleScheme_EntityFields', 'scheme_id'),
		'link_settings' => array(EntityORM::RELATION_TYPE_HAS_MANY, 'PluginSimplecatalog_ModuleScheme_EntityLink', 'scheme_id'),
	);


	/**
	 * Вызывается перед удалением схемы
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
		 * удалить комментарии из прямого эфира
		 */
		$this->Comment_DeleteOnlineCommentsByTargetType($this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->getSchemeUrl()));
		$this->Database_removeEnumType('prefix_comment_online', 'target_type', $this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->getSchemeUrl()));

		/*
		 * удалить настройки связей: схемы и где схема является привязанной (и, соответственно, сами связи схемы)
		 */
		$this->PluginSimplecatalog_Scheme_MyDeleteAllSchemeLinkSettingsByScheme($this);

		/*
		 * удалить продукты схемы (комментарии продуктов и другие данные)
		 */
		foreach($this->PluginSimplecatalog_Product_MyGetProductItemsByScheme($this) as $oProduct) {
			/*
			 * удалить продукт (все связанные данные будут удалены через автоматически вызываемый обработчик в сущности)
			 */
			$oProduct->Delete();
		}

		/*
		 * удалить категории схемы (выбирать только корневые категории, а те удалят всю свою ветку)
		 */
		foreach($this->PluginSimplecatalog_Category_MyGetRootCategoriesByTargetTypeAndTargetId(PluginSimplecatalog_ModuleCategory::TARGET_TYPE_SCHEME, $this->getId()) as $oCategory) {
			$oCategory->Delete();
		}

		/*
		 * удалить группы прав схемы, а те удалят все связи каждой группы прав с пользователями
		 */
		foreach($this->PluginSimplecatalog_Usergroup_MyGetUsergroupItemsBySchemeId($this->getId()) as $oUserGroup) {
			$oUserGroup->Delete();
		}

		/*
		 * удалить поля схемы
		 */
		foreach($this->getFields() as $oField) {
			$oField->Delete();
		}

		return parent::beforeDelete();
	}


	/**
	 * Выполняется после удаления сущности
	 */
	protected function afterDelete() {
		/*
		 * tip: сущность уже удалена из БД
		 */
		$this->Hook_Run('sc_scheme_delete_after', array('oScheme' => $this));
	}


	/**
	 * Выполняется перед сохранением сущности
	 *
	 * @return bool
	 */
	protected function beforeSave() {
		if ($this->getId()) {
			/*
			 * получить сущность старой схемы для сравнения scheme_url и изменения перечисления типов в таблице прямого эфира
			 * tip: схема всегда будет найдена если будет пройдена валидация сущности
			 */
			if (!$oSchemeOld = $this->PluginSimplecatalog_Scheme_MyGetSchemeById($this->getId())) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
				return false;
			}
			$this->sSchemeUrlOld = $oSchemeOld->getSchemeUrl();
		}
		return true;
	}


	/**
	 * Выполяется после сохранения сущности
	 */
	protected function afterSave() {
		/*
		 * добавить новый тип перечисления для прямого эфира для: новой схемы или если изменен урл существующей схемы
		 */
		if ($this->sSchemeUrlOld != $this->getSchemeUrl()) {
			$this->Database_addEnumType('prefix_comment_online', 'target_type', $this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->getSchemeUrl()));
		}
		/*
		 * сравнить старый и новый урл схемы для установки корректного типа для прямого эфира
		 */
		if ($this->sSchemeUrlOld and $this->sSchemeUrlOld != $this->getSchemeUrl()) {
			/*
			 * перенести комментарии со старого на новый урл схемы
			 */
			$this->Comment_TransferOnlineCommentsTargetTypeFromOldToNew(
				$this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->sSchemeUrlOld),
				$this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->getSchemeUrl())
			);
			$this->Database_removeEnumType('prefix_comment_online', 'target_type', $this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($this->sSchemeUrlOld));
		}
		$this->Hook_Run('sc_scheme_add_after', array('oScheme' => $this));
	}


	/*
	 *
	 * --- Валидаторы ---
	 *
	 */

	/**
	 * Проверить чтобы для схемы была указана сортировка
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateCheckSorting($mValue, $aParams) {
		/*
		 * если сортировка не была указана вручную - установить максимальное новое значение (добавлять схему в конец списка)
		 */
		if (!$this->getSorting()) {
			$this->setSorting($this->PluginSimplecatalog_Scheme_GetNextFreeSortingValueForScheme());
		}
		return true;
	}


	/**
	 * Проверить разрешение на комментарии к продуктам схемы
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateCheckAllowComments($mValue, $aParams) {
		if (!in_array($mValue, array(
			PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_FORCED_TO_ALLOW,
			PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_DENY,
			PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_USER_DEFINED
		))) return $this->Lang_Get('plugin.simplecatalog.Errors.Incorrect_Allow_Comments_Value');
		return true;
	}


	/**
	 * Проверить разрешение кто может публиковать продукт (базовое условие)
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateCheckCanAddProducts($mValue, $aParams) {
		if (!in_array($mValue, array(
			PluginSimplecatalog_ModuleScheme::CAN_ADD_PRODUCTS_ADMINS,
			PluginSimplecatalog_ModuleScheme::CAN_ADD_PRODUCTS_ANY_USER
		))) return $this->Lang_Get('plugin.simplecatalog.Errors.Incorrect_Can_Add_Products');
		return true;
	}


	/**
	 * Проверить что показывать на главной странице каталога
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateCheckWhatToShowOnItemsPage($mValue, $aParams) {
		if (!in_array($mValue, array(
			PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS,
			PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_CATEGORIES,
			PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_MAP
		))) return $this->Lang_Get('plugin.simplecatalog.Errors.Incorrect_What_To_Show_On_Items_Page');
		return true;
	}


	/**
	 * Проверить опцию вывода блока последних продуктов
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateBlockShowLastProducts($mValue, $aParams) {
		if (!in_array($mValue, array(
			PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_NONE,
			PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_BEFORE_CONTENT,
			PluginSimplecatalog_ModuleScheme::SHOW_PRODUCTS_BLOCK_PLACE_TYPE_IN_SIDEBAR
		))) return $this->Lang_Get('plugin.simplecatalog.Errors.incorrect_block_show_last_products');
		return true;
	}


	/**
	 * Проверить шаблон схемы (№ 1 и 2)
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateTemplateName($mValue, $aParams) {
		if (!in_array($mValue, array_keys($this->PluginSimplecatalog_Scheme_GetTemplatesAll()))) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.scheme.wrong_template_name', array(), false);
		}
		return true;
	}


	/**
	 * Проверить урл схемы
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validateSchemeUrl($mValue, $aParams) {
		/*
		 * проверить не занят ли указанный урл другой схемой
		 */
		if ($oSchemeWithUrl = $this->PluginSimplecatalog_Scheme_MyGetSchemeBySchemeUrl($mValue) and $oSchemeWithUrl->getId() != $this->getId()) {
			return $this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Url_Already_Exists');
		}
		/*
		 * если включена поддержка коротких урлов для каталогов - проверить чтобы урл не совпадал с существующими назначенными урлами в роутере
		 */
		if (Config::Get('plugin.simplecatalog.urls.catalog.enable_short_urls')) {
			$aRouterPages = array_keys(Config::Get('router.page'));
			if (in_array($mValue, $aRouterPages)) {
				return $this->Lang_Get('plugin.simplecatalog.Errors.scheme.url_already_assigned_by_router', array('registered_urls' => implode(', ', $aRouterPages)));
			}
		}
		return true;
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Получить поля схемы (ручное кеширование)
	 *
	 * @param array $aExcludeFieldsByCodes			массив кодов полей схемы, которые нужно исключить из возвращаемого массива
	 * @return mixed
	 */
	public function getFields($aExcludeFieldsByCodes = array()) {
		/*
		 * ручное кеширование полей схемы
		 */
		if (!$this->aSchemeFields and SCRootStorage::GetParam('schemes_base_setup')) {
			$this->aSchemeFields = $this->{SCRootStorage::GetParam('schemes_base_setup')}($this);
		}
		/*
		 * если нужно исключить поля схемы по указанному списку их кодов
		 */
		if (!empty($aExcludeFieldsByCodes)) {
			/*
			 * новый набор возвращаемых полей
			 */
			$aNewFields = array();
			/*
			 * кода должны быть массивом
			 */
			if (!is_array($aExcludeFieldsByCodes)) {
				$aExcludeFieldsByCodes = (array) $aExcludeFieldsByCodes;
			}
			/*
			 * исключить поля с указанными кодами
			 */
			foreach($this->aSchemeFields as $oField) {
				if (!in_array($oField->getCode(), $aExcludeFieldsByCodes)) {
					$aNewFields[] = $oField;
				}
			}
			return $aNewFields;
		}
		//p($this->aSchemeFields, '$this->aSchemeFields');
		return $this->aSchemeFields;
	}


	/**
	 * Получить поля схемы без первого поля, которое является заголовком у продуктов
	 *
	 * @param array $aParams			параметры, передаваемые в метод получения всех полей схемы
	 * @return mixed
	 */
	public function getFieldsWOFirstField($aParams = array()) {
		$aData = $this->getFields($aParams);
		array_shift($aData);
		return $aData;
	}


	/**
	 * Получить поле схемы по его коду
	 * tip: данный метод нужен чтобы в шаблонах можно было получить нужное поле по его коду (аналогичный метод существует и для полей продуктов)
	 *
	 * @param $sCode					код поля схемы, как он прописан при создании поля схемы
	 * @return Entity|null
	 */
	public function getFieldByCode($sCode) {
		foreach($this->getFields() as $oField) {
			if ($oField->getCode() == $sCode) {
				return $oField;
			}
		}
		return null;
	}


	/*
	 *
	 * --- Проверка по константам ---
	 *
	 */

	/**
	 * Нужно ли показывать последние продукты на главной странице продуктов
	 *
	 * @return bool
	 */
	public function getNeedToShowLastProductsOnProductItemsPage() {
		return $this->getWhatToShowOnItemsPage() == PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_LAST_PRODUCTS;
	}


	/**
	 * Нужно ли показывать категории на главной странице продуктов
	 *
	 * @return bool
	 */
	public function getNeedToShowCategoriesOnProductItemsPage() {
		return $this->getWhatToShowOnItemsPage() == PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_CATEGORIES;
	}


	/**
	 * Нужно ли показывать карту меток на главной странице продуктов
	 *
	 * @return bool
	 */
	public function getNeedToShowMapOnProductItemsPage() {
		return $this->getWhatToShowOnItemsPage() == PluginSimplecatalog_ModuleScheme::SHOW_ON_ITEMS_PAGE_MAP;
	}


	/**
	 * Активна ли схема
	 *
	 * @return bool
	 */
	public function getActiveEnabled() {
		return $this->getActive() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Показывать ли пункт в меню "создать"
	 *
	 * @return bool
	 */
	public function getMenuAddTopicCreateEnabled() {
		return $this->getMenuAddTopicCreate() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включено ли ЧПУ для продуктов этой схемы
	 *
	 * @return bool
	 */
	public function getAllowUserFriendlyUrlEnabled() {
		return $this->getAllowUserFriendlyUrl() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включено ли заполнение СЕО мета полей для продуктов этой схемы
	 *
	 * @return bool
	 */
	public function getAllowEditAdditionalSeoMetaEnabled() {
		return $this->getAllowEditAdditionalSeoMeta() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нужна ли модерация для всех создаваемых продуктов схемы
	 *
	 * @return bool
	 */
	public function getModerationNeededEnabled() {
		return $this->getModerationNeeded() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включен ли алфавитный поиск в схеме
	 *
	 * @return bool
	 */
	public function getShowFirstLetterGroupsEnabled() {
		return $this->getShowFirstLetterGroups() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включен ли функционал интернет магазина для этой схемы
	 *
	 * @return bool
	 */
	public function getShopEnabled() {
		return $this->getShop() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Разрешены ли черновики для этой схемы
	 *
	 * @return bool
	 */
	public function getAllowDraftsEnabled() {
		return $this->getAllowDrafts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Разрешена ли отложенная публикация продуктов в этой схеме
	 *
	 * @return bool
	 */
	public function getAllowDeferredProductsEnabled() {
		return $this->getAllowDeferredProducts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нужно ли точно вырезать изображения для продукта по указанным в схеме размерам или подгонять ближайшую из сторон изображения, а другую - пропорционально
	 *
	 * @return bool
	 */
	public function getExactImageProportionsEnabled() {
		return $this->getExactImageProportions() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включен ли подсчет количества просмотров продуктов схемы
	 *
	 * @return bool
	 */
	public function getAllowCountViewsEnabled() {
		return $this->getAllowCountViews() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Использовать ли первый шаблон схемы в качестве основного (иначе - второй)
	 *
	 * @return bool
	 */
	public function getUseFirstTemplateAsDefaultEnabled() {
		return $this->getUseFirstTemplateAsDefault() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/*
	 *
	 * --- Карты ---
	 *
	 */

	/**
	 * Включены ли метки на картах для этой схемы
	 *
	 * @return bool
	 */
	public function getMapItemsEnabled() {
		return $this->getMapItems() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Включен ли выбор типа метки на карте для этой схемы
	 *
	 * @return bool
	 */
	public function getSelectPresetForMapItemsEnabled() {
		return $this->getSelectPresetForMapItems() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/*
	 *
	 * --- Связи ---
	 *
	 */

	/**
	 * Получить включенные и отсортированные настройки связей схемы
	 *
	 * @return mixed
	 */
	public function getActiveLinkSettingsSorted() {
		return $this->getLinkSettings(array(
			'active' => PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			'#order' => $this->PluginSimplecatalog_Scheme_GetDefaultSortingOrderForSchemeLinkSettings()
		));
	}


	/*
	 *
	 * --- Шаблоны схемы ---
	 *
	 */

	/**
	 * Получить имя текущего шаблона схемы
	 *
	 * @return string
	 */
	public function getTemplateName() {
		/*
		 * если переключатель отключен - сразу показывать первый шаблон
		 */
		if (!$this->getTemplateSwitchEnabled()) {
			return $this->getTemplateNameFirst();
		}
		/*
		 * получить шаблон, выбранный пользователем
		 */
		if (isset($_SESSION[PluginSimplecatalog_ModuleScheme::TEMPLATE_NAME_SESSION_KEY][$this->getId()])) {
			/*
			 * разрешен ли данный шаблон для выбора в переключателе
			 */
			if (in_array($_SESSION[PluginSimplecatalog_ModuleScheme::TEMPLATE_NAME_SESSION_KEY][$this->getId()], array($this->getTemplateNameFirst(), $this->getTemplateNameSecond()))) {
				return $_SESSION[PluginSimplecatalog_ModuleScheme::TEMPLATE_NAME_SESSION_KEY][$this->getId()];
			}
			/*
			 * удалить ранее выбранный шаблон, который больше не доступен в переключателе шаблонов
			 */
			unset($_SESSION[PluginSimplecatalog_ModuleScheme::TEMPLATE_NAME_SESSION_KEY][$this->getId()]);
		}
		/*
		 * получить текущий шаблон на основе настроек схемы
		 */
		return $this->getUseFirstTemplateAsDefaultEnabled() ? $this->getTemplateNameFirst() : $this->getTemplateNameSecond();
	}


	/**
	 * Был ли выбран первый шаблон
	 *
	 * @return bool
	 */
	public function getFirstTemplateIsCurrent() {
		return $this->getTemplateName() == $this->getTemplateNameFirst();
	}


	/**
	 * Был ли выбран второй шаблон
	 *
	 * @return bool
	 */
	public function getSecondTemplateIsCurrent() {
		return $this->getTemplateName() == $this->getTemplateNameSecond();
	}


	/**
	 * Нужен ли переключатель шаблонов (выбраны ли два разных шаблона для переключения)
	 *
	 * @return bool
	 */
	public function getTemplateSwitchEnabled() {
		return $this->getTemplateNameFirst() != $this->getTemplateNameSecond();
	}


	/*
	 *
	 * --- Общие ---
	 *
	 */

	/**
	 * Получить количество продуктов на страницу, заданное пользователем или значение по-умолчанию из настроек схемы
	 *
	 * @return int
	 */
	public function getItemsPerPageDefinedByUserOrDefault() {
		/*
		 * если пользователь выбрал количество
		 */
		if ($iPerPage = $this->PluginSimplecatalog_Itemsperpage_GetValueForScheme($this->getId())) {
			return $iPerPage;
		}
		/*
		 * вернуть количество элементов на страницу, установленное в настройках схемы
		 */
		return $this->getItemsPerPage();
	}


	/*
	 * 
	 * --- Урлы ---
	 * 
	 */

	/**
	 * Получить путь к главной продуктов схемы
	 *
	 * @return string
	 */
	public function getCatalogItemsWebPath() {
		/*
		 * включена ли поддержка коротких урлов для каталогов
		 */
		if (Config::Get('plugin.simplecatalog.urls.catalog.enable_short_urls')) {
			/*
			 * tip: без слеша "/" в конце т.к. роутер разбирает по этому слешу урл (и убирает крайние) и добавить его нельзя
			 * @see config/urls/loader.php
			 */
			return Config::Get('path.root.web') . '/' . $this->getSchemeUrl();
		}
		/*
		 * полный стандартный урл
		 */
		return Router::GetPath('product') . 'items/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь к странице модерации продуктов схемы
	 *
	 * @return string
	 */
	public function getCatalogModerationNeededItemsWebPath() {
		return Router::GetPath('product') . 'moderation/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь к списку продуктов текущего пользователя
	 *
	 * @return string
	 */
	public function getCatalogMyItemsWebPath() {
		return Router::GetPath('product') . 'my/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь к списку черновиков продуктов текущего пользователя
	 *
	 * @return string
	 */
	public function getCatalogDraftsItemsWebPath() {
		return Router::GetPath('product') . 'drafts/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь к отображению всех меток продуктов на карте
	 *
	 * @return string
	 */
	public function getCatalogMapItemsWebPath() {
		return Router::GetPath('product') . 'map/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь для фильтра схемы
	 *
	 * @return string
	 */
	public function getCatalogItemsFilterWebPath() {
		return Router::GetPath('product') . 'filter/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь к полному системному виду продуктов
	 *
	 * @return string
	 */
	public function getCatalogItemsAdminIndexWebPath() {
		return Router::GetPath('product') . 'index/' . $this->getSchemeUrl();
	}


	/**
	 * Получить урл редактирования схемы
	 *
	 * @return string
	 */
	public function getEditWebPath() {
		return Router::GetPath('scheme') . 'edit/' . $this->getId();
	}


	/**
	 * Получить урл удаления схемы
	 *
	 * @return string
	 */
	public function getDeleteWebPath() {
		return Router::GetPath('scheme') . 'delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Получить урл списка полей схемы
	 *
	 * @return string
	 */
	public function getFieldsListWebPath() {
		return Router::GetPath('field') . 'schemefields/' . $this->getId();
	}


	/**
	 * Получить урл добавления нового поля к схеме
	 *
	 * @return string
	 */
	public function getNewFieldAddWebPath() {
		return Router::GetPath('field') . 'add/' . $this->getId();
	}


	/**
	 * Получить урл для переключения на шаблон № 1 схемы
	 *
	 * @return string
	 */
	public function getChangeToFirstTemplateWebPath() {
		return Router::GetPath('scheme-public') . 'change-template/' . $this->getSchemeUrl() . '/' . $this->getTemplateNameFirst() . '/?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Получить урл для переключения на шаблон № 2 схемы
	 *
	 * @return string
	 */
	public function getChangeToSecondTemplateWebPath() {
		return Router::GetPath('scheme-public') . 'change-template/' . $this->getSchemeUrl() . '/' . $this->getTemplateNameSecond() . '/?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Получить урл для изменения количества элементов на страницу
	 *
	 * @param int $iNewCount	новое количество элементов на страницу
	 * @return string
	 */
	public function getChangeItemsPerPageWebPath($iNewCount) {
		return Router::GetPath('scheme-public') . 'change-items-per-page/' . $this->getId() . '/' . $iNewCount . '/?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Получить путь для обычного поиска по каталогу
	 *
	 * @return string
	 */
	public function getCatalogItemsSearchWebPath() {
		return Router::GetPath('product-search') . 'product/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь для алфавитного поиска по каталогу
	 *
	 * @return string
	 */
	public function getCatalogItemsAlphabeticalSearchWebPath() {
		return Router::GetPath('product-search') . 'letter/' . $this->getSchemeUrl();
	}


	/**
	 * Получить путь для создания нового продукта схемы
	 *
	 * @return string
	 */
	public function getAddProductWebPath() {
		return Router::GetPath('product') . 'add/' . $this->getSchemeUrl();
	}
	
}
