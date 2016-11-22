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

class PluginSimplecatalog_ModuleValidator extends Module {
	
	public function Init () {}


	/**
	 * Корректно ли поле по ид валидатора
	 *
	 * @param $sValidatorKey		ид валидатора
	 * @param $sValue				проверяемое значение
	 * @return bool
	 */
	public function IsFieldValidByValidatorIdAndFieldValue($sValidatorKey, $sValue) {
		if (!$sValidatorKey or !$aValidatorData = $this->GetValidatorDataByKey($sValidatorKey) or !$this->ValidateFieldByValidator($aValidatorData, $sValue)) {
			return false;
		}
		return true;
	}


	/**
	 * Провести валидацию поля по данным валидатора
	 *
	 * @param $aValidatorData		данные валидатора
	 * @param $sValue				проверяемое значение
	 * @return bool
	 */
	protected function ValidateFieldByValidator($aValidatorData, $sValue) {
		if ($aValidatorData['regexp']) {
			/*
			 * проверить значение на корректность по регулярному выражению
			 */
			if (!preg_match($aValidatorData['regexp'], $sValue)) return false;
		} elseif ($aValidatorData['callback']) {
			/*
			 * вызвать пользовательский колбек для проверки значения
			 */
			if (!call_user_func($aValidatorData['callback'], $sValue)) return false;
		} else {
			/*
			 * неверная настройка валидатора
			 */
			return false;
		}
		return true;
	}


	/**
	 * Получить данные валидатора (из конфига) по его ид (ключу)
	 *
	 * @param $sKey					ключ валидатора
	 * @return mixed
	 */
	public function GetValidatorDataByKey($sKey) {
		return Config::Get('plugin.simplecatalog.validators.list.' . $sKey);
	}
	
}

?>