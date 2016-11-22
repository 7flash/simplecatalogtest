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

class PluginSimplecatalog_ModuleCategory extends ModuleORM {

	private $oMapper = null;

	/*
	 * Категории для схем
	 */
	const TARGET_TYPE_SCHEME = 1;


	/*
	 * Направление сортировки по-умолчанию (новые внизу)
	 */
	private $aDefaultOrder = array('sorting' => 'asc');


	public function Init() {
		/*
		 * orm требует этого
		 */
		parent::Init();
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}


	/*
	 *
	 * --- Обертки для ORM методов ---
	 *
	 */
	
	/*
	 * --- Категория ---
	 */

	/**
	 * Получить категорию по ид
	 *
	 * @param $iId		ид категории
	 * @return mixed
	 */
	public function MyGetCategoryById($iId) {
		return $this->GetCategoryById($iId);
	}


	/**
	 * Получить категорию по её урлу, ид связи и типе связи
	 *
	 * @param $sCategoryUrl		урл категории
	 * @param $iTargetId		ид связи
	 * @param $iTargetType		тип связи
	 * @return mixed
	 */
	public function MyGetCategoryByUrlAndTargetIdAndTargetType($sCategoryUrl, $iTargetId, $iTargetType) {
		return $this->GetCategoryByUrlAndTargetIdAndTargetType($sCategoryUrl, $iTargetId, $iTargetType);
	}


	/**
	 * Получить категорию по её урлу и схеме, которой она принадлежит
	 *
	 * @param string $sCategoryUrl       урл категории
	 * @param        $oScheme            сущность схемы
	 * @return mixed
	 */
	public function MyGetCategoryByUrlAndScheme($sCategoryUrl, $oScheme) {
		return $this->MyGetCategoryByUrlAndTargetIdAndTargetType($sCategoryUrl, $oScheme->getId(), self::TARGET_TYPE_SCHEME);
	}


	/**
	 * Получить все корневые категории для типа связи и id связи (нужен при удалении схемы)
	 *
	 * @param $iTargetType		тип связи
	 * @param $iTargetId		ид связи
	 * @return mixed
	 */
	public function MyGetRootCategoriesByTargetTypeAndTargetId($iTargetType, $iTargetId) {
		return $this->GetCategoryItemsByTargetTypeAndTargetIdAndParentId($iTargetType, $iTargetId, 0);
	}


	/*
	 *
	 * --- Дерево категорий ---
	 *
	 */

	/**
	 * Получить дерево категорий по ид и типу связи
	 *
	 * @param int $iTargetId   ид связи
	 * @param int $sTargetType тип связи
	 * @return mixed
	 */
	public function MyGetCategoryTree($iTargetId, $sTargetType) {
		return $this->LoadTreeOfCategory(array(
			'target_type' => $sTargetType,
			'target_id' => $iTargetId,
			'#order' => $this->aDefaultOrder
		));
	}


	/**
	 * Получить развернутое дерево категорий для схемы
	 *
	 * @param $oScheme        объект схемы
	 * @return array|null
	 */
	public function MyBuildCategoryTreeForScheme($oScheme) {
		if ($aCategoryTree = $this->MyGetCategoryTree($oScheme->getId(), self::TARGET_TYPE_SCHEME)) {
			return ModuleORM::buildTree($aCategoryTree);
		}
		return null;
	}


	/*
	 *
	 * --- Количество элементов категории ---
	 *
	 */

	/**
	 * Изменить количество элементов у категории
	 *
	 * @param $iId			ид категории
	 * @param $iStep		прирост к количеству (1, -1)
	 */
	protected function ChangeItemsCountForCategoryById($iId, $iStep) {
		if ($oCategory = $this->MyGetCategoryById($iId)) {
			$oCategory->setItemsCount($oCategory->getItemsCount() + $iStep);
			$oCategory->Save();
		}
	}


	/**
	 * Уменьшить счетчик продуктов у категории на единицу
	 *
	 * @param $iId			ид категории
	 */
	public function DecreaseItemsCountForCategoryById($iId) {
		$this->ChangeItemsCountForCategoryById($iId, -1);
	}


	/**
	 * Увеличить счетчик продуктов у категории на единицу
	 *
	 * @param $iId			ид категории
	 */
	public function IncreaseItemsCountForCategoryById($iId) {
		$this->ChangeItemsCountForCategoryById($iId, 1);
	}


	/*
	 *
	 * --- Перенос категорий для продуктов ---
	 *
	 */

	/**
	 * Всем продуктам этой и дочерних категорий установить родительскую категорию вместо них и пересчитать счетчики
	 *
	 * @param $oCategory		объект категории
	 */
	public function TransferCategoryAndItsDescendantsToParentForAssignedProducts($oCategory) {
		/*
		 * обход странного поведения орм, когда тот возвращает "%%NULL_PARENT%%" вместо null
		 */
		if ($oCategory->getParentId()) {
			$oParentCategory = $oCategory->getParent();
		} else {
			$oParentCategory = null;
		}

		/*
		 * установить родительскую категорию всем продуктам дочерних категорий, которые удаляются
		 */
		foreach ($oCategory->getDescendants() as $oDescendingCategory) {
			$this->ReplaceCategoryInProductsFromOldToNew($oDescendingCategory, $oParentCategory);
		}
		/*
		 * для самой категории
		 */
		$this->ReplaceCategoryInProductsFromOldToNew($oCategory, $oParentCategory);
	}


	/**
	 * Заменить в продуктах старую категорию на новую с пересчетом счетчиков для обеих категорий
	 *
	 * @param object 		$oOldCategory		объект старой категории
	 * @param object|null 	$oNewCategory		объект новой категории (или нулл есть новой категории нет, а только удаление старой)
	 */
	protected function ReplaceCategoryInProductsFromOldToNew($oOldCategory, $oNewCategory = null) {
		foreach($this->PluginSimplecatalog_Product_MyGetProductItemsByCategory($oOldCategory) as $oProduct) {
			/*
			 * получить массив ид категорий, в которых состоит продукт
			 */
			$aProductCategoriesIds = $oProduct->getCategoriesIds();
			/*
			 * удалить из массива ид старой (удаляемой) категории
			 */
			$aProductCategoriesIds = array_diff($aProductCategoriesIds, array($oOldCategory->getId()));
			/*
			 * добавить ид новой категории
			 */
			if ($oNewCategory) {
				$aProductCategoriesIds = array_merge($aProductCategoriesIds, array($oNewCategory->getId()));
			}
			/*
			 * добавить категории продукту (метод сам посчитает что нужно удалить и сам пересчитает счетчики количества)
			 */
			$this->PluginSimplecatalog_Product_AddCategoriesToProduct($oProduct, $aProductCategoriesIds);
		}
	}


	/*
	 *
	 * --- Сортировка ---
	 *
	 */

	/**
	 * Получить максимальное значение сортировки для типа источника
	 *
	 * @param $iTargetType		тип источника
	 * @return int
	 */
	protected function GetMaxSortingValue($iTargetType) {
		return $this->oMapper->GetMaxSortingValue($iTargetType);
	}


	/**
	 * Получить значение сортировки по-умолчанию
	 *
	 * @return int
	 */
	public function GetNextFreeSortingValueForProducts() {
		return $this->GetMaxSortingValue(self::TARGET_TYPE_SCHEME) + 1;
	}


	/**
	 * Проверить корректность указания родительской категории для категории (нельзя вкладывать в саму себя и дочерние категории)
	 *
	 * @param $oCategory		существующая (редактируемая) категория
	 * @param $oCategoryParent	родительская категория
	 * @return bool				флаг проверки
	 */
	public function CheckCategoryParentToBeCorrect($oCategory, $oCategoryParent) {
		/*
		 * проверить на вложенность категории в саму себя
		 */
		if ($oCategory->getId() == $oCategoryParent->getId()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Cant_Be_Nested_In_Itself'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить чтобы категория не была вложена в дочерние категории
		 */
		if (in_array($oCategoryParent->getId(), $oCategory->getDescendingCategoriesIds())) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Category_Cant_Be_Nested_In_Its_Descending_Categories'), $this->Lang_Get('error'));
			return false;
		}
		return true;
	}


	/**
	 * Получить полный урл для категории (вместе с урлами родителей)
	 *
	 * @param $oCategory	объект родительской категории
	 * @return string		полный урл
	 */
	public function GetFullUrlForCategory($oCategory) {
		$sResult = $oCategory->getUrl();
		/*
		 * для всех родителей собрать их урлы вместе
		 */
		foreach ($oCategory->getAncestors() as $oParent) {
			$sResult = $oParent->getUrl() . '/' . $sResult;
		}
		/*
		 * после этого будет добавлена последняя категория в цепочке
		 */
		return $sResult . '/';
	}


	/**
	 * Проверить массив ид категорий на существование всех категорий с такими ид и возвращение массива сущностей категорий в случае корректности всех указанных ид
	 *
	 * @param array $aCategoriesIds массив ид категорий
	 * @param bool  $bBreakOnError  прерывать и возвращать false если хоть одна категория не найдена или вернуть только существующие
	 * @return array|bool			массив сущностей категорий или false если хотя бы одна категория не найдена
	 */
	public function GetCategoriesArrayIdExists($aCategoriesIds, $bBreakOnError = true) {
		$aCategories = array();
		foreach ($aCategoriesIds as $iCategoryId) {
			if (!is_scalar($iCategoryId) or !$oCategory = $this->MyGetCategoryById($iCategoryId)) {
				if ($bBreakOnError) {
					return false;
				}
				continue;
			}
			$aCategories[] = $oCategory;
		}
		return $aCategories;
	}


	/*
	 *
	 * --- Обработка изображения категории ---
	 *
	 */

	/**
	 * Загрузить изображение для категории
	 *
	 * @param array  $aFile          массив данных файла
	 * @param string $sUrlOld        старый урл изображения
	 * @param        $oScheme        сущность схемы (для создания подкаталога по ид схемы)
	 * @return string
	 */
	public function UploadCategoryImage($aFile, $sUrlOld, $oScheme) {
		/*
		 * если файл был загружен и помещен в директорию
		 */
		if ($sFile = $this->PluginSimplecatalog_File_UploadCategoryImageFile($aFile, $oScheme)) {
			$aImageSize = Config::Get('plugin.simplecatalog.categories.image_size');
			/*
			 * получить изображение для категории нужных размеров и удалить оригинал
			 */
			if ($sFileNew = $this->PluginSimplecatalog_Images_GetImageWithSize($sFile, $aImageSize[0], $aImageSize[1])) {
				/*
				 * удалить старое изображение категории
				 */
				if ($sUrlOld) {
					$this->PluginSimplecatalog_File_RemoveFile($sUrlOld);
				}
				return $sFileNew;
			}
		}
		/*
		 * при загрузке произошла ошибка или файл не был загружен - сохранить старое значение
		 */
		return $sUrlOld;
	}


	/**
	 * Удалить изображение категории
	 *
	 * @param string $sUrlOld        старый урл изображения
	 * @return null
	 */
	public function DeleteCategoryImage($sUrlOld) {
		/*
		 * удалить старое изображение категории
		 */
		if ($sUrlOld) {
			$this->PluginSimplecatalog_File_RemoveFile($sUrlOld);
		}
		return null;
	}


}

?>