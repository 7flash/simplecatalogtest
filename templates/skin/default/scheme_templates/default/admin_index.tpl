
{*
	Системный вывод списка продуктов в админке
*}

{if $aProducts and count($aProducts)>0}
	<table class="table-items-list">
		<thead>
			<tr>
				<th>{$aLang.plugin.simplecatalog.Products.List.table_header.info}</th>
				<th>{$aLang.plugin.simplecatalog.Products.List.table_header.fields}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $aProducts as $oProduct}
				<tr{if $oProduct@iteration % 2 == 0} class="second"{/if}>
					{*
						информация
					*}
					<td class="nameleft">
						{*
							заголовок и иконки статуса модерации
						*}
						<div class="mb-15">
							<h2 class="page-header">
								<a href="{$oProduct->getItemShowWebPath()}" title="{$oProduct->getFirstFieldTitle()}">{$oProduct->getFirstFieldTitle(50)}</a>
								{sc_scheme_template scheme=$oScheme file="item/moderation_icons.tpl"}
							</h2>
						</div>
						{*
							даты создания/редактирования, автор и кто последний редактировал
						*}
						<div class="mb-15">
							<div class="mb-5">
								{$aLang.plugin.simplecatalog.Products.List.info.date_add}: <b>{$oProduct->getAddDate()}</b>
							</div>
							<div class="mb-5">
								{$aLang.plugin.simplecatalog.Products.List.info.date_edit}: <b>{$oProduct->getEditDate()}</b>
							</div>
							<div class="mb-5">
								{$aLang.plugin.simplecatalog.Products.List.info.user}: <a href="{$oProduct->getUser()->getUserWebPath()}">{$oProduct->getUser()->getLogin()}</a>
							</div>
							<div class="mb-5">
								{$aLang.plugin.simplecatalog.Products.List.info.user_edit_last}: <a href="{$oProduct->getUserEditLast()->getUserWebPath()}">{$oProduct->getUserEditLast()->getLogin()}</a>
							</div>
						</div>
						{*
							управление
						*}
						<div>
							{$aLang.plugin.simplecatalog.Controls}:
							{if $oUserCurrent and $oUserCurrent->getCanManageProduct($oProduct)}
								<a href="{$oProduct->getItemEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oProduct->getItemDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							{/if}
						</div>
					</td>
					{*
						поля продукта без первого поля (заголовка)
					*}
					<td class="nameleft fields-list-row">
						<ul class="admin-index-fields-list">
							{foreach $oProduct->getProductFieldsWOFirstField() as $oProductField}
								<li{if $oProductField@iteration % 2 == 0} class="second"{/if}>
									<span class="field" title="{$oProductField->getField()->getDescription()|escape}">{$oProductField->getField()->getTitle()}</span>:
									<span class="content" title="{$oProductField->getDisplayValue(false, 300, '...', false)|escape}">
										{$oProductField->getDisplayValue(false, 100, '...', false)}
									</span>
								</li>
							{/foreach}
						</ul>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	{include file='paging.tpl'}

{else}
	{$aLang.plugin.simplecatalog.Products.List.no_products}
{/if}
