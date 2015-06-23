<?php
	if (is_object($TYPO3backend)) {

		$pageRenderer = $GLOBALS['TBE_TEMPLATE']->getPageRenderer();
		$pageRenderer->addExtDirectCode();
		
		$pageRenderer->addExtOnReadyCode('

			Ext.apply(TYPO3.Components.PageTree.Actions, {
				hidePageInMenu: function(node, tree) {
					var uid = node.attributes.realId;
					
					TYPO3.T3pimper.hidePageInMenu(
						0,
						node.attributes.nodeData,
						function(response) {
							TYPO3.Components.PageTree.Actions.updateNode(node, node.isExpanded(), response);
						},
						this
					);
				},
				showPageInMenu: function(node, tree) {
					var uid = node.attributes.realId;
					
					TYPO3.T3pimper.hidePageInMenu(
						1,
						node.attributes.nodeData,
						function(response) {
							TYPO3.Components.PageTree.Actions.updateNode(node, node.isExpanded(), response);
						},
						this
					);
				}

			});
			
		');
		
		
//		$path = t3lib_extMgm::extRelPath('t3pimper');
//		$pageRenderer->addJsFile($path . '/clickmenu/clickmenu.js');
	}
?>