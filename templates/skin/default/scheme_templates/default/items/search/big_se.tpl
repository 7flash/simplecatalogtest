
{*
	Форма поиска в других ПС
*}

{if Config::Get("plugin.simplecatalog.search.products.show_links_for_other_se") and $_aRequest.q}
	<div class="search-results-are-empty">
		<h2 class="page-header">
			{$aLang.plugin.simplecatalog.Products.Items.search.find_in_other_SE|ls_lang:"value%%`$_aRequest.q|escape:'html'`"}
		</h2>

		<a href="http://www.google.com/search?ie=UTF-8&amp;hl=ru&amp;q={$_aRequest.q|escape:'html'}&amp;as_sitesearch={Config::Get("path.root.web")}"
		   target="_blank" rel="external nofollow">Google</a>,
		<a href="http://yandex.ru/yandsearch?text={$_aRequest.q|escape:'html'}&amp;site={Config::Get("path.root.web")}" target="_blank" rel="external nofollow">Yandex</a>,
		<a href="http://www.bing.com/search?q={$_aRequest.q|escape:'html'}+site%3A{Config::Get("path.root.web")}" target="_blank" rel="external nofollow">Bing</a>.
	</div>
{/if}
