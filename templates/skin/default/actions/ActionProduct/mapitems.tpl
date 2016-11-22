{include file='header.tpl' noSidebar=true}

	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.items.tpl"}

	<div class="Simplecatalog Product MapItems">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Products.Items.titles.mapitems} "{$oScheme->getSchemeName()}"
			{if $iTotalMapItemsCount}(<span>{$iTotalMapItemsCount}</span>){/if}
		</h2>

		<div class="mb-20">{$oScheme->getDescription()|nl2br}</div>

		{sc_scheme_template scheme=$oScheme file="mapitems.tpl"}
	</div>

{include file='footer.tpl'}