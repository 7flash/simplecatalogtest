
{*
	Вывод списка продуктов
*}

{*
	форма поиска
*}
{*sc_scheme_template scheme=$oScheme file="items/search/form.tpl"*}

{*
	если есть продукты для отображения
*}
{if $aProducts and count($aProducts)>0 and SCRootStorage::IsInit()}
	{hook run='sc_product_items_before' oScheme=$oScheme aProducts=$aProducts aSortOrderData=$aSortOrderData assign=sHookResult}
	{if $sHookResult}
		<div class="product-items-hook content-before mb-20" data-scheme-template-name="{$oScheme->getTemplateName()}">
			{$sHookResult}
		</div>
	{/if}

	{*
		вывод продуктов
	*}
	<div class="row">
		{foreach $aProducts as $oProduct}
			{sc_scheme_template scheme=$oScheme file="items.item.tpl"
				bProductList=true
			}
		{/foreach}
	</div>

	{hook run='sc_product_items_after' oScheme=$oScheme aProducts=$aProducts aSortOrderData=$aSortOrderData assign=sHookResult}
	{if $sHookResult}
		<div class="product-items-hook content-after" data-scheme-template-name="{$oScheme->getTemplateName()}">
			{$sHookResult}
		</div>
	{/if}

	{include file='paging.tpl'}

	{*
		подключить просмотр в модальном окне изображений каждого продукта без превью (для экономии трафика)
	*}
	{include file="{$aTemplatePathPlugin.simplecatalog}helpers/modals/images_modal.tpl"
		bDisablePreviews=true
	}

{else}
	{*
		если продуктов для отображения нет
	*}

	{*
		по результатам поиска
	*}
	{if $bSearchResults}
		{$aLang.plugin.simplecatalog.Products.Items.search.no_results}
		{sc_scheme_template scheme=$oScheme file="items/search/big_se.tpl"}
	{else}
		{$aLang.plugin.simplecatalog.Products.Items.no_products}
	{/if}
{/if}
