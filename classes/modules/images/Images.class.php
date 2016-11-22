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
 * --- Модуль работы с изображениями ---
 *
 */

/*
 * tip: все публичные методы перед началом работы конвертируют все пути в серверные, при завершении работы метода все пути снова конвертируются в веб-пути
 * tip: задаваемая директория должна иметь в конце слеш
 *
 */

class PluginSimplecatalog_ModuleImages extends ModuleORM {

	/*
	 * Качество сохраняемых jpg изображений в %
	 */
	const JPG_IMAGE_QUALITY = 100;

	/*
	 * Тип привязки - изображения для продуктов
	 */
	const TARGET_TYPE_PRODUCTS = 1;

	/*
	 * Сортировка для изображений по-умолчанию
	 */
	protected $aDefaultImagesSorting = array('sorting' => 'desc');


	public function Init() {
		/*
		 * для ORM
		 */
		parent::Init();
	}


	/*
	 *
	 * --- Обработка изображений ---
	 *
	 */

	/**
	 * Получить новое изображение с нужными размерами
	 *
	 * @param string      $sFile             исходное изображение (полный путь)
	 * @param int         $iWidth            новая ширина
	 * @param int         $iHeight           новая высота
	 * @param bool        $bExactProportions нужно ли точно вырезать указанные размеры с сохранением пропорций или подгонять ближайшую сторону к нужному размеру, а другую - пропорционально
	 * @param string|null $sDir              новая директория расположения файла
	 * @param bool        $bRemoveOriginal   удалить исходное изображение (оригинал)
	 * @return bool|string                   новое изображение (полный путь)
	 */
	public function GetImageWithSize($sFile, $iWidth, $iHeight, $bExactProportions = true, $sDir = null, $bRemoveOriginal = true, $scheme = null,
		$arSetParams = array()) {

		/*
		 * нужны серверные пути
		 */
		$sFile = $this->PluginSimplecatalog_Tools_GetServerPath($sFile);
		/*
		 * есть ли файл и доступен ли он
		 */
		if (!file_exists($sFile) or !is_readable($sFile)) {
			return false;
		}

		//////////////////////////////////////////////
		// Создание основного файла

		// получить новое имя файла
		$sFileNew = $this->GetImageFileNameForSize($sFile, $iWidth, $iHeight, $sDir);

		// записать новый файл с нужными размерами и сохраненными пропорциями
		$bSuccessMain = $this->CreateResizedImageWithSavedProportion($sFile, $sFileNew, $iWidth, $iHeight, $bExactProportions);


		// Если это Проект - делаем картинку первью
		if ($scheme == Config::Get("getfunded.project_code")) {

			// Если удалось создать главное изображение -> создаем его превью
			if ($bSuccessMain && file_exists($sFileNew)) {

				// Если это картинка, загружаемая из редактора
				if (isset($arSetParams["editor_image"])) {

					$arRes = array(
						"res" => "ok",
						"file_main" => $this->PluginSimplecatalog_Tools_GetWebPathProject($sFileNew)
					);

				} else {
					$maxPreviewWidth = Config::Get("getfunded.preview_img_width_max");
					$maxPreviewHeight = Config::Get("getfunded.preview_img_height_max");
					$sFileNewPreview = $this->GetImageFileNameForSize($sFile, $maxPreviewWidth, $maxPreviewHeight, $sDir);
					$bSuccessPreview = $this->CreateResizedImageWithSavedProportion($sFile, $sFileNewPreview, $maxPreviewWidth, $maxPreviewHeight, $bExactProportions);

					// Если превью успешно создано
					if ($bSuccessPreview && file_exists($sFileNewPreview)) {
						$arRes = array(
							"res" => "ok",
							"file_main" => $this->PluginSimplecatalog_Tools_GetWebPathProject($sFileNew),
							"file_preview" => $this->PluginSimplecatalog_Tools_GetWebPathProject($sFileNewPreview)
						);
					} else {
						$arRes = array(
							"res" => "fail"
						);
					}
				}

			// Не удалось создать главное изображение
			} else {
				$arRes = array(
					"res" => "fail"
				);
			}

			// удалить загруженный оригинал
			if ($bRemoveOriginal and file_exists($sFile)) {
				$this->PluginSimplecatalog_File_RemoveFile($sFile);
			}

			return $arRes;

		} else {
			// удалить загруженный оригинал
			if ($bRemoveOriginal and file_exists($sFile)) {
				$this->PluginSimplecatalog_File_RemoveFile($sFile);
			}

			// вернуть веб-путь в случае успеха
			return ($bSuccessMain ? $this->PluginSimplecatalog_Tools_GetWebPath($sFileNew) : false);
		}
	}


	/**
	 * Получить имя файла по размерам
	 *
	 * @param      $sFile		полный путь и имя файла
	 * @param      $iWidth		ширина
	 * @param      $iHeight		высота
	 * @param null $sDir		директория для нового имени файла, если не указана - используется та же что и для исходного файла
	 * @return string
	 */
	protected function GetImageFileNameForSize($sFile, $iWidth, $iHeight, $sDir = null) {
		/*
		 * если директория не указана - установить ту же, где находится исходный файл
		 */
		$sDir = is_null($sDir) ? pathinfo($sFile, PATHINFO_DIRNAME) . '/' : $sDir;
		/*
		 * вернуть имя файла с добавленными размерами
		 */
		return $sDir . pathinfo($sFile, PATHINFO_FILENAME) . '_' . $iWidth . 'x' . $iHeight . '.' . pathinfo($sFile, PATHINFO_EXTENSION);
	}


	/**
	 * Сохранить новое изображение с нужными размерами с сохранением пропорций
	 *
	 * @param string $sImageFileOriginal серверный путь к исходному изображению
	 * @param string $sImageFileToSave   серверный путь и имя файла для сохранения нового изображения
	 * @param int    $iWidth             новая ширина
	 * @param int    $iHeight            новая высота
	 * @param bool   $bExactProportions  нужно ли точно вырезать указанные размеры с сохранением пропорций или подгонять ближайшую сторону к нужному размеру, а другую - пропорционально
	 * @return bool						 результат
	 */
	protected function CreateResizedImageWithSavedProportion($sImageFileOriginal, $sImageFileToSave, $iWidth, $iHeight, $bExactProportions) {
		$oImage = $this->Image_CreateImageObject($sImageFileOriginal);
		/*
		 * если не удалось создать изображение или файл не является изображением
		 */
		if ($oImage->get_last_error()) {
			return false;
		}
		/*
		 * не увеличивать изображение если оно маленькое
		 */
		$iWidth = $oImage->get_image_params('width') < $iWidth ? $oImage->get_image_params('width') : $iWidth;
		$iHeight = $oImage->get_image_params('height') < $iHeight ? $oImage->get_image_params('height') : $iHeight;
		/*
		 * получить изображение нужных пропорций
		 */
		if ($bExactProportions) {
			if (!$this->Image_CropProportion($oImage, $iWidth, $iHeight, true)) {
				return false;
			}
		}
		/*
		 * изменить размер изображения (если изображение было уже в нужных пропорциях, то предыдущий метод не будет изменять изображение(!), поэтому нужно всеравно ресайзить)
		 * tip: этот метод не из лс модуля Image
		 */
		if (!$oImage->resize($iWidth, $iHeight, true, true)) {
			return false;
		}
		/*
		 * установить качество для JPEG формата
		 */
		if ($oImage->get_image_params('format') == 'jpg') {
			$oImage->set_jpg_quality(self::JPG_IMAGE_QUALITY);
		}
		/*
		 * сохранить в том же формате и установить атрибуты
		 */
		$oImage->output(null, $sImageFileToSave);
		@chmod($sImageFileToSave, 0666);
		return true;
	}


	/*
	 *
	 * --- Методы ORM ---
	 *
	 */

	/**
	 * Получить изображение по ид
	 *
	 * @param $iId				ид изображения
	 * @return mixed
	 */
	public function MyGetImageById($iId) {
		return $this->GetImageById($iId);
	}


	/**
	 * Получить изображение по ид, типу привязки и ид изображения
	 *
	 * @param $iTargetId		ид привязки
	 * @param $iTargetType		тип привязки
	 * @param $iId				ид изображения
	 * @return mixed
	 */
	public function MyGetImageByTargetIdAndTargetTypeAndId($iTargetId, $iTargetType, $iId) {
		return $this->GetImageByTargetIdAndTargetTypeAndId($iTargetId, $iTargetType, $iId);
	}


	/**
	 * Получить все отсортированные изображения по ид и типу привязки
	 *
	 * @param $iTargetId		ид привязки
	 * @param $iTargetType		тип привязки
	 * @return mixed
	 */
	public function MyGetImageItemsSortedByTargetIdAndTargetType($iTargetId, $iTargetType) {
		return $this->GetImageItemsByTargetIdAndTargetType($iTargetId, $iTargetType, array('#order' => $this->aDefaultImagesSorting));
	}


	/**
	 * Получить все изображения по ид, типу привязки и массиву ид изображений
	 *
	 * @param       $iTargetId           ид привязки
	 * @param       $iTargetType         тип привязки
	 * @param       $aIds                массив ид изображений
	 * @param array $aParams             дополнительные параметры
	 * @return mixed
	 */
	public function MyGetImageItemsByTargetIdAndTargetTypeAndIdIn($iTargetId, $iTargetType, $aIds, $aParams = array()) {
		return $this->GetImageItemsByTargetIdAndTargetTypeAndIdIn($iTargetId, $iTargetType, $aIds, $aParams);
	}


	/*
	 *
	 * --- Сортировка ---
	 *
	 */

	/**
	 * Получить текущую максимальную сортировку для ид и типа привязки
	 *
	 * @param $iTargetId		ид привязки
	 * @param $iTargetType		тип привязки
	 * @return int
	 */
	protected function MyGetMaxCurrentSortingByTargetIdAndTargetType($iTargetId, $iTargetType) {
		/*
		 * получить изображение с максимальной сортировкой для ид и типа привязки
		 * tip: используется получение массива элементов, т.к. получение одного (GetItemByFilter) в маппере орм не учитывает сортировку
		 */
		if ($aImageLast = $this->GetImageItemsByTargetIdAndTargetType($iTargetId, $iTargetType, array(
			'#order' => array('sorting' => 'desc'),
			'#limit' => array(1)
		))) {
			return (int) array_shift($aImageLast)->getSorting();
		}
		return 0;
	}


	/**
	 * Получить свободный номер сортировки для ид и типа привязки
	 *
	 * @param $iTargetId		ид привязки
	 * @param $iTargetType		тип привязки
	 * @return int
	 */
	public function MyGetNextFreeSortingByTargetIdAndTargetType($iTargetId, $iTargetType) {
		return $this->MyGetMaxCurrentSortingByTargetIdAndTargetType($iTargetId, $iTargetType) + 1;
	}


	/**
	 * Получить массив сортировки по-умолчанию для орм
	 *
	 * @return array
	 */
	public function GetDefaultSortingOrder() {
		return $this->aDefaultImagesSorting;
	}

	
}

?>