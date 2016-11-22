{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.userassign.tpl"}

	<div class="Simplecatalog Userassign Index">
		<h2 class="page-header">{$aLang.plugin.simplecatalog.Userassign.List.title} (<span>{count($aUserassign)}</span>)</h2>

		{if $aUserassign and count($aUserassign)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th>{$aLang.plugin.simplecatalog.Userassign.List.table_header.name}</th>
						<th>{$aLang.plugin.simplecatalog.Userassign.List.table_header.scheme}</th>
						<th>{$aLang.plugin.simplecatalog.Userassign.List.table_header.user}</th>

						<th>{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$aUserassign item=oUserassign}
						<tr{if $oUserassign@iteration % 2 == 0} class="second"{/if}>
							<td>{$oUserassign->getGroup()->getGroupName()}</td>
							<td>{$oUserassign->getGroup()->getScheme()->getSchemeName()}</td>
							<td>
								<a href="{$oUserassign->getUser()->getUserWebPath()}">{$oUserassign->getUser()->getLogin()}</a>
							</td>

							<td>
								<a href="{$oUserassign->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oUserassign->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.Userassign.List.no_userassign}
		{/if}
	</div>

{include file='footer.tpl'}