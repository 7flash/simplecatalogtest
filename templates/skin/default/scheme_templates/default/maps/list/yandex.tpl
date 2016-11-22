
{*
	Список меток продукта на карте
*}

{if $aProductMapItems = $oProduct->getMapItems()}
	<div>
		<a name="product_map"></a>
		<h2 class="page-header mb-30">
			{$aLang.plugin.simplecatalog.Products.Item.maps.title}
			(<span>{count($aProductMapItems)}</span>)
		</h2>

		{*
			враппер для карты
		*}
		<div id="sc_product_item_map_wrapper" class="mb-20"></div>

		<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		<script>
			jQuery (document).ready (function ($) {
				/**
				 * создать просмотр карты
				 */
				ls.sc_map_editor_yandex.BuildMapWithControls('sc_product_item_map_wrapper', {
					aCenter: [{Config::Get('plugin.simplecatalog.maps.settings.base.center')}],
					iZoom: {Config::Get('plugin.simplecatalog.maps.settings.base.zoom')},
					bDisableEvents: true
				});

				/**
				 * отобразить метки на карте (используя кластеризацию)
				 */
				var aGeoObjects = [];
				{foreach $aProductMapItems as $oMapItem}
					aGeoObjects.push({json var=[
						'id' => $oMapItem->getId(),
						'coords' => [$oMapItem->getLat(), $oMapItem->getLng()],
						'title' => $oMapItem->getTitle(),
						'hint' => $oMapItem->getExtraHint(),
						'content' => $oMapItem->getDescription(),
						'preset' => $oMapItem->getExtraPreset()
					]});
				{/foreach}
				ls.sc_map_editor_yandex.AddPlaceholdersOnMapUsingCluster(aGeoObjects);
				/**
				 * задать масштаб чтобы были видны все метки на карте
				 */
				ls.sc_map_editor_yandex.SetBoundsByGeoObjects();
				/**
				 * добавить метку "Я" с координатами пользователя
				 */
				ls.sc_map_editor_yandex.SetPointToUsersCoords();
				/**
				 * установить слушатель для ссылок центрирования координат (списка меток под картой)
				 */
				ls.sc_map_editor_yandex.AssignListenerForCenterMapLinks();
			});
		</script>
		<div class="mb-20">
			{foreach $aProductMapItems as $oMapItem}
				<div>
					{*
						список точек для быстрого центрирования на карте
					*}
					{$sLat = $oMapItem->getLat()}
					{$sLng = $oMapItem->getLng()}
					{$sTitle = $oMapItem->getTitle()}
					{$sHint = $oMapItem->getExtraHint()}
					{$sDescription = $oMapItem->getDescription()}

					<div class="mb-10">
						<i class="sc-icon-map-marker"></i>
						<a href="#" class="link-dotted js-sc-map-center-coords" data-map-coords-lat="{$sLat}" data-map-coords-lng="{$sLng}" title="{$sHint}">{strip}
							{if $sTitle or $sDescription}
								{if $sTitle}<b>{$sTitle}</b>{/if}
								{if $sTitle and $sDescription}, {/if}
								{if $sDescription}{$sDescription}{/if}
							{else}
								<b># {$oMapItem@index}</b>
							{/if}
						{/strip}</a>
					</div>
					{*
						данные точек для микроразметки
					*}
					<div itemscope itemtype="http://schema.org/GeoCoordinates">
						<meta itemprop="latitude" content="{$sLat}" />
						<meta itemprop="longitude" content="{$sLng}" />
						<meta itemprop="name" content="{$sTitle}" />
						<meta itemprop="alternateName" content="{$sHint}" />
						<meta itemprop="description" content="{$sDescription}" />
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
