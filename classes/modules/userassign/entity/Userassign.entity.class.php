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

class PluginSimplecatalog_ModuleUserassign_EntityUserassign extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'integerOnly' => true),
		array('user_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),
		array('group_id', 'sc_method', 'method' => 'PluginSimplecatalog_Usergroup_MyGetActiveUsergroupById', 'errorMsgId' => 'plugin.simplecatalog.Errors.Usergroup_Not_Found'),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		'group' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleUsergroup_EntityUsergroup', 'group_id'),
		'user' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id'),
	);


	/**
	 * Вызывается перед удалением сущности
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		return parent::beforeDelete();
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить урл редактирования назначения
	 *
	 * @return string
	 */
	public function getEditWebPath() {
		return Router::GetPath('userassign') . 'edit/' . $this->getId();
	}


	/**
	 * Получить урл удаления назначения
	 *
	 * @return string
	 */
	public function getDeleteWebPath() {
		return Router::GetPath('userassign') . 'delete/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}
	
}

?>