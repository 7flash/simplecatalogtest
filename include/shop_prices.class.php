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
 * Обработка цен продуктов
 *
 */

class ShopPrice {


	/*
	 *
	 * --- Вторая цена со скидкой ---
	 *
	 */

	/**
	 * Получить записываемое значение для второго поля цены из "сырого" значения
	 *
	 * @param $sPrice
	 * @return int
	 */
	static public function GetNewPriceCheckedFromRaw($sPrice) {
		if (self::GetNewPriceIsNormal($sPrice)) {
			/*
			 * это обычная цена
			 */
			return $sPrice;
		} elseif (self::GetNewPriceIsDiscount($sPrice)) {
			/*
			 * это скидка
			 */
			return $sPrice;
		} elseif (self::GetNewPriceIsMarkup($sPrice)) {
			/*
			 * это наценка
			 */
			return $sPrice;
		}
		return null;
	}


	/**
	 * Является ли указанное значение нормальной корректной ценой
	 *
	 * @param $sPrice		цена
	 * @return int
	 */
	static public function GetNewPriceIsNormal($sPrice) {
		return preg_match('#^\d{1,12}(?:\.\d{0,3})?$#', $sPrice);
	}


	/**
	 * Является ли указанное значение скидкой (-5%)
	 *
	 * @param $sPrice
	 * @return int
	 */
	static public function GetNewPriceIsDiscount($sPrice) {
		return preg_match('#^-\d{1,3}(?:\.\d{0,3})?%$#', $sPrice);
	}


	/**
	 * Является ли указанное значение наценкой (5%)
	 *
	 * @param $sPrice
	 * @return int
	 */
	static public function GetNewPriceIsMarkup($sPrice) {
		return preg_match('#^\d{1,3}(?:\.\d{0,3})?%$#', $sPrice);
	}


	/**
	 * Рассчитать новую цену продукта, если был указан процент от основной цены или получить цену
	 *
	 * @param $sNewPrice		новая цена
	 * @param $iPrice			цена
	 * @return null
	 */
	static public function GetCalculatedPriceFromNewPriceValue($sNewPrice, $iPrice) {
		if (self::GetNewPriceIsNormal($sNewPrice)) {
			/*
			 * это обычная цена
			 */
			return (float) $sNewPrice;
		} elseif (self::GetNewPriceIsDiscount($sNewPrice)) {
			/*
			 * рассчитать скидку
			 */
			return self::GetDifferedPrice($iPrice, $sNewPrice);
		} elseif (self::GetNewPriceIsMarkup($sNewPrice)) {
			/*
			 * рассчитать наценку
			 */
			return self::GetDifferedPrice($iPrice, $sNewPrice);
		}
		return null;
	}


	/**
	 * Рассчитать новую цену на основе скидки или наценки в виде записи "-5%" или "5%"
	 *
	 * @param $iPrice			цена
	 * @param $sPercentage		процент
	 * @return mixed			новая цена
	 */
	static public function GetDifferedPrice($iPrice, $sPercentage) {
		/*
		 * рассчитать процент от цены
		 */
		$fDifference = (intval($sPercentage) * $iPrice) / 100;
		/*
		 * добавить процент (учитывая его знак) к цене
		 */
		return $iPrice + $fDifference;
	}


}

?>