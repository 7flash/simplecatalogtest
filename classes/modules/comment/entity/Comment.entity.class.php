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

class PluginSimplecatalog_ModuleComment_EntityComment extends PluginSimplecatalog_Inherits_ModuleComment_EntityComment {

	/**
	 * Получить сущность продукта комментария
	 *
	 * @return bool
	 */
	public function getProduct() {
		if ($this->getTargetType() == PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT) {
			return $this->PluginSimplecatalog_Product_MyGetProductById($this->getTargetId());
		}
		return false;
	}

}

?>