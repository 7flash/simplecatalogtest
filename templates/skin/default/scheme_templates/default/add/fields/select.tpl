
	<dl class="fields-wrapper">
		<dt>
			{$oField->getTitle()}

			{$mValuePrefix = $oField->getValuePrefix()}
			{$mValuePostfix = $oField->getValuePostfix()}
			{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}

			{if $oField->getMandatoryEnabled()}
				<b>*</b>
			{/if}
		</dt>
		<dd>
			{*
				разрешен ли множественный выбор значений (в виде флажков)
			*}
			{if $oField->getSelectMultipleItemsEnabled()}
				{*
					после ошибки при валидации значений в реквесте уже массив
				*}
				{if !is_array($sValue)}
					{$sValue=$oField->getArrayOfIndexesForMultipleSelectFromStringValue($sValue)}
				{/if}

				{foreach from=$oField->getDefinedSelectValues() item=sSelectValue}
					<label>
						<input type="checkbox" value="{$sSelectValue@index}" name="{$sName}[]" {if $sValue.0 !== '' and in_array($sSelectValue@index, $sValue)}checked="checked"{/if} />
						{$sSelectValue}
					</label>
				{/foreach}

			{else}
				<select name="{$sName}" class="input-text input-width-full">
					<option value="">---</option>
					{foreach from=$oField->getDefinedSelectValues() item=sSelectValue}
						<option value="{$sSelectValue@index}" {if $sValue !== '' and $sValue==$sSelectValue@index}selected="selected"{/if}>{$sSelectValue}</option>
					{/foreach}
				</select>
			{/if}

			<span class="note mt5">
				{$oField->getDescription()}
			</span>
		</dd>
	</dl>
