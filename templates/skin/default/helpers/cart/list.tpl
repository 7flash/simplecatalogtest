
{*
	Вывод списка продуктов в корзине
*}

{if $aCartData and $aCartData.count > 0}
	<table class="shop-cart-items">
		<thead>
			<tr>
				<th>{$aLang.plugin.simplecatalog.shop.cart.table_header.n}</th>
				<th>{$aLang.plugin.simplecatalog.shop.cart.table_header.item}</th>
				<th>{$aLang.plugin.simplecatalog.shop.cart.table_header.price}</th>
				<th>{$aLang.plugin.simplecatalog.shop.cart.table_header.count}</th>
				<th>{$aLang.plugin.simplecatalog.shop.cart.table_header.summ}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$aCartData.collection item=aCartItem}
				{assign var=oProduct value=$aCartItem.oProduct}
				<tr>
					<td class="num">{$aCartItem@iteration}</td>

					<td class="title">
						{*
							первое изображение (главное)
						*}
						{if $oFirstProductImage = $oProduct->getFirstImage()}
							<img class="preview" src="{$oFirstProductImage->getFilePath()}" alt="{$oProduct->getFirstFieldTitle()|escape:'html'}" title="{$oProduct->getFirstFieldTitle()|escape:'html'}" />
						{/if}
						<a href="{$oProduct->getItemShowWebPath()}">{$oProduct->getFirstFieldTitle()}</a>
					</td>
					{*
						актуальная цена
					*}
					<td class="price short ta-r">
						{number_format($oProduct->getActualPrice(), 2, '.', ' ')}
						<span>{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}</span>
						{*
							значок скидки
						*}
						{if $oProduct->getPriceNewCalculated()}
							{assign var=bNewPriceExists value=true}
							<span class="new-price">*</span>
						{/if}
					</td>
					{*
						количество
					*}
					<td class="count short ta-r">
						{*
							если кнопки не выключены
						*}
						{if !$bDisableActiveElements}
							<input type="text" class="js-sc-cart-count-change input-text input-width-50" value="{$aCartItem.iCount}" data-product-id="{$oProduct->getId()}" />
							<a href="#" class="js-sc-cart-remove-item" data-product-id="{$oProduct->getId()}" title="{$aLang.plugin.simplecatalog.Delete}"><i class="sc-icon-remove"></i></a>
						{else}
							{$aCartItem.iCount}
						{/if}
					</td>
					{*
						сумма по продукту
					*}
					<td class="summ short ta-r">
						{number_format($oProduct->getSummaryPrice(), 2, '.', ' ')}
						<span>{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}</span>
					</td>

				</tr>
			{/foreach}
		</tbody>
	</table>
	{*
		общая сумма заказа
	*}
	<div class="fl-r">
		<b>{$aLang.plugin.simplecatalog.shop.cart.total}</b>:
		{number_format($aCartData.total_price, 2, '.', ' ')}
		<span>{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}</span>
	</div>
	{*
		были ли в заказе скидки
	*}
	{if $bNewPriceExists}
		<span class="new-prices">*</span> {$aLang.plugin.simplecatalog.shop.cart.new_prices_in_order}
	{/if}

{else}
	{$aLang.plugin.simplecatalog.shop.cart.empty_cart}
{/if}