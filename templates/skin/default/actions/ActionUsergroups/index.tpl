{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.usergroups.tpl"}

	<div class="Simplecatalog Usergroups Index">
		<h2 class="page-header">{$aLang.plugin.simplecatalog.Usergroups.List.title} (<span>{count($aUsergroups)}</span>)</h2>

		{if $aUsergroups and count($aUsergroups)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.group_name}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.active}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.scheme_id}</th>

						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.can_user_edit_products}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.user_products_need_moderation}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.user_can_moderate_products}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.user_can_defer_products}</th>
						<th>{$aLang.plugin.simplecatalog.Usergroups.List.table_header.user_can_create_new_products}</th>

						<th>{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$aUsergroups item=oUsergroup}
						<tr{if $oUsergroup@iteration % 2 == 0} class="second"{/if}>
							<td>{$oUsergroup->getGroupName()}</td>
							<td>{if $oUsergroup->getActiveEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{$oUsergroup->getScheme()->getSchemeName()}</td>

							<td>{if $oUsergroup->getCanUserEditProductsEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oUsergroup->getUserProductsNeedModerationEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oUsergroup->getUserCanModerateProductsEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oUsergroup->getUserCanDeferProductsEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{if $oUsergroup->getUserCanCreateNewProductsEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>

							<td>
								<a href="{$oUsergroup->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oUsergroup->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.Usergroups.List.no_usergroups}
		{/if}
	</div>

{include file='footer.tpl'}