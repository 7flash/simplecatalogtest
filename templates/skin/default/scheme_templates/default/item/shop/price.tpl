
{*
	Цены продукта
*}

<div class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	{*
		текущая
	*}
	<span class="main {if $oProduct->getPriceNewCalculated()}old{else}active{/if}" itemprop="price">{$oProduct->getPrice()}</span>
	{*
		новая
	*}
	{if $oProduct->getPriceNewCalculated()}
		<span class="new active" itemprop="price">
			{$oProduct->getPriceNewCalculated()}
		</span>
	{/if}
	{*
		валюта
	*}
	<span class="currency">
		{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
	</span>
	<meta itemprop="priceCurrency" content="{Config::Get('plugin.simplecatalog.product.shop.currency_default')}" />
	<link itemprop="availability" href="http://schema.org/InStock" />
</div>
