
{*
	Поле фильтра блока "Выбор по фильтру продуктов схемы"
	Простое поле ввода
*}

{sc_scheme_template scheme=$oScheme file="filter/fields/helpers/description.tpl"}

<div class="field-title">{$oField->getTitle()}</div>

<input type="text"
	   class="input-text input-width-full {if $oField->getValidatorTypeIsDate()}js-date-picker-php{/if}"
	   maxlength="{$aDisplayParams.maxlength}"
	   name="{$sInputName}"
	   value="{$mRequestValue}" />
