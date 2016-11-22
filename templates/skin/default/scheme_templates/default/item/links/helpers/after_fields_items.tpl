
{*
	Вывод элементов связи после полей продукта по указанному типу

	Необходимые переменные:

		aProductLinksData - данные о связях
		sLinkItemType - тип элемента связи для отображения

	Дополнительные параметры:

		sLinksListPrependText - текст, который должен быть добавлен перед выводом всего списка элементов связей
		sLinksListAppendText - текст, который должен быть добавлен после вывода всего списка элементов связей
*}

{if $aProductLinksData and count($aProductLinksData) > 0}
	<div class="product-links-in-footer">
		{*
			вывод списка продуктов связей
		*}
		<div class="product-links-list">
			{*
				для каждой связи
			*}
			{foreach from=$aProductLinksData item=aLinkData}
				{assign var=aProductLinks value=$aLinkData['aProductLinks']}
				{assign var=oLinkSettings value=$aLinkData['oLinkSettings']}

				<div class="linked-products mb-20">
					{*
						заголовок
					*}
					<h3 class="sub-title mb-5">
						{$oLinkSettings->getDisplayName()}
						{*
							не выводить количество для типа связей "1 к 1"
						*}
						{if !$oLinkSettings->getTypeHasOne()}
							(<span>{count($aProductLinks)}</span>)
						{/if}
					</h3>
					{*
						описание
					*}
					{if $oLinkSettings->getDescription()}
						<div class="description">
							{$oLinkSettings->getDescription()|nl2br}
						</div>
					{/if}
					{*
						вывод продуктов
					*}
					<div>
						{$sLinksListPrependText}

						{foreach from=$aProductLinks item=oLink}
							{sc_scheme_template scheme=$oScheme file="item/links/elements/{$sLinkItemType}.tpl"}
						{/foreach}

						{$sLinksListAppendText}
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
