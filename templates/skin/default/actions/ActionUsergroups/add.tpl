{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.usergroups.tpl"}

	<div class="Simplecatalog Usergroups Add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.Usergroups.Add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.Usergroups.Add.titles.new}
			{/if}
		</h2>

		<form action="{router page='usergroups'}add" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
			<input type="hidden" name="id" value="{$_aRequest.id}" />

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.group_name}</dt>
				<dd>
					<input type="text" name="group_name" value="{$_aRequest.group_name}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.Usergroups.Add.group_name_ph}" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.active}</dt>
				<dd>
					<select name="active" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.active==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.active==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.scheme_id}</dt>
				<dd>
					<select name="scheme_id" class="input-text input-width-250">
						{foreach from=$aSchemes item=oScheme}
							<option value="{$oScheme->getId()}" {if $_aRequest.scheme_id==$oScheme->getId()}selected="selected"{/if}>
								{$oScheme->getSchemeName()}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			<h2 class="page-header title-underline">
				{$aLang.plugin.simplecatalog.Usergroups.Add.titles.rights}
			</h2>


			{*
				набор прав
			*}

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.can_user_edit_products}</dt>
				<dd>
					<select name="can_user_edit_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.can_user_edit_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.can_user_edit_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.user_products_need_moderation}</dt>
				<dd>
					<select name="user_products_need_moderation" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.user_products_need_moderation==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.user_products_need_moderation==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.user_can_moderate_products}</dt>
				<dd>
					<select name="user_can_moderate_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.user_can_moderate_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.user_can_moderate_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.user_can_defer_products}</dt>
				<dd>
					<select name="user_can_defer_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.user_can_defer_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.user_can_defer_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Usergroups.Add.user_can_create_new_products}</dt>
				<dd>
					<select name="user_can_create_new_products" class="input-text input-width-250">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.user_can_create_new_products==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.user_can_create_new_products==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>


			<input type="submit" value="{$aLang.plugin.simplecatalog.Usergroups.Add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{include file='footer.tpl'}