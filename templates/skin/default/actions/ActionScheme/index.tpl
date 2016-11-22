{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.schemes.tpl"}

	<div class="Simplecatalog Scheme Index">
		<h2 class="page-header">{$aLang.plugin.simplecatalog.Schemes.List.title} (<span>{count($aSchemes)}</span>)</h2>

		{if $aSchemes and count($aSchemes)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th rowspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.scheme_url}</th>
						<th rowspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.scheme_name}</th>
						<th rowspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.active}</th>
						<th rowspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.menu_add_topic_create}</th>
						<th rowspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.menu_main_add_link}</th>

						<th colspan="2">{$aLang.plugin.simplecatalog.Schemes.List.table_header.title_fields_count}</th>

						<th rowspan="2">{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
					<tr>
						<th>{$aLang.plugin.simplecatalog.Schemes.List.table_header.short_view_fields_count}</th>
						<th class="mw100px">{$aLang.plugin.simplecatalog.Schemes.List.table_header.fields_count_total}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$aSchemes item=oScheme}
						<tr{if $oScheme@iteration % 2 == 0} class="second"{/if} data-item-id="{$oScheme->getId()}">
							<td>{$oScheme->getSchemeUrl()}</td>
							<td>{$oScheme->getSchemeName()}</td>
							<td>{if $oScheme->getActiveEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oScheme->getMenuAddTopicCreate()==$SC_COMPONENT_ENABLED}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oScheme->getMenuMainAddLink()==$SC_COMPONENT_ENABLED}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{$oScheme->getShortViewFieldsCount()}</td>

							<td>
								{*
									кнопка отображения короткого списка полей, добавление нового поля и страница со списком всех полей схемы
								*}
								<a href="{$oScheme->getFieldsListWebPath()}"
								   title="{$aLang.plugin.simplecatalog.Schemes.List.list_of_all_fields}"
								   class="toggle-field-list js-toggle-field-list">{$oScheme->getFields()|count}</a>
								<span class="fl-r">
									<a href="{$oScheme->getNewFieldAddWebPath()}" class="sc-icon-plus" title="{$aLang.plugin.simplecatalog.Schemes.List.add_new_field}"></a>
									<a href="{$oScheme->getFieldsListWebPath()}" class="sc-icon-list" title="{$aLang.plugin.simplecatalog.Schemes.List.list_of_all_fields}"></a>
								</span>
								{*
									короткий список полей
								*}
								<ul class="field-list js-field-list">
									{foreach from=$oScheme->getFields() item=oField}
										<li title="{$oField->getDescription()|escape:'html'}"{if $oField@iteration % 2 == 0} class="second"{/if}>
											<span class="fl-r"><a href="{$oField->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$oField->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a></span>
											{$oField->getTitle()}:<i>{$oField->getFieldType()}</i>
											{if $oField->getMandatoryEnabled()}<i class="sc-icon-asterisk" title="{$aLang.plugin.simplecatalog.Schemes.List.mandatory}"></i>{/if}
											{if $oField->getForAuthUsersOnlyEnabled()}<i class="sc-icon-user" title="{$aLang.plugin.simplecatalog.Schemes.List.for_auth_users_only}"></i>{/if}
										</li>
									{/foreach}
								</ul>
							</td>
							<td>
								<a href="{$oScheme->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oScheme->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.Schemes.List.no_schemes}
		{/if}
	</div>

{include file='footer.tpl'}