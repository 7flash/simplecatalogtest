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

class PluginSimplecatalog_BlockSameuserproducts extends Block {


	public function Exec() {
		/*
		 * можно найти продукт через УРЛ, но данный метод уменьшит количество запросов к БД
		 */
		if ($oProduct = $this->Viewer_GetSmartyObject()->getTemplateVars('oProduct') and $oScheme = $this->Viewer_GetSmartyObject()->getTemplateVars('oScheme')) {
			$aSameProducts = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE
				),
				$oProduct->getUser(),
				$oScheme,
				/*
				 * пагинация будет перекрыта ниже
				 */
				0,
				0,
				$this->PluginSimplecatalog_Product_GetDefaultProductSortingOrder(),
				array(
					/*
					 * исключить текущий продукт
					 */
					'#where' => array('id <> ?d' => array($oProduct->getId())),
					/*
					 * перекрыть пагинацию чтобы использовать лимит без подсчета общего количества продуктов пользователя
					 */
					'#page' => null,
					/*
					 * показать максимум 5 продуктов
					 */
					'#limit' => array(0, 5),
					'#with' => array('scheme'),
				)
			);
			$this->Viewer_Assign('aSameProducts', $aSameProducts);
		}
	}
	
}

?>