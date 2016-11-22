{include file='header.tpl'}

	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.items.tpl"}

	<div class="Simplecatalog Product Items CategoriesList">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Categories.catalog_items_page.title|ls_lang:"name%%`$oScheme->getSchemeName()`"}
			{if $aCategoryTree and count($aCategoryTree)}(<span>{count($aCategoryTree)}</span>){/if}
		</h2>

		<div class="mb-20">{$oScheme->getDescription()|nl2br}</div>

		{sc_scheme_template scheme=$oScheme file="items_categories.tpl"}
	</div>

{include file='footer.tpl'}