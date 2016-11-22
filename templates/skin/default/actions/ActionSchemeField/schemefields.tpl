{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.schemes_fields.tpl"}

	<div class="Simplecatalog Field Index">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Fields.List.title} "<a href="{$oScheme->getCatalogItemsWebPath()}">{$oScheme->getSchemeName()}</a>"
			({$oScheme->getSchemeUrl()}{if count($aSchemeFields)}, <span>{count($aSchemeFields)}</span>{/if})
		</h2>

		{if $aSchemeFields and count($aSchemeFields)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.title}</th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.code}</th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.description}</th>
						<th></th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.field_type}</th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.value_prefix}</th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.value_postfix}</th>
						<th>{$aLang.plugin.simplecatalog.Fields.List.table_header.sorting}</th>
						<th>{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
				</thead>
				<tbody data-scheme-id="{$oScheme->getId()}">
					{foreach from=$aSchemeFields item=oField}
						<tr{if $oField@iteration % 2 == 0} class="second"{/if} data-item-id="{$oField->getId()}">
							<td>{$oField->getTitle()}</td>
							<td>{$oField->getCode()}</td>
							<td>{$oField->getDescription()}</td>
							<td class="cd">
								<i class="sc-icon-asterisk {if !$oField->getMandatoryEnabled()}option-disabled{/if}" title="{$aLang.plugin.simplecatalog.Fields.List.mandatory}"></i>
								<i class="sc-icon-user {if !$oField->getForAuthUsersOnlyEnabled()}option-disabled{/if}" title="{$aLang.plugin.simplecatalog.Fields.List.for_auth_users_only}"></i>
							</td>
							<td>{$oField->getFieldType()}</td>
							<td>{$oField->getValuePrefix()}</td>
							<td>{$oField->getValuePostfix()}</td>
							<td>{$oField->getSorting()}</td>
							<td><a href="{$oField->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$oField->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a></td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.Fields.List.no_fields}
		{/if}
	</div>

{include file='footer.tpl'}