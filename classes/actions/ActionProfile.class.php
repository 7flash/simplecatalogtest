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

class PluginSimplecatalog_ActionProfile extends PluginSimplecatalog_Inherits_ActionProfile {


	protected function RegisterEvent() {
		parent::RegisterEvent();
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		/*
		 * привязать событие к каждой схеме
		 * трюк: переменная ядра будет инициализирована классом экшена после запуска метода RegisterEvent ()
		 */
		$aSchemes = Engine::getInstance()->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems();
		foreach ($aSchemes as $oScheme) {
			/*
			 * если не нужно показывать созданные продукты этой схемы в "публикациях"
			 */
			if ($oScheme->getProfileShowCreatedProducts() != PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED) {
				continue;
			}

			$this->AddEventPreg(
				'#^.+$#',																	// login
				'#^created$#i',																// event match #0
				'#^' . $oScheme->getSchemeUrl() . '$#',
				'#^(page([1-9]\d{0,5}))?$#i',
				'EventCreatedProducts'
			);
		}
	}


	/**
	 * Созданные пользователем продукты
	 * 
	 * @return bool
	 */
	protected function EventCreatedProducts() {
		if (!$this->CheckUserProfile()) {
			return parent::EventNotFound();
		}
		$sSchemeUrl = $this->GetParamEventMatch(1, 0);
		$this->sMenuSubItemSelect = $sSchemeUrl;

		/*
		 * получить схему
		 */
		if (!$oScheme = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeBySchemeUrl($sSchemeUrl)) {
			$this->Message_AddError($this->Lang('Errors.Scheme_Not_Found'), $this->Lang_Get('error'), true);
			$this->RedirectToReferer();
			return false;
		}

		/*
		 * настройка постраничности
		 */
		$iPage = (int) ($this->GetParamEventMatch(2, 2) ? $this->GetParamEventMatch(2, 2) : 1);
		$iPerPage = $oScheme->getItemsPerPage();

		/*
		 * получить продукты
		 */
		$aProductsData = $this->PluginSimplecatalog_Product_MyGetProductItemsByModerationInAndUserAndSchemeAndPageAndPerPageAndSortOrder(
			array(
				PluginSimplecatalog_ModuleProduct::MODERATION_DONE
			),
			$this->oUserProfile,
			$oScheme,
			$iPage,
			$iPerPage,
			$this->PluginSimplecatalog_Product_GetDefaultProductSortingOrder()
		);

		/*
		 * пагинация
		 */
		$aPaging = $this->Viewer_MakePaging(
			$aProductsData ['count'],
			$iPage,
			$iPerPage,
			Config::Get('pagination.pages.count'),
			$this->oUserProfile->getUserWebPath() . 'created/' . $sSchemeUrl
		);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('oScheme', $oScheme);
		$this->Viewer_Assign('aProducts', $aProductsData ['collection']);
		$this->Viewer_Assign('iTotalProductCount', $aProductsData ['count']);

		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_publication') . ' ' . $this->oUserProfile->getLogin());

		$this->SetTemplateAction('created_products');
	}


	/**
	 * Увеличить общее число публикаций на количество опубликованных продуктов в каждой схеме
	 *
	 * @return bool
	 */
	public function EventShutdown() {
		parent::EventShutdown();
		if (!SCRootStorage::IsInit()) {
			return false;
		}
		if (!$this->oUserProfile) {
			return false;
		}

		/*
		 * текущий счетчик всех публикаций
		 */
		$iTotalPubCount = (int) $this->Viewer_GetSmartyObject()->getTemplateVars('iCountCreated');

		$aSchemes = $this->PluginSimplecatalog_Scheme_MyGetActiveSchemeItems();
		foreach ($aSchemes as $oScheme) {
			/*
			 * нужно ли показывать созданные продукты этой схемы в публикациях
			 */
			if ($oScheme->getProfileShowCreatedProducts() != PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED) {
				continue;
			}
			
			$iProducts = $this->PluginSimplecatalog_Product_MyGetCountProductItemsBySchemeIdAndUserIdAndModerationIn(
				$oScheme->getId(),
				$this->oUserProfile->getId(),
				array(
					PluginSimplecatalog_ModuleProduct::MODERATION_DONE
				)
			);
			$iTotalPubCount += $iProducts;
		}
		$this->Viewer_Assign('iCountCreated', $iTotalPubCount);
	}
	
}

?>