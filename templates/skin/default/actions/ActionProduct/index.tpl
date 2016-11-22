{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.product_index.tpl"}

	<div class="Simplecatalog Product Index">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Products.List.title} "<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>" (<span>{$iTotalProductCount}</span>)
		</h2>

		{sc_scheme_template scheme=$oScheme file='admin_index.tpl'}
	</div>

{include file='footer.tpl'}