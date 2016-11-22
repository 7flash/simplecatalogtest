
	<!-- Simplecatalog plugin -->
	<ul class="nav nav-pills">
		<li {if $sMenuSubItemSelect=='index_info'}class="active"{/if}>
			<a href="{router page='product'}index">{$aLang.plugin.simplecatalog.Menu.Products.index_info}</a>
		</li>
		{foreach from=$aSchemesMenuItems item=oScheme}
			<li {if $sMenuSubItemSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
				<a href="{$oScheme->getCatalogItemsAdminIndexWebPath()}">{$aLang.plugin.simplecatalog.Menu.Products.products} "{$oScheme->getSchemeName()}"</a>
			</li>
		{/foreach}
	</ul>
	<!-- /Simplecatalog plugin -->
