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

class PluginSimplecatalog_ModuleImages_EntityImage extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('target_id', 'number', 'min' => 1, 'integerOnly' => true, 'allowEmpty' => false),
		array('target_type', 'number', 'min' => 1, 'max' => 256, 'integerOnly' => true, 'allowEmpty' => false),

		array('file_path', 'string', 'max' => 500, 'allowEmpty' => false),

		/*
		 * tip: для остальных полей валидация не нужна т.к. они будут заполнены перед записью автоматически
		 */
	);


	/**
	 * Вызывается перед сохранением сущности
	 *
	 * @return bool|void
	 */
	protected function beforeSave() {
		/*
		 * если сущность новая - поставить дату и автора
		 */
		if ($this->_isNew()) {
			$this->setDateAdd(date('Y-m-d H:i:s'));
			$this->setUserId($this->User_GetUserCurrent()->getId());
		}
		/*
		 * если сортировка не указана - задать последнюю свободную
		 */
		if (!$this->getSorting()) {
			$this->setSorting($this->PluginSimplecatalog_Images_MyGetNextFreeSortingByTargetIdAndTargetType($this->getTargetId(), $this->getTargetType()));
		}
		return parent::beforeSave();
	}


	/**
	 * Вызывается перед удалением сущности
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		/*
		 * удалить файл изображения
		 */
		$this->PluginSimplecatalog_File_RemoveFile($this->getFilePath());
		return parent::beforeDelete();
	}


}

?>