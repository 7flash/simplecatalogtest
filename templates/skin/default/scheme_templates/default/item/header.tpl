
	{*
		заголовок
	*}
	<h2 class="page-header" itemprop="headline">
		{if $bProductList}
			<a href="{$oProduct->getItemShowWebPath()}">{$oProduct->getFirstFieldTitle()}</a>
		{else}
			<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>
			&rarr;
			{$oProduct->getFirstFieldTitle()}
		{/if}
		{*
			иконки статуса модерации
		*}
		{sc_scheme_template scheme=$oScheme file="item/moderation_icons.tpl"}
	</h2>
	{*
		социальные кнопки
	*}
	{sc_scheme_template scheme=$oScheme file="item/social/list.tpl"}
	{*
		категории продукта
	*}
	{sc_scheme_template scheme=$oScheme file="item/categories/list.tpl"}
	{*
		модерация
	*}
	{if $bShowModeratorControls and $oUserCurrent and $oUserCurrent->getUserCanModerateProductsBySchemeOrIsAdmin($oScheme)}
		<ul class="controls">
			<li>
				<span>{$aLang.plugin.simplecatalog.Products.moderation.question_to_approve_product}</span>
			</li>
			<li>
				<a href="{$oProduct->getItemModerationApproveWebPath()}" class="approve js-question"><i class="sc-icon-ok"></i>{$aLang.plugin.simplecatalog.Yes}</a>
			</li>
			<li>
				<a href="{$oProduct->getItemModerationDisapproveWebPath()}" class="disapprove js-question"><i class="sc-icon-minus"></i>{$aLang.plugin.simplecatalog.No}</a>
			</li>
			{hook run='sc_product_item_header_moderation_controls_item' oProduct=$oProduct oScheme=$oScheme bProductList=$bProductList}
		</ul>
	{/if}
	{*
		редактирование и удаление пользователем
	*}
	{if $oUserCurrent and $oUserCurrent->getCanManageProduct($oProduct)}
		<ul class="controls">
			<li>
				<a href="{$oProduct->getItemEditWebPath()}" class="edit"><i class="sc-icon-edit"></i>{$aLang.plugin.simplecatalog.Edit}</a>
			</li>
			<li>
				<a href="{$oProduct->getItemDeleteWebPath()}" class="delete js-question"><i class="sc-icon-remove"></i>{$aLang.plugin.simplecatalog.Delete}</a>
			</li>
			{hook run='sc_product_item_header_edit_controls_item' oProduct=$oProduct oScheme=$oScheme bProductList=$bProductList}
		</ul>
	{/if}
	{hook run='sc_product_item_header_after_controls' oProduct=$oProduct oScheme=$oScheme bProductList=$bProductList}
	{*
		сравнение продуктов
	*}
	{if $oProduct->getModerationDone()}
		<div class="fl-r mb-20 cb">
			<div class="compare-wrapper">
				{$aLang.plugin.simplecatalog.Products.Items.Comparing.info}:
				{*
					есть ли продукт в списке сравнений
				*}
				{if $oProduct->getProductInCompareList()}
					{*
						в сравнении есть хотя бы два продукта
					*}
					{if $oProduct->getInCompareListAreAtLeastTwoProducts()}
						<a href="{$oProduct->getCompareProductsUrl()}" rel="nofollow">
							{$aLang.plugin.simplecatalog.Products.Items.Comparing.comparing_ready}
						</a>
					{else}
						{$aLang.plugin.simplecatalog.Products.Items.Comparing.added_first}
					{/if}
				{else}
					{*
						добавить продукт в сравнение
					*}
					<span class="compare-link-wrapper js-sc-compare-link-wrapper">
						<a href="#" class="compare-products-link active js-sc-compare-products-link" data-product-id="{$oProduct->getId()}" rel="nofollow">
							{$aLang.plugin.simplecatalog.Products.Items.Comparing.compare_link}
						</a>
					</span>
				{/if}
			</div>
		</div>
	{/if}
	{*
		если включено использование вставки кода в блоги
	*}
	{if Config::Get('plugin.simplecatalog.product.allow_embed_code') and $oProduct->getModerationDone()}
		{sc_scheme_template scheme=$oScheme file="embed/add_link.tpl"}
	{/if}
	{*
		если включен функционал интернет-магазина и продукт платный
	*}
	{if $oScheme->getShopEnabled() and floatval($oProduct->getPrice())}
		{sc_scheme_template scheme=$oScheme file="item/shop/price_and_buy.tpl"}
	{/if}
