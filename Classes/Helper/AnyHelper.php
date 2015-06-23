<?php

namespace NNGrad\T3pimper\Helper;

class AnyHelper {

	
	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	
	/* 
	 *	Old-School piBase-Object erzeugen um alte Plugins zu initialisieren
	 *
	 */
	 
	function piBaseObj () {
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
		$configurationManager = $objectManager->get('\TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
		$piObj = $objectManager->create('\TYPO3\CMS\Frontend\Plugin\AbstractPlugin');
		$piObj->cObj = $configurationManager->getContentObject();
		return $piObj;
	}
	
	
	function setPageTitle ( $titleStr ) {
		$GLOBALS['TSFE']->page['title'] = $titleStr;
		$GLOBALS['TSFE']->indexedDocTitle = $titleStr;
	}
		
	/* --------------------------------------------------------------- 
		Schlüssel erzeugen zur Validierung einer Abfrage
	*/
	
	function createKeyForUid ( $uid ) {
		$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['nnmembers']);
		if ($extConfig['encodingKey'] == '99grad') die('<h1>nnfesubmit</h1><p>Bitte aendere die "Salting Key" in der Extension-Konfiguration auf etwas anderes als "99grad" (im Extension-Manager auf die Extension klicken)</p>');
		return substr(strrev(md5($uid.$extConfig['encodingKey'])), 0, 8);
	}
	
	function validateKeyForUid ( $uid, $key ) {
		return self::createKeyForUid( $uid ) == $key;
	}
	
	/* --------------------------------------------------------------- */

	
	function trimExplode ( $del, $str ) {
		if (!trim($str)) return array();
		$str = explode($del, $str);
		foreach ($str as $k=>$v) $str[$k] = trim($v);
		return $str;
	}
	
	function trimExplodeArray ( $arr ) {
		if (!$arr) return array();
		if (!is_array($arr)) $arr = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $arr);
		$final = array();
		foreach ($arr as $n) {
			if (trim($n)) $final[] = $n;
		}
		return $final;
	}
		
	/* --------------------------------------------------------------- */

	function calculate_images ( $data, $conf ) {
		$cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		if (!$data) return array();
		if (!$data['image']) return array();
		if (!is_array($data['image'])) $data['image'] = explode(',', $data['image']);
		$imgs = array();
		foreach ($conf as $k=>$c) {
			$key = substr($k,0,-1);
			if (!$imgs[$key]) $imgs[$key] = array();
			foreach ($data['image'] as $img) {
				$c['file'] = 'uploads/pics/'.$img;
				$imgs[$key][] = $cObj->IMG_RESOURCE($c);
			}
		}
		return $imgs;
	}
	
	
	/* --------------------------------------------------------------- */

	function dropdown ( $arr, $selected = null, $name = null, $class = null ) {
		$tmp = array();
		foreach ($arr as $k=>$v) {
			$sel = $k == $selected ? ' selected="selected"' : '';
			$tmp[] = "<option value=\"{$k}\"{$sel}>{$v}</option>";
		}
		return "<select name=\"{$name}\" data-mid=\"{$class}\" class=\"mdata mdata-{$class}\">".join('', $tmp)."</select>";
	}
	
	
	function renderTemplate ( $path, $vars, $flattenVars = true, $pathPartials = null, $doubleRender = false ) {
		
		if (!$path) return '';
		if (!file_exists($path)) $path = PATH_site.$path;
		if (!file_exists($path)) return '';

		$view = $this->objectManager->get('\TYPO3\CMS\Fluid\View\StandaloneView');
		$view->setTemplatePathAndFilename($path);
		$view->setPartialRootPath( $pathPartials ? $pathPartials : \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('t3pimper').'Resources/Private/TCA/Partials/' );		
		
		if ($flattenVars) {
			$view->assignMultiple($vars);
		} else {
			$view->assign('data', $vars);
		}
		
		$html = $view->render();
		
		if ($doubleRender) {
			$view->setTemplateSource($html);
			$html = $view->render();
		}
		
		return $html;
	}
	
	
	function renderTemplateSource ( $template, $vars, $pathPartials = null ) {
		
		if (!$template) return '';
		
		if (strpos($template, '{namespace') === false) {
			$template = '{namespace VH=Nng\Nnmembers\ViewHelpers}'.$template;
		}
		
		$view = $this->objectManager->get('\TYPO3\CMS\Fluid\View\StandaloneView');		
		$view->setTemplateSource($template);
		$view->setPartialRootPath( $pathPartials ? $pathPartials : \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('nnmembers').'Resources/Private/Partials/' );
		$view->assignMultiple( $vars );
		$html = $view->render();
		
		return $html;
	}
	
	
	/* --------------------------------------------------------------- 
		NOTICE, ERROR, WARNING, OK
		$this->anyHelper->addFlashMessage('so,so', 'ja ja');

	*/
	
	function addFlashMessage ( $title = '', $text = '', $type = 'OK') {
		
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
		$controllerContext = $objectManager->create('\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext');
		$controllerContext->getFlashMessageQueue()->enqueue(
			$objectManager->get( '\TYPO3\CMS\Core\Messaging\FlashMessage', $text, $title, constant('\TYPO3\CMS\Core\Messaging\FlashMessage::'.$type), false )
		);
	}
	
	function renderFlashMessages () {
		
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
		$controllerContext = $objectManager->create('\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext');
		if (count($controllerContext->getFlashMessageQueue()->getAllMessages())) {
			return $this->renderTemplate('typo3conf/ext/nnmembers/Resources/Private/Templates/FlashMessages.html', array() );
		}
		return '';
	}
	
	/* --------------------------------------------------------------- */
	

	
	function cloneArray( $arr ) {
		$ret = array();
		foreach ($arr as $k=>$v) $ret[$k] = $v;
		return $ret;
	}
	
	function cleanIntList ( $str='', $returnArray = null ) {
		$is_arr = is_array($str);
		if (trim($str) == '') return (($returnArray == null && !$is_arr) || $returnArr === false) ? '' : array();
		if ($is_arr) $str = join(',', $str);
		$str = $GLOBALS['TYPO3_DB']->cleanIntList( $str );
		if (($returnArray == null && !$is_arr) || $returnArr === false) return $str;
		return explode(',', $str);
	}
			
	function get_obj_by_attribute ( &$data, $key, $val = false, $retArr = false ) {
		$ref = array();
		foreach ($data as $k=>$v) {
			if ($val === false) {
				if ($retArr === true) {
					if (!is_array($ref[$v[$key]])) $ref[$v[$key]] = array();
					$ref[$v[$key]][] = &$data[$k];
				} else {
					$ref[$v[$key]] = &$data[$k];
				}
			} else {
				$ref[$v[$key]] = $val === true ? $v : $v[$val];
			}
		}
		return $ref;
	}
	
	
	
	/**
	* Debugs a SQL query from a QueryResult
	*
	* @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult
	* @param boolean $explainOutput
	* @return void
	*/
	
	public function debugQuery(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult, $explainOutput = FALSE){
		$GLOBALS['TYPO3_DB']->debugOuput = 2;
		if ($explainOutput){
			$GLOBALS['TYPO3_DB']->explainOutput = true;
		}
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
		$queryResult->toArray();
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
	 
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
		$GLOBALS['TYPO3_DB']->explainOutput = false;
		$GLOBALS['TYPO3_DB']->debugOuput = false;
	}	
	
	
	// --------------------------------------------------------------------------------------------------------------------
	// Weiterleitung über http-Header location:...
	
	static function httpRedirect ( $pid = null, $vars = array() ) {
		if (!$pid) $pid = $GLOBALS['TSFE']->id;		
		$pi = self::piBaseObj();
		$link = $pi->pi_getPageLink($pid, '', $vars); 
		$link = \TYPO3\CMS\Core\Utility\GeneralUtility::locationHeaderUrl($link); 
		header('Location: '.$link); 
		exit(); 
	}

	
}

?>