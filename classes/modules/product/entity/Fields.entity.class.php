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

class PluginSimplecatalog_ModuleProduct_EntityFields extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		/*
		 * на момент валидации может быть пустым т.к. будет заполнен после сохранения (через AUTO_INCREMENT)
		 */
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),
		/*
		 * на момент валидации может быть пустым - будет заполнен после сохранения продукта
		 */
		array('product_id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),
		array('field_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),

		array('content_type', 'sc_enum', 'allowed' => array(
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_INT,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_FLOAT,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_VARCHAR,
			PluginSimplecatalog_ModuleProduct::FIELD_TYPE_TEXT,
		), 'allowEmpty' => false),

		/*
		 * валидация проходит по правилам полей схемы (и валидатора, если задан)
		 * tip: в content_varchar может быть массив файла для последующей валидации в второй очереди сохранения полей
		 * 		из-за него нет возможности добавить валидацию здесь
		 */
/*		array('content_int', 'number', 'allowEmpty' => true, 'integerOnly' => true),
		array('content_float', 'number', 'allowEmpty' => true, 'integerOnly' => false),
		array('content_varchar', 'string', 'max' => 2000, 'allowEmpty' => true),
		array('content_text', 'string', 'max' => 65535, 'allowEmpty' => true),*/

		array('content_source', 'string', 'max' => 65535, 'allowEmpty' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		'field' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleScheme_EntityFields', 'field_id'),
		'product' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleProduct_EntityProduct', 'product_id'),
	);


	/**
	 * Получить отображаемое значение поля продукта с префиксом и постфиксом
	 *
	 * @param bool     $bWithoutPreAndPostFixes   получить отображаемое значение без пре- и постфиксов
	 * @param int|null $iLengthMax                максимальная длина получаемого значения в символах или null если нету ограничений на длину
	 *                                            если длина указана и значение без тегов её превышает, то из значения удаляются все теги и строка обрезается
	 * @param string   $sBreakPostfix             добавляемый постфикс к значению если превышена длина строки
	 * @param bool     $bKeepWords                оставлять целыми слова, не разрывая слово посредине
	 * @return string
	 */
	public function getDisplayValue($bWithoutPreAndPostFixes = false, $iLengthMax = null, $sBreakPostfix = '...', $bKeepWords = true) {
		$oSchemeField = $this->getField();
		/*
		 * значение поля должно возвращаться "как есть", без изменения длины, пре- и постфиксов
		 */
		$bSensitiveContent = false;
		/*
		 * получить отображаемое значение на основе типа поля схемы
		 */
		switch($oSchemeField->getFieldType()) {
			/*
			 * для селекта
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_SELECT:
				$sValueDisplay = $oSchemeField->getDisplayValueForSelectFromStringValueIndexes($this->getContent());
				break;
			/*
			 * для файла
			 */
			case PluginSimplecatalog_ModuleScheme::FIELD_TYPE_FILE:
				$sValueDisplay = $oSchemeField->getFileUrlForCurrentUserAccess($this);
				$bSensitiveContent = true;
				break;
			/*
			 * значение для остальных типов полей схемы
			 */
			default:
				$sValueDisplay = $this->getContent();
		}
		/*
		 * если нужно точное значение
		 */
		if ($bSensitiveContent) {
			return $sValueDisplay;
		}
		/*
		 * если нужно ограничить длину строки
		 */
		if (!is_null($iLengthMax)) {
			$sValueDisplay = $this->LimitContentValueLength($sValueDisplay, $iLengthMax, $sBreakPostfix, $bKeepWords);
		}
		/*
		 * если нужно только само значение
		 */
		if ($bWithoutPreAndPostFixes) {
			return $sValueDisplay;
		}
		return $oSchemeField->getValuePrefix() . $sValueDisplay . $oSchemeField->getValuePostfix();
	}


	/**
	 * Получить строку обрезанную по указанное количество символов с удалением тегов из неё
	 * tip: метод достаточно универсальный и его можно вынести, например, в модуль tools если будет необходимость в использовании где-нибудь ещё,
	 * 		оставлен здесь для быстроты вызова
	 *
	 * @param string $sSource       исходная строка
	 * @param int    $iLengthMax    максимальная длина строки
	 * @param string $sBreakPostfix добавляемый постфикс при обрезании строки
	 * @param bool   $bKeepWords    оставлять целыми слова, не разрывая слово посредине
	 * @return string
	 */
	private function LimitContentValueLength($sSource, $iLengthMax, $sBreakPostfix = '...', $bKeepWords = true) {
		/*
		 * нужно удалить все теги т.к. место разрыва может быть посредине тега
		 */
		$sValueWOTags = strip_tags($sSource);
		/*
		 * нужно ли обрезать строку
		 */
		if (mb_strlen($sValueWOTags, 'utf-8') <= $iLengthMax) {
			return $sSource;
		}
		/*
		 * в длине учитывать длину постфикса
		 */
		$iLengthMax -= min($iLengthMax, mb_strlen($sBreakPostfix, 'utf-8'));
		/*
		 * получить укороченную строку
		 */
		$sValueTruncated = mb_substr($sValueWOTags, 0, $iLengthMax, 'utf-8');
		/*
		 * удалить остатки последнего слова
		 */
		if ($bKeepWords) {
			$sValueTruncated = preg_replace('#\s+\S*$#u', '', $sValueTruncated);
		}
		$sValueTruncated .= $sBreakPostfix;
		/*
		 * т.к. все теги были убраны из значения, то вернуть хотя бы переводы строк
		 */
		return nl2br($sValueTruncated);
	}


	/*
	 *
	 * --- Получение и сохранение значения поля ---
	 *
	 */

	/**
	 * Получить значение поля продукта (на основе его типа из соответствующего поля таблицы)
	 *
	 * @return mixed
	 */
	public function getContent() {
		/*
		 * если это не новое поле
		 */
		if ($this->getContentType()) {
			/*
			 * получить имя метода (геттер) на основе типа контента
			 */
			$sMethod = $this->PluginSimplecatalog_Product_GetEntityMethodNameToOperateWithContentField('get', $this->getContentType());
			/*
			 * получить значение из соответствующего поля таблицы
			 */
			return $this->{$sMethod}();
		}
		/*
		 * это новое поле
		 */
		return null;
	}


	/**
	 * Установить значение поля продукта (на основе данных поля схемы, описывающего его)
	 *
	 * @param $mValue		устанавливаемое значение
	 */
	public function setContent($mValue) {
		/*
		 * получить тип контента поля продукта на основе типа поля схемы, описывающего это поле или валидатора поля схемы
		 */
		$iProductContentType = $this->PluginSimplecatalog_Product_GetContentTypeFromSchemeFieldTypeOrSchemeFieldValidator($this->getField());
		/*
		 * получить имя метода (cеттер) на основе типа контента
		 */
		$sMethod = $this->PluginSimplecatalog_Product_GetEntityMethodNameToOperateWithContentField('set', $iProductContentType);
		/*
		 * также нужно установить тип поля в таблице (колонка)
		 */
		$sMethod .= 'WithType';
		/*
		 * установить значение
		 */
		$this->{$sMethod}($mValue);
	}


	/**
	 * Установить значение целого числового поля продукта вместе с его типом
	 *
	 * @param $mValue	устанавливаемое значение
	 */
	protected function setContentIntWithType($mValue) {
		$this->setContentInt($mValue);
		$this->setContentType(PluginSimplecatalog_ModuleProduct::FIELD_TYPE_INT);
	}


	/**
	 * Установить значение дробного числового поля продукта вместе с его типом
	 *
	 * @param $mValue	устанавливаемое значение
	 */
	protected function setContentFloatWithType($mValue) {
		$this->setContentFloat($mValue);
		$this->setContentType(PluginSimplecatalog_ModuleProduct::FIELD_TYPE_FLOAT);
	}


	/**
	 * Установить значение строкового поля продукта вместе с его типом
	 *
	 * @param $mValue	устанавливаемое значение
	 */
	protected function setContentVarcharWithType($mValue) {
		$this->setContentVarchar($mValue);
		$this->setContentType(PluginSimplecatalog_ModuleProduct::FIELD_TYPE_VARCHAR);
	}


	/**
	 * Установить значение текстового поля продукта вместе с его типом
	 *
	 * @param $mValue	устанавливаемое значение
	 */
	protected function setContentTextWithType($mValue) {
		$this->setContentText($mValue);
		$this->setContentType(PluginSimplecatalog_ModuleProduct::FIELD_TYPE_TEXT);
	}


}

?>