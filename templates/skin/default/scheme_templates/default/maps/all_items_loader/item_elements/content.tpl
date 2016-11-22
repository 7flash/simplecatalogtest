
{*
	Балун с информацией о метке продукта
*}

{*
	заголовок
*}
<div class="mb-10 map-item-header">
	<a href="{$oProduct->getItemShowWebPath()}" target="_blank">{$oProduct->getFirstFieldTitle()}</a>
</div>
{*
	содержимое
*}
<div class="mb-5 map-item-body">
	<div>
		{*
			категории продукта
		*}
		{sc_scheme_template scheme=$oScheme file="item/categories/list.tpl"
			aLocalParams=[
				'classes' => 'mb-10',
				'link_target_blank' => true
			]
		}
	</div>
	<div class="oh cb">
		{*
			первое изображение (главное)
		*}
		{if $oFirstProductImage = $oProduct->getFirstImage()}
			<div class="map-item-product-image">
				<img class="main" src="{$oFirstProductImage->getFilePath()}" alt="{$oProduct->getFirstFieldTitle()}" title="{$oProduct->getFirstFieldTitle()}" />
			</div>
		{/if}
		{*
			описание
		*}
		<div class="oh">
			{if $sDescription = $oItem->getDescription()}
				{$sDescription}
			{else}
				{$oProduct->getSeoDescription()}
			{/if}
			<span class="note">{date_format date=$oProduct->getAddDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</span>
		</div>
		{hook run='sc_mapitems_item_content' oScheme=$oScheme oProduct=$oProduct oItem=$oItem}
	</div>
</div>
{*
	футер
*}
<div class="map-item-footer">
	<div class="soft-underline mb-5"></div>

	{if $oProduct->getCommentsEnabled()}
		<a href="{$oProduct->getItemShowWebPath()}#comments" target="_blank">{$aLang.plugin.simplecatalog.Products.Item.comments.add_comment}</a>
		{*
			количество комментариев
		*}
		<span title="{$aLang.plugin.simplecatalog.common.comments_count}">(<i class="sc-icon-comment"></i>{$oProduct->getCommentCount()})</span>
		&nbsp;
	{/if}
	{*
		количество меток на карте
	*}
	{if $oScheme->getMapItemsEnabled()}
		{if $iProductMapItemsCount = count($oProduct->getMapItems())}
			<a href="{$oProduct->getItemShowWebPath()}#product_map" target="_blank"
			   title="{$aLang.plugin.simplecatalog.common.map_items_count}"><i class="sc-icon-map-marker"></i>{$iProductMapItemsCount}</a>
			&nbsp;
		{/if}
	{/if}
	{*
		количество просмотров продукта
	*}
	{if $iViewsCount = $oProduct->getViewsCount()}
		<span title="{$aLang.plugin.simplecatalog.common.views_count}"><i class="sc-icon-eye-open"></i>{$iViewsCount}</span>
	{/if}
	{hook run='sc_mapitems_item_footer' oScheme=$oScheme oProduct=$oProduct oItem=$oItem}
</div>
