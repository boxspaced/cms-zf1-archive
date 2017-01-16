(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Submission panels app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.SubmissionPanelsApp = {

		init: function() {

			// Setup vars
			var i;

			// Find show submission panel links on page
			var submissionPanelLinks = Dom.getElementsByClassName('show-submission-panel-link', 'img');

			for ( i = 0; i < submissionPanelLinks.length; i++ )
			{
				// Get panel div
				var panelDivId = submissionPanelLinks[i].id.substring(0, submissionPanelLinks[i].id.length-5) + '_panel';

				if ( Dom.get(panelDivId) )
				{
					var oPanelDiv = Dom.get(panelDivId);

					// Initial css does display: none, visible is used
					// by Panel but makes big gap so reverse css here
					Dom.setStyle(oPanelDiv, 'display', 'block');

					// Create panel
					var oPanel = new YAHOO.widget.Panel(oPanelDiv, { modal:true, draggable:true, width:'750px', visible:false } );
					oPanel.render();

					// Add listener
					Event.on(submissionPanelLinks[i], 'click', this.showPanel, oPanel);
				}
			}


			// Chart panel
			var oPanelDiv = Dom.get('chart_panel');
			var oLink = Dom.get('chart_link');

			// Initial css does display: none, visible is used
			// by Panel but makes big gap so reverse css here
			Dom.setStyle(oPanelDiv, 'display', 'block');

			// Create panel
			var oPanel = new YAHOO.widget.Panel(oPanelDiv, { modal:true, draggable:true, width:'750px', visible:false } );
			oPanel.render();

			// Add listener
			Event.on(oLink, 'click', this.showPanel, oPanel);

		},

		showPanel: function(e, oPanel) {

			oPanel.center();
			oPanel.show();
		}
	};

	Event.onDOMReady(CMS.apps.SubmissionPanelsApp.init, CMS.apps.SubmissionPanelsApp, true);

})();
