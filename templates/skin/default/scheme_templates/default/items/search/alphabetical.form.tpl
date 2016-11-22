
{*
	Алфавитный поиск
*}

<ul class="search-abc">
	{*
		текущая выбранная буква из алфавитного поиска
	*}
	{$sSchemeAlphabeticalSearchItemCurrent = $_aRequest.filter.letter}

	<li {if $sSchemeAlphabeticalSearchItemCurrent==''}class="active"{/if}>
		<a href="{$oScheme->getCatalogItemsWebPath()}" rel="nofollow"><span>{$aLang.user_search_filter_all}</span></a>
	</li>
	{foreach $aSchemeAlphabeticalSearchItems as $sSchemeAlphabeticalSearchItem}
		<li {if $sSchemeAlphabeticalSearchItemCurrent==$sSchemeAlphabeticalSearchItem}class="active"{/if}>
			<a href="{$oScheme->getCatalogItemsAlphabeticalSearchWebPath()}?filter[letter]={$sSchemeAlphabeticalSearchItem}" rel="nofollow"><span>{$sSchemeAlphabeticalSearchItem}</span></a>
		</li>
	{/foreach}
</ul>
