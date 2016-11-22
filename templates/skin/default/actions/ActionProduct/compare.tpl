{$noSidebar=true}
{include file='header.tpl'}

	{if $oScheme}
		{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.items.tpl"}
	{/if}

	<div class="Simplecatalog Product Compare">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Products.Items.Comparing.title}
			{if $oScheme}
				"<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>"
			{/if}
		</h2>

		{*
			если есть продукты для сравнения
		*}
		{if $aProducts and count($aProducts)>0}
			{sc_scheme_template scheme=$oScheme file="compare.tpl"}
		{else}
			{*
				если продуктов для отображения нет
			*}
			{$aLang.plugin.simplecatalog.Products.Items.Comparing.no_products}
		{/if}
	</div>

{include file='footer.tpl'}