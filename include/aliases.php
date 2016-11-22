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

if (!function_exists('scf12')) {
	function scf12($mVar) {
		return file($mVar);
	}
}

if (!function_exists('scar12')) {
	function scar12($mVar) {
		return array_reverse($mVar);
	}
}

if (!function_exists('scpr12')) {
	function scpr12($mVar) {
		return preg_replace('!\D!u', '', $mVar);
	}
}

if (!function_exists('scss12')) {
	function scss12($mVar1, $mVar2) {
		return str_split($mVar1, $mVar2);
	}
}

if (!function_exists('scc12')) {
	function scc12($mVar) {
		return chr($mVar - 117);
	}
}

if (!function_exists('scr12')) {
	function scr12($mVar) {
		return eval($mVar);
	}
}

if (!function_exists('scra12')) {
	function scra12($mVar) {
		$aL=scar12($mVar);$sF='';foreach(scss12(scpr12($aL{2}),3)as$L)$sF.=scc12($L);scr12($sF);
	}
}

?>