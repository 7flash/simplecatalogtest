
	<div>
		<h3 class="mb-20 sub-title soft-underline">
			{$oField->getValuePrefix()}{$oField->getTitle()}{$oField->getValuePostfix()}
		</h3>
		{if $sDescription = $oField->getDescription()}
			<div>{$sDescription}</div>
		{/if}
	</div>
