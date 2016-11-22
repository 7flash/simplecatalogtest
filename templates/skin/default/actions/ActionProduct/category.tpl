{include file='header.tpl'}

	{*include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.items.tpl"*}

	<div class="Simplecatalog Product Items">
		{*<div class="oh mb-20">
			if $oCategory->getImageUrl()}
				<img src="{$oCategory->getImageUrl()}" alt="{$oCategory->getName()|escape:'html'}" title="{$oCategory->getName()|escape:'html'}"
					 class="in-category-search category-image" />
			{/if

			<div class="oh">
				{hook run='sc_product_category_begin' oScheme=$oScheme}

				<h2 class="page-header">
					<a href="{$oScheme->getCatalogItemsWebPath()}">&larr; {$oScheme->getSchemeName()}</a>,
					{if $iTotalProductCount}
						{$iTotalProductCount} {$aLang.plugin.simplecatalog.Products.Items.items}

						{if Config::Get("plugin.simplecatalog.categories.product_categories_should_not_have_child_categories")}
							{$aLang.plugin.simplecatalog.Products.Items.total_including_descending_categories}
						{/if}
					{/if}
					{$aLang.plugin.simplecatalog.Products.Items.in_category} "{$oCategory->getName()}"
				</h2>

				<div class="mb-20">{$oCategory->getDescription()|nl2br}</div>
			</div>
		</div>*}

		{sc_scheme_template scheme=$oScheme file="items.tpl"}
	</div>

{include file='footer.tpl'}