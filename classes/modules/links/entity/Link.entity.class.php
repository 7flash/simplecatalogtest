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

class PluginSimplecatalog_ModuleLinks_EntityLink extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),

		array('parent_type', 'number', 'min' => 1, 'max' => 127, 'allowEmpty' => false, 'integerOnly' => true),
		array('parent_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),

		array('from_target_type', 'number', 'min' => 1, 'max' => 127, 'allowEmpty' => false, 'integerOnly' => true),
		array('from_target_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),

		array('to_target_type', 'number', 'min' => 1, 'max' => 127, 'allowEmpty' => false, 'integerOnly' => true),
		array('to_target_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * tip: сущности записывать в полном формате
		 */
		'product' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleProduct_EntityProduct', 'to_target_id'),
	);


	/**
	 * Вызывается перед сохранением сущности
	 *
	 * @return bool|void
	 */
	protected function beforeSave() {
		return parent::beforeSave();
	}


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
	 * --- Методы ---
	 *
	 */


}

?>