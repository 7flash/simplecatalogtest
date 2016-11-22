
{*
	Добавление к продукту объектов на карте
*}

{if $oScheme->getMapItemsEnabled()}
	{sc_scheme_template scheme=$oScheme file="maps/add/{Config::Get('plugin.simplecatalog.maps.type')}.tpl"}
{/if}
