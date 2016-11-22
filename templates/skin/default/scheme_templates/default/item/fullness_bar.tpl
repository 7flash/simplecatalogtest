
{*
	Полоска заполненности продукта
*}

{*
	плюс единица нужна т.к. заголовок продукта отсутствует в $aProductFields
*}
{$iTotalProductFieldsCountLocal = count($aProductFields) + 1}

{if $iFieldsFilledCountLocal = $oProduct->getFieldsFilledCount()}
	{*
		фикс: если раньше было больше полей и количество заполненных больше чем всего полей на данный момент
	*}
	{$iFieldsFilledCountLocal = min($iFieldsFilledCountLocal, $iTotalProductFieldsCountLocal)}

	<div class="fullness-wrapper" title="{$aLang.plugin.simplecatalog.Products.Item.fields_filled_count|ls_lang:"count%%`$iFieldsFilledCountLocal`":"total%%`$iTotalProductFieldsCountLocal`"}">
		{$iFullnessPercentage = number_format($iFieldsFilledCountLocal*100/$iTotalProductFieldsCountLocal, 1, '.', '')}

		<div class="fullness-bar" style="width: {$iFullnessPercentage}%">
			<div class="shadow-bar"></div>
		</div>
	</div>
{/if}
