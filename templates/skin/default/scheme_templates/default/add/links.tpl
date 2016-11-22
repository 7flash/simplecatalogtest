
{*
	Добавление связей продукту

	Передаваемые параметры:

		$aSchemeLinksData - все наборы настроек связей схемы с продуктами для выбора

	Примечание:

		Массив текущих связей продукта для всех настроек связей схем содержится в $_aRequest.product_links

*}

{if $aSchemeLinksData and count($aSchemeLinksData) > 0}
	<h2 class="page-header title-underline">
		{$aLang.plugin.simplecatalog.Products.Add.links.title}
	</h2>

	{*
		по всем настройкам связей схемы
	*}
	{foreach from=$aSchemeLinksData item=aLinksData}
		{assign var=oLinkSettings value=$aLinksData['oLinkSettings']}
		{assign var=aProductsData value=$aLinksData['aProductsData']['collection']}
		<dl class="w50p mb-20">
			<dt>
				{*
					имя связи или связанного каталога
				*}
				{$oLinkSettings->getDisplayName()}
				{*
					описание
				*}
				{if $oLinkSettings->getDescription()}
					<span class="note">{$oLinkSettings->getDescription()|nl2br}</span>
				{/if}
			</dt>
			<dd>
				{*
					список разрешенных продуктов для установки связи
				*}

				{if $oLinkSettings->getTypeHasMany()}
					{*
						выбор нескольких связей через флажки
					*}
					<div class="input-text input-width-full checkboxes-multi-select-wrapper">
						{foreach from=$aProductsData item=oProduct}
							<label>
								<input type="checkbox" value="{$oProduct->getId()}" name="product_links[{$oLinkSettings->getId()}][]"
									   {if in_array($oProduct->getId(), (array) $_aRequest['product_links'][$oLinkSettings->getId()])}checked="checked"{/if}
									   />
								{$oProduct->getFirstFieldTitle()} ({date_format date=$oProduct->getAddDate() format='j F Y, H:i'})
							</label>
						{/foreach}
					</div>

				{else}
					{*
						выбор одной связи через селект
					*}
					<select name="product_links[{$oLinkSettings->getId()}]" class="input-text input-width-full">
						<option value="">---</option>

						{foreach from=$aProductsData item=oProduct}
							<option value="{$oProduct->getId()}" {if in_array($oProduct->getId(), (array) $_aRequest['product_links'][$oLinkSettings->getId()])}selected="selected"{/if}>
								{$oProduct->getFirstFieldTitle()} ({date_format date=$oProduct->getAddDate() format='j F Y, H:i'})
							</option>
						{/foreach}
					</select>
				{/if}
			</dd>
		</dl>
	{/foreach}
{/if}
