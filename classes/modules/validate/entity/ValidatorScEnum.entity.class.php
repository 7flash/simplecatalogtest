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
 * Валидатор вхождения значения в массив
 *
 */

class PluginSimplecatalog_ModuleValidate_EntityValidatorScEnum extends ModuleValidate_EntityValidator {

	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * Массив разрешенных элементов
	 *
	 * @var array
	 */
	public $allowed = array();

	/**
	 * Ключ языкового файла для сообщения о неверном типе значения
	 *
	 * @var string
	 */
	public $errorMsgId = 'plugin.simplecatalog.validators.error';

	/*
	 * todo: удалить при переходе на новую версию лс, это баг в сущности валидатора из-за отсутствия проверки на существование переменной класса
	 */
	public $errorMsg = null;


	/**
	 * Запуск валидации
	 *
	 * @param mixed $mValue    			данные для валидации
	 * @return bool|string
	 */
	public function validate($mValue) {
		/*
		 * проверка типа значения
		 */
		if (!is_scalar($mValue)) {
			return $this->getMessage(null, 'errorMsg', array('val' => $mValue));
		}
		/*
		 * разрешение на пустое значение
		 */
		if ($this->allowEmpty and $this->isEmpty($mValue)) {
			return true;
		}
		/*
		 * проверка на вхождение в перечисление
		 */
		if (!in_array($mValue, $this->allowed)) {
			return $this->getMessage(null, 'errorMsg', array('val' => $mValue));
		}
		/*
		 * значение корректно
		 */
		return true;
	}

}

?>