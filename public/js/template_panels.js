(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Template panels app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.TemplatePanelsApp = {

		init: function() {

			// Setup vars
			var i;

			// Find show template panel links on page
			var templatesPanelLinks = Dom.getElementsByClassName('show-templates-panel-link', 'img');

			for ( i = 0; i < templatesPanelLinks.length; i++ )
			{
				// Get panel div
				var panelDivId = templatesPanelLinks[i].id.substring(0, templatesPanelLinks[i].id.length-5) + '_panel';

				if ( Dom.get(panelDivId) )
				{
					var oPanelDiv = Dom.get(panelDivId);

					// Initial css does display: none, visible is used
					// by Panel but makes big gap so reverse css here
					Dom.setStyle(oPanelDiv, 'display', 'block');

					// Create panel
					var oPanel = new YAHOO.widget.Panel(oPanelDiv, { modal:true, draggable:true, width:'500px', visible:false } );
					oPanel.render();

					// Add listener
					Event.on(templatesPanelLinks[i], 'click', this.showPanel, oPanel);
				}
			}
		},

		showPanel: function(e, oPanel) {

			oPanel.center();
			oPanel.show();
		}
	};

	Event.onDOMReady(CMS.apps.TemplatePanelsApp.init, CMS.apps.TemplatePanelsApp, true);

})();
