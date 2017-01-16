(function() {

    var CMS = window.CMS || {};
    CMS.apps = function() {};

    var Dom = YAHOO.util.Dom;
    var Event = YAHOO.util.Event;
    var DDM = YAHOO.util.DragDropMgr;


    whichItemBuilder = function(o) {

        // Setup vars
        var i;
        var itemBuilderName = false;
        var possibleItemBuilderNames = new Array('ib');

        // Which item builder, if more than one
        for ( i = 0; i < possibleItemBuilderNames.length; i++ )
        {
            if ( o.id.split('_')[0] == possibleItemBuilderNames[i] )
            {
                // Item builder name found
                var itemBuilderName = possibleItemBuilderNames[i];
            }
        }

        return itemBuilderName;
    }

    setOrderBys = function(oItemBuilder) {

        // Setup vars
        var i, j;

        if ( whichItemBuilder(oItemBuilder) )
        {
            // Item builder name
            var itemBuilderName = whichItemBuilder(oItemBuilder);

            // Find lists within item builder
            var itemBuilderLists = oItemBuilder.getElementsByTagName('ul');

            for ( i = 0; i < itemBuilderLists.length; i++ )
            {
                if ( itemBuilderLists[i].className.indexOf('ib-draglist') != -1 )
                {
                    // Find items within list
                    var listItems = itemBuilderLists[i].getElementsByTagName('li');

                    for ( j = 0; j < listItems.length; j++ )
                    {
                        if ( listItems[j].className.indexOf('ib-draglist-item') != -1 )
                        {
                            // Get suffix
                            var suffix = listItems[j].id.substring(itemBuilderName.length+10, listItems[j].id.length);

                            // Set order_by hidden element
                            Dom.get('parts-' + suffix + '-order-by').value = j+1;
                        }
                    }
                }
            }
        }
    }


    //////////////////////////////////////////////////////////////////////////////
    // Item builder app
    //////////////////////////////////////////////////////////////////////////////

    CMS.apps.IBApp = {

        init: function() {

            // Setup vars
            var i, j, k, l;
            var partTabSets = new Array();

            // Find item builders on page
            var itemBuilders = Dom.getElementsByClassName('ib-container', 'div');

            // Create part tabs, draggable items and targets
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                // Create part tabs
                partTabSets[i] = new YAHOO.widget.TabView(itemBuilders[i]);
                partTabSets[i].addListener('activeTabChange', this.partTabChange, partTabSets[i]);


                // Find lists within item builder
                var itemBuilderLists = itemBuilders[i].getElementsByTagName('ul');

                for ( j = 0; j < itemBuilderLists.length; j++ )
                {
                    if ( itemBuilderLists[j].className.indexOf('ib-draglist') != -1 )
                    {
                        // Make list a target area
                        new YAHOO.util.DDTarget(itemBuilderLists[j], 'group' + i);
                    }

                    // Find items within list
                    var listItems = itemBuilderLists[j].getElementsByTagName('li');

                    for ( k = 0; k < listItems.length; k++ )
                    {
                        if ( listItems[k].className.indexOf('ib-draglist-item') != -1 )
                        {
                            // Make list item draggable
                            new CMS.apps.IBList(listItems[k], 'group' + i);
                        }
                    }
                }
            }

            // Set order_by values to catch any old order_by sequences (wrong/any zeros)
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                setOrderBys(itemBuilders[i]);
            }

            // Add listeners to part delete icons
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                // Find imgs within item builder
                var itemBuilderImgs = itemBuilders[i].getElementsByTagName('img');

                for ( j = 0; j < itemBuilderImgs.length; j++ )
                {
                    if ( itemBuilderImgs[j].className == 'ib-delete-part-icon' )
                    {
                        Event.on(itemBuilderImgs[j], 'click', this.deletePart, partTabSets[i]);
                    }
                }
            }

            // Add listeners to add part selects
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                // Find selects within item builder
                var itemBuilderSelects = itemBuilders[i].getElementsByTagName('select');

                for ( j = 0; j < itemBuilderSelects.length; j++ )
                {
                    // Add part selects
                    if ( itemBuilderSelects[j].className == 'ib-add-part-select' )
                    {
                        Event.on(itemBuilderSelects[j], 'change', this.addParts, itemBuilderSelects[j]);
                    }
                }
            }

            // Make first tab active
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                // Get all tabs
                var allTabs = partTabSets[i].get('tabs');

                // Find lists within item builder
                var itemBuilderLists = itemBuilders[i].getElementsByTagName('ul');

                for ( j = 0; j < itemBuilderLists.length; j++ )
                {
                    // Find items within list
                    var listItems = itemBuilderLists[j].getElementsByTagName('li');

                    // Find tab index of first list item that hasn't been deleted
                    var tabsWillBeDisplayed = false;
                    var firstTabIndexFound = false;
                    for ( k = 0; k < listItems.length; k++ )
                    {
                        if ( Dom.getStyle(listItems[k], 'display') != 'none' )
                        {
                            // Some tabs will be displayed
                            var tabsWillBeDisplayed = true;

                            // Go through tabs and get id
                            for ( l = 0; l < allTabs.length; l++ )
                            {
                                if ( listItems[k].id == allTabs[l].get('id') )
                                {
                                    // Get index
                                    var firstTabIndexFound = true;
                                    var firstTabIndex = partTabSets[i].getTabIndex(allTabs[l]);
                                }
                            }

                            // Found first so stop looping
                            break;
                        }
                    }

                    // Make first tab active (if there are any tabs)
                    if ( firstTabIndexFound )
                    {
                        partTabSets[i].set('activeIndex', firstTabIndex);
                    }
                }
            }


            // If no tabs displayed, hide all parts content
            for ( i = 0; i < itemBuilders.length; i++ )
            {
                // Find lists within item builder
                var itemBuilderLists = itemBuilders[i].getElementsByTagName('ul');

                for ( j = 0; j < itemBuilderLists.length; j++ )
                {
                    // Find items within list
                    var listItems = itemBuilderLists[j].getElementsByTagName('li');

                    // Will any tabs be displayed
                    var tabsWillBeDisplayed = false;
                    for ( k = 0; k < listItems.length; k++ )
                    {
                        if ( Dom.getStyle(listItems[k], 'display') != 'none' )
                        {
                            // Some tabs will be displayed
                            var tabsWillBeDisplayed = true;

                            // Found at least one so stop looping
                            break;
                        }
                    }

                    if ( !tabsWillBeDisplayed )
                    {
                        // Find part divs
                        var itemBuilderPartDivs = itemBuilders[i].getElementsByTagName('div');

                        for ( j = 0; j < itemBuilderPartDivs.length; j++ )
                        {
                            if ( itemBuilderPartDivs[j].className == 'ib-part' )
                            {
                                // Hide part contents
                                Dom.setStyle(itemBuilderPartDivs[j], 'opacity', 0);
                            }
                        }
                    }
                }
            }
        },

        partTabChange: function(e, partTabSet) {

            // Setup vars
            var i;

            // Get new tab
            var newActiveTab = partTabSet.get('activeTab');
            var newActiveTabId = newActiveTab.get('id');


            //
            // Find list items position in list
            //

            var oListItem = Dom.get(newActiveTabId);
            var oList = oListItem.parentNode;

            // Get items
            var listItems = oList.getElementsByTagName('li');

            for ( i = 0; i < listItems.length; i++ )
            {
                if ( listItems[i].id == oListItem.id )
                {
                    var position = i+1;
                }
            }

            // Set selected-part hidden input
            Dom.get('selected-part').value = position;
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
            Dom.get('partial').value = 1;
            Dom.getAncestorByTagName(oAddPartsSelect, 'form').submit();
        },

        deletePart: function(e, partTabSet) {

            // Setup vars
            var i, j, k;


            // Get part to delete and fade it
            var oPartDiv = Dom.getAncestorByTagName(Event.getTarget(e), 'div');

            // Fade part div
            var attributes = {
                opacity: {
                    to: 0
                }
            };

            var anim = new YAHOO.util.Anim(oPartDiv, attributes, 1, YAHOO.util.Easing.easeOut);
            anim.animate();


            // Get list item to delete
            var oListItem = Dom.get(partTabSet.get('activeTab').get('id'));

            if ( whichItemBuilder(oListItem) )
            {
                // Item builder name
                var itemBuilderName = whichItemBuilder(oListItem);

                // Set hidden delete input
                Dom.getNextSibling(Event.getTarget(e)).value = 1;


                // Fade list item
                var attributes = {
                    opacity: {
                        to: 0
                    }
                };

                var anim = new YAHOO.util.Anim(oListItem, attributes, 1, YAHOO.util.Easing.easeOut);

                anim.onComplete.subscribe(function() {

                    // Completely hide list item
                    Dom.setStyle(oListItem, 'display', 'none');

                    // Get list
                    var oList = oListItem.parentNode;

                    // Get all items
                    var listItems = oList.getElementsByTagName('li');

                    // Get all tabs
                    var allTabs = partTabSet.get('tabs');

                    // Find tab index of first list item that hasn't been deleted
                    var firstTabIndexFound = false;
                    for ( i = 0; i < listItems.length; i++ )
                    {
                        if ( Dom.getStyle(listItems[i], 'display') != 'none' )
                        {
                            // Go through tabs and get id
                            for ( j = 0; j < allTabs.length; j++ )
                            {
                                if ( listItems[i].id == allTabs[j].get('id') )
                                {
                                    // Get index
                                    var firstTabIndexFound = true;
                                    var firstTabIndex = partTabSet.getTabIndex(allTabs[j]);
                                }
                            }

                            // Found first so stop looping
                            break;
                        }
                    }

                    // Make first tab active (if there are any tabs)
                    if ( firstTabIndexFound )
                    {
                        partTabSet.set('activeIndex', firstTabIndex);
                    }
                });

                anim.animate();
            }
        }
    };


    //////////////////////////////////////////////////////////////////////////////
    // Custom drag and drop implementation
    //////////////////////////////////////////////////////////////////////////////

    CMS.apps.IBList = function(id, sGroup, config) {

        CMS.apps.IBList.superclass.constructor.call(this, id, sGroup, config);

        this.logger = this.logger || YAHOO;
        var el = this.getDragEl();
        Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

        this.goingUp = false;
        this.lastY = 0;
    };

    YAHOO.extend(CMS.apps.IBList, YAHOO.util.DDProxy, {

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
            var oItemBuilder = Dom.getAncestorByClassName(srcEl, 'ib-container', 'div');

            setOrderBys(oItemBuilder);
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

    Event.onDOMReady(CMS.apps.IBApp.init, CMS.apps.IBApp, true);

})();
