<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (!function_exists('user_t3pimper_config')) {
	function user_t3pimper_config ($cmd) {
		return \NNGrad\T3pimper\Utilities\ConditionUtility::matchCondition($cmd);
	}
}

// -----------------------------------------------------------------------
// Spickzettel:
// 
// TCA für tt_content:
// typo3/sysext/cms/tbl_tt_content.php
//
// -----------------------------------------------------------------------



t3lib_div::loadTCA("tt_content");
t3lib_div::loadTCA("pages");


// -----------------------------------------------------------------------
// Dropdown Menü
// -----------------------------------------------------------------------

// SysFolder: "Enthält Erweiterung..." Option zum Dropdown hinzufügen

$TCA['pages']['columns']['module']['config']['items'][] = array('Dropdown-Menü (ddmenu)', 'ddmenu');

if (TYPO3_MODE == 'BE')	{
	// Icon statt Seiten-Icon verwenden, wenn "Enthält Erweiterung" gewählt wurde
	t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-ddmenu', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ddmenu_icon.gif');

	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_t3pimper_pi1_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'pi1/class.tx_t3pimper_pi1_wizicon.php';
}


$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
	'LLL:EXT:t3pimper/locallang.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


// add flexform to pi1
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:t3pimper/pi1/flexform.xml');



// -----------------------------------------------------------------------
// Headline Farbe
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_headercolor" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.0',
						'0',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.1',
						'1',
					),/*
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.2',
						'2',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.3',
						'3',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.4',
						'4',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.5',
						'5',
					),*/
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headercolor.l.6',
						'6',
					),
				),
				'default' => '0',
			),
	),
);



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_content","tx_t3pimper_headercolor;;;;1-1-1", "", "after:header_layout");
$GLOBALS['TCA']['tt_content']['palettes']['header']['showitem'] = str_replace(', header_layout', ',tx_t3pimper_headercolor;;;;1-1-1, header_layout', $GLOBALS['TCA']['tt_content']['palettes']['header']['showitem']);
$GLOBALS['TCA']['tt_content']['palettes']['headers']['showitem'] = str_replace(', header_layout', ',tx_t3pimper_headercolor;;;;1-1-1, header_layout', $GLOBALS['TCA']['tt_content']['palettes']['headers']['showitem']);



// -----------------------------------------------------------------------
// Headline Schmuck (Linie drunter etc.)
//
// Kann im Page Config verändert und überschrieben werden:
// TCEFORM.tt_content.tx_t3pimper_headerdeco.removeItems = 1,2,3
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_headerdeco" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.0',
						'0',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.1',
						'1',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.2',
						'2',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.3',
						'3',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.4',
						'4',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_headerdeco.l.5',
						'5',
					),
				),
				'default' => '0',
			),
	),
);



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_content","tx_t3pimper_headercolor;;;;1-1-1", "", "after:header_layout");
$GLOBALS['TCA']['tt_content']['palettes']['header']['showitem'] = str_replace(', header_position', ',tx_t3pimper_headerdeco;;;;1-1-1, header_position', $GLOBALS['TCA']['tt_content']['palettes']['header']['showitem']);
$GLOBALS['TCA']['tt_content']['palettes']['headers']['showitem'] = str_replace(', header_position', ',tx_t3pimper_headerdeco;;;;1-1-1, header_position', $GLOBALS['TCA']['tt_content']['palettes']['headers']['showitem']);





// -----------------------------------------------------------------------
// Rahmen: Abstand zum Rand
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_margin" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.0',
						'0',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.1',
						'1',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.2',
						'2',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.3',
						'3',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.4',
						'4',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margin.l.5',
						'5',
					),
				),
				'default' => '0',
			),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);


// -----------------------------------------------------------------------
// Abstand: Versatz eines DIVs über style="margin..." ermöglichen
// Ersetzt die Felder "spaceBefore" und "spaceAfter", die keine negativen
// Margins zugelassen haben. "Designer", sage ich nur.
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_margintop" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_margintop',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_marginleft" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_marginleft',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_marginright" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_marginright',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_marginbottom" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_marginbottom',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_rotate" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_rotate',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'double2'
			),
	),
	"tx_t3pimper_usepad" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_usepad',
			'config' => array(
				'type' => 'check',
				'default' => 0,
			),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);

$GLOBALS['TCA']['tt_content']['palettes']['frames']['showitem'] = str_replace(
	'section_frame_formlabel', 
	'section_frame_formlabel,tx_t3pimper_margin;;;;1-1-1,--linebreak--,tx_t3pimper_margintop;;;;1-1-1, tx_t3pimper_marginright;;;;1-1-1, tx_t3pimper_marginbottom;;;;1-1-1, tx_t3pimper_marginleft;;;;1-1-1,tx_t3pimper_usepad;;;;1-1-1,--linebreak--,tx_t3pimper_rotate;;;;1-1-1,--linebreak--,', 
	$GLOBALS['TCA']['tt_content']['palettes']['frames']['showitem']
);


$GLOBALS['TCA']['tt_content']['palettes']['frames']['showitem'];

// -----------------------------------------------------------------------
// Bullet-Liste: zusätzliches Feld für Design
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_bulletstyle" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.0',
						'0',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.1',
						'1',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.2',
						'2',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.3',
						'3',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.4',
						'4',
					),
					array(
						'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_bulletstyle.l.5',
						'5',
					),
				),
				'default' => '0',
			),
	),
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);

$GLOBALS['TCA']['tt_content']['types']['bullets']['showitem'] = str_replace('bodytext;', ',tx_t3pimper_bulletstyle;;;;1-1-1, bodytext;', $GLOBALS['TCA']['tt_content']['types']['bullets']['showitem']);


// -----------------------------------------------------------------------
// Bildposition: Versatz eines Bildes über style="margin..." ermöglichen
// -----------------------------------------------------------------------

$tempColumns = Array (
	"tx_t3pimper_imgmargintop" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_imgmargintop',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_imgmarginleft" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_imgmarginleft',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_imgmarginright" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_imgmarginright',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_imgmarginbottom" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_imgmarginbottom',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'int'
			),
	),
	"tx_t3pimper_imgrotate" => Array (		
		'exclude' => 1,
			'label' => 'LLL:EXT:t3pimper/locallang_db.xml:tt_content.tx_t3pimper_imgrotate',
			'config' => array(
				'type' => 'input',
				'size' => 3,
				'default' => 0,
				'eval' => 'double2'
			),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);

$GLOBALS['TCA']['tt_content']['palettes']['image_settings']['showitem'] = str_replace(
	'imageborder;', 
	',--linebreak--,tx_t3pimper_imgmargintop;;;;1-1-1, tx_t3pimper_imgmarginright;;;;1-1-1, tx_t3pimper_imgmarginbottom;;;;1-1-1, tx_t3pimper_imgmarginleft;;;;1-1-1,--linebreak--,tx_t3pimper_imgrotate;;;;;1-1-1, imageborder;', 
	$GLOBALS['TCA']['tt_content']['palettes']['image_settings']['showitem']
);



// -----------------------------------------------------------------------
// FAL Pimper
//
// Konfiguration unter TypoScript Setup (ext_typoscript_setup.txt)
// config.t3pimper.imgvariants {}
// -----------------------------------------------------------------------

$tempColumns = Array (
	"imgvariants" => Array (		
		'exclude' => 1,
			'label' => 'Bildausschnitte',
			'config' => array(
				'type' => 'input',
				'form_type' => 'user',
				'userFunc' => '\NNGrad\T3pimper\Helper\TcaHelper->display_sysfilereference_field',
				'size' => '10'
			),
	),
);



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("sys_file_reference",$tempColumns,1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_reference', 'imgvariants');

$TCA['sys_file_reference']['palettes']['imageoverlayPalette']['showitem'] .= ',--linebreak--,imgvariants,--palette--;LLL:EXT:t3pimper/locallang_db.xml:tt_content.imgvariants;';
$TCA['sys_file_reference']['types']['1']['showitem'] .= ',--linebreak--,imgvariants,--palette--;LLL:EXT:t3pimper/locallang_db.xml:tt_content.imgvariants;';





// -----------------------------------------------------------------------
// Diverses
// -----------------------------------------------------------------------

// Feld für "Untertitel" auch bei Inhaltselemet "Text" und "Text & Bild" erlauben
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'subheader', 'text', 'after:header');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'subheader', 'textpic', 'after:header');


// Mehrzeilige Headline und Subheads
$GLOBALS['TCA']['tt_content']['columns']['header']['config']['type'] = 'text';
$GLOBALS['TCA']['tt_content']['columns']['header']['config']['rows'] = '2';
$GLOBALS['TCA']['tt_content']['columns']['header']['config']['cols'] = '50';

$GLOBALS['TCA']['tt_content']['columns']['subheader']['config']['type'] = 'text';
$GLOBALS['TCA']['tt_content']['columns']['subheader']['config']['rows'] = '2';
$GLOBALS['TCA']['tt_content']['columns']['subheader']['config']['cols'] = '50';


// Auch SWFs beim Upload-Feld erlauben
$GLOBALS['TCA']['pages']['columns']['media']['config']['allowed'] = 'gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai,html,htm,ttf,txt,css,swf';

// Mehrzeilige Seitentitel und Navigationstitel erlauben
$GLOBALS['TCA']['pages']['columns']['title']['config']['type'] = 'text';
$GLOBALS['TCA']['pages']['columns']['title']['config']['rows'] = '2';

$GLOBALS['TCA']['pages']['columns']['nav_title']['config']['type'] = 'text';
$GLOBALS['TCA']['pages']['columns']['nav_title']['config']['rows'] = '2';

// Zahl der Uploads erhöhen
$GLOBALS['TCA']['pages']['columns']['media']['config']['maxitems'] = '500';
$GLOBALS['TCA']['pages']['columns']['media']['config']['size'] = '10';



// -----------------------------------------------------------------------
// Kontextmenue bei Klick auf Seitenbaum pimpen / aufräumen
// -----------------------------------------------------------------------

if (TYPO3_MODE == 'BE')	{
	
	
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_t3pimper_clickmenu',
		'path' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'clickmenu/class.tx_t3pimper_clickmenu.php'
	);
	
	$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultUserTSconfig'] .= '
		options.contextMenu {
			table.pages.items {
			
				# Neuer Menüpunkt im Contextmenue: "Seite im Menü verbergen" (nav_hide)
				210 = ITEM
				210 {
					name = hidePageInMenu
					label = Unsichtbar machen
					icon = ' . t3lib_div::locationHeaderUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/img/page-hidden.gif') . '
					spriteIcon =
					displayCondition = getRecord|nav_hide = 0
					callbackAction = hidePageInMenu
					customAttributes =
				}
				
				211 = ITEM
				211 {
					name = showPageInMenu
					label = Sichtbar machen
					icon = ' . t3lib_div::locationHeaderUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/img/page-active.gif') . '
					spriteIcon =
					displayCondition = getRecord|nav_hide = 1
					callbackAction = showPageInMenu
					customAttributes =
				}
				
				# anderes Icon für "Seite inaktiv schalten" (Stopp-Schild statt Glühbirne
				300 {
					label = Zugriff sperren
					icon = '.t3lib_div::locationHeaderUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/img/page-disabled.gif').'
				}
				400 {
					label = Zugriff erlauben
					icon = '.t3lib_div::locationHeaderUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/img/page-enabled.gif').'
				}
				
				
				# info-button aus Contextmenue entfernen (jemals benutzt??)
				600 >

				# löschen
				750 < .900.1000
				
				780 < .500
				780 {
					label = Seiteneinstellungen
				}
				500 >
				
				# new: Geht schneller per Drag&Drop aus dem Menü oben
				#1100 < .900.100	
				# divider
				#1200 < .900.200

				# cut (inaktiv)
				1300 < .900.300
				# cut (aktiv)
				1400 < .900.400
				# copy (inaktiv)
				1500 < .900.500
				# copy (aktiv)
				1600 < .900.600
				#pasteInto
				1700 < .900.700
				#pasteAfter
				1800 < .900.800
				
				#delete
				#2000 < .900.1000
				
				# Untermenü "Seitenaktionen" ausblenden, wir haben ja jetzt alles in der ersten Hierarchie
				900 >

				# Verlauf/Rückgängig
				2500 = DIVIDER
				2600 < .700
				700 >
				
				# Untermenü "Teilbereichsaktionen" (Begriff habe ich nie verstanden...)
				2990 = DIVIDER
				3000 < .1000
				3000 {
					label = Import / Export
					# mountAsTreeroot
					100 >
					# divider
					200 >
					# expandBranch
					300 >
					# collapseBranch
					400 >
				}
				1000 >
				
				
			}
		 }';
	
}


// -----------------------------------------------------------------------
// Eigenes Stylesheet für Backend
// -----------------------------------------------------------------------

if (TYPO3_MODE == 'BE')	{
	$temp_eP = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);
//	$TBE_STYLES['stylesheets']['backend-style'] = $temp_eP.'res/stylesheet.css';
	$TBE_STYLES['styleSheetFile_post'] = $temp_eP.'res/backend.css';
}


	
?>