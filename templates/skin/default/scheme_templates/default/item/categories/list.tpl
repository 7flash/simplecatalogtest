
{*
	Категории продукта
*}

{assign var=aProductCategories value=$oProduct->getCategories()}
{if $aProductCategories and count($aProductCategories)>0}
	{*
		tip: aLocalParams по сути это хак, от него нужно избавиться когда компоненты шаблонов схем будут переведены на полный перечень передаваемых параметров (исключая глобальные)
			 тогда вместо этого можно будет использовать просто sClass
	*}
	<div class="mb-20 product-categories-list {$aLocalParams.classes}">
		{*
			показывать текст "категории" только на странице продукта и если категорий меньше 3-х т.к. справа социальные кнопки
		*}
		{if !$bProductList and count($aProductCategories) < 3}
			{$aLang.plugin.simplecatalog.Products.Items.categories.title}:&nbsp;
		{/if}
		{foreach from=$aProductCategories item=oCategory}
			<div class="one-category">
				{*
					подсказка с именем категории и количеством элементов в ней
				*}
				{capture assign=sCategoryTitle}{strip}
					{$aLang.plugin.simplecatalog.Products.Items.categories.category_info|ls_lang:"name%%`$oCategory->getName()`":"count%%`$oCategory->getItemsCount()`"}
				{/strip}{/capture}
				{*
					показать изображение категории или иконку
				*}
				{if $oCategory->getImageUrl()}
					<img src="{$oCategory->getImageUrl()}" alt="{$sCategoryTitle|escape}" title="{$sCategoryTitle|escape}" class="category-image" />
				{else}
					<i class="sc-icon-info-sign" title="{$sCategoryTitle|escape}"></i>
				{/if}

				<a href="{$oCategory->getCategoryUrl($oScheme)}" itemprop="genre" title="{$sCategoryTitle|escape}"
				   {if $aLocalParams.link_target_blank}target="_blank"{/if}>{$oCategory->getName()}</a>
			</div>
		{/foreach}
	</div>
{/if}
