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

class PluginSimplecatalog_ModuleDatabase extends PluginSimplecatalog_Inherits_ModuleDatabase {

	/**
	 * Удалить тип перечисления у таблицы
	 * 
	 * @param      $sTableName
	 * @param      $sFieldName
	 * @param      $sType
	 * @param null $aConfig
	 */
	public function removeEnumType($sTableName, $sFieldName, $sType, $aConfig = null) {
		$sTableName = str_replace('prefix_', Config::Get('db.table.prefix'), $sTableName);
		$sQuery = "SHOW COLUMNS FROM `{$sTableName}`";

		if ($aRows = $this->GetConnect($aConfig)->select($sQuery)) {
			foreach ($aRows as $aRow) {
				if ($aRow['Field'] == $sFieldName) break;
			}
			if (strpos($aRow['Type'], "'{$sType}'") !== false) {
				$aRow['Type'] = str_ireplace("'{$sType}',", '', $aRow['Type']);
				
				$sQuery = "ALTER TABLE `{$sTableName}` MODIFY `{$sFieldName}` " . $aRow['Type'];
				$sQuery .= ($aRow['Null'] == 'NO') ? ' NOT NULL ' : ' NULL ';
				$sQuery .= is_null ($aRow['Default']) ? ' DEFAULT NULL ' : " DEFAULT '{$aRow['Default']}' ";
				$this->GetConnect($aConfig)->select($sQuery);
			}
		}
	}

}

?>