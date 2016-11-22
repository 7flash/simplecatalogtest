
	<!-- Simplecatalog plugin -->
	<div class="SC-UserCreateInSchemes">
		{foreach $aSchemesOfThisUser as $oScheme}
			<h2 class="header-table">
				{$aLang.plugin.simplecatalog.Profile.added_by_this_user_to_schemes.title} "<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>"
			</h2>
			
			<ul class="last-products-list">
				{foreach $oScheme->getProductsOfThisUser() as $oProduct}
					<li>
						<a href="{$oProduct->getItemShowWebPath()}">{$oProduct->getFirstFieldTitle()}</a>
					</li>
				{/foreach}
			</ul>
		{/foreach}
	</div>
	<!-- /Simplecatalog plugin -->
