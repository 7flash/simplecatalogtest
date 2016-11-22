
{*
	Фильтр продуктов (параметрический поиск)
*}

<form action="{$oScheme->getCatalogItemsFilterWebPath()}" method="get" enctype="application/x-www-form-urlencoded">

	{*
		вывод категорий для выбора
	*}
	{if $aCategoryTree and count($aCategoryTree)>0}
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
	{/if}

	{*
		вывод полей для поиска по ним
	*}
	{if $aProductFilterData and count($aProductFilterData)>0}

		<ul class="mb-20 product-filter-fields">
			{foreach from=$aProductFilterData item=aFilterData}
				{assign var=oField value=$aFilterData['field']}
				{assign var=sDisplayType value=$aFilterData['options']['type']}
				{assign var=aDisplayParams value=$aFilterData['options']['params']}

				{assign var=sInputName value="filter[fields][{$oField->getId()}]"}
				{*
					значение из реквеста (если использовали фильтр со своими параметрами)
					tip: применяется в каждом отображаемом типе индивидуально т.к. набор параметров для разных типов отличается
				*}
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

		<div class="oh">
			<input type="submit" class="button button-primary fl-l" value="{$aLang.plugin.simplecatalog.filter.submit}" />
			{if $sEvent == 'filter'}
				<input type="submit" class="button fl-r js-sc-button-url" value="{$aLang.plugin.simplecatalog.filter.cancel}" data-url="{$oScheme->getCatalogItemsWebPath()}" />
			{/if}
		</div>

	{else}
		<div class="mt15 mb-15">
			{$aLang.plugin.simplecatalog.filter.no_fields_allowed_to_search}
		</div>
	{/if}

</form>
