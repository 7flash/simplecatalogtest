
{*
	Цены продукта и кнопка "купить"
*}

<div class="price-n-buy js-product-count-field-wrapper">
	{*
		кнопка "купить" и количество
	*}
	{if $oProduct->getModerationDone()}
		<div class="fl-r buy">
			{*
				включен ли выбор количества товара возле кнопки "купить"
			*}
			{if Config::Get('plugin.simplecatalog.product.shop.item_count_field_near_buy_button')}
				<div class="items-to-buy-field-and-controls">
					{*
						уменьшить
					*}
					<div class="cp product-count-field-control js-product-count-field-minus"><i class="sc-icon-minus"></i></div>

					<input type="text" class="input-text input-width-30 ta-c js-product-count-field" value="1" />

					{*
						увеличить
					*}
					<div class="cp product-count-field-control js-product-count-field-plus"><i class="sc-icon-plus"></i></div>
				</div>
			{else}
				<input type="hidden" class="js-product-count-field" value="1" />
			{/if}
			{*
				кнопка "купить"
			*}
			<a href="#" class="js-product-buy-button" data-product-id="{$oProduct->getId()}">{$aLang.plugin.simplecatalog.shop.buy}</a>
		</div>
	{/if}
	{*
		цены
	*}
	{sc_scheme_template scheme=$oScheme file="item/shop/price.tpl"}
</div>
