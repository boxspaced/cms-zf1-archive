(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Overview button app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.OverviewButtonsApp = {

		init: function() {

			// Setup vars
			var i, j;


			// Find add beneath menu buttons on page
			var addToMenuButtons = Dom.getElementsByClassName('add-to-menu-button', 'input');

			for ( i = 0; i < addToMenuButtons.length; i++ )
			{
				// Get the div element after
				var oDiv = Dom.getNextSibling(addToMenuButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(addToMenuButtons[i], { type: 'menu', menu: oDiv });
			}


			// Array of possible set ids
			var possibleSets = new Array('btn_add_to_container_set', 'btn_add_at_top_level_set');

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

	Event.onDOMReady(CMS.apps.OverviewButtonsApp.init, CMS.apps.OverviewButtonsApp, true);

})();
