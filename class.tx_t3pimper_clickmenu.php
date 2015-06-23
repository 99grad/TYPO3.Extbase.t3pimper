<?php

class tx_t3pimper_clickmenu {

	/**
	 * Processing of clickmenu items
	 *
	 * @param	object		Reference to parent
	 * @param	array		Menu items array to modify
	 * @param	string		Table name
	 * @param	integer		Uid of the record
	 * @return	array		Menu item array, returned after modification
	 * @todo	Skinning for icons...
	 */
	 
	function main(&$backRef,$menuItems,$table,$uid)	{
		$localItems=array();
		if (($backRef->cmLevel && t3lib_div::_GP('subname')=='moreoptions') || ($table==='pages' && $uid==0))	{	// Show import/export on second level menu OR root level.

			$LL = $this->includeLL();

			$modUrl = $backRef->backPath.t3lib_extMgm::extRelPath('impexp').'app/index.php';
			$url = $modUrl . '?tx_t3pimper[action]=hide_page&id=' . ($table == 'pages' ? $uid : $backRef->rec['pid']);
			if ($table=='pages')	{
				$url.='&tx_t3pimper[pagetree][id]='.$uid;
				$url.='&tx_t3pimper[pagetree][levels]=0';
				$url.='&tx_t3pimper[pagetree][tables][]=_ALL';
			} else {
				$url.='&tx_t3pimper[record][]='.rawurlencode($table.':'.$uid);
				$url.='&tx_t3pimper[external_ref][tables][]=_ALL';
			}

			$localItems[] = $backRef->linkItem(
				$GLOBALS['LANG']->makeEntities($GLOBALS['LANG']->getLLL('export',$LL)),
				$backRef->excludeIcon(t3lib_iconWorks::getSpriteIcon('actions-document-export-t3d')),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar
			);

			if ($table=='pages')	{
				$url = $modUrl . '?id='. $uid . '&table=' . $table . '&tx_t3pimper[action]=import';
				$localItems[] = $backRef->linkItem(
					$GLOBALS['LANG']->makeEntities($GLOBALS['LANG']->getLLL('import',$LL)),
					$backRef->excludeIcon(t3lib_iconWorks::getSpriteIcon('actions-document-import-t3d')),
					$backRef->urlRefForCM($url),
					1	// Disables the item in the top-bar
				);
			}
		}
		
		return array_merge($menuItems,$localItems);
	}

	/**
	 * Include local lang file and return $LOCAL_LANG array loaded.
	 *
	 * @return	array		Local lang array
	 */
	function includeLL()	{
		global $LANG;

		return $LANG->includeLLFile('EXT:t3pimper/locallang.xml',FALSE);
	}
	
	
	public function test ($a, $b) {
		return "yep";
	}
	
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/impexp/class.tx_t3pimper_clickmenu.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/impexp/class.tx_t3pimper_clickmenu.php']);
}
?>