(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Button app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.ButtonsApp = {

		init: function() {

			// Setup vars
			var i, j;


			// N.B. Have not converted some links and buttons because they have complex
			// inline onclick code, any onclick stuff gets removed on init of a button

			// Plus need to have more control over style through css, can't
			// seem to add classes


			// Find normal buttons on page
			var buttons = Dom.getElementsByClassName('button', 'input');
			for ( i = 0; i < buttons.length; i++ )
			{
				var oNewButton = new YAHOO.widget.Button(buttons[i]);
			}


			/*/ Find link buttons on page
			var linkButtons = Dom.getElementsByClassName('link-button', 'a');
			for ( i = 0; i < linkButtons.length; i++ )
			{
				var oNewButton = new YAHOO.widget.Button(linkButtons[i]);
			}*/


			// Find image browse buttons on page
			var imageBrowseButtons = Dom.getElementsByClassName('image-browse-button', 'input');
			for ( i = 0; i < imageBrowseButtons.length; i++ )
			{
				// Get id of related text box
				var oTxtBox = Dom.getPreviousSibling(imageBrowseButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(imageBrowseButtons[i]);

                                var openPopup = function() {
                                    CKFinder.popup({
                                        basePath: '/ckfinder/',
                                        selectActionFunction: function(fileUrl) {
                                            oTxtBox.value = fileUrl;
                                        },
                                        startupPath : 'Images:/assets/images'
                                    });
                                }

				// Add click event
				oNewButton.on('click', openPopup, oTxtBox);
			}


			// Find document browse buttons on page
			var documentBrowseButtons = Dom.getElementsByClassName('document-browse-button', 'input');
			for ( i = 0; i < documentBrowseButtons.length; i++ )
			{
				// Get id of related text box
				var oTxtBox = Dom.getPreviousSibling(documentBrowseButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(documentBrowseButtons[i]);

                                var openPopup = function() {
                                    CKFinder.popup({
                                        basePath: '/ckfinder/',
                                        selectActionFunction: function(fileUrl) {
                                            oTxtBox.value = fileUrl;
                                        },
                                        startupPath : 'Documents:/assets/documents/'
                                    });
                                }

				// Add click event
				oNewButton.on('click', openPopup, oTxtBox);
			}


			// Find media browse buttons on page
			var mediaBrowseButtons = Dom.getElementsByClassName('media-browse-button', 'input');
			for ( i = 0; i < mediaBrowseButtons.length; i++ )
			{
				// Get id of related text box
				var oTxtBox = Dom.getPreviousSibling(mediaBrowseButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(mediaBrowseButtons[i]);

                                var openPopup = function() {
                                    CKFinder.popup({
                                        basePath: '/ckfinder/',
                                        selectActionFunction: function(fileUrl) {
                                            oTxtBox.value = fileUrl;
                                        },
                                        startupPath : 'Media:/assets/media/'
                                    });
                                }

				// Add click event
				oNewButton.on('click', openPopup, oTxtBox);
			}


			// Find link browse buttons on page
			var linkBrowseButtons = Dom.getElementsByClassName('link-browse-button', 'input');
			for ( i = 0; i < linkBrowseButtons.length; i++ )
			{
				// Get id of related text box
				var oTxtBox = Dom.getPreviousSibling(linkBrowseButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(linkBrowseButtons[i]);

                                var openPopup = function() {
                                    CKFinder.popup({
                                        basePath: '/ckfinder/',
                                        selectActionFunction: function(fileUrl) {
                                            oTxtBox.value = fileUrl;
                                        }
                                    });
                                }

				// Add click event
				oNewButton.on('click', openPopup, oTxtBox);
			}

                        // Find flash browse buttons on page
			var flashBrowseButtons = Dom.getElementsByClassName('flash-browse-button', 'input');
			for ( i = 0; i < flashBrowseButtons.length; i++ )
			{
				// Get id of related text box
				var oTxtBox = Dom.getPreviousSibling(flashBrowseButtons[i]);

				// Create new button
				var oNewButton = new YAHOO.widget.Button(flashBrowseButtons[i]);

                                var openPopup = function() {
                                    CKFinder.popup({
                                        basePath: '/ckfinder/',
                                        selectActionFunction: function(fileUrl) {
                                            oTxtBox.value = fileUrl;
                                        },
                                        startupPath : 'Flash:/assets/flash/'
                                    });
                                }

				// Add click event
				oNewButton.on('click', openPopup, oTxtBox);
			}

			// Find menu buttons on page
			var menuButtons = Dom.getElementsByClassName('menu-button', 'input');
			for ( i = 0; i < menuButtons.length; i++ )
			{
				// Find set on page
				if ( Dom.get(menuButtons[i]) )
				{
					// Get the button
					var oButton = Dom.get(menuButtons[i]);

					// Get the select element after
					var oSelect = Dom.getNextSibling(oButton);

					// Remove first select option if it is blank
					for ( j = 0; j < oSelect.options.length; j++ )
					{
						if ( !oSelect.options[j].value && !oSelect.options[j].text )
						{
							oSelect.remove(j);
						}
					}

					// Create new button
					var oNewButton = new YAHOO.widget.Button(oButton, { type: 'menu', menu: oSelect });
				}
			}


			// Find publishing index menu buttons on page
			var menuButtons = Dom.getElementsByClassName('publishing-index-preview-menu-button', 'input');
			for ( i = 0; i < menuButtons.length; i++ )
			{
				// Find set on page
				if ( Dom.get(menuButtons[i]) )
				{
					// Get the button
					var oButton = Dom.get(menuButtons[i]);

					// Get the select element after
					var oSelect = Dom.getNextSibling(oButton);

					// Remove first select option if it is blank
					for ( j = 0; j < oSelect.options.length; j++ )
					{
						if ( !oSelect.options[j].value && !oSelect.options[j].text )
						{
							oSelect.remove(j);
						}
					}

					// Create new button
					var oNewButton = new YAHOO.widget.Button(oButton, { type: 'menu', menu: oSelect });

					// Event listener
					var onclick = function(type, args) {
						var menuItem = args[1];
						//alert(menuItem.value);
						open_preview(menuItem.value);
					}

					//  Add a 'click' event listener for the button's menu
					oNewButton.getMenu().subscribe('click', onclick);
				}
			}


			// Find publishing index link buttons on page
			var linkButtons = Dom.getElementsByClassName('link-button', 'a');
			for ( i = 0; i < linkButtons.length; i++ )
			{
				var oNewButton = new YAHOO.widget.Button(linkButtons[i], {
					onclick : {
						fn: linkButtons[i].onclick
					}
				});
			}
		}
	};

	Event.onDOMReady(CMS.apps.ButtonsApp.init, CMS.apps.ButtonsApp, true);

})();
