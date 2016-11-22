
{*
	Добавление меток на карту
*}

<h2 class="page-header title-underline">
	{$aLang.plugin.simplecatalog.Products.Add.maps.title}
	{if $_aRequest.product_map_items}
		(<span>{$_aRequest.product_map_items.id|count}</span>)
	{/if}
</h2>

<div class="mb-20">
	{$aLang.plugin.simplecatalog.Products.Add.maps.help}
</div>

{*
	враппер для карты
*}
<div id="sc_product_add_map_wrapper"></div>
{*
	враппер для инпутов данных о метках карты
*}
<div id="sc_product_add_map_items_wrapper">
	{*
		добавить текущие метки продукта
	*}
	{if $_aRequest.product_map_items and count($_aRequest.product_map_items)}
		{foreach from=$_aRequest.product_map_items.id key=iKey item=aMapItem}
			<input type="hidden" name="product_map_items[id][]" value="{$_aRequest.product_map_items.id.$iKey}" />
			<input type="hidden" name="product_map_items[coord_lat][]" value="{$_aRequest.product_map_items.coord_lat.$iKey}" />
			<input type="hidden" name="product_map_items[coord_lng][]" value="{$_aRequest.product_map_items.coord_lng.$iKey}" />
			<input type="hidden" name="product_map_items[name][]" value="{$_aRequest.product_map_items.name.$iKey}" />
			<input type="hidden" name="product_map_items[hint][]" value="{$_aRequest.product_map_items.hint.$iKey}" />
			<input type="hidden" name="product_map_items[content][]" value="{$_aRequest.product_map_items.content.$iKey}" />
			<input type="hidden" name="product_map_items[preset][]" value="{$_aRequest.product_map_items.preset.$iKey}" />
		{/foreach}
	{/if}
</div>

<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
	jQuery (document).ready (function ($) {
		{*
			для сообщения о превышении лимита точек на карте
		*}
		ls.lang.load({lang_load name="plugin.simplecatalog.Errors.map_items.limit_reached"});

		/**
		 * создать редактор карты
		 */
		ls.sc_map_editor_yandex.BuildMapWithControls('sc_product_add_map_wrapper', {
			aCenter: [{Config::Get('plugin.simplecatalog.maps.settings.base.center')}],
			iZoom: {Config::Get('plugin.simplecatalog.maps.settings.base.zoom')},
			/**
			 * можно ли выбирать пресет для метки
			 */
			{if $oScheme->getSelectPresetForMapItemsEnabled()}
				bAllowPresetSelect: true,
			{/if}
			/**
			 * обновление полей с данными меток
			 */
			mOnUpdateMethod: function() {
				ls.sc_map_helpers_yandex.ItemsChangeHandler();
			},
			mOnAfterDeleteMethod: function() {
				ls.sc_map_helpers_yandex.ItemsChangeHandler();
			},
			mOnDragEndMethod: function() {
				ls.sc_map_helpers_yandex.ItemsChangeHandler();
			},
			/**
			 * максимальное количество разрешенных точек на карте
			 */
			iMaxItemsCount: {$oScheme->getMapItemsMax()},
			/**
			 * для вывода сообщения о превышении лимита точек
			 */
			mOnItemsLimitExceedMethod: function() {
				ls.sc_map_helpers_yandex.ItemsLimitExceedHandler();
			}
		}, {
			/**
			 * если можно выбирать пресет для метки - увеличить высоту балуна
			 */
			{if $oScheme->getSelectPresetForMapItemsEnabled()}
				min_height: 450,
			{else}
				min_height: 200,
			{/if}
			min_width: 420
		});

		/**
		 * горизонтальная прокрутка списка пресетов (для удобства)
		 */
		{if $oScheme->getSelectPresetForMapItemsEnabled()}
			$(document).on('mousewheel', '#' + ls.sc_map_editor_yandex.aFormSettings.image_preset_wrapper_id, function(event) {
				if(event.deltaY == 1) {
					this.scrollLeft -= 70;
				}
				else {
					this.scrollLeft += 70;
				}
				return false;
			});
		{/if}

		{if $_aRequest.product_map_items and count($_aRequest.product_map_items)}
			/**
			 * загрузить точки на карту (после создания самой карты)
			 */
			ls.sc_map_helpers_yandex.LoadItemsIntoMapFromInputsWrapper();
			/**
			 * установить масштаб чтобы были видны все метки на карте
			 */
			ls.sc_map_editor_yandex.SetBoundsByGeoObjects();
		{/if}
		/**
		 * добавить метку "Я" с координатами пользователя
		 */
		ls.sc_map_editor_yandex.SetPointToUsersCoords();
	});
</script>
