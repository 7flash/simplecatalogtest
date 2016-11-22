{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.links.tpl"}

{if $sMenuSchemeSelect}
	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.links_submenu.tpl"}

	<div class="Simplecatalog Links add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.links.add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.links.add.titles.new}
			{/if}
			"{$oScheme->getSchemeName()}"
		</h2>

		<form action="{router page='sc_links'}add/{$sMenuSchemeSelect}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
			<input type="hidden" name="id" value="{$_aRequest.id}" />

			{*
				включена ли связь
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.active}</dt>
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


			{*
				имя
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.name}</dt>
				<dd>
					<input type="text" name="name" value="{$_aRequest.name}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.links.add.name_ph}" />
				</dd>
			</dl>


			{*
				описание
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.description}</dt>
				<dd>
					<textarea name="description" class="input-text input-width-250" maxlength="2000"
							  placeholder="{$aLang.plugin.simplecatalog.links.add.description_ph}">{$_aRequest.description}</textarea>
				</dd>
			</dl>


			<h2 class="page-header title-underline">
				{$aLang.plugin.simplecatalog.links.add.titles.settings}
			</h2>


			{*
				связанная схема
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.target_scheme_id}</dt>
				<dd>
					<select name="target_scheme_id" class="input-text input-width-250">
						{foreach from=$aSchemesMenuItems item=oScheme}
							{*
								tip: можно делать связи для схем даже саму с собой
							*}
							<option value="{$oScheme->getId()}" {if $_aRequest.target_scheme_id==$oScheme->getId()}selected="selected"{/if}>
								{$oScheme->getSchemeName()}
							</option>
						{/foreach}
					</select>
				</dd>
			</dl>


			{*
				тип связи
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.type}</dt>
				<dd>
					<select name="type" class="input-text input-width-250">
						<option value="{PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_ONE}"
								{if $_aRequest.type==PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_ONE}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.type[PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_ONE]}
						</option>
						<option value="{PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_MANY}"
								{if $_aRequest.type==PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_MANY}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.type[PluginSimplecatalog_ModuleLinks::LINK_TYPE_HAS_MANY]}
						</option>
					</select>
				</dd>
			</dl>


			{*
				формат вывода
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.show_type}</dt>
				<dd>
					<select name="show_type" class="input-text input-width-250">
						<option value="{PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_TAB}"
								{if $_aRequest.show_type==PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_TAB}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.show_type[PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_TAB]}
						</option>
						<option value="{PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_LINKS}"
								{if $_aRequest.show_type==PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_LINKS}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.show_type[PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_LINKS]}
						</option>
						<option value="{PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_IMAGES}"
								{if $_aRequest.show_type==PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_IMAGES}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.show_type[PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_AS_IMAGES]}
						</option>
						<option value="{PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_SELECT}"
								{if $_aRequest.show_type==PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_SELECT}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.show_type[PluginSimplecatalog_ModuleLinks::DISPLAY_LINK_TYPE_IN_SELECT]}
						</option>
					</select>
				</dd>
			</dl>


			{*
				выбор продуктов
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.select_type}</dt>
				<dd>
					<select name="select_type" class="input-text input-width-250">
						<option value="{PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_ALL}"
								{if $_aRequest.select_type==PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_ALL}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.select_type[PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_ALL]}
						</option>
						<option value="{PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_SELF}"
								{if $_aRequest.select_type==PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_SELF}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.links.select_type[PluginSimplecatalog_ModuleLinks::SELECT_LINK_TYPE_SELF]}
						</option>
					</select>
				</dd>
			</dl>


			{*
				сортировка
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.sorting}</dt>
				<dd>
					<input type="text" name="sorting" value="{$_aRequest.sorting}" class="input-text input-width-250" placeholder="1" />
				</dd>
			</dl>


			{*
				количество продуктов для выбора в селекте для установки связи
			*}
			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.links.add.products_count_to_select}</dt>
				<dd>
					<input type="text" name="products_count_to_select" value="{$_aRequest.products_count_to_select}" class="input-text input-width-250" placeholder="100" />
				</dd>
			</dl>


			<input type="submit" value="{$aLang.plugin.simplecatalog.links.add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{elseif $aSchemesMenuItems and count($aSchemesMenuItems)>0}
	{$aLang.plugin.simplecatalog.links.select_scheme_you_want_to_work_with}
{/if}

{include file='footer.tpl'}