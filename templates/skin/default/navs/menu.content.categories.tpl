
	<!-- Simplecatalog plugin -->
	{if $sMenuItemSelect=='sccategories' and $aSchemesMenuItems and count($aSchemesMenuItems)>0}
		<ul class="nav nav-pills">
			{foreach from=$aSchemesMenuItems item=oScheme}
				<li {if $sMenuSchemeSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
					<a href="{router page='sccategories'}index/{$oScheme->getSchemeUrl()}">{$aLang.plugin.simplecatalog.Categories.scheme} "{$oScheme->getSchemeName()}"</a>
				</li>
			{/foreach}
		</ul>
	{else}
		{$aLang.plugin.simplecatalog.Categories.no_schemes_for_adding_categories}
	{/if}
	<!-- /Simplecatalog plugin -->
