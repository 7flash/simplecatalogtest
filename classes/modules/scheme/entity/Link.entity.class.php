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

class PluginSimplecatalog_ModuleScheme_EntityLink extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),

		array('active', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),

		array('name', 'string', 'min' => 2, 'max' => 50, 'allowEmpty' => true),
		array('description', 'string', 'min' => 2, 'max' => 200, 'allowEmpty' => true),

		/*
		 * tip: собственные валидаторы плагина
		 */
		array('scheme_id', 'sc_method', 'method' => 'PluginSimplecatalog_Scheme_MyGetActiveSchemeById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Scheme_Not_Found'),
		array('target_scheme_id', 'sc_method', 'method' => 'PluginSimplecatalog_Scheme_MyGetActiveSchemeById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Scheme_Not_Found'),

		array('type', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_ONE,
			PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_MANY
		), 'allowEmpty' => false),
		array('show_type', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_TAB,
			PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_LINKS,
			PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_IMAGES,
			PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_SELECT
		), 'allowEmpty' => false),
		array('select_type', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_ALL,
			PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_SELF
		), 'allowEmpty' => false),

		/*
		 * сортировка: если сортировка не была указана вручную - установить значение по-умолчанию и проверка на число
		 */
		array('sorting', 'sc_default', 'value' => 100),
		array('sorting', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),

		array('products_count_to_select', 'sc_default', 'value' => 100),
		array('products_count_to_select', 'number', 'min' => 1, 'max' => 10000, 'allowEmpty' => false, 'integerOnly' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * tip: сущности записывать в полном формате
		 */
		'scheme' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityScheme', 'scheme_id'),
		'target_scheme' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityScheme', 'target_scheme_id'),
	);


	public function Init() {
		parent::Init();
	}


	/**
	 * Вызывается перед удалением настройки связи схемы
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		/*
		 * удалить все связи этой настройки связей схемы
		 */
		$this->PluginSimplecatalog_Links_MyDeleteProductLinkItemsByParentSchemeLinkSettings($this);
		return parent::beforeDelete();
	}


	/**
	 * Вызывается после сохранения связи схемы
	 */
	protected function afterSave() {}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Является ли связь типом "1 к 1"
	 *
	 * @return bool
	 */
	public function getTypeHasOne() {
		return $this->getType() == PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_ONE;
	}


	/**
	 * Является ли связь типом "1 ко многим"
	 *
	 * @return bool
	 */
	public function getTypeHasMany() {
		return $this->getType() == PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_MANY;
	}


	/**
	 * Осуществляется ли выбор ВСЕХ продуктов для связи
	 *
	 * @return bool
	 */
	public function getSelectTypeAll() {
		return $this->getSelectType() == PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_ALL;
	}


	/**
	 * Осуществляется ли выбор только СВОИХ продуктов для связи
	 *
	 * @return bool
	 */
	public function getSelectTypeSelf() {
		return $this->getSelectType() == PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_SELF;
	}


	/**
	 * Показывать ли связи в отдельной вкладке
	 *
	 * @return bool
	 */
	public function getShowTypeIsInTab() {
		return $this->getShowType() == PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_TAB;
	}


	/**
	 * Показывать ли связи ссылками
	 *
	 * @return bool
	 */
	public function getShowTypeIsAsLinks() {
		return $this->getShowType() == PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_LINKS;
	}


	/**
	 * Показывать ли связи изображениями
	 *
	 * @return bool
	 */
	public function getShowTypeIsAsImages() {
		return $this->getShowType() == PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_IMAGES;
	}


	/**
	 * Показывать ли связи в селекте
	 *
	 * @return bool
	 */
	public function getShowTypeIsInSelect() {
		return $this->getShowType() == PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_SELECT;
	}


	/**
	 * Получить отображаемое имя связи
	 *
	 * @return string
	 */
	public function getDisplayName() {
		/*
		 * если задано имя - показать
		 */
		if ($this->getName()) {
			return $this->getName();
		}
		/*
		 * иначе использовать имя связанной схемы
		 */
		return $this->getTargetScheme()->getSchemeName();
	}


	/**
	 * Активна ли настройка связи схемы
	 *
	 * @return bool
	 */
	public function getActiveEnabled() {
		return $this->getActive() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить урл редактирования связи схемы
	 *
	 * @return string
	 */
	public function getEditWebPath() {
		return Router::GetPath('sc_links') . 'edit/' . $this->getScheme()->getSchemeUrl() . '/' . $this->getId();
	}


	/**
	 * Получить урл удаления связи схемы
	 *
	 * @return string
	 */
	public function getDeleteWebPath() {
		return Router::GetPath('sc_links') . 'delete/' . $this->getScheme()->getSchemeUrl() . '/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}
	
}

?>