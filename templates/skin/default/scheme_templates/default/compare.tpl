
{*
	Таблица сравнения продуктов
*}

{*
	кнопки "все параметры" и "только отличия"
	tip: показывать если есть несколько продуктов, т.к. если один, то все его поля "одинаковые" и нету смысла в фильтре т.к. нечего сравнивать
*}
{if count($aProducts) > 1}
	<ul class="nav nav-pills mb-20">
		<li class="active">
			<a href="#" id="js_sc_compare_products_show_all">{$aLang.plugin.simplecatalog.Products.Items.Comparing.show_all}</a>
		</li>
		<li>
			<a href="#" id="js_sc_compare_products_show_different">{$aLang.plugin.simplecatalog.Products.Items.Comparing.show_different}</a>
		</li>
	</ul>
{/if}

<table class="table-items-list compare-products" id="js_sc_compare_products_table">
	<thead>
		<tr>
			{*
				столбец для названия полей
			*}
			<th></th>
			{*
				заголовки продуктов
			*}
			{foreach from=$aProducts item=oProduct}
				<th>
					{*
						изображение продукта
					*}
					{if $oFirstProductImage = $oProduct->getFirstImage()}
						<a href="{$oProduct->getItemShowWebPath()}" class="img-wrapper">
							<img src="{$oFirstProductImage->getFilePath()}" alt="{$oProduct->getFirstFieldTitle()|escape:'html'}" title="{$oProduct->getFirstFieldTitle()|escape:'html'}" />
						</a>
					{/if}

					<a href="{$oProduct->getItemShowWebPath()}">{$oProduct->getFirstFieldTitle()}</a>
					{*
						ссылка удаления продукта из сравнения
					*}
					<a href="{$oProduct->getCompareProductDeleteFromCompareTable()}" class="js-question"
					   title="{$aLang.plugin.simplecatalog.Products.Items.Comparing.remove_from_compare_list}"><i class="sc-icon-remove"></i></a>
				</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		{*
			вывод имени каждого поля схемы в левой части таблицы (БЕЗ первого поля, которое является заголовком продукта и выведено в шапке таблицы)
		*}
		{foreach from=$oScheme->getFieldsWOFirstField() item=oSchemeField key=iSchemeFieldsKey}
			{*
				пропустить вывод полей, которые используются как заголовки
			*}
			{if in_array($oSchemeField->getFieldType(), array(PluginSimplecatalog_ModuleScheme::FIELD_TYPE_NOT_EDITABLE))}
				{continue}
			{/if}

			<tr class="{if $aComparedProductFields[$iSchemeFieldsKey]}equal{else}different{/if}">
				<td class="field-title">
					{$oSchemeField->getTitle()}
				</td>
				{*
					вывод по каждому полю продукта
				*}
				{foreach from=$aProducts item=oProduct}
					{assign var="aProductFields" value=$oProduct->getProductFieldsWOFirstField()}
					<td class="field-value">
						{$oProductField = $aProductFields[$iSchemeFieldsKey]}
						{*
							и поля схемы и поля продуктов получены без первого поля (заголовка)
						*}
						{if $oProductField}
							{sc_scheme_template scheme=$oScheme file="item/field_value.tpl"
								oField=$oProductField->getField()
								sValue=$oProductField->getDisplayValue(false, 300)
								oProductField=$oProductField
								oProduct=$oProduct
								oScheme=$oScheme
							}
						{else}
							{*
								нет поля, может в схеме только создали, но продукт не сохраняли после этого
								tip: т.к. добавлена миграция полей, то данное условие не будет выведено в нормальных условиях
							*}
							&ndash;
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</tbody>
</table>
