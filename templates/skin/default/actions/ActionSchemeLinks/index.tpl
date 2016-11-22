{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.links.tpl"}

{if $sMenuSchemeSelect}
	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.links_submenu.tpl"}

	<div class="Simplecatalog Links Index">
		{*
			справка
		*}
		<div class="fl-r">
			<a href="#" class="link-dotted js-sc-toggle-switch" data-linked-id="js-sc-scheme-links-help">{$aLang.plugin.simplecatalog.links.list.help}</a>
		</div>
		<div id="js-sc-scheme-links-help" class="mb-15 d-n">
			{$aLang.plugin.simplecatalog.links.list.help_text}
		</div>

		<h2 class="page-header">{$aLang.plugin.simplecatalog.links.list.title} (<span>{count($aSchemeLinks)}</span>)</h2>

		{if $aSchemeLinks and count($aSchemeLinks)>0}

			<table class="table-items-list">
				<thead>
					<tr>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.name}</th>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.active}</th>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.target_scheme}</th>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.type}</th>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.show_type}</th>
						<th>{$aLang.plugin.simplecatalog.links.list.table_header.select_type}</th>

						<th>{$aLang.plugin.simplecatalog.Controls}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$aSchemeLinks item=oSchemeLink}
						<tr{if $oSchemeLink@iteration % 2 == 0} class="second"{/if} data-item-id="{$oSchemeLink->getId()}">
							<td>{$oSchemeLink->getName()}</td>
							<td>{if $oSchemeLink->getActiveEnabled()}{$aLang.plugin.simplecatalog.Yes}{else}{$aLang.plugin.simplecatalog.No}{/if}</td>
							<td>{$oSchemeLink->getTargetScheme()->getSchemeName()}</td>
							<td>
								{$aLang.plugin.simplecatalog.links.type[$oSchemeLink->getType()]}
							</td>
							<td>
								{$aLang.plugin.simplecatalog.links.show_type[$oSchemeLink->getShowType()]}
							</td>
							<td>
								{$aLang.plugin.simplecatalog.links.select_type[$oSchemeLink->getSelectType()]}
							</td>
							<td>
								<a href="{$oSchemeLink->getEditWebPath()}" class="sc-icon-edit" title="{$aLang.plugin.simplecatalog.Edit}"></a>
								<a href="{$oSchemeLink->getDeleteWebPath()}" class="sc-icon-remove js-question" title="{$aLang.plugin.simplecatalog.Delete}"></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>

		{else}
			{$aLang.plugin.simplecatalog.links.list.no_links}
		{/if}
	</div>

{elseif $aSchemesMenuItems and count($aSchemesMenuItems)>0}
	{$aLang.plugin.simplecatalog.links.select_scheme_you_want_to_work_with}
{/if}

{include file='footer.tpl'}