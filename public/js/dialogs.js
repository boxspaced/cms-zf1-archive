(function() {

	var CMS = window.CMS || {};
	CMS.apps = function() {};

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;


	//////////////////////////////////////////////////////////////////////////////
	// Dialog app
	//////////////////////////////////////////////////////////////////////////////

	CMS.apps.DialogApp = {

		init: function() {

			var handleSubmit = function() {
				this.doSubmit();
			};
			var handleCancel = function() {
				this.cancel();
			};
			var dialog = new YAHOO.widget.Dialog('send_back_dialog', {
				width: '30em',
				fixedcenter: true,
				modal: true,
				visible: false,
				constraintoviewport: true,
				postmethod: 'form',
				buttons: [{
					text: 'Submit',
					handler: handleSubmit,
					isDefault: true
				}, {
					text: 'Cancel',
					handler: handleCancel
				}]
			});
			dialog.render();

			var sendBackLinks = Dom.getElementsByClassName('workflow-send-back', 'a');
			for ( i = 0; i < sendBackLinks.length; i++ )
			{
				var args = {
					dialog: dialog,
					link: sendBackLinks[i]
				};
				Event.on(sendBackLinks[i], 'click', this.showDialog, args);
			}

			var notesIcons = Dom.getElementsByClassName('notes-icon', 'img');
			for (i = 0; i < notesIcons.length; i++) {
				var panel = new YAHOO.widget.Panel(Dom.getNextSibling(notesIcons[i]), {
					width: '30em',
					fixedcenter: true,
					modal: true,
					visible: false,
					constraintoviewport: true
				});
				panel.render();

				var args = {
					panel: panel,
					icon: notesIcons[i]
				};
				Event.on(notesIcons[i], 'click', this.showNotes, args);
			}
		},

		showNotes: function(e, args) {
			args.panel.show();
		},

		showDialog: function(e, args) {

                        e.preventDefault();

			var dialog = args.dialog;
			var link = args.link;
			var form = dialog.form;
			var elements = dialog.form.elements;

			var hiddenValues = link.id.split('|');

			for (i = 0; i < elements.length; i++) {
				if (elements[i].name != 'notes') {
					elements[i].value = hiddenValues[i];
				} else {
					elements[i].value = '';
				}
			}

			dialog.show();
		}
	};

	Event.onDOMReady(CMS.apps.DialogApp.init, CMS.apps.DialogApp, true);

})();
