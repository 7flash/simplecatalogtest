/**
 * Simplecatalog plugin
 *
 * @copyright Serge Pustovit (PSNet), 2008 - 2015
 * @author    Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 * @link      http://psnet.lookformp3.net
 * @link      http://livestreet.ru/profile/PSNet/
 * @link      https://catalog.livestreetcms.com/profile/PSNet/
 * @link      http://livestreetguide.com/developer/PSNet/
 */

/**
 *
 * Редактор меток на карте (карты Яндекса)
 *
 * Этот редактор был написан для плагина Simplecatalog и является его частью и не может быть использован в сторонних продуктах.
 *
 */

var ls = ls || {};

ls.sc_map_editor_yandex = (function ($) {

	/**
	 * Объект карты
	 */
	this.oMap = null;
	/**
	 * Текущие координаты
	 */
	this.aCoordsCurrent = null;
	/**
	 * Текущая точка на карте
	 */
	this.oPlacemarkCurrent = null;
	/**
	 * Кластеризатор меток
	 */
	this.oClusterer = null;
	/**
	 * Количество внутренних меток на карте (таких как метка "Я"), которые не нужно учитывать при подсчете количества добавленных меток
	 */
	this.iSystemGeoObjectsOnMap = 0;
	/**
	 * Настройки для карты
	 */
	this.aOptions = {
		/**
		 * центр карты
		 * docs: http://api.yandex.ru/maps/tools/getlonglat/
		 */
		aCenter: [50.450323, 30.525918],
		/**
		 * зум
		 */
		iZoom: 16,
		/**
		 * тип карты
		 */
		sType: 'yandex#hybrid',

		/**
		 * заголовки формы
		 */
		titles: {
			new:  'Новая метка',
			edit: 'Редактирование метки'
		},

		/**
		 * подписи к инпутам
		 */
		labels: {
			name: 'Название',
			hint: 'Подсказка',
			content: 'Содержимое',
			preset: 'Вид метки'
		},

		/**
		 * подписи к кнопкам
		 */
		buttons: {
			save: 'Сохранить',
			delete: 'Удалить'
		},

		/**
		 * метод, который будет вызван при добавлении/обновлении метки на карту, в качестве параметра будет передана метка
		 */
		mOnUpdateMethod: null,
		/**
		 * метод, который будет вызван перед удалением метки из карты, в качестве параметра будет передана удаляемая метка, должен вернуть булево true если можно удалить метку
		 */
		mOnBeforeDeleteMethod: null,
		/**
		 * метод, который будет вызван после удаления метки из карты, в качестве параметра будет передана удаляемая метка
		 */
		mOnAfterDeleteMethod: null,
		/**
		 * метод, который будет вызван после перемещения метки по карте, в качестве параметра будет передана перемещаемая метка
		 */
		mOnDragEndMethod: null,
		/**
		 * метод, который будет вызван перед добавлением метки на карту, должен вернуть булево true если можно добавить метку
		 */
		mOnBeforeAddMethod: null,
		/**
		 * метод, который будет вызван при превышении лимита разрешенных точек на карте
		 */
		mOnItemsLimitExceedMethod: null,

		/**
		 * разрешить выбирать пресет для метки
		 */
		bAllowPresetSelect: false,

		/**
		 * максимальное количество точек на карте
		 */
		iMaxItemsCount: 15,

		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};

	/**
	 * Настройки для формы
	 */
	this.aFormSettings = {
		/**
		 * ид кнопки сохранения точки на карте
		 */
		save_button_id: 'sc_map_point_save_button',
		/**
		 * ид кнопки удаления точки на карте
		 */
		delete_button_id: 'sc_map_point_delete_button',

		/**
		 * ид полей формы
		 */
		field_name_id: 'sc_map_form_name',
		field_hint_id: 'sc_map_form_hint',
		field_content_id: 'sc_map_form_content',
		field_preset_id: 'sc_map_form_preset',

		/**
		 * классы полей формы
		 */
		field_name_class: 'input-text input-width-200',
		field_hint_class: 'input-text input-width-200',
		field_content_class: 'input-text input-width-400',

		/**
		 * классы кнопок формы
		 */
		button_save_class: 'button button-primary',
		button_delete_class: 'button',

		/**
		 * пресеты
		 */
		image_preset_data_name: 'data-preset',
		image_preset_class: 'js-sc-map-preset-image',
		image_preset_wrapper_id: 'sc_map_preset_list',
		image_preset_default: 'islands#blueStretchyIcon',

		/**
		 * размеры балуна
		 */
		min_width: 400,
		min_height: 450,

		/**
		 * ссылка для центрирования карты по координатам
		 */
		map_center_link_class: 'js-sc-map-center-coords',

		/**
		 * последний элемент без запятой
		 */
		last_element_wo_comma: true
	};


	/**
	 * Создание карты в указанном контейнере
	 *
	 * @param sContainerId			ид контейнера
	 * @constructor
	 */
	this.CreateMap = function (sContainerId) {
		this.oMap = new ymaps.Map(sContainerId, {
			center: this.aOptions.aCenter,
			zoom: this.aOptions.iZoom,
			type: this.aOptions.sType
			/**
			 * по умолчанию карта создается со стандартными элементами управления (с версии 2.1)
			 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/control.Manager.xml#add
			 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/update/concepts/update.xml#add-controls
			 */
			//controls: []
		}, {
			/**
			 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Map.xml#param-options.autoFitToViewport
			 */
			//autoFitToViewport: 'always'
		});
	};


	/**
	 * Слушатели событий для карты
	 *
	 * @constructor
	 */
	this.AddMapEvents = function() {
		var oThis = this;
		/**
		 * Установить слушатель открытия окна для добавления новой метки на карту
		 */
		this.oMap.events.add('click', function (e) {
			/**
			 * если не открыта форма балуна
			 */
			if (!oThis.oMap.balloon.isOpen()) {
				/**
				 * сохранить координаты текущей метки
				 */
				oThis.aCoordsCurrent = e.get('coords');
				/**
				 * открыть форму
				 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Balloon.xml
				 */
				oThis.oMap.balloon.open(oThis.aCoordsCurrent, {
					contentHeader: oThis.aOptions.titles.new,
					contentBody: oThis.GetEditForm({sPreset: oThis.aFormSettings.image_preset_default})
				}, oThis.GetBalloonDefaultDataOptions());
				/**
				 * добавить информацию о координатах
				 */
				oThis.AddGeoInfoForNewPlacemark(oThis.aCoordsCurrent);
			} else {
				oThis.oMap.balloon.close();
			}
		});
		/**
		 * установить слушатель закрытия балуна чтобы очищать статус текущей метки
		 */
		this.oMap.balloon.events.add('close', function(e) {
			oThis.oPlacemarkCurrent = null;
		});
	};


	/**
	 * Получить настройки для балуна по-умолчанию
	 *
	 * @constructor
	 */
	this.GetBalloonDefaultDataOptions = function() {
		return {
			minWidth: this.aFormSettings.min_width,
			minHeight: this.aFormSettings.min_height,
			autoPanDuration: 100
		};
	};


	/**
	 * Слушатели событий кнопок на форме редактирования метки
	 *
	 * @constructor
	 */
	this.AddFormEvents = function() {
		var oThis = this;
		/**
		 * кнопка сохранения данных метки
		 */
		$ (document).on('click', '#' + this.aFormSettings.save_button_id, function() {
			/**
			 * вызвать событие до добавления метки
			 */
			if (!oThis.TriggerOnBeforeAddListener()) {
				return false;
			}
			/**
			 * если это не редактирование
			 */
			if (!oThis.oPlacemarkCurrent) {
				/**
				 * проверить максимально разрешенное количество точек
				 */
				if (oThis.GetGeoObjects().getLength() - oThis.iSystemGeoObjectsOnMap >= oThis.aOptions.iMaxItemsCount) {
					oThis.TriggerOnItemsLimitExceedListener();
					return false;
				}
				/**
				 * создание новой метки с текущими координатами
				 */
				oThis.oPlacemarkCurrent = new ymaps.Placemark(oThis.aCoordsCurrent);
				oThis.oPlacemarkCurrent.options.set({
					draggable: true,
					cursor: 'move'
				});
				/**
				 * добавление метки на карту
				 */
				oThis.oMap.geoObjects.add(oThis.oPlacemarkCurrent);
				/**
				 * установить для новой метки слушатель редактирования
				 */
				oThis.AssignListenerForEditPlacemark(oThis.oPlacemarkCurrent);
			}
			/**
			 * обновление текстовок
			 */
			oThis.oPlacemarkCurrent.properties.set({
				iconContent: $('#' + oThis.aFormSettings.field_name_id).val(),
				hintContent: $('#' + oThis.aFormSettings.field_hint_id).val(),
				balloonContent: $('#' + oThis.aFormSettings.field_content_id).val()
			});
			oThis.oPlacemarkCurrent.options.set({
				preset: $('#' + oThis.aFormSettings.field_preset_id).val()
			});

			/**
			 * вызвать событие обновления
			 */
			oThis.TriggerOnUpdateListener(oThis.oPlacemarkCurrent);

			/**
			 * закрыть форму
			 */
			oThis.oMap.balloon.close();
		});

		/**
		 * кнопка удаления метки
		 */
		$ (document).on('click', '#' + this.aFormSettings.delete_button_id, function() {
			/**
			 * вызвать событие до удаления
			 */
			if (oThis.TriggerOnBeforeDeleteListener(oThis.oPlacemarkCurrent)) {
				oThis.oMap.geoObjects.remove(oThis.oPlacemarkCurrent);
				/**
				 * вызвать событие после удаления
				 */
				oThis.TriggerOnAfterDeleteListener(oThis.oPlacemarkCurrent);
				oThis.oMap.balloon.close();
			}
		});

		/**
		 * кнопка выбора пресета метки
		 */
		$ (document).on('click', '#' + this.aFormSettings.image_preset_wrapper_id + ' .' + this.aFormSettings.image_preset_class, function() {
			$('#' + oThis.aFormSettings.field_preset_id).val($ (this).attr(oThis.aFormSettings.image_preset_data_name));
			/**
			 * позначить выбранное изображение пресета
			 */
			$('#' + oThis.aFormSettings.image_preset_wrapper_id + ' .' + oThis.aFormSettings.image_preset_class).removeClass('active');
			$(this).addClass('active');
		});
	};


	/**
	 * Установка слушателя контекстного меню для редактирования метки
	 *
	 * @param oPlacemarkLocal			метка, которой будет назначен слушатель
	 * @constructor
	 */
	this.AssignListenerForEditPlacemark = function (oPlacemarkLocal) {
		var oThis = this;
		/**
		 * редактирование метки через контекстное меню (правая кнопка мыши)
		 */
		oPlacemarkLocal.events.add('contextmenu', function (e) {
			if (!oThis.oMap.balloon.isOpen()) {
				var aCoords = e.get('coords');
				oThis.oMap.balloon.open(aCoords, {
					contentHeader: oThis.aOptions.titles.edit,
					contentBody: oThis.GetEditForm({
						sName: oPlacemarkLocal.properties.get('iconContent'),
						sHint: oPlacemarkLocal.properties.get('hintContent'),
						sContent: oPlacemarkLocal.properties.get('balloonContent'),
						sPreset: oPlacemarkLocal.options.get('preset'),
						bAddRemoveButton: true
					})
				}, oThis.GetBalloonDefaultDataOptions());
				oThis.oPlacemarkCurrent = oPlacemarkLocal;

			} else {
				oThis.oMap.balloon.close();
			}
		});
		/**
		 * перемещение метки
		 */
		oPlacemarkLocal.events.add('dragend', function (e) {
			/**
			 * вызвать событие после перемещения метки
			 */
			oThis.TriggerOnDragEndListener(this);
		});
	};


	/**
	 * Получить все геообъекты на карте
	 *
	 * @constructor
	 */
	this.GetGeoObjects = function() {
		return this.oMap.geoObjects;
	};


	/**
	 * Установить новые опции и параметры
	 *
	 * @param aOptions			дополнительные настройки карты и формы
	 * @param aFormSettings		дополнительные настройки полей и кнопок формы
	 * @constructor
	 */
	this.ExtendOptionsAndSettings = function(aOptions, aFormSettings) {
		/**
		 * настроить массив основных параметров
		 */
		this.aOptions = $.extend({}, this.aOptions, aOptions || {});

		/**
		 * настроить массив параметров формы
		 */
		this.aFormSettings = $.extend({}, this.aFormSettings, aFormSettings || {});
	};


	/**
	 *
	 * --- Добавление меток на карту ---
	 *
	 */

	/**
	 * Добавить новую метку на карту по переданным параметрам с возможностью редактирования
	 *
	 * @param aParams		параметры
	 * @constructor
	 */
	this.AddNewPlaceholder = function(aParams) {
		var oThis = this;
		ymaps.ready(function () {
			/**
			 * создание новой метки с заданными координатами
			 */
			var oPlacemark = new ymaps.Placemark(aParams.coords);
			oPlacemark.id = aParams.id;
			oPlacemark.options.set({
				draggable: true,
				cursor: 'move',
				preset: aParams.preset
			});
			/**
			 * установка текстовок
			 */
			oPlacemark.properties.set({
				iconContent: aParams.title,
				hintContent: aParams.hint,
				balloonContent: aParams.content
			});
			/**
			 * добавление метки на карту
			 */
			oThis.oMap.geoObjects.add(oPlacemark);
			/**
			 * установить для новой метки слушатель редактирования
			 */
			oThis.AssignListenerForEditPlacemark(oPlacemark);
		});
	};


	/**
	 * Отобразить массив меток на карте по переданным параметрам используя кластеризацию (вывод меток в продукте)
	 *
	 * @param aGeoObjects		массив меток
	 * @constructor
	 */
	this.AddPlaceholdersOnMapUsingCluster = function(aGeoObjects) {
		var oThis = this;
		ymaps.ready(function () {
			oThis.oClusterer = new ymaps.Clusterer({
				/**
				 * tip: чтобы были названия в списке - нужно указывать для геообъектов поля clusterCaption или balloonContentHeader
				 */
				//clusterDisableClickZoom: true
				//hasBalloon: false,
				hasHint: false,
				clusterHideIconOnBalloonOpen: false
				/**
				 * метка кластера
				 * http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Clusterer.xml#param-options.preset
				 */
			});
			/**
			 * по всем меткам
			 */
			$.each(aGeoObjects, function(i, aGeoObjectData) {
				/**
				 * создание новой метки с заданными координатами
				 */
				var oPlacemark = new ymaps.Placemark(aGeoObjectData.coords);
				oPlacemark.id = aGeoObjectData.id;
				oPlacemark.options.set({
					preset: aGeoObjectData.preset
				});
				/**
				 * установка текстовок
				 */
				oPlacemark.properties.set({
					iconContent: aGeoObjectData.title,
					hintContent: aGeoObjectData.hint,
					balloonContent: aGeoObjectData.content,
					clusterCaption: aGeoObjectData.title
				});

				/**
				 * добавить метку в кластер
				 */
				oThis.oClusterer.add(oPlacemark);
			});
			/**
			 * отобразить кластер на карте
			 */
			oThis.oMap.geoObjects.add(oThis.oClusterer);
		});
	};


	/**
	 * Создать загружающий менеджер объектов для получения точек с сервера по координатам видимой области карты
	 *
	 * @constructor
	 */
	this.AddLoadingObjectManagerOnMap = function(aUrlParams) {
		var oThis = this;
		ymaps.ready(function () {
			/**
			 * tip:
			 * %b заменяется на массив географических координат, описывающих прямоугольную область, для которой требуется загрузить данные.
			 * %t заменяется на массив номеров тайлов, описывающих прямоугольную область, для которой требуется загрузить данные.
			 */
			var sUrl = aRouter['product'] + 'ajax-map-items-loader/?coords=%b&' + $.param(aUrlParams || {});
			/**
			 * Создать загружающий менеджер объектов
			 * tip: LoadingObjectManager кеширует результаты, RemoteObjectManager - нет, но требует серверной кластеризации
			 * docs: https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/loading-object-manager/frontend-docpage/
			 */
			var oLoadingObjectManager = new ymaps.LoadingObjectManager(
				sUrl,
				{
					clusterize: true,
					/**
					 * чтобы очень близко расположенные точки могли открыться в балуне кластера (когда масштаб максимальный)
					 */
					//clusterHasBalloon: false,
					clusterhasHint: false,
					clusterHideIconOnBalloonOpen: false,
					/**
					 * в вьюере лс жестко прописано получать имя колбека из "jsonpCallback", Яндекс Карты генерят имя колбека из "paddingParamName"
					 */
					paddingTemplate: 'sc_products_map_callback_%b',
					paddingParamName: 'jsonpCallback'
				}
			);
			/**
			 * добавить менеджер объектов к карте
			 */
			oThis.oMap.geoObjects.add(oLoadingObjectManager);
		});
	};


	/**
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Установить масштаб чтобы были видны одновременно все метки на карте
	 *
	 * @constructor
	 */
	this.SetBoundsByGeoObjects = function() {
		var oThis = this;
		ymaps.ready(function () {
			/**
			 * docs: http://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Map.xml#setBounds
			 */
			oThis.oMap.setBounds(oThis.oMap.geoObjects.getBounds(), {
				/**
				 * с проверкой есть ли фото для нового масштаба
				 */
				checkZoomRange: true,
				/**
				 * отступ от края карты для отображаемых меток
				 */
				zoomMargin: 20
			});
		});
	};


	/**
	 * Установить метку "Я" с координатами пользователя
	 *
	 * @constructor
	 */
	this.SetPointToUsersCoords = function() {
		var oThis = this;
		ymaps.ready(function () {
			ymaps.geolocation.get({
				/**
				 * определение координат по айпи адресу
				 */
				provider: 'yandex',
				/**
				 * не центровать карту по положению пользователя
				 */
				mapStateAutoApply: false
			}).then(function (result) {
				/**
				 * добавить на карту метку "Я"
				 * tip: поле geoObjects - коллекция, содержащая геобъект с координатами пользователя, будет доступна в списке всех геообъектов карты
				 */
				oThis.oMap.geoObjects.add(result.geoObjects);
				oThis.iSystemGeoObjectsOnMap++;
			});
		});
	};


	/**
	 * Добавить информацию о координатах в окно новой метки (обратное геокодирование)
	 *
	 * @param aCoords		координаты
	 * @constructor
	 */
	this.AddGeoInfoForNewPlacemark = function(aCoords) {
		var oThis = this;
		ymaps.geocode(aCoords).then(function (oData) {
			var oFirstGeoObject = oData.geoObjects.get(0);

			$ ('#' + oThis.aFormSettings.field_name_id).val(oFirstGeoObject.properties.get('name'));
			$ ('#' + oThis.aFormSettings.field_content_id).text(oFirstGeoObject.properties.get('text'));
		});
	};


	/**
	 * Отцентрировать карту по координатам ссылки при клике
	 *
	 * @constructor
	 */
	this.AssignListenerForCenterMapLinks = function() {
		var oThis = this;
		$ (document).on('click.sc', '.' + this.aFormSettings.map_center_link_class, function() {
			var sLat = $ (this).data('map-coords-lat'),
				sLng = $ (this).data('map-coords-lng');
			/**
			 * docs: https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Map-docpage/#setCenter
			 */
			oThis.oMap.setCenter([sLat, sLng], oThis.aOptions.iZoom, {
				checkZoomRange: true,
				duration: 200
			});
			return false;
		});
	};


	/**
	 *
	 * --- Подписчики на события ---
	 *
	 */

	/**
	 * Вызвать подписчика на событие обновления/добавления метки на карту
	 *
	 * @param oPlacemark		обновляемая/добавляемая метка
	 * @constructor
	 */
	this.TriggerOnUpdateListener = function(oPlacemark) {
		if (typeof (this.aOptions.mOnUpdateMethod) === 'function') {
			this.aOptions.mOnUpdateMethod(oPlacemark);
		}
	};


	/**
	 * Вызвать подписчика на событие ПЕРЕД удалением метки из карты
	 *
	 * @param oPlacemark		удаляемая метка
	 * @return bool				результат вызова метода (можно ли удалять метку)
	 * @constructor
	 */
	this.TriggerOnBeforeDeleteListener = function(oPlacemark) {
		if (typeof (this.aOptions.mOnBeforeDeleteMethod) === 'function') {
			return this.aOptions.mOnBeforeDeleteMethod(oPlacemark);
		}
		return true;
	};


	/**
	 * Вызвать подписчика на событие ПОСЛЕ удаления метки из карты
	 *
	 * @param oPlacemark		удаляемая метка
	 * @constructor
	 */
	this.TriggerOnAfterDeleteListener = function(oPlacemark) {
		if (typeof (this.aOptions.mOnAfterDeleteMethod) === 'function') {
			this.aOptions.mOnAfterDeleteMethod(oPlacemark);
		}
	};


	/**
	 * Вызвать подписчика на событие окончания перемещения метки на карте
	 *
	 * @param oPlacemark		обновляемая/добавляемая метка
	 * @constructor
	 */
	this.TriggerOnDragEndListener = function(oPlacemark) {
		if (typeof (this.aOptions.mOnDragEndMethod) === 'function') {
			this.aOptions.mOnDragEndMethod(oPlacemark);
		}
	};


	/**
	 * Вызвать подписчика на событие ПЕРЕД добавлением метки на карту
	 *
	 * @return bool				результат вызова метода (можно ли добавлять метку)
	 * @constructor
	 */
	this.TriggerOnBeforeAddListener = function() {
		if (typeof (this.aOptions.mOnBeforeAddMethod) === 'function') {
			return this.aOptions.mOnBeforeAddMethod();
		}
		return true;
	};


	/**
	 * Вызвать подписчика на событие превышения количества разрешенных точек
	 *
	 * @constructor
	 */
	this.TriggerOnItemsLimitExceedListener = function() {
		if (typeof (this.aOptions.mOnItemsLimitExceedMethod) === 'function') {
			this.aOptions.mOnItemsLimitExceedMethod();
		}
	};


	/**
	 *
	 * --- Шаблон формы ---
	 *
	 */

	/**
	 * Получить форму редактирования метки
	 *
	 * @param mSettings							данные формы
	 * @returns {string}						хтмл строка
	 * @constructor
	 */
	this.GetEditForm = function(mSettings) {
		var aSettings = mSettings || {};
		/**
		 * значение названия
		 */
		var sName = aSettings.sName || '',
			/**
			 * подсказка
			 */
			sHint = aSettings.sHint || '',
			/**
			 * содержимое
			 */
			sContent = aSettings.sContent || '',
			/**
			 * пресет
			 */
			sPreset = aSettings.sPreset || '',
			/**
			 * добавить ли кнопку удаления метки
			 */
			bAddRemoveButton = aSettings.bAddRemoveButton || null;

		return '<div class="fl-l mr-10">\
					<div>' + this.aOptions.labels.name + ':</div>\
					<input type="text" id="' + this.aFormSettings.field_name_id + '" value="' + sName + '" class="' + this.aFormSettings.field_name_class + '" />\
				</div>\
				<div class="oh">\
					<div>' + this.aOptions.labels.hint + ':</div>\
					<input type="text" id="' + this.aFormSettings.field_hint_id + '" value="' + sHint + '" class="' + this.aFormSettings.field_hint_class + '" />\
				</div>\
				<div class="cb">\
					<div>' + this.aOptions.labels.content + ':</div>\
					<textarea id="' + this.aFormSettings.field_content_id + '" class="' + this.aFormSettings.field_content_class + '">' + sContent + '</textarea>\
				</div>\
				' + (this.aOptions.bAllowPresetSelect ? '\
				<div class="mb-10">\
					<div>' + this.aOptions.labels.preset + ':</div>\
					<div id="' + this.aFormSettings.image_preset_wrapper_id + '">' +
						this.GetPresetImagesTable(
							this.aFormSettings.image_preset_class,
							this.aFormSettings.image_preset_data_name,
							sPreset
						) + '\
						<input type="hidden" id="' + this.aFormSettings.field_preset_id + '" value="' + sPreset + '" />\
					</div>\
				</div>\
				' : '<div class="mb-10"></div>') + '\
				<div>\
					<button type="submit" id="' + this.aFormSettings.save_button_id + '" class="' + this.aFormSettings.button_save_class + '">' + this.aOptions.buttons.save + '</button>\
					' + (bAddRemoveButton ? '\
					<button type="submit" id="' + this.aFormSettings.delete_button_id + '" class="' + this.aFormSettings.button_delete_class + '">' + this.aOptions.buttons.delete + '</button>\
					' : '') + '\
        		</div>';
	};


	/**
	 * Получить список всех возможных пресетов в таблице
	 *
	 * @param sClass			класс для изображений
	 * @param sDataAttr			имя дата атрибута для установки значения пресета
	 * @param sPresetCurrent	текущее значение пресета
	 * @return {string}			хтмл строка таблицы
	 * @constructor
	 */
	this.GetPresetImagesTable = function(sClass, sDataAttr, sPresetCurrent) {
		var sText = '<table style="text-align: center; border: none;"><tbody>';
		ls.sc_map_yandex_preset_storage.aPresets.forEach(function(arr, iarr) {
			sText += '<tr>';
			arr.forEach(function(el, i) {
				sText += '<td><img src="' + el.image + '" class="' + sClass + (sPresetCurrent == el.value ? ' active' : '') + '" ' + sDataAttr + '="' + el.value + '" /></td>';
			});
			sText += '</tr>';
		});
		sText += '</tbody></table>';
		return sText;
	};


	/**
	 *
	 * --- Конечный метод ---
	 *
	 */

	/**
	 * Построить карту в указанном контейнере со всеми активными элементами управления
	 *
	 * @param sId				ид контейнера, где нужно создать карту
	 * @param aOptions			дополнительные настройки карты и формы
	 * @param aFormSettings		дополнительные настройки полей и кнопок формы
	 * @constructor
	 */
	this.BuildMapWithControls = function(sId, aOptions, aFormSettings) {
		var oThis = this;

		/**
		 * выполнить настройку
		 */
		this.ExtendOptionsAndSettings(aOptions, aFormSettings);

		/**
		 * слушатель готовности АПИ карт яндекса
		 */
		ymaps.ready(function () {
			oThis.CreateMap(sId);
			/**
			 * если нужно отключить все события (только просмотр карты)
			 */
			if (!aOptions.bDisableEvents) {
				oThis.AddMapEvents();
				oThis.AddFormEvents();
			}
		});
	};

	// ---

	return this;

}).call (ls.sc_map_editor_yandex || {}, jQuery);
