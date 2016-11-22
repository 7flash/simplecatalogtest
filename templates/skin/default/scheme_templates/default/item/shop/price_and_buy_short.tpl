
{*
	Цены продукта на кнопке "купить" (минималистичный вариант для вывода продуктов плиткой и т.п.)
*}

{if $oProduct->getModerationDone()}
	<div class="price-n-buy short js-product-count-field-wrapper">
		<div class="buy">
			{*
				количество
			*}
			<input type="hidden" class="js-product-count-field" value="1" />
			{*
				кнопка "купить"
			*}
			<a href="#" class="js-product-buy-button" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.shop.buy}">
				{*
					новая цена
				*}
				{if $oProduct->getPriceNewCalculated()}
					<s>{$oProduct->getPrice()}</s>
					{$oProduct->getPriceNewCalculated()}
				{else}
					{*
						текущая цена
					*}
					{$oProduct->getPrice()}
				{/if}
				{*
					валюта
				*}
				{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
			</a>
		</div>
	</div>
{/if}
