
	<!-- Simplecatalog plugin -->
	{if $sMenuItemSelect=='sc_links' and $aSchemesMenuItems and count($aSchemesMenuItems)>0}
		<ul class="nav nav-pills">
			{foreach from=$aSchemesMenuItems item=oScheme}
				<li {if $sMenuSchemeSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
					<a href="{router page='sc_links'}index/{$oScheme->getSchemeUrl()}">{$aLang.plugin.simplecatalog.links.scheme} "{$oScheme->getSchemeName()}"</a>
				</li>
			{/foreach}
		</ul>
	{else}
		{$aLang.plugin.simplecatalog.links.no_schemes_for_processing}
	{/if}
	<!-- /Simplecatalog plugin -->
