
{*
	Список категорий с множественным выбором через флажки

	Переменные, которые нужно передать при подключении файла:

		$sName - имя селекта
		$aCategoryTree - дерево категорий

	Дополнительные переменные:

		$mRequestValue - значение из реквеста (не обязательно, тогда значения будут получены из $_aRequest.$sName)
		$aBlockedIds - список ид категорий, которые выбирать нельзя (не обязательно, тогда значения будут получены из $_aRequest.blocked_categories_ids)
					(например, чтобы запретить вложенность категории самой в себя и дочерние)

*}

{*
	текущие значения из реквеста
*}
{if !$mRequestValue}
	{assign var=mRequestValue value=$_aRequest.$sName}
{/if}
{*
	ид, которые нужно пометить как disabled
*}
{if !$aBlockedIds}
	{assign var=aBlockedIds value=$_aRequest.blocked_categories_ids}
{/if}

{*
	параметры обрабатываются как массивы
*}
{if !is_array($mRequestValue)}
	{assign var=mRequestValue value=(array) $mRequestValue}
{/if}
{if !is_array($aBlockedIds)}
	{assign var=aBlockedIds value=(array) $aBlockedIds}
{/if}

{*
	показать дерево категорий с флажками
*}
{if $aCategoryTree and count($aCategoryTree)>0}
	{foreach from=$aCategoryTree item=aCategoryItem}
		{assign var=oCategory value=$aCategoryItem['entity']}
		{assign var=iLevel value=$aCategoryItem['level']}
		{assign var=iChildrenCount value=$aCategoryItem['children_count']}
		{assign var=iItemsCount value=$oCategory->getItemsCount()}

		<label style="margin-left: {$iLevel*20}px"
			   title="{strip}
			   				{if $iChildrenCount}{$iChildrenCount} {$aLang.plugin.simplecatalog.Categories.child_sub_categories}{/if}
			   				{if $iItemsCount}{$iItemsCount} {$aLang.plugin.simplecatalog.Categories.items_in_category}{/if}
			   {/strip}">
			<input type="checkbox" value="{$oCategory->getId()}" name="{$sName}[]"
					{if in_array($oCategory->getId(), $mRequestValue)}checked="checked"{/if}
					{if in_array($oCategory->getId(), $aBlockedIds)}disabled="disabled"{/if}
					/>

			{$oCategory->getName()}

			{if $iItemsCount}
				(<span class="category-item-targets-count">{$iItemsCount}</span>)
			{/if}
		</label>
	{/foreach}
{else}
	{$aLang.plugin.simplecatalog.Categories.none}
{/if}
