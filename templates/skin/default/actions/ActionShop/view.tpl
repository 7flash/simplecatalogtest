{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

<div class="Simplecatalog Shop Order View">
	{if $oOrder}

		<h2 class="page-header">
			<a href="{router page='shop'}">{$aLang.plugin.simplecatalog.shop.view.orders}</a>
			&rarr;
			{$aLang.plugin.simplecatalog.shop.view.title} <span>#{$oOrder->getId()}</span>
			{if $oOrder->getNewEnabled()}
				,
				<span>{$aLang.plugin.simplecatalog.shop.view.new}</span>
			{/if}
			,
			<span>{$oOrder->getDateAdd()}</span>
		</h2>

		{*
			контактные данные
		*}
		<h2 class="page-header"><span class="step">1</span>{$aLang.plugin.simplecatalog.shop.view.contacts}</h2>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.name}</dt>
			<dd>
				{$oOrder->getName()}
			</dd>
		</dl>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.phone}</dt>
			<dd>
				{$oOrder->getPhone()}
			</dd>
		</dl>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.comment}</dt>
			<dd>
				{$oOrder->getComment()}
			</dd>
		</dl>

		{*
			доставка
		*}
		<h2 class="page-header"><span class="step">2</span>{$aLang.plugin.simplecatalog.shop.view.delivery}</h2>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.delivery_type}</dt>
			<dd>
				{if $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF}
					{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.self}
				{elseif $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}
					{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.courier}
				{else}
					{hook run='sc_shop_show_custom_delivery_type' sType=$oOrder->getDeliveryType()}
				{/if}
			</dd>
		</dl>

		{*
			показывать адрес только если доставка через курьера
		*}
		{if $oOrder->getDeliveryType() == PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.adress}</dt>
				<dd>
					{$oOrder->getGeoName()}
					{if $oOrder->getExactAdress()}
						,
						{$oOrder->getExactAdress()}
					{/if}
				</dd>
			</dl>

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.receiver_name}</dt>
				<dd>
					{$oOrder->getReceiverName()}
				</dd>
			</dl>
		{/if}

		{hook run='sc_shop_view_adress' oOrder=$oOrder}

		{*
			оплата
		*}
		<h2 class="page-header"><span class="step">3</span>{$aLang.plugin.simplecatalog.shop.view.payment}</h2>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.payment_type}</dt>
			<dd>
				{if $oOrder->getPaymentType() == PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_CASH}
					{$aLang.plugin.simplecatalog.shop.order.options.payment_type.cash}
				{elseif $oOrder->getPaymentType() == PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_NONCASH}
					{$aLang.plugin.simplecatalog.shop.order.options.payment_type.noncash}
				{else}
					{hook run='sc_shop_show_custom_payment_type' sType=$oOrder->getPaymentType()}
				{/if}
			</dd>
		</dl>

		<dl class="w50p mb-20">
			<dt>{$aLang.plugin.simplecatalog.shop.list.table_header.total_price}</dt>
			<dd>
				{$oOrder->getTotalPrice()}
				<span>{$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}</span>
			</dd>
		</dl>

		{*
			заказ
		*}
		<h2 class="page-header">{$aLang.plugin.simplecatalog.shop.view.order_list}</h2>

		{*
			список продуктов корзины
		*}
		<div class="mb-20 oh cb">
			{include file="{$aTemplatePathPlugin.simplecatalog}helpers/cart/list.tpl" bDisableActiveElements=true}
		</div>

		{*
			смена статуса только для новых заказов
		*}
		{if $oOrder->getNewEnabled()}
			<a href="{$oOrder->getChangeStatusDoneUrl()}" class="button button-primary js-question">{$aLang.plugin.simplecatalog.shop.view.setdone}</a>
		{/if}

	{/if}
</div>

{include file='footer.tpl'}