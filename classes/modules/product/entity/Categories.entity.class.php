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

class PluginSimplecatalog_ModuleProduct_EntityCategories extends EntityORM {

	/*
	 * Правила валидации данных сущности
	 */
	protected $aValidateRules = array(
		array('id', 'number', 'integerOnly' => true),
		array('product_id', 'number', 'integerOnly' => true),
		array('category_id', 'number', 'integerOnly' => true),
	);


	/*
	 * Связи сущности
	 */
	protected $aRelations = array(
		/*
		 * сущности записывать в полном формате
		 */
		'category' => array(EntityORM::RELATION_TYPE_BELONGS_TO, 'PluginSimplecatalog_ModuleCategory_EntityCategory', 'category_id'),
	);


}

?>