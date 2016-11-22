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
 * Обработка количества скачиваний файла у поля продукта
 *
 */

class PluginSimplecatalog_HookProductFieldFileDownloadCountPerformer extends Hook {


	public function RegisterHook() {
		$this->AddHook('sc_product_field_file_download', 'ProductFileDownload');
		$this->AddHook('sc_product_field_file_delete', 'ProductFileDelete');
	}


	/**
	 * Хук эвента скачивания файла у поля продукта
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ProductFileDownload($aData) {
		
		$oProductField = $aData['oProductField'];
		/*
		 * увеличить счетчик числа загрузок
		 */
		if (Config::Get('plugin.simplecatalog.product.count_file_downloads')) {
			if (($mResult = $this->PluginSimplecatalog_Counter_IncreaseProductFieldCounter($oProductField)) !== true) {
				$this->Message_AddError($mResult, $this->Lang_Get('error'), true);
				$this->RedirectToReferer();
				return false;
			}
		}
	}


	/**
	 * Хук удаления файла у поля продукта
	 *
	 * @param $aData		параметры хука
	 * @return bool
	 */
	public function ProductFileDelete($aData) {
		
		$oProductField = $aData['oProductField'];
		/*
		 * удалить количество загрузок
		 */
		if (Config::Get('plugin.simplecatalog.product.count_file_downloads')) {
			$this->PluginSimplecatalog_Counter_DeleteProductFieldCounter($oProductField);
		}
	}


}

?>