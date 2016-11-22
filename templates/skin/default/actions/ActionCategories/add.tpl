{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="simplecatalog_menu"}

{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.categories.tpl"}

{if $sMenuSchemeSelect}
	{include file="{$aTemplatePathPlugin.simplecatalog}navs/menu.content.categories_submenu.tpl"}

	<div class="Simplecatalog Categories Add">
		<h2 class="page-header title-underline">
			{if $_aRequest.id}
				{$aLang.plugin.simplecatalog.Categories.Add.titles.edit}
			{else}
				{$aLang.plugin.simplecatalog.Categories.Add.titles.new}
			{/if}
		</h2>

		<form action="{router page='sccategories'}add/{$sMenuSchemeSelect}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
			<input type="hidden" name="id" value="{$_aRequest.id}" />

			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.url}</dt>
				<dd>
					<input type="text" name="url" value="{$_aRequest.url}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.Categories.Add.url_ph}" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.name}</dt>
				<dd>
					<input type="text" name="name" value="{$_aRequest.name}" class="input-text input-width-250"
						   placeholder="{$aLang.plugin.simplecatalog.Categories.Add.name_ph}" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.description}</dt>
				<dd>
					<textarea name="description" class="input-text input-width-250" maxlength="500">{$_aRequest.description}</textarea>
				</dd>
			</dl>


			<h2 class="page-header title-underline">
				{$aLang.plugin.simplecatalog.Categories.Add.titles.settings}
			</h2>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.parent_id}</dt>
				<dd>
					{include file="{$aTemplatePathPlugin.simplecatalog}helpers/categories/select.tpl"
						sName="parent_id"
						sNoCategoryText=$aLang.plugin.simplecatalog.Categories.Add.no_parent_id
					}
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.sorting}</dt>
				<dd>
					<input type="text" name="sorting" value="{$_aRequest.sorting}" class="input-text input-width-250" placeholder="1" />
				</dd>
			</dl>


			<dl class="w50p mb-20">
				<dt>{$aLang.plugin.simplecatalog.Categories.Add.image}</dt>
				<dd>
					<input type="file" name="image_url" />
					{if $_aRequest.image_url_original}
						<div>
							<div class="mb-15 mt15">
								{$aLang.plugin.simplecatalog.Categories.Add.image_current}:
							</div>
							<a href="{$_aRequest.image_url_original}"
							   target="_blank"><img src="{$_aRequest.image_url_original}" title="{$_aRequest.image_url_original|escape:'html'|truncate:100:'...'}" /></a>
						</div>
						<input type="hidden" name="image_url_original" value="{$_aRequest.image_url_original}" />
						{*
							удалить изображение
						*}
						<label>
							<input type="checkbox" value="1" name="delete_image">
							{$aLang.plugin.simplecatalog.Categories.Add.delete_image}
						</label>
					{/if}
				</dd>
			</dl>


			<input type="submit" value="{$aLang.plugin.simplecatalog.Categories.Add.submit_title}" name="submit_add" class="button button-primary" />
		</form>
	</div>

{elseif $aSchemesMenuItems and count($aSchemesMenuItems)>0}
	{$aLang.plugin.simplecatalog.Categories.select_scheme_you_want_to_work_with}
{/if}

{include file='footer.tpl'}