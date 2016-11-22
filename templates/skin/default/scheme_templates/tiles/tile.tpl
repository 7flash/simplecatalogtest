
{*
	Вывод продукта плиткой
*}

<div class="one-product-wrapper tile">
	<div class="header-wrapper">
		{*
			первое изображение (главное)
			tip: можно подключить стандартный вывод изображений, но тогда не будет изображения по-умолчанию в случае отсутствия основного
		*}
		<div class="product-images-list">
			<a href="{$oProduct->getItemShowWebPath()}" class="img-wrapper" title="{$oProduct->getFirstFieldTitle()}">
				<img class="tile" src="{$oProduct->getFirstImageOrDefaultPlaceholderPath()}" alt="{$oProduct->getFirstFieldTitle()}" title="{$oProduct->getFirstFieldTitle()}" />
			</a>
			{*
				количество комментариев
			*}
			{if $oProduct->getCommentsEnabled()}
				<a class="tile-info-item bottom-left" href="{$oProduct->getItemShowWebPath()}#comments"
				   title="{$aLang.plugin.simplecatalog.common.comments_count}"><i class="sc-icon-comment sc-icon-white"></i>{$oProduct->getCommentCount()}</a>
			{/if}
			{*
				количество просмотров продукта
			*}
			{if $iViewsCount = $oProduct->getViewsCount()}
				<a class="tile-info-item bottom-right" href="{$oProduct->getItemShowWebPath()}"
				   title="{$aLang.plugin.simplecatalog.common.views_count}"><i class="sc-icon-eye-open sc-icon-white"></i>{$iViewsCount}</a>
			{/if}
			{*
				количество меток на карте
			*}
			{if $oScheme->getMapItemsEnabled()}
				{$iProductMapItemsCount = count($oProduct->getMapItems())}
				{if $iProductMapItemsCount}
					<a class="tile-info-item top-right" href="{$oProduct->getItemShowWebPath()}#product_map"
					   title="{$aLang.plugin.simplecatalog.common.map_items_count}"><i class="sc-icon-map-marker sc-icon-white"></i>{$iProductMapItemsCount}</a>
				{/if}
			{/if}
		</div>
		{*
			заголовок продукта и цены
		*}
		<div class="product-header">
			{*
				заголовок и иконки статуса модерации
			*}
			<h2 class="mb-15 product-title">
				<a href="{$oProduct->getItemShowWebPath()}" title="{$oProduct->getFirstFieldTitle()}">{$oProduct->getFirstFieldTitle(50)}</a>
				{sc_scheme_template scheme=$oScheme file="item/moderation_icons.tpl"}
			</h2>
			{*
				категории продукта
			*}
			{sc_scheme_template scheme=$oScheme file="item/categories/list.tpl"}
			{*
				если включен функционал интернет-магазина и продукт платный
			*}
			{if $oScheme->getShopEnabled() and floatval($oProduct->getPrice())}
				{sc_scheme_template scheme=$oScheme file="item/shop/price_and_buy_short.tpl"}
			{/if}
		</div>
	</div>
</div>
