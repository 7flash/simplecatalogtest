
{*
	Поле фильтра блока "Выбор по фильтру продуктов схемы"
	Для диапазона чисел со слайдером
*}

{sc_scheme_template scheme=$oScheme file="filter/fields/helpers/description.tpl"}

<div class="field-title">{$oField->getTitle()}</div>

{assign var=iValueMin value="{if $mRequestValue.0}{$mRequestValue.0}{else}{$aDisplayParams.min}{/if}"}
{assign var=iValueMax value="{if $mRequestValue.1}{$mRequestValue.1}{else}{$aDisplayParams.max}{/if}"}

<script>
	jQuery (document).ready (function ($) {
		/**
		 * добавить слайдер для чисел
		 */
		ls.sc_product_filter.AddSlider(
			'.js-ui-slider-{$oField->getId()}',
			{$aDisplayParams.min},
			{$aDisplayParams.max},
			{$iValueMin},
			{$iValueMax},
			{$aDisplayParams.accuracy},
			'.js-ui-slider-val-min-{$oField->getId()}',
			'.js-ui-slider-val-max-{$oField->getId()}'
		);
	});
</script>
<div class="js-ui-slider-{$oField->getId()} ui-slider"></div>

<div class="cb oh">
	<input type="text" class="input-text input-width-100 fl-l js-ui-slider-val-min-{$oField->getId()}" name="{$sInputName}[]" value="{$iValueMin}" />
	<input type="text" class="input-text input-width-100 fl-r js-ui-slider-val-max-{$oField->getId()}" name="{$sInputName}[]" value="{$iValueMax}" />
</div>
