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

class PluginSimplecatalog_ModuleCategory_EntityCategory extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'min' => 1, 'allowEmpty' => true, 'integerOnly' => true),
		array('url', 'regexp', 'pattern' => '#^[\w]{1,50}$#', 'allowEmpty' => false),
		/*
		 * будет установлен перед сохранением
		 */
		//array('full_url', 'string', 'min' => 1, 'max' => 50, 'allowEmpty' => false),
		array('name', 'string', 'min' => 2, 'max' => 100, 'allowEmpty' => false),
		/*
		 * проверка на существование родительского ид производится перед сохранением, т.к. там нужны данные родителя для других проверок
		 */
		//array('parent_id', 'check_parent_id'),
		array('sorting', 'check_sorting'),
		array('target_type', 'number', 'min' => 1, 'max' => 256, 'allowEmpty' => false, 'integerOnly' => true),
		array('target_id', 'number', 'min' => 1, 'allowEmpty' => false, 'integerOnly' => true),
		/*
		 * tip: изображение будет заполнено автоматически
		 */
		array('image_url', 'string', 'min' => 0, 'max' => 500, 'allowEmpty' => true),
		array('description', 'string', 'min' => 0, 'max' => 500, 'allowEmpty' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		self::RELATION_TYPE_TREE,
		/*
		 * сущности записывать в полном формате
		 */
	);


	/**
	 * Вызывается перед сохранением категории
	 *
	 * @return bool|void
	 */
	protected function beforeSave() {
		/*
		 * существует ли родительская категория
		 */
		if ($this->getParentId() and !$oCategoryParent = $this->PluginSimplecatalog_Category_MyGetCategoryById($this->getParentId())) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить ид категории (если это редактирование т.е. существует и корректен ид)
		 */
		if ($this->getId() and !$oCategory = $this->PluginSimplecatalog_Category_MyGetCategoryById($this->getId())) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить использован ли уже указанный урл для категории в этой схеме, если использован - этой ли категорией
		 */
		if ($oCategoryUrlExists = $this->PluginSimplecatalog_Category_MyGetCategoryByUrlAndTargetIdAndTargetType($this->getUrl(), $this->getTargetId(), $this->getTargetType()) and
			$this->getId() != $oCategoryUrlExists->getId()
		) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Url_Already_Exists'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить корректность вложенности категории, если указана родительская категория и это редактирование (где в дереве есть уже эта запись)
		 */
		if (isset($oCategory) and isset($oCategoryParent)) {
			/*
			 * нельзя вкладывать категорию саму в себя и в дочерние категории
			 */
			if (!$this->PluginSimplecatalog_Category_CheckCategoryParentToBeCorrect($oCategory, $oCategoryParent)) {
				return false;
			}
		}

		/*
		 * задать полный путь к категории
		 */
		$this->setFullUrl((isset($oCategoryParent) ? $this->PluginSimplecatalog_Category_GetFullUrlForCategory($oCategoryParent) : '') . $this->getUrl());

		return parent::beforeSave();
	}


	/**
	 * Вызывается после сохранения категории
	 */
	protected function afterSave() {
		/*
		 * если это было редактирование категории
		 */
		if (!$this->_isNew()) {
			/*
			 * обновить полные урлы у всех прямых дочерних категорий
			 * tip: для всех только прямых потомков установить новый урл, а те, в свою очередь, вызовут свой afterSave для своих прямых потомков
			 */
			foreach ($this->getChildren() as $oChildCategory) {
				$oChildCategory->setFullUrl($this->PluginSimplecatalog_Category_GetFullUrlForCategory($this) . $oChildCategory->getUrl());
				$oChildCategory->Save();
			}
		}
	}


	/**
	 * Вызывается перед удалением категории
	 *
	 * @return bool
	 */
	protected function beforeDelete() {
		/*
		 * если у категории было изображение - удалить его
		 */
		if ($this->getImageUrl()) {
			$this->PluginSimplecatalog_File_RemoveFile($this->getImageUrl());
		}
		/*
		 * удалить все дочерние категории (рекурсивно выбирая только прямых потомков!)
		 */
		foreach ($this->getChildren() as $oChildCategory) {
			$oChildCategory->Delete();
		}
		return true;
	}


	/*
	 *
	 * --- Валидация ---
	 *
	 */

	/*
	 * хотелось сделать для внешних ключей, чтобы был нулл, но для орм лс нужно чтобы корневой элемент имел родительский ключ равный 0, а не нулл
	 * поэтому категории не имеют внешних ключей
	 */
	/*
	public function ValidateCheckParentId($sValue, $aParams) {
		if (!$this->getParentId()) {
			$this->setParentId(null);
		}
		return true;
	}
	*/


	/**
	 * Проверить чтобы для категории была указана сортировка
	 *
	 * @param $mValue		значение
	 * @param $aParams		параметры
	 * @return bool
	 */
	public function ValidateCheckSorting($mValue, $aParams) {
		/*
		 * если сортировка не была указана вручную - установить максимальное новое значение (добавлять категорию последней)
		 */
		if (!$this->getSorting()) {
			$this->setSorting($this->PluginSimplecatalog_Category_GetNextFreeSortingValueForProducts());
		}
		return true;
	}


	/*
	 *
	 * --- Методы ---
	 *
	 */

	/**
	 * Получить массив ид всех дочерних категорий
	 *
	 * @return array
	 */
	public function getDescendingCategoriesIds() {
		$aIds = array();
		foreach($this->getDescendants() as $oDescendingCategory) {
			$aIds[] = $oDescendingCategory->getId();
		}
		return $aIds;
	}


	/**
	 * Получить массив ид всех дочерних категорий и самой себя
	 *
	 * @return array
	 */
	public function getSelfAndDescendingCategoriesIds() {
		return array_merge($this->getDescendingCategoriesIds(), array($this->getId()));
	}


	/**
	 * Входит ли категория в указанную категорию или её ветки дерева
	 *
	 * @param $oCategory		категория (и вся её ветка) для проверки
	 * @return mixed
	 */
	public function getIsInCategory($oCategory) {
		return in_array($this->getId(), $oCategory->getSelfAndDescendingCategoriesIds());
	}


	/*
	 *
	 * --- Урлы ---
	 *
	 */

	/**
	 * Получить относительный путь к категории (полный или короткий, в зависимости от настроек конфига)
	 *
	 * @return mixed
	 */
	public function getUrlPath() {
		if (Config::Get('plugin.simplecatalog.categories.use_full_path')) {
			return $this->getFullUrl();
		}
		return $this->getUrl();
	}


	/**
	 * Получить урл на список продуктов категории
	 * tip: важно в параметре передать объект схемы продукта (просто для экономии ресурсов)
	 *
	 * @param $oScheme		объект схемы
	 * @return string
	 */
	public function getCategoryUrl($oScheme) {
		return Router::GetPath('product') . 'category/' . $oScheme->getSchemeUrl() . '/' . $this->getUrlPath();
	}


	/**
	 * Получить урл редактирования категории
	 *
	 * @param $sMenuSchemeSelect		выбранная схема
	 * @return string
	 */
	public function getEditWebPath($sMenuSchemeSelect) {
		return Router::GetPath('sccategories') . 'edit/' . $sMenuSchemeSelect . '/' . $this->getId();
	}


	/**
	 * Получить урл удаления категории
	 *
	 * @param $sMenuSchemeSelect		выбранная схема
	 * @return string
	 */
	public function getDeleteWebPath($sMenuSchemeSelect) {
		return Router::GetPath('sccategories') . 'delete/' . $sMenuSchemeSelect . '/' . $this->getId() . '?security_ls_key=' . $this->Security_SetSessionKey();
	}
	
}

?>