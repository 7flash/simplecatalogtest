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
 * Валидатор, который устанавливает значение валидируемого поля сущности если оно пустое
 *
 */

class PluginSimplecatalog_ModuleValidate_EntityValidatorScDefault extends ModuleValidate_EntityValidator {

	/**
	 * Устанавливаемое значение
	 *
	 * @var mixed
	 */
	public $value = null;


	/**
	 * Запуск валидации
	 *
	 * @param $mValue					данные для валидации
	 * @return bool
	 */
	public function validate($mValue) {
		if ($this->isEmpty($mValue)) {
			$this->setValueOfCurrentEntity($this->sFieldCurrent, $this->value);
		}
		return true;
	}

}

?>