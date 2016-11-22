
	<dl class="fields-wrapper">
		<dt>
			{$oField->getDescription()}

			{if $oField->getMandatoryEnabled()}
				<b>*</b>
			{/if}
		</dt>
		<dd>
			<label>
				<input type="checkbox" name="{$sName}" value="1" class="input-checkbox" {if $sValue}checked="checked"{/if} />
				{$oField->getTitle()}

				{$mValuePrefix = $oField->getValuePrefix()}
				{$mValuePostfix = $oField->getValuePostfix()}
				{if $mValuePrefix or $mValuePostfix}({$mValuePrefix}{if $mValuePrefix and $mValuePostfix}, {/if}{$mValuePostfix}){/if}
			</label>
		</dd>
	</dl>
