
	{if $sValue}
		{*
			имя файла хранится в хештеге ссылки
		*}
		{$sFileName = parse_url($sValue, constant('PHP_URL_FRAGMENT'))}
		{*
			зашифрованная или обычная ссылка на файл
		*}
		<a href="{$sValue}" rel="nofollow" title="{$sFileName}">{$aLang.plugin.simplecatalog.Products.Item.values.file.download_file|ls_lang:"name%%`$sFileName|truncate:27:'...':true`"}</a>
		{*
			количество скачиваний файла
		*}
		{if $iDownloadsCount = $oField->getFileDownloadsCount($oProductField)}
			<span title="{$aLang.plugin.simplecatalog.Products.Item.values.file.downloads_count}">
				<i class="sc-icon-download-alt"></i>{$iDownloadsCount}
			</span>
		{/if}
	{else}
		{$aLang.plugin.simplecatalog.Products.Item.values.file.no_file}
	{/if}
