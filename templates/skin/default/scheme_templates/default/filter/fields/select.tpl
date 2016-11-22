
{*
	Поле фильтра блока "Выбор по фильтру продуктов схемы"
	Селект простой или множественный (представляется в виде флажков)
*}

{sc_scheme_template scheme=$oScheme file="filter/fields/helpers/description.tpl"}

<div class="field-title">{$oField->getTitle()}</div>

{*
	разрешен ли множественный выбор значений (будет представлен в виде флажков)
*}
{if $oField->getSelectMultipleItemsEnabled()}
	{foreach from=$oField->getDefinedSelectValues() item=sSelectValue}
		<label>
			<input type="checkbox" value="{$sSelectValue@index}" name="{$sInputName}[]"
					{*
						унифицировать значение селекта в массив (чтобы выполнять проверку всегда как в селекте с мультивыбором
						tip: проверка нужна чтобы не приводить пустую строку в массив т.к. индексы селектов начинаются с нуля.
							первая проверка нужна для обычного селекта, вторая - для мультиселекта
						tip: при тестах использовать в фильтре первые элементы селектов (со значением "0") и проверять два обычный селект и мультиселект одновременно
					*}
				   {if $mRequestValue !== '' and $mRequestValue.0 !== '' and in_array($sSelectValue@index, (array) $mRequestValue)}checked="checked"{/if}
					/>
			{$sSelectValue}
		</label>
	{/foreach}

{else}
	<select name="{$sInputName}" class="input-text input-width-full">
		{*
			чтобы можно было не выбирать ничего из списка для обязательного поиска
		*}
		<option value="">---</option>
		{foreach from=$oField->getDefinedSelectValues() item=sSelectValue}
			{*
				унифицировать значение селекта в массив (чтобы выполнять проверку всегда как в селекте с мультивыбором
				tip: проверка нужна чтобы не приводить пустую строку в массив т.к. индексы селектов начинаются с нуля.
					первая проверка нужна для обычного селекта, вторая - для мультиселекта
				tip: при тестах использовать в фильтре первые элементы селектов (со значением "0") и проверять два обычный селект и мультиселект одновременно
			*}
			<option value="{$sSelectValue@index}" {if $mRequestValue !== '' and $mRequestValue.0 !== '' and in_array($sSelectValue@index, (array) $mRequestValue)}selected="selected"{/if}
					>{$sSelectValue}</option>
		{/foreach}
	</select>
{/if}
