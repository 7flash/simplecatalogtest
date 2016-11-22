
{*
	Вывод одной связи как изображения
*}

<div class="listed-as-images">
	{assign var=oLinkedProduct value=$oLink->getProduct()}
	<div class="base-wrapper">
		<div class="product-image">
			<a href="{$oLinkedProduct->getItemShowWebPath()}">
				<img src="{$oLinkedProduct->getFirstImageOrDefaultPlaceholderPath()}"
					 border="0" alt="{$oLinkedProduct->getFirstFieldTitle()|escape:'html'}" title="{$oLinkedProduct->getFirstFieldTitle()|escape:'html'}" />
			</a>
		</div>
		<div class="title-wrapper">
			<a href="{$oLinkedProduct->getItemShowWebPath()}" title="{$oLinkedProduct->getFirstFieldTitle()|escape:'html'}">{$oLinkedProduct->getFirstFieldTitle(15)}</a>
		</div>
	</div>
	{*
		если включен функционал интернет-магазина и продукт платный
	*}
	{if $oLinkedProduct->getScheme()->getShopEnabled() and floatval($oLinkedProduct->getPrice())}
		<div class="ta-c mt5">
			{sc_scheme_template scheme=$oScheme file="item/shop/price_and_buy_short.tpl" oProduct=$oLinkedProduct}
		</div>
	{/if}
</div>
