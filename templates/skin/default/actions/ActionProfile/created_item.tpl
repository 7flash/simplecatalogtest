
	<!-- Simplecatalog plugin -->
	{foreach from=$aSchemesList item=oScheme}
		<li {if $sMenuSubItemSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
			<a href="{$oUserProfile->getUserWebPath()}created/{$oScheme->getSchemeUrl()}/">{$oScheme->getSchemeName()} ({$oScheme->getProductsCount()})</a>
		</li>
	{/foreach}
	<!-- /Simplecatalog plugin -->
