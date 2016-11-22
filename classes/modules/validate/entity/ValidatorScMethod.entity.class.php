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
 * Валидатор для вызова кастомного метода с проверяемым значением в качестве параметра и проверки булевого результата
 *
 */

class PluginSimplecatalog_ModuleValidate_EntityValidatorScMethod extends ModuleValidate_EntityValidator {

	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = false;

	/**
	 * Метод для вызова
	 *
	 * @var null
	 */
	public $method = null;

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
		 * разрешение на пустое значение
		 */
		if ($this->allowEmpty and $this->isEmpty($mValue)) {
			return true;
		}
		/*
		 * вызов кастомного метода со значением в качестве параметра
		 */
		if (!$this->method or !$this->{$this->method}($mValue)) {
			return $this->getMessage(null, 'errorMsg', array('val' => $mValue));
		}
		/*
		 * значение корректно
		 */
		return true;
	}

}

?>