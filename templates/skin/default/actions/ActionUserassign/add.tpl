{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.userassign.tpl"}

	<div class="Simplecatalog Userassign Add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.Userassign.Add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.Userassign.Add.titles.new}
			{/if}
		</h2>

		<form action="{router page='userassign'}add" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

			<input type="hidden" name="id" value="{$_aRequest.id}" />

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Userassign.Add.group_id}</dt>
				<dd>
					<select name="group_id" class="input-text input-width-250">
						{foreach from=$aUsergroups item=oUsergroup}
							<option value="{$oUsergroup->getId()}" {if $_aRequest.group_id==$oUsergroup->getId()}selected="selected"{/if}>
								{$oUsergroup->getGroupName()} ({$oUsergroup->getScheme()->getSchemeName()})
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Userassign.Add.userlogins}</dt>
				<dd>
					<input type="text" name="userlogins" value="{$_aRequest.userlogins}" class="input-text input-width-250 SC_AC_Multi_Logins"
						   placeholder="{$aLang.plugin.simplecatalog.Userassign.Add.userlogins_ph}" />
				</dd>
			</dl>


			<input type="submit" value="{$aLang.plugin.simplecatalog.Userassign.Add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{include file='footer.tpl'}