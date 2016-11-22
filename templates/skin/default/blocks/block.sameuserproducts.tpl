
<!-- Simplecatalog plugin -->
<div class="block Simplecatalog">
	<header class="block-header sep">
		<h3>{$aLang.plugin.simplecatalog.Blocks.more_from_this_author.title} "{$oScheme->getSchemeName()}"</h3>
	</header>
	<div class="block-content">
		{if $aSameProducts}
			{foreach from=$aSameProducts item=oSameProduct}

				<div class="one-product-short">
					<a href="{$oSameProduct->getItemShowWebPath()}">{$oSameProduct->getFirstFieldTitle(70)}</a>

					<ul>
						<li>
							<time datetime="{date_format date=$oSameProduct->getAddDate() format='c'}" title="{date_format date=$oSameProduct->getAddDate() format='j F Y, H:i'}">
								{date_format date=$oSameProduct->getAddDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
							</time>
						</li>
					</ul>
				</div>

			{/foreach}

		{else}
			{$aLang.plugin.simplecatalog.Blocks.more_from_this_author.author_has_only_one_product}
		{/if}
	</div>
</div>
<!-- /Simplecatalog plugin -->
