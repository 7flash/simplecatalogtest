
{*
	Поле фильтра блока "Выбор по фильтру продуктов схемы"
	Флажок
*}

{sc_scheme_template scheme=$oScheme file="filter/fields/helpers/description.tpl"}

<label>
	<input type="checkbox" value="1" name="{$sInputName}" {if $mRequestValue}checked="checked"{/if} />
	{$oField->getTitle()}
</label>
