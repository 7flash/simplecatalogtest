{*
	Вывод дерева категорий на главной каталога
*}
{if $aCategoryTree and count($aCategoryTree)>0}
	{if count($aCategoryTree)>40}{$bBigCategoryList=true}{/if}
	<div class="row {if $bBigCategoryList} big{/if}">
		{foreach $aCategoryTree as $aCategoryItem}
			{assign var=oCategory value=$aCategoryItem['entity']}
			{assign var=iLevel value=$aCategoryItem['level']}
			{assign var=iChildrenCount value=$aCategoryItem['children_count']}

			{if $iLevel >0}
				{continue}
			{/if}
			<div class="col-xs-12 col-xs-6 level-{$iLevel}">
				<div class="role-catalog-category">
					<a href="{$oCategory->getCategoryUrl($oScheme)}">
						<div class="role-catalog-category-inner">
							{if $oCategory->getImageUrl()}
								<img src="{$oCategory->getImageUrl()}" alt="{$oCategory->getName()|escape:'html'}" class="img-full" />
							{else}
								<img src="back.jpg" alt="{$oCategory->getName()|escape:'html'}" class="img-full" />
							{/if}


						</div>
						<div class="overlay">
							<div class="center-vertical">
								<div class="title">
									{$oCategory->getName()}

								</div>
							</div>

						</div>
					</a>


				</div>
				{*
					изображение категори
				<div class="category-info">
					<div class="category-header">

							{*
								количество элементов

							{if $oCategory->getItemsCount()}
								(<span title="{$aLang.plugin.simplecatalog.Categories.catalog_items_page.products_count}">{$oCategory->getItemsCount()}</span>)
							{/if}
					</div>
				</div>*}
			</div>
		{/foreach}
	</div>
{else}
	{$aLang.plugin.simplecatalog.Categories.catalog_items_page.none}
{/if}
