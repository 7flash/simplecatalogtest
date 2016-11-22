
		<!-- Simplecatalog plugin -->
		{if $oUserCurrent}
			{foreach $aSchemesMenuItems as $oScheme}
				{if $oUserCurrent->getCanAddNewProductsInScheme($oScheme)}
					<li class="write-item-type-topic">
						<a href="{$oScheme->getAddProductWebPath()}" class="write-item-image"></a>
						<a href="{$oScheme->getAddProductWebPath()}" class="write-item-link">{$oScheme->getSchemeName()}</a>
					</li>
				{/if}
			{/foreach}
		{/if}
		<!-- /Simplecatalog plugin -->
