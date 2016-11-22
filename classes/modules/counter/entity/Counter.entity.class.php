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

class PluginSimplecatalog_ModuleCounter_EntityCounter extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),

		array('target_type', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleCounter::TARGET_TYPE_PRODUCT_VIEWS,
			PluginSimplecatalog_ModuleCounter::TARGET_TYPE_PRODUCT_FIELD_FILE_DOWNLOADS,
		), 'allowEmpty' => false),
		array('target_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),

		array('count', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),
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

}

?>