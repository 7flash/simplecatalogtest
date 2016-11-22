
{*
	Карточка продукта получаемая через ембед код
*}

{if $oProduct}
	<div class="one-product-wrapper embed-product">
		{*
			первое изображение (главное)
		*}
		{if $oFirstProductImage = $oProduct->getFirstImage()}
			<div class="product-images-list">
				<img class="main" src="{$oFirstProductImage->getFilePath()}" alt="{$oProduct->getFirstFieldTitle()|escape:'html'}" title="{$oProduct->getFirstFieldTitle()|escape:'html'}" />
			</div>
		{/if}
		<div class="base-info">
			{*
				заголовок
			*}
			<h2 class="page-header">
				<a href="{$oScheme->getCatalogItemsWebPath()}" target="_top">{$oScheme->getSchemeName()}</a>
				&rarr;
				<a href="{$oProduct->getItemShowWebPath()}" target="_top">{$oProduct->getFirstFieldTitle()}</a>
			</h2>
			{*
				если включен функционал интернет-магазина и продукт платный
			*}
			{if $oScheme->getShopEnabled() and floatval($oProduct->getPrice())}
				<div class="price-n-buy">
					{*
						кнопка "купить" ведет на страницу продукта
					*}
					<div class="fl-r buy">
						<a href="{$oProduct->getItemShowWebPath()}" target="_top">{$aLang.plugin.simplecatalog.shop.buy}</a>
					</div>
					{*
						цены
					*}
					{sc_scheme_template scheme=$oScheme file="item/shop/price.tpl"}
				</div>
			{/if}
		</div>
	</div>
{/if}
