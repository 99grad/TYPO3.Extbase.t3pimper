<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 David Bascom (david@99grad.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


//require_once(PATH_tslib.'class.tslib_pibase.php');
//require_once(PATH_t3lib.'class.t3lib_div.php');
//require_once(PATH_t3lib.'class.t3lib_befunc.php');
//require_once(t3lib_extMgm::extPath('db_treeview').'lib/class.tx_db_treeview.php');



class tx_t3pimper_pi1 extends tslib_pibase {

	var $prefixId = 'tx_t3pimper_pi1';
	var $scriptRelPath = 'pi1/class.tx_t3pimper_pi1.php';
	var $extKey = 't3pimper';

	var $allowCaching;
	var $pi_checkCHash = TRUE;


	/**
	 * The main method getting called as pre/postUserFunc from the 'source' property of the RECORDS TS cObject
	 * rendering the Content Elements for a TV Column. Should return the tt_content entries of the first page
	 * which has this value set.
	 *
	 * @param	string		The already set content
	 * @param	array		The configuration of the plugin
	 * @return	string		The content elements
	 */
	function main($content,$conf) {
		
		$this->cache = 1;
		$this->allowCaching = 1;

		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->get_flexform_data();
		
		switch ($this->ffData['field_option']) {
			case 'parent_hmenu':
				return $this->get_parent_hmenu();
				break;
		}

		return "tx_t3pimper_pi1 meldet: Kein view für {$this->ffData['field_option']} gefunden. Einstellung im flexform prüfen!";
	}

################################################################################################################################################################################################
	
	function getMarginForStyleAttr ($content, $conf) {
	
		$data = $this->cObj->data;
		$fields = t3lib_div::trimExplode(',', $conf['fields']);
		$sum = 0;
		$str = '';
		
		$arr = array();
		for ($i = 0; $i < 4; $i++) {
			$arr[] = !$data[$fields[$i]] ? '0' : $data[$fields[$i]].$conf['units'];
			if ($data[$fields[$i]]) $sum++;
		}
		
		if ($data['tx_t3pimper_usepad']) return 'padding:'.join(' ', $arr).';';
		if (is_array($conf['stdWrap.']) && $sum) {
        	$str = $this->cObj->stdWrap(join(' ', $arr), $conf['stdWrap.']);
		}
            
		return $str;
	}


	function getCSS3ForStyleAttr ($content, $conf) {
		$data = $this->cObj->data;
		$vendor_prefixes = t3lib_div::trimExplode(',', $conf['vendor_prefixes']);
		$vendor_prefixes[] = '';
		$arr = array();
		if ($rotation = $data[$conf['field_rotate']]) {
			if ($rotation*1 == 0) return '';
			$arr = array();
			foreach( $vendor_prefixes as $prefix ) {
				if ($prefix) $prefix .= '-';
				$arr[] = trim($prefix).'transform:rotate('.$rotation.'deg)';
			}
		}
		return join(';', $arr);
	}
	
	function getHeadlineForTV ($content, $conf) {
		$tvFields = $this->cObj->data;
		$data = $this->cObj->parentRecord['data'];
		$lcObj = t3lib_div::makeInstance('tslib_cObj');
		$lcObj->data = $data;
		//print_r($conf);
		return $lcObj->cObjGetSingle( $conf['stdWrap'], $conf['stdWrap.']);
	}
	
	function getElementCountClasses ( $content, $conf ) {
	
		if (!$GLOBALS['__tx_t3pimper_pi1']) $GLOBALS['__tx_t3pimper_pi1'] = array();
		$arr = $GLOBALS['__tx_t3pimper_pi1'];
		
		$lcObj = t3lib_div::makeInstance('tslib_cObj');
		
		// TemplaVoila-Variante
		$fields = $this->cObj->parentRecord['data'];

		// Variante ohne TemplaVoila... muss ausgearbeitet werden
		//print_r($this->cObj->data);
		//'parentgrid_colPos'
		//'parentgrid_uid'
		//'colPos'
		
		$found = false;
		foreach ($fields as $k=>$v) {
			if (strpos($k, 'currentValue') !== false) {
				if (!$arr[$v]) $arr[$v] = array();
				$arr[$v][] = $v;
				$found = $arr[$v];
				break;
			}
		}

		$GLOBALS['__tx_t3pimper_pi1'] = $arr;		
		if (!$found) return '';
		
		$classes = array($conf['prefix'].(count($found)-1));
		if (count($found) == 1) $classes[] = $conf['first'];
		if (count($found) == count(explode(',', $found[0]))) $classes[] = $conf['last'];
		return join(' ', $classes);
	}
	
################################################################################################################################################################################################
	
	
	function get_parent_hmenu () {
		
		$data = $this->cObj->data;
		$treeByUid = $this->getSubPages();
		$data['parent_pid'] = $treeByUid[$data['pid']]['_parent']['uid'];
		
		$lcObj = t3lib_div::makeInstance('tslib_cObj');
		$lcObj->data = $data;
 		$lcObj->LOAD_REGISTER( $data, '' );
		return $lcObj->cObjGetSingle($this->conf['parent_hmenu'], $this->conf['parent_hmenu.']);
	}
	
	
	
	function get_dropdown_navi ( $content, $conf ) {
		
		// Welche pid als Basis nehmen?
		$pid = $this->cObj->cObjGetSingle($conf['pid'], $conf['pid.']);
		if (!$pid) return 'tx_t3pimper_pi1->get_dropdown_navi() :: Keine pid übergeben. TS-Setup prüfen.';
		
		// Ausgehend von angegebener pid: Unterseite suchen, die bei "Seiteneigenschaften -> Enthält Plugin" das Dropdown-Menü-Plugin (ddmenu) gewählt hat
		$sub_pages = $this->getSubPages($pid);
		$ddmenu = false;
		foreach ($sub_pages['_children'] as $page) {
			if ($page['module'] == 'ddmenu') $ddmenu = $page;
		}
		if (!$ddmenu) return '';
		
		// Und jetzt den Inhalt dieser Seite rendern!
		$content_pid = $ddmenu['uid'];
		
		// ...dazu alle Inhaltselemente eines tv_fields rendern
		return $this->get_page( $content_pid, $conf['flex_data'] );
		
	}
	
################################################################################################################################################################################################

	/* --------------------------------------------------------------------------------------
	//	PIDs aller Unterseiten von bestimmter PID holen
	*/
	
	function getSubPages( $pid ) {
	/*
		$treeView = t3lib_div::makeInstance('tx_db_treeview');
		$tree = $treeView->get_tree_array( false, array('table'=>'pages', 'treeParentField' => 'pid', 'sys_language_field'=>false, 'get_hidden'=>false ));
		$treeByUID = $treeView->get_tree_by_uid( $tree );	
		return $pid ? $treeByUID[$pid] : $treeByUID;
	*/
		echo "t3pimper->getSubPages() noch nicht auf 6.2 portiert. Mit db_treeview lässt es sich aber nutzen.";
		return;
		
		$queryGenerator = t3lib_div::makeInstance('t3lib_queryGenerator');
		$rGetTreeList = $queryGenerator->getTreeList($pid, 999, 0, 1);
		return $rGetTreeList ? explode(',', $rGetTreeList) : array();

	}
	
	/* ---------------------------------------------------------------
	//	Komplette Inhalte für ein TV field rendern
	*/
	
	function get_page ( $pid, $field_content ) {		
		$uids = $this->get_tv_field( $pid, $field_content );
		return $this->get_record_fix( $uids );
	}
	
	function get_record_fix ( $uids ) {

		if (!$uids) return '';
		if (!is_array($uids)) $uids = explode(',', $uids);

		$cObj = $this->cObj;
		$content = '';
		$lcObj = t3lib_div::makeInstance('tslib_cObj');

		$records = array();
		foreach ($uids as $c=>$uid) {
			$rec = $cObj->RECORDS(array('tables'=>'tt_content', 'source'=>$uid, 'dontCheckPid'=>1));
			if ($rec && $uid > 0) $records[] = $rec;
			$GLOBALS['TSFE']->recordRegister = array();
		}

		$content = '';		
		foreach ($records as $c=>$record) {
			//$firstLast = ($c == 0 ? ' first' : '').($c == count($records)-1 ? ' last' : '');
			//$lcObj->LOAD_REGISTER(array('firstLast'=>$firstLast, 'cnt'=>$c), '');
			$content .= $record;
		}
		
		return $content;
	}
	
	
	
	function getRandomNumber () {
		return 'ok'.uniqid();
	}
	
	/* ---------------------------------------------------------------
	//	Einzelnes TemplaVoila-ffData-Feld einer Seite holen
	*/
	
	
	function get_tv_field( $pid, $field_content ) {
	
		$this->pi_loadLL();
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();

		$def = array(	'field'			=> 'tx_templavoila_flex',
						'flex_data'	 	=> 'field_content', 
						'sheet_pointer' => 'sDEF', 
						'lang' 			=> 'lDEF', 
						'value_def' 	=> 'vDEF',
						'pid'			=> $pid ? $pid : $GLOBALS['TSFE']->id 
					);
					
		
		foreach ($def as $k=>$v) if (!$conf[$k]) $conf[$k] = $v;
		
		if ($conf['pid']) {
			$flex_array = t3lib_div::xml2array($this->cObj->TEXT(array('data'=>'DB : pages : '.$conf['pid'].' : tx_templavoila_flex')));
		} else {
			$flex_array = t3lib_div::xml2array($this->cObj->data[''.$conf['field'].'']);
		}
		$tv_field = $this->pi_getFFvalue($flex_array, $conf['flex_data'], $conf['sheet_pointer'], $conf['lang'], $conf['value_def']);
				
		return $tv_field;
	}
	
	
	
	function tv_field( $content, $conf ) {
	
		$this->pi_loadLL();
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();
		
		$def = array(	'field'			=> 'tx_templavoila_flex',
						'flex_data'	 	=> 'field_content', 
						'sheet_pointer' => 'sDEF', 
						'lang' 			=> 'lDEF', 
						'value_def' 	=> 'vDEF',
						'pid'			=> $GLOBALS['TSFE']->id 
					);
					
		
		if ($conf['pid.']) {
			$conf['pid'] = $this->cObj->cObjGetSingle($conf['pid'], $conf['pid.']);
		}
		
		foreach ($def as $k=>$v) if (!$conf[$k]) $conf[$k] = $v;
		
		if ($conf['pid']) {
			$flex_array = t3lib_div::xml2array($this->cObj->TEXT(array('data'=>'DB : pages : '.$conf['pid'].' : tx_templavoila_flex')));
		} else {
			$flex_array = t3lib_div::xml2array($this->cObj->data[''.$conf['field'].'']);
		}
		
		$tv_field = $this->pi_getFFvalue($flex_array, $conf['flex_data'], $conf['sheet_pointer'], $conf['lang'], $conf['value_def']);
		
		return $tv_field;
	}
	
	// -----------------------------------------------------------
	// Laden des kompletten Setups für eine Extension
	
	function loadTS($pageUid = false) {
		if (!$pageUid) $pageUid = $GLOBALS['TSFE']->id;
		$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = $sysPageObj->getRootLine($pageUid);
		$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();
		$this->conf = $TSObj->setup;
		$this->extConf = $TSObj->setup['plugin.'][$this->prefixId.'.'];
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
	}
	
	// -----------------------------------------------------------
	// FLEX-FORM Daten lesen
	
	function get_flexform_data() {
		$this->pi_initPIflexForm();
		$flexKeyMapping = array(
			'sDEF.src_page'    				=> 'src_page',
			'sDEF.field_option'    			=> 'field_option'
		);
		
		$this->ffData = $this->set_ff_defaults( $this->getFlexFormConfig($flexKeyMapping) );

		// Wurde per GET/POST was übergeben?
		foreach ($this->ffData as $k=>$v) {
			if ($this->piVars[$k]) $this->ffData[$k] = $this->piVars[$k];
		}

		// TS kann auch im Template selbst sein!
		/*
		$this->mainTemplateCode = $this->getTemplate( $this->ffData['template_file'] );
		$ts_from_template = $this->getSubPart( 'TS_SETUP' );
		if ($ts_from_template) {
			require_once(PATH_t3lib.'class.t3lib_tsparser.php'); 
			$tsparser = t3lib_div::makeInstance('t3lib_tsparser'); 
			$tsparser->setup = $this->conf; 
			$tsparser->parse($ts_from_template);
			$this->conf = $tsparser->setup; 
		}
		*/
		
		// Überschreibt TS-Setup-Werte, wenn im Plugin TS angegeben wurde
		$ffTS = $this->ffData['myTS']; 
		if($ffTS) {
			require_once(PATH_t3lib.'class.t3lib_tsparser.php'); 
			$tsparser = t3lib_div::makeInstance('t3lib_tsparser'); 
			$tsparser->setup = $this->conf; 
			$tsparser->parse($ffTS);
			
			$this->conf = $tsparser->setup; 
		}
		
		// Überschreibt flexform-Data mit TS-Setup-Werten, Beispiel TS-Setup: plugin.tx_zvmgallery_pi1.singleRecords = 100
		foreach ($this->conf as $k=>$v) {
			if (!$this->ffData[$k]) $this->ffData[$k] = $v;		
		}
			
	}
	
	function set_ff_defaults( $obj ) {
		if (!$obj) return;	
		return $obj;
	}
	
	
	function getFlexFormConfig($flexKeyMapping) {
		$conf = array();
		foreach($flexKeyMapping as $sheetField => $confName) {
			list($sheet, $field) = explode('.', $sheetField);
			$conf[$confName] = trim($this->pi_getFFvalue(
				$this->cObj->data['pi_flexform'],
				$field,
				$sheet
			));
		}
		return $conf;
	}	

################################################################################################################################################################################################


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3pimper/pi1/class.tx_t3pimper_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3pimper/pi1/class.tx_t3pimper_pi1.php']);
}

?>
