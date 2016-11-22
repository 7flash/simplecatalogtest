
{*
	Вывод полей продукта для редактирования
*}

<div>
	{foreach from=$oScheme->getFields() item=oField}
		{*
			не выводить поля, которые не заполняются через форму
		*}
		{if !$oField->getEditableByUserEnabled()}{continue}{/if}

		<div class="mb-20{if $oField@iteration % 2 == 0} second{/if}" data-field-type="{$oField->getFieldType()}">
			{*
				"product_data" - массив для хранения значений и удобного доступа к ним
			*}

			{assign var="sName" value="product_data[{$oField->getId()}]"}					{* должен быть массивом *}
			{assign var="sValue" value=$_aRequest['product_data'][$oField->getId()]}		{* только так получать данные *}

			{*
				задать значение по-умолчанию только для новых продуктов
			*}
			{if $sValue === null and !$_aRequest.id}
				{assign var="sValue" value=$oField->getDefaultValue()}
			{/if}

			{sc_scheme_template scheme=$oScheme file="add/fields/{$oField->getFieldType()}.tpl"}
		</div>
	{/foreach}
</div>
