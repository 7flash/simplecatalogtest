
{*
	Отложенная публикация
*}

{if $oUserCurrent->getUserCanDeferProductsBySchemeOrIsAdmin($oScheme)}
	<div class="mb-20">
		{*
			флажок отложенной публикации
		*}
		<label>
			<input type="checkbox" value="1" name="save_deferred" class="js-sc-toggle-switch" data-linked-id="js-sc-product-add-deferred-date-select"
				   {if $_aRequest.save_deferred}checked="checked"{/if} />
			{$aLang.plugin.simplecatalog.Products.Add.deferred.enable}
		</label>
		{*
			выбор даты публикации
		*}
		<div id="js-sc-product-add-deferred-date-select" {if !$_aRequest.save_deferred}class="d-n"{/if}>
			<i class="sc-icon-time"></i>
			{*
				дата
			*}
			<input type="text" name="deferred_date" value="{$_aRequest.deferred_date|default:date('Y-m-d')}"
				   class="input-text input-width-150 js-date-picker-php" placeholder="{date('Y-m-d')}" title="{$aLang.plugin.simplecatalog.Products.Add.deferred.tips.date}" />

			{$aLang.plugin.simplecatalog.Products.Add.deferred.at}
			{*
				часы
			*}
			<select name="deferred_hour" class="input-text input-width-60" title="{$aLang.plugin.simplecatalog.Products.Add.deferred.tips.hour}">
				{$sDeferredHour = $_aRequest.deferred_hour|default:date('H')}
				{section name=deferred_hour start=0 loop=24 step=1}
					{$iIndex = $smarty.section.deferred_hour.index}
					<option value="{$iIndex}" {if $iIndex==$sDeferredHour}selected="selected"{/if}>{$iIndex}</option>
				{/section}
			</select>
			:
			{*
				минуты
			*}
			<select name="deferred_minute" class="input-text input-width-60" title="{$aLang.plugin.simplecatalog.Products.Add.deferred.tips.minute}">
				{$sDeferredMin = $_aRequest.deferred_minute|default:date('i')}
				{section name=deferred_minute start=0 loop=60 step=1}
					{$iIndex = $smarty.section.deferred_minute.index}
					<option value="{$iIndex}" {if $iIndex==$sDeferredMin}selected="selected"{/if}>{$iIndex}</option>
				{/section}
			</select>
			{*
				текущее время на сервере
			*}
			<div>
				{$aLang.plugin.simplecatalog.Products.Add.deferred.server_time}: <b>{date('Y-m-d H:i')}</b>
			</div>
		</div>
	</div>
{/if}
