{foreach from=$aSchemesMenuItems item=oSchemeItem}
	{$oScheme=$oSchemeItem['scheme']}
	{$aCategories=$oSchemeItem['categories']}
 {$iCountCol=$oSchemeItem['col_count']}
	<li {if $sMenuHeadItemSelect==$oScheme->getSchemeUrl()}class="active"{/if}>
		<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>


		{if count($aCategories) > 0}
			<div class="list-unstyled sub-menu">
			<div class="container">

				<div class="row">
					{$bOpen = false}
						{foreach from=$aCategories item=aCategoryItem name=foo}
							{assign var=oCategory value=$aCategoryItem['entity']}
							{assign var=iLevel value=$aCategoryItem['level']}

					{if $bOpen && $iLevel==0 && !$smarty.foreach.products.last}
				</div>
				{$bOpen=false}
				{/if}

				{if $iLevel==0}
				<div class="col-xs-12 col-sm-{$iCountCol}">
					{$bOpen = true}
					{/if}



							<div data-category-id="{$oCategory->getId()}" class="category-item {if $iLevel==0}root-category{/if}">
								<a href="{$oCategory->getCategoryUrl($oScheme)}">{$oCategory->getName()}</a>
							</div>
					{if $smarty.foreach.products.last}
				</div>
				{$bOpen=false}
				{/if}

						{/foreach}
				</div>
			</div>
			</div>
		{/if}

	</li>

{/foreach}


