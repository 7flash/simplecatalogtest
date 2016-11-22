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

class PluginSimplecatalog_ModuleSearch extends Module {
	
	protected $oMapper = null;


	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}


	/**
	 * Выполнить поиск
	 *
	 * @param       $oScheme            объект схемы
	 * @param       $sText              текст запроса
	 * @param int   $iCurrentPage       страница
	 * @param int   $iPerPage           количество на страницу
	 * @param bool  $bFullSearch        полный ли поиск или алфавитный (по первой букве)
	 * @param array $aSortOrder         орм сортировка
	 * @return array
	 */
	public function MyGetProductsBySchemeAndSearchQuery($oScheme, $sText, $iCurrentPage = 1, $iPerPage = 100, $bFullSearch = true, $aSortOrder = array()) {
		/*
		 * провести проверку текста на корректность
		 */
		if (($sMsg = $this->ValidateText($sText, $bFullSearch)) !== true) {
			return array(
				'collection' => null,
				'count' => 0,
				'q' => '',
				'is_error' => true,
				'msg' => $sMsg
			);
		}
		
		if ($bFullSearch) {
			/*
			 * обычный неточный поиск
			 */
			$sQuerySql = '%' . $sText . '%';
		} else {
			/*
			 * алфавитный поиск (по первой букве)
			 */
			$sQuerySql = $sText . '%';
		}
		
		$aProductsData = $this->MyGetProductsByModerationAndSchemeIdAndContentLike(
			PluginSimplecatalog_ModuleProduct::MODERATION_DONE,
			$oScheme->getId(),
			$sQuerySql,
			$iCurrentPage,
			$iPerPage,
			$aSortOrder
		);
		
		return array(
			'collection' => $aProductsData['collection'],
			'count' => $aProductsData['count'],
			'q' => $sText,
			'is_error' => false,
			'msg' => null
		);
	}


	/**
	 * Проверить текст запроса на корректность
	 *
	 * @param string $sText       текст
	 * @param bool   $bFullSearch полный поиск или алфавитный
	 * @return mixed
	 */
	private function ValidateText(&$sText, $bFullSearch) {
		/*
		 * проверить длину
		 */
		if (
			(
				/*
				 * для полного поиска
				 */
				$bFullSearch and !$this->Validate_Validate('string', $sText, array(
					'min' => Config::Get('plugin.simplecatalog.search.products.query_length.min'),
					'max' => Config::Get('plugin.simplecatalog.search.products.query_length.max'),
					'allowEmpty' => false
				))
			) or (
				/*
				 * для алфавитного
				 */
				!$bFullSearch and !$this->Validate_Validate('string', $sText, array('min' => 1, 'max' => 1, 'allowEmpty' => false))
			)
		) {
			return $this->Validate_GetErrorLast();
		}

		/*
		 * если кодировка была повреждена - восстановить её
		 */
		if (!mb_check_encoding($sText, 'UTF-8')) {
			$sText = mb_convert_encoding($sText, 'UTF-8', 'auto');
		}

		/*
		 * убрать ненужные символы
		 */
		$sText = preg_replace(Config::Get('plugin.simplecatalog.search.products.validate_regexp'), ' ', $sText);
		$sText = preg_replace('#\s++#', ' ', $sText);
		$sText = trim($sText);
		if ($sText === '') {
			return $this->Lang_Get('plugin.simplecatalog.Errors.Wrong_Search_Query');
		}

		return true;
	}


	/**
	 * Найти продукты по проверенному запросу
	 *
	 * @param       $iModeration         тип модерации
	 * @param       $iSchemeId           ид схемы
	 * @param       $sQuery              запрос
	 * @param       $iCurrentPage        страница
	 * @param       $iPerPage            количество на страницу
	 * @param array $aSortOrder          орм сортировка
	 * @return mixed            продукты
	 */
	private function MyGetProductsByModerationAndSchemeIdAndContentLike($iModeration, $iSchemeId, $sQuery, $iCurrentPage, $iPerPage, $aSortOrder) {
		$sCacheKey = 'simplecatalog_search_get_products_' . serialize(func_get_args());
		if (($mData = $this->Cache_Get($sCacheKey)) === false) {
			$mData = $this->oMapper->GetProductsByModerationAndSchemeIdAndContentLike($iModeration, $iSchemeId, $sQuery, $iCurrentPage, $iPerPage, $aSortOrder);
			/*
			 * теги кеша (продукты и их поля)
			 */
			$aTags = array(
				/*
				 * имена тегов кеша орм для продуктов
				 */
				'PluginSimplecatalog_ModuleProduct_EntityProduct_save',
				'PluginSimplecatalog_ModuleProduct_EntityProduct_delete',
				/*
				 * имена тегов кеша орм для полей продуктов
				 */
				'PluginSimplecatalog_ModuleProduct_EntityFields_save',
				'PluginSimplecatalog_ModuleProduct_EntityFields_delete',
			);
			$this->Cache_Set($mData, $sCacheKey, $aTags, 60 * 60 * 24 * 7);	// 7 days
		}
		return $mData;
	}
	
}
