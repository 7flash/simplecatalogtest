
	<h3 class="mb-15 mt15 sub-title">
		{*{$sValue}*}
		{$oField->getValuePrefix()}{$oField->getTitle()}{$oField->getValuePostfix()}
	</h3>
	{if $sDescription = $oField->getDescription()}
		<div class="mb-20">{$sDescription}</div>
	{/if}
