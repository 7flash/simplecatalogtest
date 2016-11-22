
		<!-- Simplecatalog plugin -->
		{foreach from=$aSchemesMenuItems item=oScheme}
			<li {if $sMenuHeadItemSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
				<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a> <i></i>
			</li>
		{/foreach}
		<!-- /Simplecatalog plugin -->
