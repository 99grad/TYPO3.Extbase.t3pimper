<?php

namespace NNGrad\T3pimper\Helper;

class TcaHelper {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 * @inject
	 */
	protected $cObj;
	
	/**
	 * @var \NNGrad\T3pimper\Utilities\SettingsUtility
	 * @inject
	 */
	protected $settingsUtility;
	
	/**
	 * @var \NNGrad\T3pimper\Helper\AnyHelper
	 * @inject
	 */
	protected $anyHelper;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Service\ImageService
	 * @inject
	 */
	protected $imageService;
	
	
	/**
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 * @inject
	 */
	protected $fileRepository;
	
	
	
	public function __construct () {
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->imageService = $this->objectManager->get('\TYPO3\CMS\Extbase\Service\ImageService');
		$this->anyHelper = $this->objectManager->get('\NNGrad\T3pimper\Helper\AnyHelper');
		$this->fileRepository = $this->objectManager->get('\TYPO3\CMS\Core\Resource\FileRepository');
		$this->settingsUtility = $this->objectManager->get('\NNGrad\T3pimper\Utilities\SettingsUtility');
		$this->cObj = $this->objectManager->get('\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
	}
	
	
   /**
	*	fe_user-Feld und Passwort-Feld darstellen, damit im Backend das Passwort eines Users geändert werden kann.
	*	Problem: Passwort muss in Tabelle fe_users gespeichert werden, nicht in der Members-Tabelle.
	*	Das Speichern übernimmt per Hook: Nnmembers_Hooks_ProcessDataMapHook
	*
	**/
	
	function display_sysfilereference_field ($PA, $fobj) {
	
	
		
		if (!\NNGrad\T3pimper\Utilities\SettingsUtility::isEnabledInConf('falCropping')) return '';
		
		$this->doc = $this->objectManager->get('\TYPO3\CMS\Backend\Template\MediumDocumentTemplate');
		$this->doc->getPageRenderer()->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3pimper').'Resources/Public/TCA/css/style.css');
		$this->doc->getPageRenderer()->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3pimper').'Resources/Public/TCA/js/jquery.focalpointselect.js');
		$this->doc->getPageRenderer()->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3pimper').'Resources/Public/TCA/js/jquery.imgareaselect.js');
		$this->doc->getPageRenderer()->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3pimper').'Resources/Public/TCA/js/scripts.js');

		
		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		$selRef = $row[$field];

		$fileRepo = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Core\Resource\FileRepository');
		
		if (substr($row['uid'],0,3) == 'NEW') {
		}
		
		
		// Hier gibt es sicher einen besseren Weg?
		
		$sysFileUid = intval(preg_replace('/[^0-9]/i', '', $row['uid_local']));
		if (!$sysFileUid) return '';
		
		$sysFileRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'sys_file', 'uid='.$sysFileUid);
		$sysFileStorage = 'fileadmin';
		if ($sysFileRow['storage'] > 1) {
			$tmp = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'sys_file_storage', 'uid='.$sysFileRow['storage']);
			$sysFileStorage = $tmp['name'];
		}
		
		$filename = $sysFileStorage.$sysFileRow['identifier'];
		
		/*
		// Die eigentlich bessere Art – funktioniert aber nicht bei noch nicht persistierten sys_file_records!
		
		$file = $fileRepo->findFileReferenceByUid($row['uid']);
		$filename = $file->getPublicUrl();
		*/
		
		$TS = $this->settingsUtility->getTsSetup($row['pid']);
		if (!($setup = $TS['imgvariants']['presets'])) return 'Keine Definition in config.t3pimper.imgvariants.presets gefunden.';

		/*
		$image = $this->imageService->getImage($filename, null, false);
		$processingInstructions = array(
			'maxWidth' => 400,
			'maxHeight' => 200,
		);
		$processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
		$imageUri = $this->imageService->getImageUri($processedImage);
		
		
		$arr = array();
		foreach ($setup as $key => $conf) {
			$arr[] = $key;
		}
		*/
		
		$html = $this->anyHelper->renderTemplate( 
			'typo3conf/ext/t3pimper/Resources/Private/TCA/Imgvariants.html', 
			array(
				'uniqid'	=> uniqid(),
				'image'		=> $filename,
				'TS'		=> $TS['imgvariants'],
				'value'		=> $selRef,
				'setup'		=> $setup,
				'arr'		=> $arr,
				'data'		=> $row,
				'table'		=> $table,
				'field'		=> $field,
				'PA'		=> $PA
			)
		);
						
		return $html;
		
	}
	
}

?>