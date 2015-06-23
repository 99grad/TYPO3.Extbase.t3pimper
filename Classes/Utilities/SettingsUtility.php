<?php

namespace NNGrad\T3pimper\Utilities;


class SettingsUtility extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {


	
	protected $configurationManager;
	protected $request;
	
	protected $cObj;
	protected $settings;
	protected $mergedSettings;
	
	protected $configuration;
	protected $typoscriptService;
	
	
	public function __construct () {
	
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->configurationManager = $objectManager->get('\TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
		$this->request = $objectManager->get('\TYPO3\CMS\Extbase\Mvc\Request');
		
		$this->cObj = $this->configurationManager->getContentObject();
		$this->configuration = $this->configurationManager->getConfiguration( \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK );
		$this->settings = $this->configuration['settings'];
		$this->typoscriptService = $objectManager->get('\TYPO3\CMS\Extbase\Service\TypoScriptService');
		
		$this->mergedSettings = $this->merge_settings_with_flexform();
		
	}


	public static function isEnabledInConf ( $key ) {
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3pimper']);  
		return $config[$key] == 1;
	}
	
	public function getMergedSettings () {
		return $this->mergedSettings;
	}
	

	/**
	* Bestimmte, wiederkehrende Voreinstellungen für die Views holen, z.B. Dropdown mit Länderliste etc.
	*
	*/

	
	public function getDefaultSettingsForView () {
		$settings = self::getTsSetup( false );
		return $settings;
	}
	
	
	
	/**
	* Merge flexform values with settings - only if flexform-value is not empty
	*
	*/
	private function merge_settings_with_flexform () {
		if (!$this->settings) return array();
		$tmp = array_merge_recursive( $this->settings );
		if ($this->settings['flexform']) {
			foreach ($this->settings['flexform'] as $k=>$v) {
				if (trim($v) != '') $tmp[$k] = $v;
			}
		}
		return $tmp;
	}
	
	
	
    /**
	*	Get TypoScript Setup for plugin (with "name."-Syntax) as array
	*
	*/
	public static function getTsSetup ( $pageUid = false, $plugin = 't3pimper', $container = 'config' ) {
		
		$cacheID = '__tsSetupCache_'.$pageUid.'_'.$plugin.'_'.$container;
		
		if (TYPO3_MODE == 'FE') {
			if (!$plugin) return self::remove_ts_setup_dots($GLOBALS['TSFE']->tmpl->setup[$container.'.']);
			return self::remove_ts_setup_dots($GLOBALS['TSFE']->tmpl->setup[$container.'.']["{$plugin}."]);
		}
		
		if ($GLOBALS[$cacheID]) return $GLOBALS[$cacheID];

		if (!$pageUid) $pageUid = (int) $GLOBALS['_REQUEST']['popViewId'];
		if (!$pageUid) $pageUid = (int) preg_replace( '/(.*)(id=)([0-9]*)(.*)/i', '\\3', $GLOBALS['_REQUEST']['returnUrl'] );
		if (!$pageUid) $pageUid = (int) preg_replace( '/(.*)(id=)([0-9]*)(.*)/i', '\\3', $GLOBALS['_POST']['returnUrl'] );
		if (!$pageUid) $pageUid = (int) preg_replace( '/(.*)(id=)([0-9]*)(.*)/i', '\\3', $GLOBALS['_GET']['returnUrl'] );
		if (!$pageUid) $pageUid = (int) $GLOBALS['TSFE']->id;
		if (!$pageUid) $pageUid = (int) $_GET['id'];

		$sysPageObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Frontend\Page\PageRepository');
		$rootLine = $sysPageObj->getRootLine($pageUid);
		$TSObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Core\TypoScript\ExtendedTemplateService');
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();

		$GLOBALS[$cacheID] = !$plugin ? self::remove_ts_setup_dots($TSObj->setup[$container.'.']) : self::remove_ts_setup_dots($TSObj->setup[$container.'.']["{$plugin}."]);
		
		if (!$plugin) return self::remove_ts_setup_dots($TSObj->setup[$container.'.']);
		return self::remove_ts_setup_dots($TSObj->setup[$container.'.']["{$plugin}."]);
		
	}
	
	
	/**
	*	Aller TCA Felder holen für bestimmte Tabelle
	*
	*/
	public static function getTCAColumns ( $table = 'tx_nnmembers_domain_model_member' ) {
		$cols = $GLOBALS['TCA'][$table]['columns'];
		foreach ($cols as $k=>$v) {
			$cols[\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($k)] = $v;
		}
		return $cols;
	}
	
	/**
	*	Label eines bestimmten TCA Feldes holen
	*
	*/
	public static function getTCALabel ( $column = '', $table = 'tx_nnmembers_domain_model_member' ) {
		$tca = self::getTCAColumns( $table );
		$label = $tca[$column]['label'];
		if ($LL = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label)) return $LL;
		return $label;
	}
	
	
	public function getEnableFields ( $table ) {
		return $GLOBALS['TSFE']->sys_page->enableFields( $table, $GLOBALS['TSFE']->showHiddenRecords);
	}
	
	
	
	
	/* --------------------------------------------------------------- 
	
		Wandelt die "."-Arrays eines TypoScripts um, damit z.B.
		per JSON oder Fluid darauf zugegriffen werden kann.
		
		array(
			'demo' 	=> 'oups',
			'demo.'	=> array(
				'test' 	=> '1',
				'was'	=> '2'
			)
		)
		
		wird zu:
		
		array(
			'demo' => array(
				'_typoscriptNodeValue' 	=> 'oups'
				'test' 	=> '1',
				'was'	=> '2'
			)
		)
		
	*/
	
	function remove_ts_setup_dots ($ts) {
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');		
		$typoscriptService = $objectManager->get('\TYPO3\CMS\Extbase\Service\TypoScriptService');
		return $typoscriptService->convertTypoScriptArrayToPlainArray($ts);
	}
	
	function parse_flexform ( $xml, $sheet='sDEF', $lang='lDEF' ) {
		if (!$xml) return array();
		$arr = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($xml);
		$flat = array();
		foreach ($arr['data'][$sheet][$lang] as $k => $v) {
			$flat[$k] = $v['vDEF'];
		}
		return $flat;
	}
	
	
}

?>