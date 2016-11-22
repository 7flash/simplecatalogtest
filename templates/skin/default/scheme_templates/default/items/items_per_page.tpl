
{*
	Выбор количества элементов на страницу

	Передаваемые переменные:

		oEntity - сущность, у которой должны быть методы:

			getItemsPerPage - значение по-умолчанию количества элементов на страницу
			getItemsPerPageDefinedByUserOrDefault - текущее значение количества элементов (ранее изменённое или значение по-умолчанию)
			getChangeItemsPerPageWebPath - урл установки (нового) количества элементов на страницу, передается параметр задаваемого значения



<div class="logic-item">
	<form action="" method="post" enctype="application/x-www-form-urlencoded">
		{$aLang.plugin.simplecatalog.common.items_per_page}

		<select class="input-text input-width-60 js-sc-select-url">

				значения для выбора вместе со значением по-умолчанию
			}
			{$aValues = $LS->PluginSimplecatalog_Itemsperpage_GetValuesWithDefault($oEntity->getItemsPerPage())}
			{$iCurrentValue=$oEntity->getItemsPerPageDefinedByUserOrDefault()}

			{foreach $aValues as $iVal}
				<option value="{$oEntity->getChangeItemsPerPageWebPath($iVal)}" {if $iVal==$iCurrentValue}selected="selected"{/if}>{$iVal}</option>
			{/foreach}
		</select>
	</form>
</div>
*}