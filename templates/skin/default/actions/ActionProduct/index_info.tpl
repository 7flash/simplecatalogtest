{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.product_index.tpl"}

	<div class="Simplecatalog Product IndexInfo">
		<h2 class="page-header">{$aLang.plugin.simplecatalog.Products.List.index_info.title}</h2>

		{$aLang.plugin.simplecatalog.Products.List.index_info.info}
	</div>

{include file='footer.tpl'}