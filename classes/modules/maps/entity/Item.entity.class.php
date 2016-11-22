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

class PluginSimplecatalog_ModuleMaps_EntityItem extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'integerOnly' => true, 'allowEmpty' => true),

		array('target_type', 'number', 'integerOnly' => true, 'allowEmpty' => false),
		array('target_id', 'number', 'integerOnly' => true, 'allowEmpty' => false),

		array('lat', 'number', 'integerOnly' => false, 'allowEmpty' => false, 'min' => -90, 'max' => 90),
		array('lng', 'number', 'integerOnly' => false, 'allowEmpty' => false, 'min' => -180, 'max' => 180),

		array('title', 'string', 'max' => 500, 'allowEmpty' => true),
		array('description', 'string', 'max' => 500, 'allowEmpty' => true),

		array('extra_data', 'string', 'max' => 4000, 'allowEmpty' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		'product' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleProduct_EntityProduct', 'target_id'),
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
	 * --- Низкоуровневые методы сериализированного хранилища ---
	 *
	 */

	/**
	 * Получить данные из сериализированного поля
	 *
	 * @return array
	 */
	protected function GetExtra() {
		$mData = @unserialize($this->getExtraData());
		return is_array($mData) ? $mData : array();
	}


	/**
	 * Сохранить данные в сериализированное поле
	 *
	 * @param array $aData				массив для сохранения
	 */
	protected function SetExtra($aData) {
		$this->setExtraData(serialize($aData));
	}


	/**
	 * Получить из сериализированного хранилища данные по указанному ключу
	 * tip: возвращение строки '' вместо null сделано чтобы не ставить условие на нулл в нескольких местах т.к. карты яндекса "падают" если указать, например, preset для метки как нулл
	 *
	 * @param string|int $sKey			ключ
	 * @return mixed|string
	 */
	protected function GetExtraField($sKey) {
		$aData = $this->GetExtra();
		return isset($aData[$sKey]) ? $aData[$sKey] : '';
	}


	/**
	 * Сохранить в сериализированное хранилище данные под указанным ключом
	 *
	 * @param string|int $sKey          ключ
	 * @param            $mValue        данные
	 */
	protected function SetExtraField($sKey, $mValue) {
		$aData = $this->GetExtra();
		$aData[$sKey] = $mValue;
		$this->SetExtra($aData);
	}


	/*
	 *
	 * --- Методы (из сериализированного хранилища) ---
	 *
	 */

	/**
	 * Сохранить подсказку
	 *
	 * @param $sText
	 */
	public function setExtraHint($sText) {
		$this->SetExtraField('hint', $sText);
	}


	/**
	 * Получить подсказку
	 */
	public function getExtraHint() {
		return $this->GetExtraField('hint');
	}


	/**
	 * Сохранить пресет
	 *
	 * @param $sText
	 */
	public function setExtraPreset($sText) {
		$this->SetExtraField('preset', $sText);
	}


	/**
	 * Получить пресет
	 */
	public function getExtraPreset() {
		return $this->GetExtraField('preset');
	}


}

?>