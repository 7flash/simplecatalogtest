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

class PluginSimplecatalog_ModuleUser extends PluginSimplecatalog_Inherits_ModuleUser {


	/**
	 * Проверяет является ли текущий пользователь администратором
	 * tip: копия метода из лс 2.0
	 *
	 * @param bool $bReturnUser		возвращать или нет объект пользователя
	 * @return bool|ModuleUser_EntityUser
	 */
	public function GetIsAdmin($bReturnUser = false) {
		if ($this->oUserCurrent and $this->oUserCurrent->isAdministrator()) {
			return $bReturnUser ? $this->oUserCurrent : true;
		}
		return false;
	}

}

?>