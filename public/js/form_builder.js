(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	var DDM = YAHOO.util.DragDropMgr;


	whichFormBuilder = function(o) {

		// Setup vars
		var i;
		var formBuilderName = false;
		var possibleFormBuilderNames = new Array('fb', 'fb2');

		// Which form builder, if more than one
		for ( i = 0; i < possibleFormBuilderNames.length; i++ )
		{
			if ( o.id.split('_')[0] == possibleFormBuilderNames[i] )
			{
				// Form builder name found
				var formBuilderName = possibleFormBuilderNames[i];
			}
		}

		return formBuilderName;
	}

	setOrderBys = function(oFormBuilder) {

		// alert('Order by func called');

		// Setup vars
		var i, j, k;

		if ( whichFormBuilder(oFormBuilder) )
		{
			// Form builder name
			var formBuilderName = whichFormBuilder(oFormBuilder);

			// Find lists within form builder
			var formBuilderLists = oFormBuilder.getElementsByTagName('ul');

			for ( i = 0; i < formBuilderLists.length; i++ )
			{
				if ( formBuilderLists[i].className == 'fb-draglist' )
				{
					// Find items within list
					var listItems = formBuilderLists[i].getElementsByTagName('li');

					for ( j = 0, listItemCount = 1; j < listItems.length; j++ )
					{
						if ( listItems[j].className == 'fb-draglist-item' )
						{
							// Get suffix
							var suffix = listItems[j].id.substring(formBuilderName.length+10, listItems[j].id.length);

							// Get inputs within list item
							var inputs = listItems[j].getElementsByTagName('input');

							for ( k = 0; k < inputs.length; k++ )
							{
								if ( inputs[k].id == 'hid_order_by' + suffix )
								{
									inputs[k].value = listItemCount;
								}
							}

							listItemCount++;
						}
					}
				}
			}
		}
	}

	setMoveTo = function(oElement) {

		// alert('Move to func called');

		// Setup vars
		var i;

		// alert(oElement.id);

		if ( whichFormBuilder(oElement) )
		{
			// Form builder name
			var formBuilderName = whichFormBuilder(oElement);

			// Get element suffix
			var elementSuffix = oElement.id.substring(formBuilderName.length+10, oElement.id.length);

			// Does move_to input exist for item we are moving
			if ( Dom.get('hid_move_to' + elementSuffix) )
			{
				// Because a move_to input exists, it means we are on the form builder
				// for 'forms' (done through workflow). Therefore suffix will be in following
				// format pp_ee_ll and part bit at position 0 in suffix array. Should really
				// standardize suffixes to ee_pp_ll. Can still have 'element' or 'part_element'
				// in types with part elements but have a underscore between e.g. ...part_element_ee_pp_ll.

				// Split element suffix
				var elementSuffixArray = elementSuffix.split('_');
				var elementSuffixPartBit = elementSuffixArray[0];

				// Get the list the item has been dropped in
				var oNewList = oElement.parentNode;

				// Get new list suffix
				var newListSuffix = oNewList.id.substring(formBuilderName.length+5, oNewList.id.length);
				var newListSuffixArray = newListSuffix.split('_');
				var newListSuffixPartBit = newListSuffixArray[0];

				// Get new list part id
				var newListPartId = Dom.get('hid_part' + newListSuffix).value;

				if ( elementSuffixPartBit != newListSuffixPartBit )
				{
					/*/ List item not in original list, change move_to hidden element
					Dom.get('hid_move_to' + elementSuffix).value = newListPartId;*/

					// Get inputs within list item
					var inputs = oElement.getElementsByTagName('input');

					for ( i = 0; i < inputs.length; i++ )
					{
						if ( inputs[i].id == 'hid_move_to' + elementSuffix )
						{
							inputs[i].value = newListPartId;
						}
					}
				}
				else
				{
					/*/ List item staying in original list or moved back to original list, change move_to hidden element to zero
					Dom.get('hid_move_to' + elementSuffix).value = 0;*/

					// Get inputs within list item
					var inputs = oElement.getElementsByTagName('input');

					for ( i = 0; i < inputs.length; i++ )
					{
						if ( inputs[i].id == 'hid_move_to' + elementSuffix )
						{
							inputs[i].value = 0;
						}
					}
				}
			}
		}
	}

	moveTranslationElements = function(oElement) {

		// Setup vars
		var h, i, j, k, l, m;

		// Get element id less language bit and language bit
		var elementIdLessLang = oElement.id.substring(0, oElement.id.length-2);

		// Get the list element was dropped in
		var oList = oElement.parentNode;

		// Get the list id, less language bit, of the list that the element is now in
		var listIdLessLang = oList.id.substring(0, oList.id.length-2);

		// Find items within list
		var listItems = oList.getElementsByTagName('li');

		// Get position in list (avoid tabs in element editing bit)
		for ( h = 0, draglistItemCount = 0; h < listItems.length; h++ )
		{
			if ( listItems[h].className == 'fb-draglist-item' )
			{
				if ( listItems[h].id.substring(0, listItems[h].id.length-2) == elementIdLessLang )
				{
					var elementPosition = draglistItemCount;
					break;
				}

				draglistItemCount++;
			}
		}

		if ( whichFormBuilder(oElement) )
		{
			// Form builder name
			var formBuilderName = whichFormBuilder(oElement);

			// Find all form builders on page
			var formBuilders = Dom.getElementsByClassName('fb-container', 'div');

			for ( i = 0; i < formBuilders.length; i++ )
			{
				// Is form builder relevant to element being moved
				if ( whichFormBuilder(formBuilders[i]) == formBuilderName )
				{
					// Find lists within form builder
					var formBuilderLists = formBuilders[i].getElementsByTagName('ul');

					for ( j = 0; j < formBuilderLists.length; j++ )
					{
						// Break list loop?
						if ( break_list_loop )
						{
							break;
						}

						// Only NON draggable lists
						if ( formBuilderLists[j].className == 'fb-list' )
						{
							// Get the list id less language bit
							var nonDragListIdLessLang = formBuilderLists[j].id.substring(0, formBuilderLists[j].id.length-2);

							// Get items within NON draggable list
							var listItems = formBuilderLists[j].getElementsByTagName('li');

							// Do break?
							var break_list_loop = false;

							for ( k = 0; k < listItems.length; k++ )
							{
								// Only NON draggable list items (avoid tabs in element editing bit)
								if ( listItems[k].className == 'fb-list-item' )
								{
									// Get the list items id less language bit and the language bit
									var listItemIdLessLang = listItems[k].id.substring(0, listItems[k].id.length-2);
									var listItemIdLang = listItems[k].id.substring(listItems[k].id.length-2, listItems[k].id.length);

									if ( listItemIdLessLang == elementIdLessLang )
									{
										// Find NON draggable list equivalent of draggable list that the element was put in
										var oMoveToList = Dom.get(listIdLessLang + listItemIdLang);

										// Find items within list
										var oMoveToAllListItems = oMoveToList.getElementsByTagName('li');

										// Create new/proper array of list items (avoid tabs in element editing bit)
										var oMoveToListItems = new Array();

										for ( m = 0, listItemCount = 0; m < oMoveToAllListItems.length; m++ )
										{
											if ( oMoveToAllListItems[m].className == 'fb-list-item' )
											{
												// Add to array
												oMoveToListItems[listItemCount] = oMoveToAllListItems[m];

												listItemCount++;
											}
										}

// alert(elementPosition+' - '+oMoveToListItems.length+' - '+listIdLessLang+' - '+nonDragListIdLessLang);

										if ( elementPosition == 0 )
										{
											// alert('First...');

											if ( oMoveToListItems.length == 0 )
											{
												// First and only element
												oMoveToList.appendChild(listItems[k]);

												// alert('First and only element');
											}
											else
											{
												// First element
												Dom.insertBefore(listItems[k], oMoveToListItems[0]);

												// alert('First element');
											}
										}
										else if ( (listIdLessLang == nonDragListIdLessLang && elementPosition == oMoveToListItems.length-1) || (listIdLessLang != nonDragListIdLessLang && elementPosition == oMoveToListItems.length) )
										{
											// Last element
											Dom.insertAfter(listItems[k], oMoveToListItems[oMoveToListItems.length-1]);

											// alert('Last element');
										}
										else
										{
											// alert('Other...');

											for ( l = 0; l < oMoveToListItems.length; l++ )
											{
												if ( elementPosition == l )
												{
													// Insert list item into new list

													if ( listItems[k] === oMoveToListItems[l-1] )
													{
														// Trying to insert after self, so insert before
														// one ahead of required position
														Dom.insertBefore(listItems[k], oMoveToListItems[l+1]);

														// alert('Trying to insert after self');
													}
													else
													{
														// Insert after one before position
														Dom.insertAfter(listItems[k], oMoveToListItems[l-1]);

														// alert('Insert after one before position');
													}
												}
											}
										}

										var break_list_loop = true;
										break;
									}
								}
							}
						}
					}
				}
			}
		}
	}


	//////////////////////////////////////////////////////////////////////////////
	// Form builder app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.FBApp = {

		init: function() {

			// Setup vars
			var i, j, k, l;

			// Find form builders on page
			var formBuilders = Dom.getElementsByClassName('fb-container', 'div');


			// Create draggable items and targets + add events to icons
			for ( i = 0; i < formBuilders.length; i++ )
			{
				// Find lists within form builder
				var formBuilderLists = formBuilders[i].getElementsByTagName('ul');

				for ( j = 0; j < formBuilderLists.length; j++ )
				{
					if ( formBuilderLists[j].className == 'fb-draglist' )
					{
						// Make list a target area
						var ddTarget = new YAHOO.util.DDTarget(formBuilderLists[j], 'group' + i);
					}

					// Find items within list
					var listItems = formBuilderLists[j].getElementsByTagName('li');

					for ( k = 0; k < listItems.length; k++ )
					{
						if ( listItems[k].className == 'fb-draglist-item' )
						{
							// Make list item draggable
							var ddListItem = new CMS.apps.FBList(listItems[k], 'group' + i);
						}

						// Get list items children
						var listItemsChildren = listItems[k].childNodes;

						// Add listeners to element images/icons
						for ( l = 0; l < listItemsChildren.length; l++ )
						{
							if ( listItemsChildren[l].nodeName.toLowerCase() == 'img' )
							{
								// Show element when image is clicked
								if ( listItemsChildren[l].className == 'fb-element-image' )
								{
									if ( whichFormBuilder(listItems[k]) )
									{
										// Form builder name
										var formBuilderName = whichFormBuilder(listItems[k]);

										// Get element to show
										var oElement = Dom.get(formBuilderName + '_element' + listItems[k].id.substring(formBuilderName.length+10, listItems[k].id.length));

										// Add listener
										Event.on(listItemsChildren[l], 'click', this.toggleElement, oElement);
									}
								}

								// Make handle
								if ( listItems[k].className == 'fb-draglist-item' && listItemsChildren[l].className == 'fb-move-element-icon' )
								{
									ddListItem.setHandleElId(listItemsChildren[l].id);
								}

								// Delete element when image is clicked
								if ( listItemsChildren[l].className == 'fb-delete-element-icon' )
								{
									Event.on(listItemsChildren[l], 'click', this.deleteElement, listItemsChildren[l]);
								}
							}
						}
					}
				}
			}

			// Set order_by values to catch any old order_by sequences (wrong/any zeros)
			for ( i = 0; i < formBuilders.length; i++ )
			{
				setOrderBys(formBuilders[i]);
			}

			// Add listeners to part delete icons
			for ( i = 0; i < formBuilders.length; i++ )
			{
				// Find imgs within form builder
				var formBuilderImgs = formBuilders[i].getElementsByTagName('img');

				for ( j = 0; j < formBuilderImgs.length; j++ )
				{
					if ( formBuilderImgs[j].className == 'fb-delete-part-icon' )
					{
						Event.on(formBuilderImgs[j], 'click', this.deletePart, formBuilderImgs[j]);
					}
				}
			}

			// Add listeners to add part and element selects
			for ( i = 0; i < formBuilders.length; i++ )
			{
				// Find selects within form builder
				var formBuilderSelects = formBuilders[i].getElementsByTagName('select');

				for ( j = 0; j < formBuilderSelects.length; j++ )
				{
					// Add part selects
					if ( formBuilderSelects[j].className == 'fb-add-part-select' )
					{
						Event.on(formBuilderSelects[j], 'change', this.addParts, formBuilderSelects[j]);
					}

					// Add element selects
					if ( formBuilderSelects[j].className == 'fb-add-element-select' )
					{
						Event.on(formBuilderSelects[j], 'change', this.addElements, formBuilderSelects[j]);
					}
				}
			}

			// Look for validation errors and show them next to element
			var elementDivs = Dom.getElementsByClassName('fb-element', 'div');

			for ( i = 0; i < elementDivs.length; i++ )
			{
				if ( whichFormBuilder(elementDivs[i]) )
				{
					// Form builder name
					var formBuilderName = whichFormBuilder(elementDivs[i]);


					// Get list item to append messages to
					var oListItem = Dom.get(formBuilderName + '_list_item' + elementDivs[i].id.substring(formBuilderName.length+8, elementDivs[i].id.length));

					// Find validation divs within element div
					var childDivs = elementDivs[i].getElementsByTagName('div');

					for ( j = 0; j < childDivs.length; j++ )
					{
						// Get validation error divs
						if ( childDivs[j].className.indexOf('validation-error') != -1 )
						{
							// Copy message text node and add <br />
							var oMessageCopy = childDivs[j].cloneNode(true);
							Dom.removeClass(oMessageCopy, 'line-up');
							oMessageCopy.appendChild(document.createElement('br'));

							// Insert before list items first child
							Dom.insertBefore(oMessageCopy, oListItem.firstChild);
						}
					}
				}
			}
		},

		addParts: function(e, oAddPartsSelect) {

			// Set hidden input
			var oNumNewPartsInput = Dom.getNextSibling(oAddPartsSelect);
			var currentValue = parseInt(oNumNewPartsInput.value, 10);
			var add = parseInt(oAddPartsSelect.options[oAddPartsSelect.selectedIndex].value, 10);
			oNumNewPartsInput.value = currentValue+add;

			// Submit form
			if ( typeof WPro != 'undefined' )
			{
				WPro.updateAll('prepareSubmission');
			}
			Dom.getAncestorByTagName(oAddPartsSelect, 'form').submit();
		},

		deletePart: function(e, oDeleteIcon) {

			// Setup vars
			var i, j;

			// Set hidden delete input
			Dom.getNextSibling(oDeleteIcon).value = 1;

			// Get part to delete
			var oPartDiv = Dom.getAncestorByTagName(oDeleteIcon, 'div');


			// Fade part div
			var attributes = {
			opacity: { to: 0 }
			};

			var anim = new YAHOO.util.Anim(oPartDiv, attributes, 1, YAHOO.util.Easing.easeOut);

			anim.onComplete.subscribe(function() {

				// Completely hide part div
				Dom.setStyle(oPartDiv, 'display', 'none');


				//
				// Hide part translations
				//

				// Get part id less language bit
				var partIdLessLang = oPartDiv.id.substring(0, oPartDiv.id.length-2);

				if ( whichFormBuilder(oPartDiv) )
				{
					// Form builder name
					var formBuilderName = whichFormBuilder(oPartDiv);

					// Find all form builders on page
					var formBuilders = Dom.getElementsByClassName('fb-container', 'div');

					for ( i = 0; i < formBuilders.length; i++ )
					{
						// Is form builder relevant to part being deleted
						if ( whichFormBuilder(formBuilders[i]) == formBuilderName )
						{
							// Find divs within form builder
							var formBuilderDivs = formBuilders[i].getElementsByTagName('div');

							for ( j = 0; j < formBuilderDivs.length; j++ )
							{
								if ( formBuilderDivs[j].className == 'fb-part' )
								{
									// Get the parts id less language bit and the language bit
									var translationPartIdLessLang = formBuilderDivs[j].id.substring(0, formBuilderDivs[j].id.length-2);
									var translationPartIdLang = formBuilderDivs[j].id.substring(formBuilderDivs[j].id.length-2, formBuilderDivs[j].id.length);

									if ( translationPartIdLessLang == partIdLessLang )
									{
										Dom.setStyle(formBuilderDivs[j], 'display', 'none');
									}
								}
							}
						}
					}
				}
			});

			anim.animate();
		},

		addElements: function(e, oAddElementsSelect) {

			// Set hidden input
			var oNumNewElementsInput = Dom.getNextSibling(oAddElementsSelect);
			var currentValue = parseInt(oNumNewElementsInput.value, 10);
			var add = parseInt(oAddElementsSelect.options[oAddElementsSelect.selectedIndex].value, 10);
			oNumNewElementsInput.value = currentValue+add;

			// Submit form
			if ( typeof WPro != 'undefined' )
			{
				WPro.updateAll('prepareSubmission');
			}
			Dom.getAncestorByTagName(oAddElementsSelect, 'form').submit();
		},

		toggleElement: function(e, oElement) {

			if ( Dom.getStyle(oElement, 'display') == 'none' )
			{
				Dom.setStyle(oElement, 'display', 'block');
			}
			else if ( Dom.getStyle(oElement, 'display') == 'block' )
			{
				Dom.setStyle(oElement, 'display', 'none');
			}
		},

		deleteElement: function(e, oDeleteIcon) {

			// Setup vars
			var h, i, j, k;

			// Get list item
			var oListItem = oDeleteIcon.parentNode;

			if ( whichFormBuilder(oListItem) )
			{
				// Form builder name
				var formBuilderName = whichFormBuilder(oListItem);


				// Get suffix
				var suffix = oListItem.id.substring(formBuilderName.length+10, oListItem.id.length);

				/*/ Set delete hidden element
				Dom.get('hid_delete' + suffix).value = 1;*/

				// Get inputs within list item
				var inputs = oListItem.getElementsByTagName('input');

				for ( h = 0; h < inputs.length; h++ )
				{
					if ( inputs[h].id == 'hid_delete' + suffix )
					{
						inputs[h].value = 1;
					}
				}


				//
				// Fade list item
				//

				var attributes = {
				opacity: { to: 0 }
				};

				var anim = new YAHOO.util.Anim(oListItem, attributes, 1, YAHOO.util.Easing.easeOut);

				anim.onComplete.subscribe(function() {

					// Completely hide list item
					Dom.setStyle(oListItem, 'display', 'none');


					//
					// Hide element translations
					//

					// Get list item id less language bit
					var listItemIdLessLang = oListItem.id.substring(0, oListItem.id.length-2);

					// Find all form builders on page
					var formBuilders = Dom.getElementsByClassName('fb-container', 'div');

					for ( i = 0; i < formBuilders.length; i++ )
					{
						// Is form builder relevant to element being deleted
						if ( whichFormBuilder(formBuilders[i]) == formBuilderName )
						{
							// Find lists within form builder
							var formBuilderLists = formBuilders[i].getElementsByTagName('ul');

							for ( j = 0; j < formBuilderLists.length; j++ )
							{
								// Only NON draggable lists
								if ( formBuilderLists[j].className == 'fb-list' )
								{
									// Get items within NON draggable list
									var listItems = formBuilderLists[j].getElementsByTagName('li');

									for ( k = 0; k < listItems.length; k++ )
									{
										// Get the list items id less language bit and the language bit
										var translationListItemIdLessLang = listItems[k].id.substring(0, listItems[k].id.length-2);
										var translationListItemIdLang = listItems[k].id.substring(listItems[k].id.length-2, listItems[k].id.length);

										if ( translationListItemIdLessLang == listItemIdLessLang )
										{
											// Hide list item in builder bit
											Dom.setStyle(listItems[k], 'display', 'none');
										}
									}
								}
							}
						}
					}
				});

				anim.animate();
			}
		}
	};


	//////////////////////////////////////////////////////////////////////////////
	// Custom drag and drop implementation
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.FBList = function(id, sGroup, config) {

		CMS.apps.FBList.superclass.constructor.call(this, id, sGroup, config);

		this.logger = this.logger || YAHOO;
		var el = this.getDragEl();
		Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

		this.goingUp = false;
		this.lastY = 0;
	};

	YAHOO.extend(CMS.apps.FBList, YAHOO.util.DDProxy, {

		startDrag: function(x, y) {

			this.logger.log(this.id + " startDrag");

			// make the proxy look like the source element
			var dragEl = this.getDragEl();
			var clickEl = this.getEl();
			Dom.setStyle(clickEl, "visibility", "hidden");

			dragEl.innerHTML = clickEl.innerHTML;

			Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
			Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
			Dom.setStyle(dragEl, "border", "2px solid gray");
		},

		endDrag: function(e) {

			var srcEl = this.getEl();
			var proxy = this.getDragEl();

			// Show the proxy element and animate it to the src element's location
			Dom.setStyle(proxy, "visibility", "");
			var a = new YAHOO.util.Motion(
			proxy, {
			points: {
			to: Dom.getXY(srcEl)
			}
			},
			0.2,
			YAHOO.util.Easing.easeOut
			)
			var proxyid = proxy.id;
			var thisid = this.id;

			// Hide the proxy and show the source element when finished with the animation
			a.onComplete.subscribe(function() {
			Dom.setStyle(proxyid, "visibility", "hidden");
			Dom.setStyle(thisid, "visibility", "");
			});
			a.animate();


			// Reset all order_by values and set the move_to value of the element being moved
			var oFormBuilder = Dom.getAncestorByClassName(srcEl, 'fb-container', 'div');

			setOrderBys(oFormBuilder);
			setMoveTo(srcEl);
			moveTranslationElements(srcEl);
		},

		onDragDrop: function(e, id) {

			// If there is one drop interaction, the li was dropped either on the list,
			// or it was dropped on the current location of the source element.
			if (DDM.interactionInfo.drop.length === 1) {

				// The position of the cursor at the time of the drop (YAHOO.util.Point)
				var pt = DDM.interactionInfo.point;

				// The region occupied by the source element at the time of the drop
				var region = DDM.interactionInfo.sourceRegion;

				// Check to see if we are over the source element's location.  We will
				// append to the bottom of the list once we are sure it was a drop in
				// the negative space (the area of the list without any list items)
				if (!region.intersect(pt)) {
				var destEl = Dom.get(id);
				var destDD = DDM.getDDById(id);
				destEl.appendChild(this.getEl());
				destDD.isEmpty = false;
				DDM.refreshCache();
				}

			}
		},

		onDrag: function(e) {

			// Keep track of the direction of the drag for use during onDragOver
			var y = Event.getPageY(e);

			if (y < this.lastY) {
			this.goingUp = true;
			} else if (y > this.lastY) {
			this.goingUp = false;
			}

			this.lastY = y;
		},

		onDragOver: function(e, id) {

			var srcEl = this.getEl();
			var destEl = Dom.get(id);

			// We are only concerned with list items, we ignore the dragover
			// notifications for the list.
			if (destEl.nodeName.toLowerCase() == "li") {

				var orig_p = srcEl.parentNode;
				var p = destEl.parentNode;

				if (this.goingUp) {
					p.insertBefore(srcEl, destEl); // insert above
				} else {
					p.insertBefore(srcEl, destEl.nextSibling); // insert below
				}

				DDM.refreshCache();
			}
		}
	});

	Event.onDOMReady(CMS.apps.FBApp.init, CMS.apps.FBApp, true);

})();
