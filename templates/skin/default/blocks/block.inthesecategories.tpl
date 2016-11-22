
<!-- Simplecatalog plugin -->
<div class="block Simplecatalog">
	<header class="block-header sep">
		<h3>{$aLang.plugin.simplecatalog.Blocks.in_this_category.title}</h3>
	</header>
	<div class="block-content">
		{if $aInTheseCategoriesProducts}
			{foreach from=$aInTheseCategoriesProducts item=oThisCategoryProduct}

				<div class="one-product-short">
					<a href="{$oThisCategoryProduct->getItemShowWebPath()}">{$oThisCategoryProduct->getFirstFieldTitle(70)}</a>

					<ul>
						<li>
							<time datetime="{date_format date=$oThisCategoryProduct->getAddDate() format='c'}" title="{date_format date=$oThisCategoryProduct->getAddDate() format='j F Y, H:i'}">
								{date_format date=$oThisCategoryProduct->getAddDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
							</time>
						</li>
					</ul>
				</div>

			{/foreach}

		{else}
			{$aLang.plugin.simplecatalog.Blocks.in_this_category.no_more_products_in_these_categories}
		{/if}
	</div>
</div>
<!-- /Simplecatalog plugin -->
