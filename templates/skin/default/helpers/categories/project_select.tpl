
	{*
		Выпадающий список категорий (селект)

		Переменные, которые нужно передать при подключении файла:

			$sName - имя селекта
			$sNoCategoryText - текст для не выбранного элемента категории (значение 0)
			$aCategoryTree - дерево категорий

		Дополнительные переменные:

			$bMultipleSelect - флаг множественного выбора категорий (не обязательно, по-умолчанию не включено)
			$mRequestValue - значение из реквеста (не обязательно, тогда значения будут получены из $_aRequest.$sName)
			$aBlockedIds - список ид категорий, которые выбирать нельзя (не обязательно, тогда значения будут получены из $_aRequest.blocked_categories_ids)
						(например, чтобы запретить вложенность категории самой в себя и дочерние)
			$sSelectClassName - класс для селекта, если не указано, то будет задано "input-width-250"

	*}


	{* текущие значения из реквеста *}
	{if !$mRequestValue}
		{assign var=mRequestValue value=$_aRequest.$sName}
	{/if}
	{* ид, которые нужно пометить как disabled *}
	{if !$aBlockedIds}
		{assign var=aBlockedIds value=$_aRequest.blocked_categories_ids}
	{/if}

	{* параметры обрабатываются как массивы *}
	{if !is_array($mRequestValue)}
		{assign var=mRequestValue value=(array) $mRequestValue}
	{/if}
	{if !is_array($aBlockedIds)}
		{assign var=aBlockedIds value=(array) $aBlockedIds}
	{/if}

	<select name="{$sName}{if $bMultipleSelect}[]{/if}"
			class="js_c_field_categselect form-control input-sm  {if $sSelectClassName}{$sSelectClassName}{else}input-text input-width-250{/if}"
			{if $bMultipleSelect}multiple="multiple"{/if}>
		<option value="0" {if in_array('0', $mRequestValue)}selected="selected"{/if}>
			{$sNoCategoryText}
		</option>

		{*
			показать дерево категорий
		*}
		{if $aCategoryTree and count($aCategoryTree)>0}
			{foreach from=$aCategoryTree item=aCategoryItem}
				{assign var=oCategory value=$aCategoryItem['entity']}
				{assign var=iLevel value=$aCategoryItem['level']}
				{assign var=iChildrenCount value=$aCategoryItem['children_count']}

				<option value="{$oCategory->getId()}"
						{if in_array($oCategory->getId(), $mRequestValue)}selected="selected"{/if}								{* приводится к массиву выше *}
						{if in_array($oCategory->getId(), $aBlockedIds)}disabled="disabled"{/if}>
					{str_repeat('&nbsp;', $iLevel * 5)}{$oCategory->getName()}

					{if $iChildrenCount}
						({$iChildrenCount} {$aLang.plugin.simplecatalog.Categories.child_sub_categories})
					{/if}
				</option>
			{/foreach}
		{/if}
	</select>

	<div class="js_c_err_categselect js_c_sub_field_err"></div>
