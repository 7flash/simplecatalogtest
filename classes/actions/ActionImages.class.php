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
 * Обработка загрузки изображений
 *
 */

class PluginSimplecatalog_ActionImages extends ActionPlugin {

	/*
	 * Сущность текущего пользователя
	 */
	protected $oUserCurrent = null;


	public function Init() {
		/*
		 * нужна авторизация
		 */
		if (!$this->oUserCurrent = $this->User_GetUserCurrent() ) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Access_Denied'), $this->Lang_Get('error'));
			return Router::Action('error');
		}
		/*
		 * экшен работает только через аякс
		 */
		$this->Viewer_SetResponseAjax('json');
	}


	protected function RegisterEvent() {
		/*
		 * загрузить новые изображения
		 */
		$this->AddEventPreg('#^ajax-upload-images$#', 'EventAjaxUploadImages');
		/*
		 * получить загруженные изображения
		 */
		$this->AddEventPreg('#^ajax-get-uploaded-images$#', 'EventAjaxGetUploadedImages');
		/*
		 * удалить загруженное изображение
		 */
		$this->AddEventPreg('#^ajax-delete-uploaded-image$#', 'EventAjaxDeleteUploadedImage');
		/*
		 * изменить сортировку изображений
		 */
		$this->AddEventPreg('#^ajax-change-images-order$#', 'EventAjaxChangeImagesSortingOrder');
	}


	/*
	 *
	 * --- Загрузка изображений ---
	 *
	 */

	/**
	 * Выполнить аякс загрузку изображений
	 */
	public function EventAjaxUploadImages() {
		/*
		 * для удобного использования файлов
		 */
		$aFilesData = isset($_FILES['images']) ? array_diverse($_FILES['images']) : array();
		/*
		 * для каждого типа свой загрузчик
		 */
		switch((int) getRequest('target_type')) {
			/*
			 * загрузка изображений для продуктов
			 */
			case PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS:
				$this->UploadProductImages($aFilesData);
				break;
			/*
			 * если тип данных не поддерживается
			 */
			default:
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.unsupported_target_type'), $this->Lang_Get('error'));
		}
	}


	/**
	 * Загрузка изображений для продукта
	 *
	 * @param $aFilesData		данные преобразованного файлового массива
	 * @return bool
	 */
	protected function UploadProductImages($aFilesData) {
		/*
		 * существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		 */
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			return false;
		}
		$oScheme = $oProduct->getScheme();
		/*
		 * получить количества разрешенных, загруженных и загружаемых изображений
		 */
		$iMaxAllowedImagesCount = $oScheme->getMaxImagesCount();
		$iCurrentlyUploadedImagesCount = count($oProduct->getImages());
		$iNewImagesToUploadCount = count($aFilesData);
		/*
		 * проверить максимальное количество разрешенных изображений для продукта
		 */
		if ($iCurrentlyUploadedImagesCount + $iNewImagesToUploadCount > $iMaxAllowedImagesCount) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.max_count_exceed', array(
				'max_count' => $iMaxAllowedImagesCount,
				'current_count' => $iCurrentlyUploadedImagesCount,
				'try_count' => $iNewImagesToUploadCount
			)), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * сохраненные изображения
		 */
		$aImages = array();
		/*
		 * для каждого файла
		 */
		foreach($aFilesData as $aFile) {
			/*
			 * проверка на ошибку загрузки файла
			 */
			if (!$this->PluginSimplecatalog_File_IsFileUploaded($aFile) or !$sFile = $this->PluginSimplecatalog_File_UploadProductImageFile($aFile, $oProduct)) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
				continue;
			}
			/*
			 * сделать изображение меньшего размера (сохранить в той же директории) и удалить оригинал
			 */
			if (!$sImageFile = $this->PluginSimplecatalog_Images_GetImageWithSize($sFile, $oScheme->getImageWidth(), $oScheme->getImageHeight(), $oScheme->getExactImageProportionsEnabled())) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
				continue;
			}
			$oEnt = Engine::GetEntity('PluginSimplecatalog_Images_Image');
			$oEnt->setTargetId($oProduct->getId());
			$oEnt->setTargetType(PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
			$oEnt->setFilePath($sImageFile);

			if (!$oEnt->_Validate()) {
				$this->Message_AddError($oEnt->_getValidateError(), $this->Lang_Get('error'));
				continue;
			}

			$oEnt->Save();
			/*
			 * добавить в список для вывода на форме
			 */
			array_unshift($aImages, $oEnt);
		}
		/*
		 * если все файлы успешно загружены
		 */
		if (count($aFilesData) == count($aImages)) {
			$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
		}
		/*
		 * отрендерить шаблон с изображениями
		 */
		$this->Viewer_AssignAjax('sText', $this->GetImagesRenderedTemplate($aImages));
	}


	/*
	 *
	 * --- Получение загруженных изображений ---
	 *
	 */

	/**
	 * Аякс обработчик получения загруженных изображений
	 */
	public function EventAjaxGetUploadedImages() {
		/*
		 * для каждого типа свой хендлер
		 */
		switch((int) getRequest('target_type')) {
			/*
			 * получение изображений для продуктов
			 */
			case PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS:
				$this->GetProductImages();
				break;
			/*
			 * если тип данных не поддерживается
			 */
			default:
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.unsupported_target_type'), $this->Lang_Get('error'));
		}
	}


	/**
	 * Получить изображения продукта
	 *
	 * @return bool
	 */
	protected function GetProductImages() {
		/*
		 * существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		 */
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			return false;
		}
		$aImages = $oProduct->getImages();
		/*
		 * отрендерить шаблон с изображениями
		 */
		$this->Viewer_AssignAjax('sText', $this->GetImagesRenderedTemplate($aImages));
	}


	/*
	 *
	 * --- Удаление изображений ---
	 *
	 */

	/**
	 * Аякс обработчик удаления загруженного изображения
	 */
	public function EventAjaxDeleteUploadedImage() {
		/*
		 * для каждого типа свой хендлер
		 */
		switch((int) getRequest('target_type')) {
			/*
			 * изображения продуктов
			 */
			case PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS:
				$this->DeleteProductImage();
				break;
			/*
			 * если тип данных не поддерживается
			 */
			default:
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.unsupported_target_type'), $this->Lang_Get('error'));
		}
	}


	/**
	 * Удалить изображение у продукта
	 *
	 * @return bool
	 */
	protected function DeleteProductImage() {
		/*
		 * существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		 */
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			return false;
		}
		/*
		 * существует ли такое изображение
		 */
		if (!$oImage = $this->PluginSimplecatalog_Images_MyGetImageByTargetIdAndTargetTypeAndId(
			$oProduct->getId(),
			PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS,
			(int) getRequest('image_id')
		)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.image_not_found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * удалить изображение
		 */
		$oImage->Delete();
		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
	}


	/*
	 *
	 * --- Изменение сортировки изображений ---
	 *
	 */

	/**
	 * Аякс обработчик изменения сортировки изображений
	 */
	public function EventAjaxChangeImagesSortingOrder() {
		/*
		 * для каждого типа свой хендлер
		 */
		switch((int) getRequest('target_type')) {
			/*
			 * изображения продуктов
			 */
			case PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS:
				$this->ChangeProductImagesSortingOrder();
				break;
			/*
			 * если тип данных не поддерживается
			 */
			default:
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.unsupported_target_type'), $this->Lang_Get('error'));
		}
	}


	/**
	 * Изменить порядок изображений у продукта
	 *
	 * @return bool
	 */
	protected function ChangeProductImagesSortingOrder() {
		/*
		 * существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		 */
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			return false;
		}
		/*
		 * получить массив ид изображений в порядке сортировки
		 */
		$aImagesIdsRaw = (array) getRequest('images_ids');
		/*
		 * получить изображения с указанием в качестве ключа ид изображения
		 */
		if (!$aImages = $this->PluginSimplecatalog_Images_MyGetImageItemsByTargetIdAndTargetTypeAndIdIn(
			$oProduct->getId(),
			PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS,
			$aImagesIdsRaw,
			array('#index-from' => 'id')
		)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.image_not_found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * выполнить сортировку
		 */
		$this->PluginSimplecatalog_Sorting_SortItemsByRawIdsAndORMSortOrderArray($aImages, $aImagesIdsRaw, $this->PluginSimplecatalog_Images_GetDefaultSortingOrder());

		$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Получить отрендеренный шаблон с изображениями
	 *
	 * @param $aImages		массив сущностей изображений
	 * @return mixed
	 */
	protected function GetImagesRenderedTemplate($aImages) {
		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('aImages', $aImages);
		$sText = $oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__) . 'helpers/images/list.tpl');
		return $sText;
	}


	/**
	 * Существует ли указанный в реквесте продукт по ид и есть ли права у текущего пользователя для его редактирования, возвращает сущность продукта в случае успеха
	 *
	 * @return Entity|bool
	 */
	protected function GetProductExistsAndRightsToEditIt() {
		/*
		 * существует ли такой продукт
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetProductById((int) getRequest('target_id'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * активна ли схема
		 */
		$oScheme = $oProduct->getScheme();
		if (!$oScheme->getActiveEnabled()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Scheme_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить права пользователя для редактирования существуюшего продукта
		 */
		if (!$this->oUserCurrent or !$this->oUserCurrent->getCanManageProduct($oProduct)) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.You_Cant_Edit_This_Product'), $this->Lang_Get('error'));
			return false;
		}
		return $oProduct;
	}


}

?>