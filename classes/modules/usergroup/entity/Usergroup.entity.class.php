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

class PluginSimplecatalog_ModuleUsergroup_EntityUsergroup extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'integerOnly' => true),
		array('group_name', 'string', 'min' => 1, 'max' => 100, 'allowEmpty' => false),
		array('active', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('scheme_id', 'sc_method', 'method' => 'PluginSimplecatalog_Scheme_MyGetActiveSchemeById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Scheme_Not_Found'),

		array('can_user_edit_products', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('user_products_need_moderation', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('user_can_moderate_products', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('user_can_defer_products', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED,
			PluginSimplecatalog_ModuleScheme::COMPONENT_DISABLED
		), 'allowEmpty' => false),
		array('user_can_create_new_products', 'sc_enum', 'allowed' => array(
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
		'scheme' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityScheme', 'scheme_id'),
	);


	/**
	 * Вызывается перед удалением группы прав пользователей
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		/*
		 * удалить все связи этой группы прав с пользователями
		 */
		foreach($this->PluginSimplecatalog_Userassign_MyGetUserassignItemsByGroupId($this->getId()) as $oUserAssign) {
			$oUserAssign->Delete();
		}
		return parent::beforeDelete();
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Активна ли группа прав
	 *
	 * @return bool
	 */
	public function getActiveEnabled() {
		return $this->getActive() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Может ли пользователь редактировать продукты
	 *
	 * @return bool
	 */
	public function getCanUserEditProductsEnabled() {
		return $this->getCanUserEditProducts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Нуждаются ли продукты пользователя в модерации
	 *
	 * @return bool
	 */
	public function getUserProductsNeedModerationEnabled() {
		return $this->getUserProductsNeedModeration() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Может ли пользователь модерировать продукты
	 *
	 * @return bool
	 */
	public function getUserCanModerateProductsEnabled() {
		return $this->getUserCanModerateProducts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Может ли пользователь использовать отложенную публикацию продуктов
	 *
	 * @return bool
	 */
	public function getUserCanDeferProductsEnabled() {
		return $this->getUserCanDeferProducts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/**
	 * Может ли пользователь создавать новые продукты
	 *
	 * @return bool
	 */
	public function getUserCanCreateNewProductsEnabled() {
		return $this->getUserCanCreateNewProducts() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED;
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить урл редактирования группы прав
	 *
	 * @return string
	 */
	public function getEditWebPath() {
		return Router::GetPath('usergroups') . 'edit/' . $this->getId();
	}


	/**
	 * Получить урл удаления группы прав
	 *
	 * @return string
	 */
	public function getDeleteWebPath() {
		return Router::GetPath('usergroups') . 'delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}

}

?>