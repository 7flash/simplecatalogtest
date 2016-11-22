
{*
	Фильтр продуктов (параметрический поиск)
*}

<form action="{$oScheme->getCatalogItemsFilterWebPath()}" id="product_filter_form" method="get" enctype="application/x-www-form-urlencoded">
	<br><br>

	{hook run="catalog_switch_category"}


	<p style="padding: 0 20px;">Цена</p>
	<script>
		jQuery (document).ready (function ($) {
			/**
			 * добавить слайдер для чисел
			 */
			{if !$iValueMin}{$iValueMin=$iMinPrice}{/if}
			{if !$iValueMax}{$iValueMax=$iMaxPrice}{/if}
			ls.sc_product_filter.AddSlider(
					'.js-slider-price',
					{$iMinPrice},
					{$iMaxPrice},
					{$iValueMin},
					{$iValueMax},
					1,
					'.js-slider-price-min',
					'.js-slider-price-max'
			);
		});
	</script>
	<div class="js-slider-price ui-slider"></div>

	<div class="cb oh" style="padding: 0 20px;">
		<input type="text" class="input-width-100 fl-l slider-input js-slider-price-min"  name="filter[price_min]" value="{$iMinPrice}" />
		<input type="text" class="input-width-100 fl-r slider-input js-slider-price-max" style="text-align: right;"  name="filter[price_max]" value="{$iMaxPrice}" />
	</div>

	<style>
		.slider-input {
			background: transparent;
			font-weight: 300;
			border: none;
		}
	</style>


	<br><br>

	{if count($aCites>0)}
		{$aCityIds = (array) $_aRequest['filter']['city']}

		<p style="padding: 0 20px;">Город</p>
		{foreach $aCites as $oCity}
			<div class="checkbox">
				<label><input type='checkbox'  name="filter[city][]" {if in_array($oCity->getId(), $aCityIds)}checked{/if}  value="{$oCity->getId()}"> {$oCity->getTitle()}</label>
			</div>
		{/foreach}
	{/if}
	<br><br>

	{if count($aProvider>0)}

		{$aProviderIds = (array) $_aRequest['filter']['provider']}

		{*if !is_array($_aRequest['filter']['provider'])}
			{assign var=aRequestProvider value=(array) $_aRequest['filter']['provider']}
		{/if}*}

		<p style="padding: 0 20px;">Производитель</p>
		{foreach $aProvider as $oProvider}
			<div class="checkbox">
				<label><input type='checkbox' name="filter[provider][]" {if in_array($oProvider->getUserId(), $aProviderIds)}checked{/if} value="{$oProvider->getUserId()}"> {$oProvider->getName()}</label>
			</div>
		{/foreach}
	{/if}


	{*
		вывод категорий для выбора

	{if $aCategoryTree and count($aCategoryTree)>0}
	<div class="block-content">
		<ul class="product-filter-fields">
			<li class="product-filter-field-item">
				<div class="filter-field-help"><i class="sc-icon-info-sign js-tip-help" title="{$aLang.plugin.simplecatalog.filter.categories.tip}"></i></div>
				<div class="field-title">{$aLang.plugin.simplecatalog.filter.categories.select}</div>

				<div class="input-text input-width-full checkboxes-multi-select-wrapper">
					{include file="{$aTemplatePathPlugin.simplecatalog}helpers/categories/checkboxes.tpl"
						sName="filter[categories_ids]"
						mRequestValue=$_aRequest['filter']['categories_ids']
					}
				</div>
			</li>

		</ul>
	</div>


	{/if}
	*}
	{*
		вывод полей для поиска по ним
	*
	{if $aProductFilterData and count($aProductFilterData)>0}

		<ul class="mb-20 product-filter-fields">
			{foreach from=$aProductFilterData item=aFilterData}
				{assign var=oField value=$aFilterData['field']}
				{assign var=sDisplayType value=$aFilterData['options']['type']}
				{assign var=aDisplayParams value=$aFilterData['options']['params']}

				{assign var=sInputName value="filter[fields][{$oField->getId()}]"}
				*
					значение из реквеста (если использовали фильтр со своими параметрами)
					tip: применяется в каждом отображаемом типе индивидуально т.к. набор параметров для разных типов отличается
				}
				{assign var=mRequestValue value=$_aRequest['filter']['fields'][$oField->getId()]}

				<li class="product-filter-field-item">
					{if in_array($sDisplayType, array(
						PluginSimplecatalog_ModuleProduct::FILTER_DISPLAY_TYPE_NUMBER,
						PluginSimplecatalog_ModuleProduct::FILTER_DISPLAY_TYPE_STRING,
						PluginSimplecatalog_ModuleProduct::FILTER_DISPLAY_TYPE_CHECKBOX,
						PluginSimplecatalog_ModuleProduct::FILTER_DISPLAY_TYPE_TITLE,
						PluginSimplecatalog_ModuleProduct::FILTER_DISPLAY_TYPE_SELECT
					))}
						{sc_scheme_template scheme=$oScheme file="filter/fields/{$sDisplayType}.tpl"}
					{else}
						{$aLang.plugin.simplecatalog.filter.unknown_display_type|ls_lang:"type%%`$sDisplayType`"}
					{/if}
				</li>
			{/foreach}
		</ul>

	{else}*}
		{*<div class="mt15 mb-15">
			{$aLang.plugin.simplecatalog.filter.no_fields_allowed_to_search}
		</div>*}

	<br>

	<div class="block-footer">
		<a href="#" onclick="$('#product_filter_form').submit(); return false;">{$aLang.plugin.simplecatalog.filter.submit}</a>

		{if $sEvent == 'filter'}
			<a href="#"  data-url="{$oScheme->getCatalogItemsWebPath()}"  class="js-sc-button-url" >{$aLang.plugin.simplecatalog.filter.cancel}</a>
		{/if}
	</div>
</form>
