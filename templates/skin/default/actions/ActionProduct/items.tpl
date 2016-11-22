{include file='header.tpl'}

	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.items.tpl"}

	<div class="Simplecatalog Product Items" data-scheme-url="{$oScheme->getSchemeUrl()}">
		{hook run='sc_product_items_begin' oScheme=$oScheme}

		<h2 class="page-header">
			{if in_array($sEvent, array('moderation', 'my', 'drafts', 'filter'))}
				{$aLang.plugin.simplecatalog.Products.Items.titles.$sEvent} "{$oScheme->getSchemeName()}"
			{else}
				{$oScheme->getSchemeName()}
			{/if}
			{if $iTotalProductCount}(<span>{$iTotalProductCount}</span>){/if}
		</h2>

		<div class="mb-20">{$oScheme->getDescription()|nl2br}</div>

		{sc_scheme_template scheme=$oScheme file='items.tpl'}
	</div>

{include file='footer.tpl'}