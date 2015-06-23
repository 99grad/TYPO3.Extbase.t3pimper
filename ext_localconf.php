
<?php

if (!defined ('TYPO3_MODE')) die ('Access denied.');


// ----------------------------------------------------
// Plugin einfügen

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY,'pi1/class.tx_t3pimper_pi1.php','_pi1','list_type',1);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
    'TYPO3.T3pimper',
    'typo3conf/ext/t3pimper/clickmenu/class.tx_t3pimper_clickmenu.php:tx_t3pimper_clickmenu'
  );
  

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
	<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3pimper/ext_typoscript_page.txt">
');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3pimper/ext_typoscript_user.txt">
');


$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('t3pimper', 'clickmenu/backend_ext.php');





?>