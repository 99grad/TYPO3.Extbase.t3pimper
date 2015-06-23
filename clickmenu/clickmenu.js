
Ext.onReady(function() {
	Ext.apply(TYPO3.Components.PageTree.Actions, {
		'hidePageInMenu': function(node, tree) {
			var uid = node.attributes.realId;
			TYPO3.T3pimper.HidePageInMenu.test(
				node.attributes.nodeData,
				function(response) {
					Ext.MessageBox.alert('Custom Action', response);
				},
				this
			);
		}
	});
});

/*

TYPO3.Components.PageTree.Commands.disableNode(
			node.attributes.nodeData,
			function(response) {
				if (this.evaluateResponse(response)) {
					this.updateNode(node, node.isExpanded(), response);
				}
			},
			this
		);
*/