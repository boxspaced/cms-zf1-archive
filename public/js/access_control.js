(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Access control app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.AccessControlApp = {

		init: function() {

			// Add toggle event to everyone checkbox
			var allowEveryoneChk = Dom.get('chk_allow_everyone');
			if (allowEveryoneChk != 'undefined') {
				Event.on(allowEveryoneChk, 'click', this.toggleEveryone);
			}

			// Add toggle event to authenticated users checkbox
			var authenticatedUsersChk = Dom.get('chk_allowed_groups_2');
			if (authenticatedUsersChk != 'undefined') {
				Event.on(authenticatedUsersChk, 'click', this.toggleAuthenticatedUsers);
			}

			// Add toggle event to inherit protection checkbox
			var inheritProtectionChk = Dom.get('chk_inherit_protection');
			if (inheritProtectionChk != 'undefined') {
				Event.on(inheritProtectionChk, 'click', this.toggleInheritProtection);
			}
		},

		toggleEveryone : function(e) {

			var allowEveryoneChk = Event.getTarget(e);
			var allowGroupChks = allowEveryoneChk.parentNode.getElementsByTagName('input');

			// Find group checkboxes
			for ( i = 0; i < allowGroupChks.length; i++ )
			{
				if (allowGroupChks[i].id != 'chk_allow_everyone') {
					if (allowEveryoneChk.checked == true) {
						// Uncheck groups and disable
						allowGroupChks[i].checked = false;
						allowGroupChks[i].disabled = true;
					} else {
						// Enable
						allowGroupChks[i].disabled = false;
					}
				}
			}
		},

		toggleAuthenticatedUsers : function(e) {

			var authenticatedUsersChk = Event.getTarget(e);
			var allowGroupChks = authenticatedUsersChk.parentNode.getElementsByTagName('input');

			// Find group checkboxes
			for ( i = 0; i < allowGroupChks.length; i++ )
			{
				if (allowGroupChks[i].id != 'chk_allow_everyone' && allowGroupChks[i].id != 'chk_allowed_groups_2') {
					if (authenticatedUsersChk.checked == true) {
						// Uncheck other groups and disable
						allowGroupChks[i].checked = false;
						allowGroupChks[i].disabled = true;
					} else {
						// Enable
						allowGroupChks[i].disabled = false;
					}
				}
			}
		},

		toggleInheritProtection : function(e) {

			var inheritProtectionChk = Event.getTarget(e);
			var allowChks = Dom.getNextSibling(Dom.getNextSibling(inheritProtectionChk.parentNode)).getElementsByTagName('input');

			if (inheritProtectionChk.checked == true) {
				for ( i = 0; i < allowChks.length; i++ ) {
					// Set them to parents values and disable
					if (allowChks[i].id == 'chk_allow_everyone') {
						allowChks[i].checked = controllingParentIsProtected ? false : true;
					} else {
						allowChks[i].checked = in_array(allowChks[i].id.substring(19), controllingParentAllowedGroups);
					}
					allowChks[i].disabled = true;
				}
			} else {
				for ( i = 0; i < allowChks.length; i++ ) {
					// Enable depending on state of each e.g. everyone = disable everything after,
					// authenticated_users = disable everything below
					if (allowChks[0].checked == true) { // Everyone checked!
						if (i == 0) {
							allowChks[i].disabled = false;
						} else {
							allowChks[i].disabled = true;
						}
					} else if (allowChks[1].checked == true) { // Authenticated users checked!
						if (i == 0 || i == 1) {
							allowChks[i].disabled = false;
						} else {
							allowChks[i].disabled = true;
						}
					} else {
						allowChks[i].disabled = false;
					}

				}
			}
		}
	};

	Event.onDOMReady(CMS.apps.AccessControlApp.init, CMS.apps.AccessControlApp, true);

})();
