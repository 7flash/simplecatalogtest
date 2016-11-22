<?php
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

/*
 *
 * Модуль, расширяющий возможности встроенного ORM в лс.
 * Модуль умеет дополнительно кешировать сущности на момент сессии при получении связанных данных
 *
 */

/*
 * Пример
 * tip: там, где обозначено "можно не указывать" - это означает что данные основной таблицы (А) и конечная сущность будут получены из контекста модуля,
 * 		в котором вызывается метод модуля Myorm (последний параметр - $this),
 * 		он укажет на имя таблицы (TABLE_A), оттуда будет получен примари ключ (ON_A_KEY) и конечная сущность для набора данных будет построена из имени модуля (ENTITY)
 */

/*
return $this->PluginSimplecatalog_Myorm_GetItemsByJoin(array(
	// данные для кастомного орм модуля
	PluginSimplecatalog_ModuleMyorm::TABLE_A => Config::Get('db.table.simplecatalog_product'),				// можно не указывать
	PluginSimplecatalog_ModuleMyorm::TABLE_B => Config::Get('db.table.simplecatalog_product_categories'),
	PluginSimplecatalog_ModuleMyorm::ON_A_KEY => 'id',														// можно не указывать
	PluginSimplecatalog_ModuleMyorm::ON_B_KEY => 'product_id',
	PluginSimplecatalog_ModuleMyorm::ENTITY => 'PluginSimplecatalog_Product',								// можно не указывать

	// условия орм лс
	'#where' => array(
		'a.`scheme_id` = ?d AND a.`moderation` IN (?a) AND a.`id` <> ?d AND b.`category_id` IN (?a)' => array(
			$oScheme->getId(),
			array(self::MODERATION_DONE),
			$oProduct->getId(),
			$oProduct->getCategoriesIds()
		)
	),
	'#limit' => array(0, 5),
	'#order' => array('add_date' => 'desc'),
), array($this));
*/

class PluginSimplecatalog_ModuleMyorm extends ModuleORM {

	private $oMapper = null;

	/*
	 *
	 * --- Ключи фильтра для запроса ---
	 *
	 */
	/*
	 * Основная таблица (её даные будут получены), устанавливается автоматически из вызываемого модуля
	 */
	const TABLE_A = '#table_a';
	/*
	 * Связанная таблица
	 */
	const TABLE_B = '#table_b';
	/*
	 * Ключ связи основной таблицы, устанавливается автоматически как примари ключ
	 */
	const ON_A_KEY = '#on_a_key';
	/*
	 * Ссылающийся на основную таблицу ключ связанной таблицы
	 */
	const ON_B_KEY = '#on_b_key';
	/*
	 * Сущность основной таблицы, устанавливается автоматически из вызываемого модуля как одноименная сущность
	 */
	const ENTITY = '#entity';
	/*
	 * Что нужно выбирать в запросе, если указано, то будет выбран только один элемент
	 */
	const SELECT = '#select';


	public function Init() {
		/*
		 * orm требует этого
		 */
		parent::Init();
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}


	/**
	 * Получить сущности по связанной таблице по фильтру
	 *
	 * @param       $aFilter		фильтр
	 * @param array $aClass			контекст модуля и имени возвращаемой сущности модуля:
	 * 								array($this, 'Product') - первый ключ всегда "$this", второй - название сущности, если не указано - совпадает с именем модуля
	 * @return mixed
	 */
	public function GetItemsByJoin($aFilter, $aClass = array()) {
		/*
		 * получить полное имя сущности
		 */
		$sEntityFull = $this->GetEntityFullName($aClass);
		/*
		 * установить сущность основной таблицы
		 */
		$aFilter[self::ENTITY] = $sEntityFull;
		/*
		 * получить экземпляр сущности чтобы из неё получать названия полей в таблице
		 */
		$oEntitySample = Engine::GetEntity($sEntityFull);
		/*
		 * получить имя основной таблицы и ключ связи (примари)
		 */
		$aFilter[self::TABLE_A] = MapperORM::GetTableName($sEntityFull);
		/*
		 * не устанавливать ключ если он задан (для получения данных, которые привязаны к другому объекту не примари ключом)
		 */
		if (!isset($aFilter[self::ON_A_KEY])) {
			$aFilter[self::ON_A_KEY] = $oEntitySample->_getPrimaryKey();
		}
		/*
		 * выполнить запрос
		 */
		$aData = $this->oMapper->GetItemsByJoin($aFilter, $oEntitySample);

		/*
		 * сущности
		 */
		$aEntities = isset($aData['collection']) ? $aData['collection'] : $aData;
		/*
		 * если необходимо подцепить связанные данные
		 */
		if (count($aEntities) and isset($aFilter['#with'])) {
			$this->AutoloadEntitiesRelations($aFilter['#with'], $aEntities, $sEntityFull, $oEntitySample);
		}

		return $aData;
	}


	/**
	 * Получить одну сущность по связанной таблице по фильтру
	 *
	 * @param       $aFilter		фильтр
	 * @param array $aClass			контекст модуля и имени возвращаемой сущности модуля:
	 * 								array($this, 'Product') - первый ключ всегда "$this", второй - название сущности, если не указано - совпадает с именем модуля
	 * @return mixed
	 */
	public function GetByJoin($aFilter, $aClass = array()) {
		/*
		 * задать выборку одного элемента
		 */
		$aFilter = array_merge($aFilter, array('#limit' => array(1)));
		/*
		 * выполнить обычный запрос
		 */
		$mData = $this->GetItemsByJoin($aFilter, $aClass);
		/*
		 * получить только одно значение
		 */
		if (is_array($mData) and count($mData) > 0) {
			return array_shift($mData);
		}
		return null;
	}


	/**
	 * Получить количество по связанной таблице по фильтру
	 *
	 * @param       $aFilter		фильтр
	 * @param array $aClass			контекст модуля и имени возвращаемой сущности модуля:
	 * 								array($this, 'Product') - первый ключ всегда "$this", второй - название сущности, если не указано - совпадает с именем модуля
	 * @return mixed
	 */
	public function CountByJoin($aFilter, $aClass = array()) {
		return $this->GetItemsByJoin(
			$aFilter + array(
				'#limit' => array(1),
				self::SELECT => 'COUNT(*)'
			),
			$aClass
		);
	}


	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Получить полное имя сущности
	 *
	 * @param array $aClass			контекст модуля и имени возвращаемой сущности модуля:
	 * 								array($this, 'Product') - первый ключ всегда "$this", второй - название сущности, если не указано - совпадает с именем модуля
	 * @throws Exception
	 * @return string
	 */
	protected function GetEntityFullName($aClass) {
		/*
		 * контекст вызываемого модуля должен быть указан
		 */
		if (empty($aClass) or count($aClass) > 2) {
			throw new Exception('SC: error: class context and/or entity name are not set or wrong in ' . __METHOD__);
		}
		/*
		 * если указано имя сущности
		 */
		if (count($aClass) == 2) {
			return Engine::GetPluginPrefix($aClass[0]) . 'Module' . Engine::GetModuleName($aClass[0]) . '_Entity' . $aClass[1];
		} else {
			/*
			 * имя сущности равно имени вызвавшего модуля
			 */
			return Engine::GetPluginPrefix($aClass[0]) . 'Module' . Engine::GetModuleName($aClass[0]) . '_Entity' . Engine::GetModuleName($aClass[0]);
		}
	}


	/**
	 * Загрузить связанные данные (в методе есть сессионное хранилище по имени сущности)
	 *
	 * @param $aWith			массив имен связанных данных
	 * @param $aEntities		сущности для которых будут получены связанные данные
	 * @param $sEntityFull		полное имя сущности
	 * @param $oEntitySample	сущность для получения имен полей
	 * @throws Exception
	 */
	protected function AutoloadEntitiesRelations($aWith, $aEntities, $sEntityFull, $oEntitySample) {
		$aWith = is_array($aWith) ? $aWith : array($aWith);
		/*
		 * получить связи сущности
		 */
		$aRelations = $oEntitySample->_getRelations();
		/*
		 * сохраненные ранее данные полученных связанных сущностей
		 * tip: могут быть разные названия связей, но запросы по одинаковым сущностям (например, сущность пользователя)
		 * это позволит сохранять данные по имени сущности на момент сессии
		 *
		 * Структура:
		 *
		 * array(
		 * 		'PluginSimplecatalog_ModuleScheme_EntityScheme' => array(
		 * 			'id1' => Object1,
		 * 			'id2' => Object2
		 * 		),
		 * 		'ModuleUser_EntityUser' => array(
		 * 			'id1' => Object1,
		 * 			'id2' => Object2
		 * 		),
		 * )
		 *
		 */
		$aGatheredRelationData = array();
		/*
		 * для каждой загружаемой связи
		 */
		foreach($aWith as $sRelationName) {
			/*
			 * массив ид связанных сущностей, которые нужно получить за один запрос
			 */
			$aRelationIds = array();
			/*
			 * тип связи
			 */
			$sRelType = $aRelations[$sRelationName][0];
			/*
			 * получить имя корневой сущности, без учета наследников
			 */
			$sRelEntity = $this->Plugin_GetRootDelegater('entity', $aRelations[$sRelationName][1]);
			/*
			 * получить поле таблицы связи
			 */
			$sRelKey = $aRelations[$sRelationName][2];

			/*
			 * автозагрузке подлежат только связи типа RELATION_TYPE_BELONGS_TO и RELATION_TYPE_HAS_ONE
			 */
			if (!array_key_exists($sRelationName, $aRelations) or !in_array($sRelType, array(EntityORM::RELATION_TYPE_BELONGS_TO, EntityORM::RELATION_TYPE_HAS_ONE))) {
				throw new Exception('SC: error: entity "' . $sEntityFull . '" doesnt have relation "' . $sRelationName . '" or this relation is not "belongs_to" or "has_one"');
			}

			/*
			 * получить список ид нужных связей
			 */
			foreach($aEntities as $oEntity) {
				$aRelationIds[] = $oEntity->_getDataOne($sRelKey);
			}
			$aRelationIds = array_unique($aRelationIds);

			/*
			 * общий запрос по всем ключам
			 */
			$oRelEntityEmpty = Engine::GetEntity($sRelEntity);
			$sRelModuleName = Engine::GetModuleName($sRelEntity);
			$sRelEntityName = Engine::GetEntityName($sRelEntity);
			$sRelPluginPrefix = Engine::GetPluginPrefix($sRelEntity);
			/*
			 * если сущность из орм - получить примари ключ, иначе считать что примари ключ - это "id"
			 */
			$sRelPrimaryKey = method_exists($oRelEntityEmpty, '_getPrimaryKey') ? func_camelize($oRelEntityEmpty->_getPrimaryKey()) : 'Id';

			/*
			 * есть ли уже какие-нибудь данные для этой сущности в сессионном хранилище-переменной
			 */
			if (isset($aGatheredRelationData[$sRelEntity])) {
				/*
				 * удалить из списка запрашиваемых ид те, которые уже получены
				 */
				$aRelationIds = array_diff($aRelationIds, array_keys($aGatheredRelationData[$sRelEntity]));
			}
			$aRelData = array();
			/*
			 * если после сравнения с уже существующими данными все ещё есть ид для получения данных
			 */
			if (!empty($aRelationIds)) {
				/*
				 * выполнить запрос на получение данных по массиву ид необходимых сущностей
				 */
				$aRelData = Engine::GetInstance()->_CallModule(
					$sRelPluginPrefix . $sRelModuleName . '_get' . $sRelEntityName . 'ItemsByArray' . $sRelPrimaryKey,
					array($aRelationIds)
				);
			}
			/*
			 * сохранить данные в сессионном кеше (объеденить с уже существующими)
			 */
			if (isset($aGatheredRelationData[$sRelEntity])) {
				/*
				 * tip: не array_merge, а именно добавление (+), чтобы не перезаписывались ключи
				 */
				$aGatheredRelationData[$sRelEntity] = $aGatheredRelationData[$sRelEntity] + $aRelData;
			} else {
				$aGatheredRelationData[$sRelEntity] = $aRelData;
			}
			/*
			 * установить значение полученных данных из только что дополненного сессионного хранилища
			 */
			$aRelData = $aGatheredRelationData[$sRelEntity];

			/*
			 * задать полученные связанные сущности
			 */
			foreach($aEntities as $oEntity) {
				/*
				 * есть ли такая связанная сушность по ид
				 */
				if (isset($aRelData[$oEntity->_getDataOne($sRelKey)])) {
					$oEntity->_setData(array($sRelationName => $aRelData[$oEntity->_getDataOne($sRelKey)]));
				}
			}
		}
	}


}

?>