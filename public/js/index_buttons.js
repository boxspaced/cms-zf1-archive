(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Index button app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.IndexButtonsApp = {

		init: function() {

			// Setup vars
			var i;


			// Find link buttons on page
			var linkButtons = Dom.getElementsByClassName('link-button', 'a');

			for ( i = 0; i < linkButtons.length; i++ )
			{
				var oNewButton = new YAHOO.widget.Button(linkButtons[i]);
			}


			// Array of possible set ids
			var possibleSets = new Array('btn_author_set');

			for ( i = 0; i < possibleSets.length; i++ )
			{
				// Find set on page
				if ( Dom.get(possibleSets[i]) )
				{
					// Get the button
					var oButton = Dom.get(possibleSets[i]);

					// Get the div element after
					var oDiv = Dom.getNextSibling(oButton);

					// Create new button
					var oNewButton = new YAHOO.widget.Button(oButton, { type: 'menu', menu: oDiv });
				}
			}
		}
	};

	Event.onDOMReady(CMS.apps.IndexButtonsApp.init, CMS.apps.IndexButtonsApp, true);

})();
