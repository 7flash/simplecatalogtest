
{*
	Вывод дерева категорий на главной каталога
*}

{if $aCategoryTree and count($aCategoryTree)>0}
	{if count($aCategoryTree)>40}{$bBigCategoryList=true}{/if}

	<ul class="sc-product-items-categories{if $bBigCategoryList} big{/if}">
		{foreach $aCategoryTree as $aCategoryItem}
			{assign var=oCategory value=$aCategoryItem['entity']}
			{assign var=iLevel value=$aCategoryItem['level']}
			{assign var=iChildrenCount value=$aCategoryItem['children_count']}

			<li class="sc-product-items-category level-{$iLevel}" style="padding-left: {$iLevel*35}px">
				{*
					изображение категории
				*}
				{if $oCategory->getImageUrl()}
					<img src="{$oCategory->getImageUrl()}" alt="{$oCategory->getName()|escape:'html'}" class="category-image" />
				{/if}

				<div class="category-info">
					<div class="category-header">
						<a href="{$oCategory->getCategoryUrl($oScheme)}" class="{if !$oCategory->getItemsCount()}empty{/if}">
							{$oCategory->getName()}
							{*
								количество элементов
							*}
							{if $oCategory->getItemsCount()}
								(<span title="{$aLang.plugin.simplecatalog.Categories.catalog_items_page.products_count}">{$oCategory->getItemsCount()}</span>)
							{/if}
						</a>
					</div>
					{*
						количество подкатегорий выводить если больше одной
						и при большом списке только для первого уровня чтобы место не занимать т.к. элементы маленькие
					*}
					{if $iChildrenCount > 1 and (!$bBigCategoryList or $iLevel==0)}
						<div class="mb-5 children-count">
							{$aLang.plugin.simplecatalog.Categories.catalog_items_page.children_count}: {$iChildrenCount}
						</div>
					{/if}

					{if $oCategory->getDescription()}
						<div class="category-description">
							{$oCategory->getDescription()|nl2br}
						</div>
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
{else}
	{$aLang.plugin.simplecatalog.Categories.catalog_items_page.none}
{/if}
