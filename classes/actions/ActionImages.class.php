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

		$this->AddEventPreg('#^ajax-upload-editor-images$#', 'EventAjaxUploadEditorImages');

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


	/////////////////////////////////////////////////////////////////////
	// START - Загрузка картинок из редактора
	/////////////////////////////////////////////////////////////////////

	public function EventAjaxUploadEditorImages() {

		$aFilesData = isset($_FILES['upload']) ? $_FILES['upload'] : array();

		$this->UploadEditorProductImages($aFilesData);
	}

	protected function UploadEditorProductImages($aFilesData) {
		$obAjax = new Gf_Ajax();

		 // существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => "Недостаочно прав для данного действия"));
		}

		$oScheme = $oProduct->getScheme();

		// Пока есть возможность из редактора загружать картинку только для Проекта
		if ($oScheme->getSchemeUrl() != Config::Get("getfunded.project_code")) {
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => "Недостаочно прав для данного действия"));
		}

		// проверка на ошибку загрузки файла
		if (!$this->PluginSimplecatalog_File_IsFileUploaded($aFilesData)
			or !$sFile = $this->PluginSimplecatalog_File_UploadProductImageFile($aFilesData, $oProduct)
		) {
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => $this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_file_upload')));
		}

		// Пробуем заресайзить и сохранить картнки на диске
		$arResImageFile = $this->PluginSimplecatalog_Images_GetImageWithSize(
			$sFile, Config::Get("getfunded.editor_img_width_max"), Config::Get("getfunded.editor_img_height_max"),
			$oScheme->getExactImageProportionsEnabled(), null, true, Config::Get("getfunded.project_code"), array("editor_image" => 1)
		);

		// Если не удалось создать картинки
		if ($arResImageFile["res"] == "fail") {
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => $this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_file_upload')));
		}

		// Сохраняем картинки в базе
		$oEntEditor = Engine::GetEntity('PluginSimplecatalog_Images_Image');

		// сохраняем картинку
		$oEntEditor->setTargetId($oProduct->getId());
		$oEntEditor->setTargetType(PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
		$oEntEditor->setFilePath($arResImageFile["file_main"]);

		if (!$oEntEditor->_Validate()) {
			//$this->Message_AddError($oEntEditor->_getValidateError(), $this->Lang_Get('error'));
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => $this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_file_upload')));
		}

		if (!$oEntEditor->Save()) {
			$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
			                                "error_message" => $this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_file_upload')));
		}

		$obAjax->showResultEditor(array("func_num" => getRequest('CKEditorFuncNum'),
		                                "image_path" => $arResImageFile["file_main"],
		                                "error_message" => ""));
	}

	/////////////////////////////////////////////////////////////////////
	// END - Загрузка картинок из редактора
	/////////////////////////////////////////////////////////////////////

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
			// проверка на ошибку загрузки файла
			if (!$this->PluginSimplecatalog_File_IsFileUploaded($aFile)
				or !$sFile = $this->PluginSimplecatalog_File_UploadProductImageFile($aFile, $oProduct)
			) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
				continue;
			}

			//////////////////////////////////////////////////
			// Для Проекта  - создаем доп. картинку превью
			if ($oScheme->getSchemeUrl() == Config::Get("getfunded.project_code")) {

				// Если у проекта уже загружена картинка -> вывод ошибки
				if ($oProduct->getImageDetail()) {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
					return false;
				}

				//////////////////////////////////////////////////
				//  START - Images for project
				//////////////////////////////////////////////////

				// Пробуем заресайзить и сохранить картнки на диске
				$arResImageFile = $this->PluginSimplecatalog_Images_GetImageWithSize(
					$sFile, $oScheme->getImageWidth(), $oScheme->getImageHeight(),
					$oScheme->getExactImageProportionsEnabled(), null, true, Config::Get("getfunded.project_code")
				);

				// Если не удалось создать картинки
				if ($arResImageFile["res"] == "fail") {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
					continue;
				}

				// Сохраняем картинки в базе
				$oEntDetail = Engine::GetEntity('PluginSimplecatalog_Images_Image');

				// сохраняем большую
				$oEntDetail->setTargetId($oProduct->getId());
				$oEntDetail->setTargetType(PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
				$oEntDetail->setFilePath($arResImageFile["file_main"]);

				if (!$oEntDetail->_Validate()) {
					$this->Message_AddError($oEntDetail->_getValidateError(), $this->Lang_Get('error'));
					continue;
				}

				if (!$oEntDetail->Save()) {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
					continue;
				}

				// сохраняем preview
				$oEntPreview = Engine::GetEntity('PluginSimplecatalog_Images_Image');
				$oEntPreview->setTargetId($oProduct->getId());
				$oEntPreview->setTargetType(PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS);
				$oEntPreview->setFilePath($arResImageFile["file_preview"]);

				if (!$oEntPreview->_Validate()) {
					$this->Message_AddError($oEntPreview->_getValidateError(), $this->Lang_Get('error'));
					continue;
				}

				if (!$oEntPreview->Save()) {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
					continue;
				}

				// Обновляем запись проекта - добавляем id созданных кратинок
				$oProduct->setImageDetail($oEntDetail->getId());
				$oProduct->setImagePreview($oEntPreview->getId());

				if (!$oProduct->save()) {
					$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.error_with_image_file', array('name' => $aFile['name'])));
					continue;
				}

				// добавить в список для вывода на форме
				$aImages = array(
					"image_preview" => $oEntPreview,
					"image_detail" => $oEntDetail
				);
				//////////////////////////////////////////////////
				//  END - Images for project
				//////////////////////////////////////////////////

			} else {

				// сделать изображение меньшего размера (сохранить в той же директории) и удалить оригинал
				if (!$sImageFile = $this->PluginSimplecatalog_Images_GetImageWithSize($sFile, $oScheme->getImageWidth(), $oScheme->getImageHeight(), $oScheme->getExactImageProportionsEnabled())
				) {
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

				// добавить в список для вывода на форме
				array_unshift($aImages, $oEnt);
			}
		}

		// если все файлы успешно загружены

		// Для Проекта
		if ($oScheme->getSchemeUrl() == Config::Get("getfunded.project_code")) {
			if (isset($aImages["image_preview"]) && isset($aImages["image_detail"])
				&& is_object($aImages["image_preview"]) && is_object($aImages["image_detail"])
			) {
				$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
			}
		} else {
			if (count($aFilesData) == count($aImages)) {
				$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
			}
		}

		 // отрендерить шаблон с изображениями
		$scheme = "";
		if ($oScheme->getSchemeUrl() == Config::Get("getfunded.project_code")) {
			$scheme = $oScheme->getSchemeUrl();
		}

		$this->Viewer_AssignAjax('sText', $this->GetImagesRenderedTemplate($aImages, $scheme));
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

		//  существует ли продукт по ид из реквеста и права пользователя на редактирование этого продукта
		if (!$oProduct = $this->GetProductExistsAndRightsToEditIt()) {
			return false;
		}

		$oScheme = $oProduct->getScheme();

		// Если это картинка Проекта
		if ($oScheme->getSchemeUrl() == Config::Get("getfunded.project_code")) {

			// Находинки картинки данного проекта
			$aFoundImages = $this->PluginSimplecatalog_Images_GetImageItemsByFilter(
				array(
				     //"target_type" => $oProduct->getId(),
				     'id IN' => array($oProduct->getImageDetail(), $oProduct->getImagePreview()),
				     "custom" => array(
					     "select_fields" => array("id", "file_path"),
					     "result_plain" => true
				     )
				)
			);

			// Удаляем картинки с диска
			foreach($aFoundImages as $arImage) {
				@unlink(Config::Get('path.root.server') . $arImage["file_path"]);
			}

			// Удаляем картинки из таблицы БД и отвязываем их от проекта
			$this->PluginSimplecatalog_Product_RemoveProjectImages(
				array(
				     "project_id" => $oProduct->getId(),
				     "image_detail_id" => $oProduct->getImageDetail(),
				     "image_preview_id" => $oProduct->getImagePreview()
				)
			);

			$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));

		} else {
			// существует ли такое изображение
			if (!$oImage = $this->PluginSimplecatalog_Images_MyGetImageByTargetIdAndTargetTypeAndId(
				$oProduct->getId(),
				PluginSimplecatalog_ModuleImages::TARGET_TYPE_PRODUCTS,
				(int) getRequest('image_id')
			)) {
				$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.image_upload.image_not_found'), $this->Lang_Get('error'));
				return false;
			}
			//  удалить изображение
			$oImage->Delete();
			$this->Message_AddNotice($this->Lang_Get('plugin.simplecatalog.notices.done'));
		}
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
	protected function GetImagesRenderedTemplate($aImages, $scheme = "") {
		$oViewer = $this->Viewer_GetLocalViewer();
		$oViewer->Assign('aImages', $aImages);

		$template = 'helpers/images/list.tpl';
		if ($scheme == Config::Get("getfunded.project_code")) {
			$template = 'helpers/images/project_list.tpl';
		}
		$sText = $oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__) . $template);
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