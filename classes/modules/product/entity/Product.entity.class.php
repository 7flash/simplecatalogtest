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

class PluginSimplecatalog_ModuleProduct_EntityProduct extends EntityORM {

	/*
	 * Кеширование списка полей продукта для возможности повторного вызова.
	 * ORM автоматически кеширует вызовы сущностей из связей, но вызов списка полей у продукта идет не через связи,
	 * а через ручной метод т.к. нужна сортировка, поэтому поля здесь кешируются вручную
	 */
	private $aProductFields = array();

	/*
	 * Кеширование изображений продукта
	 */
	private $aImages = null;

	/*
	 * Кеширование связей продукта
	 */
	private $aLinks = null;

	/*
	 * Кеширование заголовков продукта по размерам (ключ массива - длина и постфикс заголовка)
	 */
	private $aProductTitles = array();

	/*
	 * Кеширование меток на карте
	 */
	private $aMapItems = null;


	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		/*
		 * на момент валидации может быть пустым т.к. будет заполнен после сохранения (через AUTO_INCREMENT)
		 */
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),
		array('scheme_id', 'number', 'min' => 1, 'allowEmpty' => false),

		/*
		 * создаются автоматически перед сохранением данных
		 */
/*		array('add_date', 'date', 'format' => array('yyyy-MM-dd hh:mm:ss'), 'allowEmpty' => false),
		array('edit_date', 'date', 'format' => array('yyyy-MM-dd hh:mm:ss'), 'allowEmpty' => false),
		array('user_id', 'number', 'min' => 1, 'allowEmpty' => false),
		array('user_id_edit_last', 'number', 'min' => 1, 'allowEmpty' => false),*/
		/*
		 * диапазон возможных состояний модерации, а не перечисление констант MODERATION_* из модуля продуктов т.к. сторонние плагины могут создавать свои типы модерации.
		 * тип модерации напрямую не задается пользователем
		 */
		array('moderation', 'number', 'min' => 0, 'max' => 255, 'allowEmpty' => false),
		array('product_url', 'string', 'min' => 1, 'max' => 2000, 'allowEmpty' => false),
		/*
		 * не все поля нужно валидировать
		 */

		array('user_allow_comments', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('price', 'number', 'min' => 0, 'allowEmpty' => true),
		array('price_new', 'price_new'),

		/*
		 * tip: seo поля валидировать не нужно т.к. они заполняются когда продукт уже прошел валидацию и там только строки без тегов
		 */
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		'scheme' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityScheme', 'scheme_id'),
		/*
		 * заменён на вариант с сортировкой
		 */
		//'product_fields' => array(EntityORM::RELATION_TYPE_HAS_MANY, 'PluginSimplecatalog_ModuleProduct_EntityFields', 'product_id'),
		'user' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id'),
			'city' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginProvider_ModuleProvider_EntityCity', 'city'),

			'user_edit_last' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id_edit_last'),
		'categories_links' => array(EntityORM::RELATION_TYPE_HAS_MANY, 'PluginSimplecatalog_ModuleProduct_EntityCategories', 'product_id'),
	);


	public function Init() {}


	/**
	 * Вызывается перед сохранением продукта
	 *
	 * @return bool|void
	 */
	protected function beforeSave() {
		return parent::beforeSave();
	}


	/**
	 * Вызывается перед удалением продукта
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
		 * если у продукта есть категории
		 */
		if ($this->getCategoriesIds()) {
			/*
			 * уменьшить счетчики количества продуктов у категорий, в которых находится продукт
			 */
			$this->PluginSimplecatalog_Product_DecreaseCategoriesItemsCount($this->getCategoriesIds());
			/*
			 * удалить связи продукта с категориями
			 */
			foreach($this->getCategoriesLinks() as $oLink) {
				$oLink->Delete();
			}
		}
		/*
		 * удалить все файлы продукта
		 */
		$this->PluginSimplecatalog_Product_DeleteAllFilesByProduct($this);
		/*
		 * удалить все изображения продукта
		 */
		$this->PluginSimplecatalog_Product_DeleteAllProductImagesByProduct($this);
		/*
		 * удалить все связи продукта и ссылки в связях на него
		 */
		$this->PluginSimplecatalog_Links_MyDeleteAllProductLinkItemsByProduct($this);
		/*
		 * удалить комментарии продукта (из избранного, прямого эфира и голоса за них)
		 */
		$this->Comment_DeleteCommentByTargetId($this->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT);
		/*
		 * удалить поля продукта
		 */
		foreach($this->getProductFields() as $oProductField) {
			$oProductField->Delete();
		}
		/*
		 * удалить метки на карте
		 */
		foreach($this->getMapItems() as $oItem) {
			$oItem->Delete();
		}
		/*
		 * tip: удаление подписки пользователей на новые комментарии продукта нет т.к. модуль подписки не предоставляет необходимые методы (только обновление каждой сущности подписки)
		 */

		$this->Hook_Run('sc_product_item_delete_before', array('oProduct' => $this));
		return parent::beforeDelete();
	}


	/*
	 *
	 * --- Валидаторы ---
	 *
	 */

	/**
	 * Проверить новую цену
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function validatePriceNew($mValue, $aParams) {
		$this->setPriceNew(ShopPrice::GetNewPriceCheckedFromRaw($mValue));
		return true;
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Получить поля продукта (ручное кеширование)
	 *
	 * @param array $aExcludeFieldsByCodes			массив кодов полей схемы, описывающих поля продуктов, которые нужно исключить из возвращаемого массива
	 * @return mixed
	 */
	public function getProductFields($aExcludeFieldsByCodes = array()) {
		/*
		 * ручное кеширование полученных полей в сущности
		 */
		if (!$this->aProductFields and SCRootStorage::GetParam('products_base_setup')) {
			$this->aProductFields = $this->{SCRootStorage::GetParam('products_base_setup')}($this);
		}
		/*
		 * если нужно исключить поля продукта по указанному списку кодов их полей схемы
		 */
		if (!empty($aExcludeFieldsByCodes)) {
			/*
			 * новый набор возвращаемых полей продукта
			 */
			$aNewProductFields = array();
			/*
			 * кода должны быть массивом
			 */
			if (!is_array($aExcludeFieldsByCodes)) {
				$aExcludeFieldsByCodes = (array) $aExcludeFieldsByCodes;
			}
			/*
			 * исключить поля с указанными кодами
			 */
			foreach($this->aProductFields as $oProductField) {
				if (!in_array($oProductField->getField()->getCode(), $aExcludeFieldsByCodes)) {
					$aNewProductFields[] = $oProductField;
				}
			}
			return $aNewProductFields;
		}
		return $this->aProductFields;
	}


	/**
	 * Получить поля продукта без первого поля, которое является заголовком продукта
	 *
	 * @param array $aParams			параметры, передаваемые в метод получения всех полей продукта
	 * @return mixed
	 */
	public function getProductFieldsWOFirstField($aParams = array()) {
		$aData = $this->getProductFields($aParams);
		array_shift($aData);
		return $aData;
	}


	/**
	 * Получить поле продукта по коду поля схемы, описывающего поле продукта
	 * tip: данный метод нужен чтобы в шаблонах можно было получить нужное поле по коду
	 *
	 * @param $sCode					код поля схемы, как он прописан при создании поля схемы
	 * @return Entity|null
	 */
	public function getProductFieldBySchemeFieldCode($sCode) {
		foreach($this->getProductFields() as $oProductField) {
			if ($oProductField->getField()->getCode() == $sCode) {
				return $oProductField;
			}
		}
		return null;
	}
	

	/**
	 * Включены ли комментарии у продукта (принудительно схемой или автором продукта, если схема разрешает)
	 *
	 * @return bool
	 */
	public function getCommentsEnabled() {
		if ($this->getScheme()->getAllowComments() == PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_FORCED_TO_ALLOW or
			(
				$this->getScheme()->getAllowComments() == PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_USER_DEFINED and
				$this->getUserAllowComments() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED
			)
		) {
			return true;
		}
		return false;
	}


	/**
	 * Установить разрешение комментирования продукта на основе управляющего правила комментирования схемы
	 *
	 * @param $iAllowComments		значение, которое будет установлено при управляющем правиле схемы "ALLOW_COMMENTS_USER_DEFINED"
	 * @throws Exception
	 */
	public function setUserAllowCommentsAccordingSchemeOptions($iAllowComments) {
		/*
		 * tip: значение принимается в учет только при ALLOW_COMMENTS_USER_DEFINED, но заполняется всегда для валидации
		 */
		switch($this->getScheme()->getAllowComments()) {
			/*
			 * комментарии на усмотрение пользователя
			 */
			case PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_USER_DEFINED:
				$this->setUserAllowComments($iAllowComments);
				break;
			/*
			 * всегда разрешены
			 */
			case PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_FORCED_TO_ALLOW:
				$this->setUserAllowComments(PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED);
				break;
			/*
			 * всегда запрещены
			 */
			case PluginSimplecatalog_ModuleScheme::ALLOW_COMMENTS_DENY:
				$this->setUserAllowComments(PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED);
				break;
			/*
			 * неизвестный тип правила схемы
			 */
			default:
				throw new Exception('SC: error: unknown "allow comments" type for scheme: "' . $this->getScheme()->getAllowComments() . '" in ' . __METHOD__);
		}
	}


	/**
	 * Получить количество просмотров продукта (если разрешено в схеме)
	 *
	 * @return bool|int
	 */
	public function getViewsCount() {
		if ($this->getScheme()->getAllowCountViewsEnabled() and $oCounter = $this->PluginSimplecatalog_Counter_MyGetCounterByProduct($this)) {
			return $oCounter->getCount();
		}
		return false;
	}


	/**
	 * Возвращает объект подписки на новые комментарии к продукту
	 *
	 * @return ModuleSubscribe_EntitySubscribe|null
	 */
	public function getSubscribeNewComment() {
		if (!$oUserCurrent = $this->User_GetUserCurrent()) {
			return null;
		}
		return $this->Subscribe_GetSubscribeByTargetAndMail(PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT . '_new_comment', $this->getId(), $oUserCurrent->getMail());
	}


	/*
	 *
	 * --- Модерация ---
	 *
	 */

	/**
	 * Установить для продукта пройденную модерацию
	 */
	public function setModerationDone() {
		$this->setModeration(PluginSimplecatalog_ModuleProduct::MODERATION_DONE);
	}


	/**
	 * Установить для продукта статус черновика
	 */
	public function setModerationDraft() {
		$this->setModeration(PluginSimplecatalog_ModuleProduct::MODERATION_DRAFT);
	}


	/**
	 * Пройдена ли модерация у продукта (не нуждается ли продукт в модерации)
	 *
	 * @return bool
	 */
	public function getModerationDone() {
		return $this->getModeration() == PluginSimplecatalog_ModuleProduct::MODERATION_DONE;
	}


	/**
	 * Нужна ли модерация для продукта
	 *
	 * @return bool
	 */
	public function getModerationNeeded() {
		return $this->getModeration() == PluginSimplecatalog_ModuleProduct::MODERATION_NEEDED;
	}


	/**
	 * Находится ли продукт в черновиках
	 *
	 * @return bool
	 */
	public function getModerationDraft() {
		return $this->getModeration() == PluginSimplecatalog_ModuleProduct::MODERATION_DRAFT;
	}


	/**
	 * Является ли продукт отложенным для публикации
	 *
	 * @return bool
	 */
	public function getModerationDeferred() {
		return $this->getModeration() == PluginSimplecatalog_ModuleProduct::MODERATION_DEFERRED;
	}


	/*
	 *
	 * --- Изображения ---
	 *
	 */

	/**
	 * Получить отсортированные изображения продукта
	 *
	 * @return array
	 */
	public function getImages() {
		if (is_null($this->aImages)) {
			$this->aImages = $this->PluginSimplecatalog_Images_MyGetImageItemsSortedByTargetIdAndTargetType($this->getId(), PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
		}
		return $this->aImages;
	}


	/**
	 * Получить первое изображение продукта
	 *
	 * @return Entity|null
	 */
	public function getFirstImage() {
		if ($aData = $this->getImages()) {
			return array_shift($aData);
		}
		return null;
	}


	/**
	 * Получить изображения продукта без самого первого (главного)
	 *
	 * @return Entity|null
	 */
	public function getImagesWOFirst() {
		if ($aData = $this->getImages()) {
			array_shift($aData);
		}
		return $aData;
	}


	/**
	 * Получить ПУТЬ к первому изображению продукта (если существует) или путь к изображению для продуктов по-умолчанию
	 *
	 * @return string
	 */
	public function getFirstImageOrDefaultPlaceholderPath() {
		if ($oImage = $this->getFirstImage()) {
			return $oImage->getFilePath();
		}
		return Plugin::GetTemplateWebPath(__CLASS__) . Config::Get('plugin.simplecatalog.images.default_product_image');
	}


	/*
	 *
	 * --- Связи ---
	 *
	 */

	/**
	 * Получить существующие связи продукта (привязанные другие ПРОМОДЕРИРОВАННЫЕ продукты) и их настройки для каждой связи схемы
	 * tip: используется сессионное кеширование
	 *
	 * @return array
	 */
	protected function getProductLinksData() {
		if (is_null($this->aLinks)) {
			$this->aLinks = $this->PluginSimplecatalog_Product_GatherProductLinksData($this);
		}
		return $this->aLinks;
	}


	/**
	 * Получить существующие связи продукта (и их настройки), которые нужно показывать в отдельной вкладке
	 *
	 * @return array
	 */
	public function getProductLinksDataWithShowTypeInTab() {
		$aProductLinksDataFiltered = array();
		foreach($this->getProductLinksData() as $mKey => $aLinkData) {
			/*
			 * если продукты этой связи нужно показывать в отдельной вкладке
			 */
			if ($aLinkData['oLinkSettings']->getShowTypeIsInTab()) {
				$aProductLinksDataFiltered[$mKey] = $aLinkData;
			}
		}
		return $aProductLinksDataFiltered;
	}


	/**
	 * Получить существующие связи продукта (и их настройки), которые нужно показывать ссылками
	 *
	 * @return array
	 */
	public function getProductLinksDataWithShowTypeAsLinks() {
		$aProductLinksDataFiltered = array();
		foreach($this->getProductLinksData() as $mKey => $aLinkData) {
			/*
			 * если продукты этой связи нужно показывать ссылками
			 */
			if ($aLinkData['oLinkSettings']->getShowTypeIsAsLinks()) {
				$aProductLinksDataFiltered[$mKey] = $aLinkData;
			}
		}
		return $aProductLinksDataFiltered;
	}


	/**
	 * Получить существующие связи продукта (и их настройки), которые нужно показывать изображениями
	 *
	 * @return array
	 */
	public function getProductLinksDataWithShowTypeAsImages() {
		$aProductLinksDataFiltered = array();
		foreach($this->getProductLinksData() as $mKey => $aLinkData) {
			/*
			 * если продукты этой связи нужно показывать изображениями
			 */
			if ($aLinkData['oLinkSettings']->getShowTypeIsAsImages()) {
				$aProductLinksDataFiltered[$mKey] = $aLinkData;
			}
		}
		return $aProductLinksDataFiltered;
	}


	/**
	 * Получить существующие связи продукта (и их настройки), которые нужно показывать в селекте
	 *
	 * @return array
	 */
	public function getProductLinksDataWithShowTypeInSelect() {
		$aProductLinksDataFiltered = array();
		foreach($this->getProductLinksData() as $mKey => $aLinkData) {
			/*
			 * если продукты этой связи нужно показывать в селекте
			 */
			if ($aLinkData['oLinkSettings']->getShowTypeIsInSelect()) {
				$aProductLinksDataFiltered[$mKey] = $aLinkData;
			}
		}
		return $aProductLinksDataFiltered;
	}


	/*
	 *
	 * --- Магазин ---
	 *
	 */

	/**
	 * Рассчитать новую цену продукта, если был указан процент от основной цены или получить цену
	 *
	 * @return null
	 */
	public function getPriceNewCalculated() {
		return ShopPrice::GetCalculatedPriceFromNewPriceValue($this->getPriceNew(), $this->getPrice());
	}


	/**
	 * Получить актуальную текущую цену продукта с учетом правил "новой" цены, где может быть новое значение цены, скидка или наценка в процентах от базовой цены
	 *
	 * @return null
	 */
	public function getActualPrice() {
		/*
		 * установлена ли базовая цена т.к. в "новой" цене может быть что угодно (скидки т.п.)
		 */
		if (!$this->getPrice()) {
			return null;
		}
		/*
		 * есть ли новая цена
		 */
		if ($fPriceNew = $this->getPriceNewCalculated()) {
			return $fPriceNew;
		}
		/*
		 * вернуть базову цену
		 */
		return $this->getPrice();
	}


	/*
	 *
	 * --- Метки на карте ---
	 *
	 */

	/**
	 * Получить метки на карте
	 *
	 * @return mixed
	 */
	public function getMapItems() {
		if (is_null($this->aMapItems)) {
			$this->aMapItems = $this->PluginSimplecatalog_Maps_MyGetProductMapItemsByProduct($this);
		}
		return $this->aMapItems;
	}


	/**
	 * Получить ид меток на карте
	 *
	 * @return array
	 */
	public function getMapItemsIds() {
		$aIds = array();
		foreach($this->getMapItems() as $oItem) {
			$aIds[] = $oItem->getId();
		}
		return $aIds;
	}

	
	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Возвращает урл для просмотра продукта
	 *
	 * @return string
	 */
	public function getItemShowWebPath() {
		/*
		 * включена ли поддержка коротких урлов для каталогов
		 */
		if (Config::Get('plugin.simplecatalog.urls.catalog.enable_short_urls')) {
			/*
			 * @see config/urls/loader.php
			 */
			return Config::Get('path.root.web') . '/' . $this->getScheme()->getSchemeUrl() . '/' . $this->getProductUrl();
		}
		/*
		 * полный стандартный урл
		 */
		return Router::GetPath('product') . 'item/' . $this->getProductUrl();
	}


	/**
	 * Возвращает урл редактирования продукта
	 *
	 * @return string
	 */
	public function getItemEditWebPath() {
		return Router::GetPath('product') . 'edit/' . $this->getId();
	}


	/**
	 * Возвращает урл для удаления продукта (вместе с ключем безопасности)
	 *
	 * @return string
	 */
	public function getItemDeleteWebPath() {
		return Router::GetPath('product') . 'delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/*
	 *
	 * --- Модерация ---
	 *
	 */

	/**
	 * Возвращает урл подтверждения модерации продукта
	 *
	 * @return string
	 */
	public function getItemModerationApproveWebPath() {
		return Router::GetPath('product') . 'moderation/approve/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/**
	 * Возвращает урл не прохождения модерации продукта
	 *
	 * @return string
	 */
	public function getItemModerationDisapproveWebPath() {
		return Router::GetPath('product') . 'moderation/disapprove/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/*
	 *
	 * --- Заголовок ---
	 *
	 */

	/**
	 * Значение первого поля продукта (используется как заголовок продукта) или заголовок по-умолчанию
	 *
	 * @param int    $iLengthMax    длина заголовка в символах
	 * @param string $sBreakPostfix добавляемый постфикс на границе превышения длины
	 * @param bool   $bKeepWords    оставлять целыми слова, не разрывая слово посредине
	 * @return string
	 */
	public function getFirstFieldTitle($iLengthMax = 150, $sBreakPostfix = '...', $bKeepWords = true) {
		$sKey = $iLengthMax . '__' . $sBreakPostfix;
		/*
		 * есть ли в кеше заголовок нужной длины
		 */
		if (!isset($this->aProductTitles[$sKey])) {
			$this->aProductTitles[$sKey] = $this->PluginSimplecatalog_Product_MyGetProductTitle($this, $iLengthMax, $sBreakPostfix, $bKeepWords);
		}
		return $this->aProductTitles[$sKey];
	}


	/*
	 *
	 * --- Сравнение продуктов ---
	 *
	 */

	/**
	 * Добавлен ли продукт в список сравнения
	 *
	 * @return mixed
	 */
	public function getProductInCompareList() {
		return $this->PluginSimplecatalog_Product_GetProductAlreadyInCompareList($this);
	}


	/**
	 * Есть ли в списке сравнения как минимум два продукта
	 *
	 * @return mixed
	 */
	public function getInCompareListAreAtLeastTwoProducts() {
		return $this->PluginSimplecatalog_Product_GetInCompareListAreAtLeastTwoProducts($this);
	}


	/**
	 * Получить ссылку для сравнения продуктов
	 *
	 * @return string
	 */
	public function getCompareProductsUrl() {
		return Router::GetPath('product') . 'compare/' . implode('/', $this->PluginSimplecatalog_Product_GetCompareProductsIdsForScheme($this->getScheme()));
	}


	/**
	 * Получить ссылку для удаления продукта из таблицы сравнения
	 *
	 * @return string
	 */
	public function getCompareProductDeleteFromCompareTable() {
		return Router::GetPath('product') . 'compare/delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}


	/*
	 *
	 * --- Категории ---
	 *
	 */

	/**
	 * Получить категории продукта
	 *
	 * @return array
	 */
	public function getCategories() {
		$aCategories = array();
		foreach($this->getCategoriesLinks() as $oLink) {
			$aCategories[] = $oLink->getCategory();
		}
		return $aCategories;
	}


	/**
	 * Получить ид категорий продукта
	 *
	 * @return array
	 */
	public function getCategoriesIds() {
		$aCategoriesIds = array();
		foreach($this->getCategoriesLinks() as $oLink) {
			$aCategoriesIds[] = $oLink->getCategoryId();
		}
		return $aCategoriesIds;
	}


	public function getRatingStar(){
		if($this->getCountVote() > 0){
			return round($this->getRating()/$this->getCountVote());
		}
		return 0;
	}
	
}

?>