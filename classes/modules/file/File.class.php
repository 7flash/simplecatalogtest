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
 * --- Модуль работы с загрузкой файлов ---
 *
 */

/*
 * tip: все публичные методы перед началом работы конвертируют все пути в серверные,
 * 		при завершении работы метода все пути снова конвертируются в веб-пути
 */

class PluginSimplecatalog_ModuleFile extends Module {


	public function Init() {}


	/**
	 * Загрузить файл поля продукта
	 *
	 * @param $aFile				массив данных файла
	 * @return bool
	 */
	public function UploadContentFile($aFile) {
		return $this->UploadFileIntoPath($aFile, $this->GetFullDirForCurrentUserContent());
	}


	/**
	 * Загрузить изображение категории
	 *
	 * @param $aFile				массив данных файла
	 * @param $oScheme				сущность схемы
	 * @return bool
	 */
	public function UploadCategoryImageFile($aFile, $oScheme) {
		return $this->UploadFileIntoPath($aFile, $this->GetFullDirForCategory($oScheme));
	}


	/**
	 * Загрузить изображение продукта
	 *
	 * @param $aFile				массив данных файла
	 * @param $oProduct				сущность продукта
	 * @return bool
	 */
	public function UploadProductImageFile($aFile, $oProduct) {
		return $this->UploadFileIntoPath($aFile, $this->GetFullDirForProduct($oProduct));
	}


	/*
	 *
	 * --- Системные методы ---
	 *
	 */

	/**
	 * Загружен ли файл
	 *
	 * @param array $aFile	массив данных файла
	 * @return bool
	 */
	public function IsFileUploaded($aFile) {
		return is_array($aFile) and isset($aFile['tmp_name']) and isset($aFile['error']) and $aFile['error'] == 0 and is_uploaded_file($aFile['tmp_name']);
	}


	/**
	 * Загрузить файл в указанный путь
	 *
	 * @param array  $aFile массив данных файла
	 * @param string $sDir  директория, куда нужно загрузить файл
	 * @return bool|string	false или веб-путь к загруженному файлу
	 */
	protected function UploadFileIntoPath($aFile, $sDir) {
		/*
		 * загружен ли файл
		 */
		if (!$this->IsFileUploaded($aFile)) {
			return false;
		}
		/*
		 * проверить директорию
		 */
		$this->CreateDirectory($sDir);
		/*
		 * сгенерировать имя файла
		 */
		$sFileNew = $this->GetFileName($sDir, $aFile['name']);
		/*
		 * переместить файл в конечную директорию
		 */
		if (!@move_uploaded_file($aFile['tmp_name'], $sFileNew)) {
			return false;
		}
		/*
		 * всегда возвращать веб-путь
		 */
		return $this->PluginSimplecatalog_Tools_GetWebPath($sFileNew);
	}


	/**
	 * Получить корректное имя файла по исходному значению
	 *
	 * @param string $sDir         директория, куда будет перемещен файл
	 * @param string $sFileNameRaw исходное значение
	 * @return string              полный путь
	 */
	protected function GetFileName($sDir, $sFileNameRaw) {
		$aFileInfo = pathinfo($sFileNameRaw);

		//  получить полный путь без расширения
		//$sFile = $sDir . sc_str_translit($aFileInfo['filename']);
		$sFile = $sDir . md5("file_".$aFileInfo['filename'].microtime().uniqid(mt_rand(), true));

		// проверить нет ли файла с таким же именем и расширением
		while (file_exists($sFile . '.' . $aFileInfo['extension'])) {
			$sFile .= '-' . func_generator(3);
		}
		return $sFile . '.' . $aFileInfo['extension'];
	}


	/**
	 * Создать директорию
	 *
	 * @param $sDirToCheck		директория
	 */
	public function CreateDirectory($sDirToCheck) {
		if (!is_dir($sDirToCheck)) {
			@mkdir($sDirToCheck, 0755, true);
		}
	}


	/**
	 * Удалить файл
	 *
	 * @param $sFile		полный путь к файлу
	 */
	public function RemoveFile($sFile) {
		@unlink($this->PluginSimplecatalog_Tools_GetServerPath($sFile));
	}


	/*
	 *
	 * --- Получение путей ---
	 *
	 */

	/**
	 * Получить директорию для пользователя по ид
	 *
	 * @param $iUserId			ид пользователя
	 * @return string
	 */
	private function GetDirForUser($iUserId) {
		return preg_replace('#(.{2})#U', '\\1/', str_pad($iUserId, 6, '0', STR_PAD_LEFT)) . date('Y/m/d/');
	}


	/**
	 * Получить полный путь к директории для загрузки контента для типа поля "файл"
	 *
	 * @return string			директория
	 */
	protected function GetFullDirForCurrentUserContent() {
		return Config::Get('plugin.simplecatalog.upload_folder.content') . $this->GetDirForUser($this->User_GetUserCurrent()->getId());
	}


	/**
	 * Получить полный путь к директории для загрузки изображений для категорий
	 *
	 * @param $oScheme			сущность схемы
	 * @return string			директория
	 */
	public function GetFullDirForCategory($oScheme) {
		return Config::Get('plugin.simplecatalog.upload_folder.categories') . $oScheme->getId() . '/';
	}


	/**
	 * Получить полный путь к директории для загрузки изображений для продуктов
	 *
	 * @param $oProduct			сущность продукта
	 * @return string			директория
	 */
	protected function GetFullDirForProduct($oProduct) {
		return Config::Get('plugin.simplecatalog.upload_folder.products') . $oProduct->getId() . '/';
	}


}

?>