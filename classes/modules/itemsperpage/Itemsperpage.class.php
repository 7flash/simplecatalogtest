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

/*
 *
 * Для обработки изменения количества элементов на страницу
 *
 */

class PluginSimplecatalog_ModuleItemsperpage extends Module {

	/*
	 * Ключ сессии в котором хранятся количество элементов в разрезе типа объекта и его ид
	 */
	const SESSION_KEY = 'sc_items_per_page';

	/*
	 * Тип - схема
	 */
	const TYPE_SCHEME = 'scheme';


	public function Init() {}


	/**
	 * Получить данные
	 *
	 * @return array
	 */
	protected function GetData() {
		return isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : array();
	}


	/**
	 * Установить данные
	 *
	 * @param array $aData
	 */
	protected function SetData($aData = array()) {
		$_SESSION[self::SESSION_KEY] = $aData;
	}


	/**
	 * Установить значение количества элементов для типа и ид объекта
	 *
	 * @param $sType
	 * @param $iId
	 * @param $mValue
	 */
	public function SetValueForTypeAndId($sType, $iId, $mValue) {
		$aData = $this->GetData();
		$aData[$sType][$iId] = $mValue;
		$this->SetData($aData);
	}


	/**
	 * Получить значение количества элементов для типа и ид объекта
	 *
	 * @param $sType
	 * @param $iId
	 * @return null
	 */
	public function GetValueForTypeAndId($sType, $iId) {
		$aData = $this->GetData();
		return isset($aData[$sType][$iId]) ? $aData[$sType][$iId] : null;
	}


	/*
	 *
	 * --- Схема ---
	 *
	 */

	/**
	 * Установить значение для схемы
	 *
	 * @param $iId			ид схемы
	 * @param $mValue
	 */
	public function SetValueForScheme($iId, $mValue) {
		$this->SetValueForTypeAndId(self::TYPE_SCHEME, $iId, $mValue);
	}


	/**
	 * Получить значение для схемы
	 *
	 * @param $iId			ид схемы
	 * @return null
	 */
	public function GetValueForScheme($iId) {
		return $this->GetValueForTypeAndId(self::TYPE_SCHEME, $iId);
	}


	/*
	 *
	 * --- Значения для выбора ---
	 *
	 */

	/**
	 * Получить значения в порядке возрастания, исключая повторения, для выбора с учетом значения по-умолчанию из объекта
	 *
	 * @param int $iDefaultValue	добавляемое значение по-умолчанию из объекта
	 * @return array
	 */
	public function GetValuesWithDefault($iDefaultValue) {
		/*
		 * добавить новое значение, проверить на дубль
		 */
		$aValues = array_unique(array_merge(array($iDefaultValue), Config::Get('plugin.simplecatalog.general.pagination.values_for_per_page')));
		/*
		 * отсортировать чтобы добавляемое значение стало на свое место
		 */
		sort($aValues);
		return $aValues;
	}

}

?>