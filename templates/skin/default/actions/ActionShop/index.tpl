{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

<div class="Simplecatalog Shop Orders">
	<h2 class="page-header">{$aLang.plugin.simplecatalog.shop.list.title}{if $aOrders} (<span>{count($aOrders)}</span>){/if}</h2>

	{if $aOrders and count($aOrders) > 0}
		<table class="table-items-list orders-list">
			<thead>
				<tr>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.n}</th>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.delivery_type}</th>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.adress}</th>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.payment_type}</th>

					{*product_ids*}

					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.total_price}</th>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.new}</th>
					<th>{$aLang.plugin.simplecatalog.shop.list.table_header.date_add}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$aOrders item=oOrder}
					<tr class="{if $oOrder->getNewEnabled()}new{/if}">
						<td class="ta-c">
							<a href="{$oOrder->getViewUrl()}">{$oOrder->getId()}</a>
						</td>
						<td class="ta-c">
							{*
								тип доставки
							*}
							{if $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF}
								{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.self}
							{elseif $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}
								{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.courier}
							{else}
								{hook run='sc_shop_show_custom_delivery_type' sType=$oOrder->getDeliveryType()}
							{/if}
						</td>
						<td>
							{*
								вывод адреса в зависимости от типа доставки
							*}
							{if $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF}
								&mdash;
							{elseif $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}
								{$oOrder->getGeoName()}
								{if $oOrder->getExactAdress()}
									,
									{$oOrder->getExactAdress()}
								{/if}
							{else}
								{hook run='sc_shop_index_show_adress_by_delivery_type' sType=$oOrder->getDeliveryType()}
							{/if}
						</td>
						<td class="ta-c">
							{*
								тип оплаты
							*}
							{if $oOrder->getPaymentType() == PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_CASH}
								{$aLang.plugin.simplecatalog.shop.order.options.payment_type.cash}
							{elseif $oOrder->getPaymentType() == PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_NONCASH}
								{$aLang.plugin.simplecatalog.shop.order.options.payment_type.noncash}
							{else}
								{hook run='sc_shop_show_custom_payment_type' sType=$oOrder->getPaymentType()}
							{/if}
						</td>
						<td class="ta-r">
							{$oOrder->getTotalPrice()}
							<span>{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}</span>
						</td>
						<td class="ta-c">
							{if $oOrder->getNewEnabled()}
								{$aLang.plugin.simplecatalog.Yes}
							{else}
								{$aLang.plugin.simplecatalog.No}
							{/if}
						</td>
						<td>
							{$oOrder->getDateAdd()}
						</td>
						<td>
							<a href="{$oOrder->getViewUrl()}" class="sc-icon-list" title="{$aLang.plugin.simplecatalog.View}"></a>
							<a href="{$oOrder->getDeleteUrl()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>

	{else}
		{$aLang.plugin.simplecatalog.shop.list.no_orders}
	{/if}
</div>

{include file='footer.tpl'}