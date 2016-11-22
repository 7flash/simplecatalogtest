
	{if $oScheme->getAllowUserFriendlyUrlEnabled() or $oScheme->getAllowEditAdditionalSeoMetaEnabled()}
		<h2 class="page-header title-underline">
			{$aLang.plugin.simplecatalog.Products.Add.titles.seo}
		</h2>
	{/if}


	{*
		ЧПУ
	*}
	{if $oScheme->getAllowUserFriendlyUrlEnabled()}
		<div class="mb-20">
			{$aLang.plugin.simplecatalog.Products.Add.product_url}
			<br />
			<input type="text" name="product_url" value="{$_aRequest.product_url}" class="input-text input-width-full" maxlength="2000" />
			<br />
			<span class="note">{$aLang.plugin.simplecatalog.Products.Add.product_url_info}</span>
		</div>
	{/if}
	{*
		если нужно указывать SEO данные
	*}
	{if $oScheme->getAllowEditAdditionalSeoMetaEnabled()}
		<div class="mb-20">
			{$aLang.plugin.simplecatalog.Products.Add.seo_title}
			<br />
			<input type="text" name="seo_title" value="{$_aRequest.seo_title}" class="input-text input-width-full" maxlength="100" />
			<br />
			<span class="note">{$aLang.plugin.simplecatalog.Products.Add.seo_title_info}</span>
		</div>
		<div class="mb-20">
			{$aLang.plugin.simplecatalog.Products.Add.seo_description}
			<br />
			<textarea name="seo_description" class="input-text input-width-full" maxlength="200">{$_aRequest.seo_description}</textarea>
			<br />
			<span class="note">{$aLang.plugin.simplecatalog.Products.Add.seo_description_info}</span>
		</div>
		<div class="mb-20">
			{$aLang.plugin.simplecatalog.Products.Add.seo_keywords}
			<br />
			<input type="text" name="seo_keywords" value="{$_aRequest.seo_keywords}" class="input-text input-width-full" maxlength="200" />
			<br />
			<span class="note">{$aLang.plugin.simplecatalog.Products.Add.seo_keywords_info}</span>
		</div>
	{/if}


	{if ($oScheme->getAllowComments()==$SC_ALLOW_COMMENTS_USER_DEFINED) or
		(Config::Get('plugin.simplecatalog.categories.show_block_when_no_categories') or ($aCategoryTree and count($aCategoryTree)>0)) or
		(!$_aRequest.id) or
		($oScheme->getShopEnabled())
	}
		<h2 class="page-header title-underline">
			{$aLang.plugin.simplecatalog.Products.Add.titles.secondary_parameters}
		</h2>
	{/if}


	{*
		разрешены ли комментарии
	*}
	{if $oScheme->getAllowComments()==$SC_ALLOW_COMMENTS_USER_DEFINED}
		<div class="mb-20">
			<dl class="w50p">
				<dt>
					{$aLang.plugin.simplecatalog.Products.Add.user_allow_comments}
				</dt>
				<dd>
					<select name="user_allow_comments" class="input-text input-width-100">
						<option value="{$SC_COMPONENT_ENABLED}" {if $_aRequest.user_allow_comments==$SC_COMPONENT_ENABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.Yes}
						</option>
						<option value="{$SC_COMPONENT_DISABLED}" {if $_aRequest.user_allow_comments==$SC_COMPONENT_DISABLED}selected="selected"{/if}>
							{$aLang.plugin.simplecatalog.No}
						</option>
					</select>
				</dd>
			</dl>
		</div>
	{/if}


	{*
		категории продукта
	*}
	{if Config::Get('plugin.simplecatalog.categories.show_block_when_no_categories') or ($aCategoryTree and count($aCategoryTree)>0)}
		<div class="mb-20">
			<dl class="w50p">
				<dt>
					{$aLang.plugin.simplecatalog.Products.Add.select_categories}
				</dt>
				<dd>
					<div class="input-text input-width-full checkboxes-multi-select-wrapper">
						{include file="{$aTemplatePathPlugin.simplecatalog}helpers/categories/checkboxes.tpl"
							sName="categories_ids"
						}
					</div>
				</dd>
			</dl>
		</div>
	{/if}


	{*
		флаг добавления фото к продукту, только если включена загрузка фото
	*}
	{if !$_aRequest.id and $oScheme->getMaxImagesCount()}
		<div class="mb-20">
			{*
				для новых продуктов по-умолчанию включен переход к загрузке фото
			*}
			<label>
				<input type="checkbox" value="1" name="upload_photo" checked="checked" />
				{$aLang.plugin.simplecatalog.Products.Add.upload_photos}
			</label>
		</div>
	{/if}


	{*
		интернет-магазин
	*}
	{if $oScheme->getShopEnabled()}
		<div class="mb-20">
			<dl class="w50p">
				<dt>
					{$aLang.plugin.simplecatalog.Products.Add.shop.price}
				</dt>
				<dd>
					<input type="text" name="price" class="input-text input-width-100" value="{$_aRequest.price}" /> {$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
				</dd>
			</dl>
		</div>
		<div class="mb-20">
			<dl class="w50p">
				<dt>
					{$aLang.plugin.simplecatalog.Products.Add.shop.price_new}
				</dt>
				<dd>
					<input type="text" name="price_new" class="input-text input-width-100" value="{$_aRequest.price_new}" /> {$LS->PluginSimplecatalog_Shop_GetCurrencyDisplayValue()}
				</dd>
			</dl>
		</div>
	{/if}


	{*
		добавление связей для продукта
	*}
	<div class="mb-20">
		{sc_scheme_template scheme=$oScheme file="add/links.tpl"}
	</div>


	{*
		добавление объектов на карту
	*}
	<div class="mb-20">
		{sc_scheme_template scheme=$oScheme file="maps/add.tpl"}
	</div>


	{*
		сообщение что продукт будет нуждаться в модерации
	*}
	{if $oUserCurrent->getUserProductsNeedModerationBySchemeAndNotAdmin($oScheme)}
		<div class="your-product-needs-moderation mb-20">
			{$aLang.plugin.simplecatalog.Products.Add.your_product_needs_moderation}
		</div>
	{/if}

	{*
		отложенная публикация
	*}
	{sc_scheme_template scheme=$oScheme file="add/controls/deferred.tpl"}

	<div>
		{hook run='sc_product_add_footer' iProductId=$_aRequest.id}
	</div>
