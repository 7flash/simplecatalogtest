{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div class="Simplecatalog Shop Order">
	{*
		если в корзине есть продукты
	*}
	{if $aCartData and $aCartData.count > 0}
		<form action="{router page='shop'}order" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

			<h2 class="page-header">{$aLang.plugin.simplecatalog.shop.order.title}</h2>

			{*
				список продуктов корзины
			*}
			<div class="mb-20 oh cb">
				{include file="{$aTemplatePathPlugin.simplecatalog}helpers/cart/list.tpl" bDisableActiveElements=true}
			</div>


			{*
				контактные данные
			*}
			<h2 class="page-header"><span class="step">1</span>{$aLang.plugin.simplecatalog.shop.order.contacts.title}</h2>

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.order.contacts.name}</dt>
				<dd>
					<input type="text" name="name" value="{$_aRequest.name}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.shop.order.contacts.name_ph}" />
				</dd>
			</dl>

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.order.contacts.phone}</dt>
				<dd>
					<input type="text" name="phone" value="{$_aRequest.phone}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.shop.order.contacts.phone_ph}" />
				</dd>
			</dl>

			{*
				если комментарий заполнен (после попытки сохранить форму), то выводить поле с комментарием вместо ссылки открытия формы
			*}
			{if !$_aRequest.comment}
				<a href="#" class="add-comment-link js-sc-order-add-comment">{$aLang.plugin.simplecatalog.shop.order.contacts.add_comment}</a>
			{/if}

			<dl class="w50p mb-20 js-sc-order-comment" {if !$_aRequest.comment}style="display: none;"{/if}>
				<dt>{$aLang.plugin.simplecatalog.shop.order.contacts.comment}</dt>
				<dd>
					<textarea name="comment" class="input-text input-width-250">{$_aRequest.comment}</textarea>
				</dd>
			</dl>


			{*
				доставка и оплата
			*}
			<h2 class="page-header"><span class="step">2</span>{$aLang.plugin.simplecatalog.shop.order.options.title}</h2>

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.order.options.delivery}</dt>
				<dd>
					<select name="delivery_type" class="input-text input-width-250 js-sc-order-delivery-select">
						<option value="{PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF}"
								{if $_aRequest.delivery_type==PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_SELF}selected="selected"{/if}
								>{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.self}</option>
						<option value="{PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}"
								{if $_aRequest.delivery_type==PluginSimplecatalog_ModuleShop::DELIVERY_TYPE_COURIER}selected="selected"{/if}
								>{$aLang.plugin.simplecatalog.shop.order.options.delivery_types.courier}</option>
						{hook run='sc_shop_order_add_delivery_type_item'}
					</select>
				</dd>
			</dl>

			{hook run='sc_shop_order_after_delivery_type_select'}

			<div class="js-sc-order-adress-data" style="display: none;">
				<dl class="w50p mb-20">
					<dt>{$aLang.plugin.simplecatalog.shop.order.options.adress}</dt>
					<dd>
						{include file="{$aTemplatePathPlugin.simplecatalog}helpers/geo/select_geo.tpl"}
					</dd>
				</dl>

				<dl class="w50p mb-20">
					<dt>{$aLang.plugin.simplecatalog.shop.order.options.exact_adress}</dt>
					<dd>
						<input type="text" name="exact_adress" value="{$_aRequest.exact_adress}" class="input-text input-width-250"
							   placeholder="{$aLang.plugin.simplecatalog.shop.order.options.exact_adress_ph}" />
					</dd>
				</dl>
			</div>

			<dl class="w50p mb-20 js-sc-order-receiver-name" style="display: none;">
				<dt>{$aLang.plugin.simplecatalog.shop.order.options.receiver_name}</dt>
				<dd>
					<input type="text" name="receiver_name" value="{$_aRequest.receiver_name}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.shop.order.options.receiver_name_ph}" />
				</dd>
			</dl>

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.shop.order.options.payment}</dt>
				<dd>
					<select name="payment_type" class="input-text input-width-250">
						<option value="{PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_CASH}"
								{if $_aRequest.payment_type==PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_CASH}selected="selected"{/if}
								>{$aLang.plugin.simplecatalog.shop.order.options.payment_type.cash}</option>
						<option value="{PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_NONCASH}"
								{if $_aRequest.payment_type==PluginSimplecatalog_ModuleShop::PAYMENT_TYPE_NONCASH}selected="selected"{/if}
								>{$aLang.plugin.simplecatalog.shop.order.options.payment_type.noncash}</option>
						{hook run='sc_shop_order_add_payment_type_item'}
					</select>
				</dd>
			</dl>

			{hook run='sc_shop_order_after_payment_type_select'}

			<input type="submit" class="button button-primary js-question" name="submit_order" value="{$aLang.plugin.simplecatalog.shop.order.options.submit}" />
		</form>
	{/if}
</div>

{include file='footer.tpl'}