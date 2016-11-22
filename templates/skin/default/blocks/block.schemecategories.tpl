
{* if Config::Get('plugin.simplecatalog.categories.show_block_when_no_categories') or ($aCategoryTree and count($aCategoryTree)>0)}
	<!-- Simplecatalog plugin -->
	<div class="block Simplecatalog Categories">
		<header class="block-header sep">
			<h3 title="{$oScheme->getSchemeName()}">{$aLang.plugin.simplecatalog.Blocks.scheme_categories.title}</h3>
		</header>
		<div class="block-content">
			{if $aCategoryTree and count($aCategoryTree)>0}
				<ul>
					{foreach $aCategoryTree as $aCategoryItem}
						{assign var=oCategory value=$aCategoryItem['entity']}
						{assign var=iLevel value=$aCategoryItem['level']}
						{assign var=iItemsCount value=$oCategory->getItemsCount()}

						<li style="margin-left: {$iLevel*20}px">
							{if $oCategory->getImageUrl()}
								<img src="{$oCategory->getImageUrl()}" alt="{$oCategory->getName()|escape:'html'}" title="{$oCategory->getName()|escape:'html'}" class="category-image" />
							{/if}

							<a href="{$oCategory->getCategoryUrl($oScheme)}" class="{if !$iItemsCount}empty{/if}">{$oCategory->getName()}</a>
							{if $iItemsCount}
								(<span class="category-item-targets-count">{$iItemsCount}</span>)
							{/if}
						</li>
					{/foreach}
				</ul>
			{else}
				{$aLang.plugin.simplecatalog.Blocks.scheme_categories.no_categories_for_scheme}
			{/if}
		</div>
	</div>
	<!-- /Simplecatalog plugin -->
{/if*}
