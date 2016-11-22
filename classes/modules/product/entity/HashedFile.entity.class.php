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
 * Сущность для работы данными доступа к защищенным ссылкам на скачивание файлов
 *
 */

class PluginSimplecatalog_ModuleProduct_EntityHashedFile extends Entity {


	/**
	 * Совпадает ли айпи записи и текущего пользователя
	 *
	 * @return bool
	 */
	public function getIpIsTheSame() {
		return $this->getIp() == func_getIp();
	}


	/**
	 * Вышло ли время доступа к ссылке
	 *
	 * @return bool
	 */
	public function getAccessTimeIsUp() {
		return $this->PluginSimplecatalog_Product_GetHashedFileTimeIsUp($this->getDate());
	}


	/**
	 * Получить сущность поля продукта файла
	 *
	 * @return mixed
	 */
	public function getProductField() {
		return $this->PluginSimplecatalog_Product_MyGetFieldsById($this->getProductFieldId());
	}

}

?>