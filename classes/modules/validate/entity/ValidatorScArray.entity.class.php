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
 * Валидатор массивов
 *
 */

/*
 * todo: в новой версии лс где исправлен баг с указанием Id текстовки переделать вызовы getMessage на использование ид, вместо прямого вызова Lang_Get
 */

class PluginSimplecatalog_ModuleValidate_EntityValidatorScArray extends ModuleValidate_EntityValidator {

	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * Максимально допустимый размер массива (элементов)
	 *
	 * @var null|integer
	 */
	public $max_items;

	/**
	 * Минимально допустимый размер массива (элементов)
	 *
	 * @var null|integer
	 */
	public $min_items;

	/**
	 * Список разрешенных элементов массива
	 *
	 * @var null|array
	 */
	public $enum;

	/**
	 * Валидатор для каждого значения массива, полный аналог обычного валидатора
	 *
	 * @var null|array
	 */
	public $item_validator;

	/**
	 * Кастомное сообщение об ошибке при слишком большом массиве
	 *
	 * @var string
	 */
	public $msgTooBig;

	/**
	 * Кастомное сообщение об ошибке при слишком маленьком массиве
	 *
	 * @var string
	 */
	public $msgTooSmall;

	/**
	 * Кастомное сообщение об ошибке при значении, не входящим в список разрешенных
	 *
	 * @var string
	 */
	public $msgValueNotAllowed;

	/**
	 * Кастомное сообщение об ошибке при значении элемента которое не проходит валидацию элемента
	 *
	 * @var string
	 */
	public $msgIncorrectValue;


	/**
	 * Запуск валидации
	 *
	 * @param mixed $mValue 		данные для валидации
	 * @return bool|string
	 */
	public function validate($mValue) {
		/*
		 * проверка типа
		 */
		if (!is_array($mValue)) {
			return $this->getMessage($this->Lang_Get('plugin.simplecatalog.validators.array.must_be_array', null, false));
		}
		/*
		 * разрешено ли пустое значение
		 */
		if ($this->allowEmpty and $this->isEmpty($mValue)) {
			return true;
		}
		/*
		 * минимальное количество элементов
		 */
		if ($this->min_items !== null and count($mValue) < $this->min_items) {
			return $this->getMessage($this->Lang_Get('plugin.simplecatalog.validators.array.too_small', null, false), 'msgTooSmall', array('min_items' => $this->min_items));
		}
		/*
		 * максимальное количество элементов
		 */
		if ($this->max_items !== null and count($mValue) > $this->max_items) {
			return $this->getMessage($this->Lang_Get('plugin.simplecatalog.validators.array.too_big', null, false), 'msgTooBig', array('max_items' => $this->max_items));
		}
		/*
		 * если задано перечисление разрешенных элементов массива
		 */
		if ($this->enum !== null and count($this->enum) > 0) {
			foreach ($mValue as $mVal) {
				if (!in_array($mVal, $this->enum)) {
					return $this->getMessage($this->Lang_Get('plugin.simplecatalog.validators.array.value_is_not_allowed', null, false), 'msgValueNotAllowed', array('val' => $mVal));
				}
			}
		}
		/*
		 * если для элементов массива задан свой валидатор
		 */
		if ($this->item_validator !== null and count($this->item_validator) > 0) {
			foreach ($mValue as $mVal) {
				if (!$this->Validate_Validate($this->item_validator['type'], $mVal, isset($this->item_validator['params']) ? $this->item_validator['params'] : array())) {
					return $this->getMessage(
						$this->Lang_Get('plugin.simplecatalog.validators.array.value_is_not_correct', null, false) . '. ' . $this->Validate_GetErrorLast(true),
						'msgIncorrectValue',
						array('val' => $mVal)
					);
				}
			}
		}
		/*
		 * валидация пройдена
		 */
		return true;
	}

}


?>