function blank_function()
{
	return '';
}

function open_preview(uri)
{
	window.open(uri);
}

function publishing_index_preview(form)
{
	for (i = 0; i < form.elements.length; i++) {
		alert(i+' '+form.elements[i].name);
	}

	//var select = form.template_id;
	//var template_id = select.options[select.selectedIndex].value;
	//open_preview(form.uri.value + template_id);

	//alert(template_id);

	return false;
}

function wpro_submit(form)
{
	if ( typeof WPro != 'undefined' )
	{
		WPro.updateAll('prepareSubmission');
	}
	form.submit();
}


function pop_up(url)
{
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(url, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=0,width=600,height=475,left = 212,top = 184');");
}


function date_add_day_suffix(day)
{
	day = intval(day);

	if ( day == 1 || day == 21 || day == 31 )
	{
		return day+'st';
	}
	else if ( day == 2 || day == 22 )
	{
		return day+'nd';
	}
	else if ( day == 3 || day == 23 )
	{
		return day+'rd';
	}
	else
	{
		return day+'th';
	}
}


function human_month(month)
{
	// Create month array
	var human_months = new Array();
	human_months['01'] = 'January';
	human_months['02'] = 'February';
	human_months['03'] = 'March';
	human_months['04'] = 'April';
	human_months['05'] = 'May';
	human_months['06'] = 'June';
	human_months['07'] = 'July';
	human_months['08'] = 'August';
	human_months['09'] = 'September';
	human_months['10'] = 'October';
	human_months['11'] = 'November';
	human_months['12'] = 'December';

	if ( typeof human_months[month] != 'undefined' )
	{
		return human_months[month];
	}
	else
	{
		return '';
	}
}


function intval(mixed_var, base)
{
	var tmp;

	if ( typeof(mixed_var) == 'string' )
	{
		tmp = parseInt(mixed_var*1);

		if ( isNaN(tmp) || !isFinite(tmp) )
		{
			return 0;
		}
		else
		{
			return tmp.toString(base || 10);
		}
	}
	else if ( typeof( mixed_var ) == 'number' && isFinite(mixed_var) )
	{
		return Math.floor(mixed_var);
	}
	else
	{
		return 0;
	}
}

function in_array(needle, haystack, argStrict) {

	var key = '', strict = !!argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}


function adminNav(targetUrl, targetFrame)
{
	if ( !targetFrame )
	{
		targetFrame = self;
	}

	if ( targetFrame )
	{
		targetFrame.location.replace(targetUrl);
	}

	return true;
}


var submit_count = 0;
function check_submit()
{
	if ( submit_count == 0 )
	{
      	submit_count++;
      	document.form.submit();
	}
}


function set_field_value(form_to_set, field_to_set, value_to_set)
{
	var field = eval('document.' + form_to_set + '.' + field_to_set);
	field.value = value_to_set;
}


// WYSIWYG Pro file browser stuff
function open_image_browser(e, oTxtBox)
{
	openFileBrowser('image', function(url) { oTxtBox.value = url; }, function() { return oTxtBox.value; } );
}
function open_document_browser(e, oTxtBox)
{
	openFileBrowser('document', function(url) { oTxtBox.value = url; }, function() { return oTxtBox.value; } );
}
function open_media_browser(e, oTxtBox)
{
	openFileBrowser('media', function(url) { oTxtBox.value = url; }, function() { return oTxtBox.value; } );
}
function open_link_browser(e, oTxtBox)
{
	openFileBrowser('link', function(url) { oTxtBox.value = url; }, function() { return oTxtBox.value; } );
}


function select_all(box)
{
	box_items = box.options.length;

	for ( i = 0; i < box_items; i++ )
	{
		box.options[i].selected = true;
	}
}

function submit_partial(form)
{
    document.getElementById('partial').value = '1';
    form.submit()
}

function move(fbox, tbox)
{
	fbox_items = fbox.options.length;

	for ( i = 0; i < fbox_items; i++ )
	{
		if ( fbox.options[i].selected == true )
		{
			var no = new Option();
			no.value = fbox.options[i].value;
			no.text = fbox.options[i].text;
			tbox.options[tbox.options.length] = no;
		}
	}
}
function move_from_txt(fbox, tbox)
{
	var no = new Option();
	no.value = fbox.value;
	no.text = fbox.value;
	tbox.options[tbox.options.length] = no;
}
function remove(box)
{
	for ( var i = 0; i < box.options.length; i++ )
	{
		if ( box.options[i].selected && box.options[i] != '' )
		{
			box.options[i].value = '';
			box.options[i].text = '';
		}
	}
	bump_up(box);
}
function bump_up(abox)
{
	for ( var i = 0; i < abox.options.length; i++ )
	{
		if ( abox.options[i].value == '' )
		{
			for ( var j = i; j < abox.options.length - 1; j++ )
			{
				abox.options[j].value = abox.options[j + 1].value;
				abox.options[j].text = abox.options[j + 1].text;
			}
			var ln = i;
			break;
		}
	}
	if ( ln < abox.options.length )
	{
		abox.options.length -= 1;
		bump_up(abox);
	}
}
function move_up(dbox)
{
	for ( var i = 0; i < dbox.options.length; i++ )
	{
		if ( dbox.options[i].selected && dbox.options[i] != '' && dbox.options[i] != dbox.options[0])
		{
			var tmp_val = dbox.options[i].value;
			var tmp_val2 = dbox.options[i].text;
			dbox.options[i].value = dbox.options[i - 1].value;
			dbox.options[i].text = dbox.options[i - 1].text
			dbox.options[i-1].value = tmp_val;
			dbox.options[i-1].text = tmp_val2;
		}
	}
}
function move_down(ebox)
{
	for ( var i = 0; i < ebox.options.length; i++ )
	{
		if ( ebox.options[i].selected && ebox.options[i] != '' && ebox.options[i+1] != ebox.options[ebox.options.length] )
		{
			var tmp_val = ebox.options[i].value;
			var tmp_val2 = ebox.options[i].text;
			ebox.options[i].value = ebox.options[i+1].value;
			ebox.options[i].text = ebox.options[i+1].text
			ebox.options[i+1].value = tmp_val;
			ebox.options[i+1].text = tmp_val2;
		}
	}
}


function toggle_enable_comments()
{
	var chk_enable_comments = document.getElementById('enable-comments');
	var chk_unmoderated_comments = document.getElementById('unmoderated-comments');

	var is_checked = chk_enable_comments.checked;

	if ( is_checked == true )
	{
		chk_unmoderated_comments.disabled = false;
	}
	else
	{
		chk_unmoderated_comments.disabled = true;
		chk_unmoderated_comments.checked = false;
	}
}


function toggle_enable_image_resize()
{
	var chk_myaccount_image_upload_resize = document.main.chk_myaccount_image_upload_resize;
	var txt_myaccount_image_upload_resize_width = document.main.txt_myaccount_image_upload_resize_width;

	var is_checked = chk_myaccount_image_upload_resize.checked;

	if ( is_checked == true )
	{
		txt_myaccount_image_upload_resize_width.disabled = false;
	}
	else
	{
		txt_myaccount_image_upload_resize_width.disabled = true;
	}
}


function toggle_instant_registrant_activation()
{
	var chk_instant_registrant_activation = document.main.chk_instant_registrant_activation;
	var sel_user_initial_type = document.main.sel_user_initial_type;
	var sel_user_initial_group = document.main.sel_user_initial_group;

	var is_checked = chk_instant_registrant_activation.checked;

	if ( is_checked == true )
	{
		sel_user_initial_type.disabled = false;
		sel_user_initial_group.disabled = false;
	}
	else
	{
		sel_user_initial_type.disabled = true;
		sel_user_initial_group.disabled = true;
		//sel_user_initial_type.selectedIndex = 0;
		sel_user_initial_group.selectedIndex = 0;
	}
}


function toggleOrderBySimple()
{
	var oOrderBySelect = document.getElementById('sel_order_by');
	var oOrderByDirectionSelect = document.getElementById('sel_order_by_direction');

	// Set order by direction selector
	if ( oOrderBySelect.value == 'random' || oOrderBySelect.value == 'custom' )
	{
		oOrderByDirectionSelect.selectedIndex = 0;
		oOrderByDirectionSelect.disabled = true;
	}
	else
	{
		oOrderByDirectionSelect.disabled = false;
	}
}


function toggleOrderBy()
{
	var oContainerItemsTable = document.getElementById('tbl_container_items_en');
	var oOrderBySelect = document.getElementById('sel_order_by');
	var oOrderByDirectionSelect = document.getElementById('sel_order_by_direction');

	// Set order by direction selector
	if ( oOrderBySelect.value == 'random' || oOrderBySelect.value == 'custom' )
	{
		oOrderByDirectionSelect.selectedIndex = 0;
		oOrderByDirectionSelect.disabled = true;
	}
	else
	{
		oOrderByDirectionSelect.disabled = false;
	}

	// Set container item table columns
	if ( oOrderBySelect.value == 'custom' )
	{
		// Add action columns row by row
		for ( rowCount = 0; rowCount < oContainerItemsTable.rows.length; rowCount++ )
		{
			var oRow = oContainerItemsTable.rows[rowCount];

			if ( rowCount == 0 )
			{
				// Header row
				var th = document.createElement('th');
				th.colSpan = '2';
				var th_text = document.createTextNode('Actions:')
				th.appendChild(th_text);
				oRow.appendChild(th);
			}
			else
			{
				// Up cell
				var up_cell = oRow.insertCell(1);
				var up_link_img = document.createElement('img');
				up_link_img.src = '/images/icons/arrow_up.png';
				up_link_img.alt = 'Arrow up icon';
				up_link_img.title = 'Shuffle up';
				var up_link = document.createElement('a');
				up_link.appendChild(up_link_img);
				up_link.href = 'javascript:void(0)';
				up_link.onclick = function() { moveRowUp('tbl_container_items_en', ['en'], this); };
				up_link.onmouseover = function() { window.status = 'Move item up'; return true; };
				up_link.onmouseout = function() { window.status = ''; return true; };
				up_cell.appendChild(up_link);

				// Down cell
				var down_cell = oRow.insertCell(2);
				var down_link_img = document.createElement('img');
				down_link_img.src = '/images/icons/arrow_down.png';
				down_link_img.alt = 'Arrow down icon';
				down_link_img.title = 'Shuffle down';
				var down_link = document.createElement('a');
				down_link.appendChild(down_link_img);
				down_link.href = 'javascript:void(0)';
				down_link.onclick = function() { moveRowDown('tbl_container_items_en', ['en'], this); };
				down_link.onmouseover = function() { window.status = 'Move item down'; return true; };
				down_link.onmouseout = function() { window.status = ''; return true; };
				down_cell.appendChild(down_link);
			}
		}

		// Re-order items
		sortTable('tbl_container_items_en', 0, 0, 'numeric', 'ASC');
	}
	else
	{
		// Remove action columns
		for ( rowCount = 0; rowCount < oContainerItemsTable.rows.length; rowCount++ )
		{
			var oRow = oContainerItemsTable.rows[rowCount];

			if ( rowCount == 0 )
			{
				if ( oRow.cells[1] )
				{
					// Header row
					oRow.deleteCell(1);
				}
			}
			else
			{
				if ( oRow.cells[1] && oRow.cells[2] )
				{
					// Up cell
					oRow.deleteCell(1);

					// Down cell
					oRow.deleteCell(1);
				}
			}
		}

		if ( oOrderBySelect.value == 'random' )
		{
			// Re-order items
			randomTable('tbl_container_items_en');
		}
		else
		{
			// Set direction selector
			if ( oOrderByDirectionSelect.value == '' )
			{
				oOrderByDirectionSelect.selectedIndex = 1;
			}

			// Set direction
			if ( oOrderByDirectionSelect.value == '' )
			{
				var orderByDirection = 'ASC';
			}
			else
			{
				var orderByDirection = oOrderByDirectionSelect.value;
			}

			// Re-order items
			if ( oOrderBySelect.value == 'name' )
			{
				sortTable('tbl_container_items_en', 0, 2, 'string', orderByDirection);
			}
			else if ( oOrderBySelect.value == 'live_from' )
			{
				sortTable('tbl_container_items_en', 0, 3, 'numeric', orderByDirection);
			}
			else if ( oOrderBySelect.value == 'expires_end' )
			{
				sortTable('tbl_container_items_en', 0, 4, 'numeric', orderByDirection);
			}
			else if ( oOrderBySelect.value == 'authored_time' )
			{
				sortTable('tbl_container_items_en', 0, 5, 'numeric', orderByDirection);
			}
			else if ( oOrderBySelect.value == 'published_time' )
			{
				sortTable('tbl_container_items_en', 0, 7, 'numeric', orderByDirection);
			}
			else if ( oOrderBySelect.value == 'last_modified_time' )
			{
				sortTable('tbl_container_items_en', 0, 8, 'numeric', orderByDirection);
			}
		}
	}
}

function toggleOrderByDirection()
{
	var oContainerItemsTable = document.getElementById('tbl_container_items_en');
	var oOrderBySelect = document.getElementById('sel_order_by');
	var oOrderByDirectionSelect = document.getElementById('sel_order_by_direction');

	if ( oOrderBySelect.value != 'custom' && oOrderBySelect.value != 'random' )
	{
		// Set direction
		if ( oOrderByDirectionSelect.value == '' )
		{
			var orderByDirection = 'ASC';
		}
		else
		{
			var orderByDirection = oOrderByDirectionSelect.value;
		}

		// Re-order items
		if ( oOrderBySelect.value == 'name' )
		{
			sortTable('tbl_container_items_en', 0, 2, 'string', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'live_from' )
		{
			sortTable('tbl_container_items_en', 0, 3, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'expires_end' )
		{
			sortTable('tbl_container_items_en', 0, 4, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'authored_time' )
		{
			sortTable('tbl_container_items_en', 0, 5, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'published_time' )
		{
			sortTable('tbl_container_items_en', 0, 7, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'last_modified_time' )
		{
			sortTable('tbl_container_items_en', 0, 8, 'numeric', orderByDirection);
		}
	}
}


function onLoadContainerItemsTable()
{
	var oContainerItemsTable = document.getElementById('tbl_container_items_en');
	var oOrderBySelect = document.getElementById('sel_order_by');
	var oOrderByDirectionSelect = document.getElementById('sel_order_by_direction');

	if ( oOrderBySelect.value == 'custom' )
	{
		// Order items
		sortTable('tbl_container_items_en', 0, 0, 'numeric', 'ASC');
	}
	else if ( oOrderBySelect.value == 'random' )
	{
		// Order items
		randomTable('tbl_container_items_en');
	}
	else
	{
		// Set direction
		if ( oOrderByDirectionSelect.value == '' )
		{
			var orderByDirection = 'ASC';
		}
		else
		{
			var orderByDirection = oOrderByDirectionSelect.value;
		}

		// Order items
		if ( oOrderBySelect.value == 'name' )
		{
			sortTable('tbl_container_items_en', 0, 2, 'string', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'live_from' )
		{
			sortTable('tbl_container_items_en', 0, 3, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'expires_end' )
		{
			sortTable('tbl_container_items_en', 0, 4, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'authored_time' )
		{
			sortTable('tbl_container_items_en', 0, 5, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'published_time' )
		{
			sortTable('tbl_container_items_en', 0, 7, 'numeric', orderByDirection);
		}
		else if ( oOrderBySelect.value == 'last_modified_time' )
		{
			sortTable('tbl_container_items_en', 0, 8, 'numeric', orderByDirection);
		}
	}
}


function statsYearChange()
{
	// Get selectors
	var oYearSelect = document.getElementById('sel_year');
	var oMonthSelect = document.getElementById('sel_month');
	var oDaySelect = document.getElementById('sel_day');

	// Clear both selectors
	if ( document.getElementById('sel_month') )
	{
		oMonthSelect.options.length = 0;
		oMonthSelect.options[0] = new Option('Month', '');
	}
	if ( document.getElementById('sel_day') )
	{
		oDaySelect.options.length = 0;
		oDaySelect.options[0] = new Option('Day', '');
	}

	// Refill with correct months
	if ( typeof oYearSelect.value != 'undefined' && oYearSelect.value != '' && document.getElementById('sel_month') )
	{
		for ( optionCount = 0; optionCount < months[oYearSelect.value].length; optionCount++ )
		{
			oMonthSelect.options[optionCount+1] = new Option(human_month(months[oYearSelect.value][optionCount]), months[oYearSelect.value][optionCount]);
		}
	}
}


function statsMonthChange()
{
	// Get selectors
	var oYearSelect = document.getElementById('sel_year');
	var oMonthSelect = document.getElementById('sel_month');
	var oDaySelect = document.getElementById('sel_day');

 	// Clear day selector
	if ( document.getElementById('sel_day') )
	{
		oDaySelect.options.length = 0;
		oDaySelect.options[0] = new Option('Day', '');
	}

	// Refill with correct days
	if ( typeof oMonthSelect.value != 'undefined' && oYearSelect.value != '' && oMonthSelect.value != '' && document.getElementById('sel_day') )
	{
		for ( optionCount = 0; optionCount < days[oYearSelect.value][oMonthSelect.value].length; optionCount++ )
		{
			oDaySelect.options[optionCount+1] = new Option(date_add_day_suffix(days[oYearSelect.value][oMonthSelect.value][optionCount]), days[oYearSelect.value][oMonthSelect.value][optionCount]);
		}
	}
}


function toggle_protected()
{
	var is_checked = document.main.chk_protected.checked;
	if ( is_checked == false )
	{
		document.main.sel_allowed_groups.disabled = true;

		for( var i = 0; i < document.main.sel_allowed_groups.length; i++)
		{
			document.main.sel_allowed_groups.options[i].selected = false;
		}
	}
	else
	{
		document.main.sel_allowed_groups.disabled = false;
	}
}


function toggle_all_types_allowed_in_container_type(oTypesAllowedInContainerTypeAllChk, typeChkIds)
{
	if ( oTypesAllowedInContainerTypeAllChk.checked == true )
	{
		for ( typeCount = 0; typeCount < typeChkIds.length; typeCount++ )
		{
			// Get each type check box
			var oTypeChk = document.getElementById(typeChkIds[typeCount]);

			oTypeChk.disabled = true;
			oTypeChk.checked = false;
		}
	}
	else
	{
		for ( typeCount = 0; typeCount < typeChkIds.length; typeCount++ )
		{
			// Get each type check box
			var oTypeChk = document.getElementById(typeChkIds[typeCount]);

			oTypeChk.disabled = false;
		}
	}
}


function format_home_change(oFormatText, formatDefaultId)
{
	// Get format default radio
	var oFormatDefaultRadio = document.getElementById(formatDefaultId);

	// Disable/enable default radio
	if ( oFormatText.value.length == 0 )
	{
		oFormatDefaultRadio.checked = false;
		oFormatDefaultRadio.disabled = true;
	}
	else
	{
		oFormatDefaultRadio.disabled = false;
	}

	// Set value of default radio to text value
	oFormatDefaultRadio.value = oFormatText.value;
}


function populateEmailPreviewFormat(templateSelectId, formatSelectId)
{
	// Get selector objects
	var oTemplateSelect = document.getElementById(templateSelectId);
	var oFormatSelect = document.getElementById(formatSelectId);

	// Clear format options
	oFormatSelect.options.length = 0;

	// Refill with correct formats
	if ( typeof oTemplateSelect.value != 'undefined' )
	{
		oFormatSelect.options[0] = new Option('', '');
		oFormatSelect.options[1] = new Option('Text', 'txt');

		// Had HTML option if the html template exists
		if ( html_template_exists_array[oTemplateSelect.value] == 1 )
		{
			oFormatSelect.options[2] = new Option('HTML', 'html');
		}
	}
}


///////////////////////////////////////////////////////////////////////////
// Form builder
///////////////////////////////////////////////////////////////////////////

function setTranslationInputs(defaultInputs, defaultLanguage, languages)
{
	// defaultInputs = array of arrays [[name, type], [name, type]]
	// defaultLanguage = string
	// languages = array of strings ['en', 'fr']


	// Go through inputs
	for ( inputCount = 0; inputCount < defaultInputs.length; inputCount++ )
	{
		// Inputs of type 'text'
		if ( defaultInputs[inputCount][1] == 'text' )
		{
			// Make sure default version of input exists
			if ( document.getElementById('txt_'+defaultInputs[inputCount][0]) )
			{
				var oDefaultInput = document.getElementById('txt_'+defaultInputs[inputCount][0]);

				// Set translation values
				for ( langCount = 0; langCount < languages.length; langCount++ )
				{
					if ( languages[langCount] != defaultLanguage )
					{
						// Get translation input
						var translationInputId = defaultInputs[inputCount][0].substring(0, defaultInputs[inputCount][0].length-2)+languages[langCount];
						var oTranslationInput = document.getElementById('txt_'+translationInputId);

						oTranslationInput.value = oDefaultInput.value;
					}
				}
			}
		}

		// Inputs of type 'checkbox'
		else if ( defaultInputs[inputCount][1] == 'checkbox' )
		{
			// Make sure default version of input exists
			if ( document.getElementById('chk_'+defaultInputs[inputCount][0]) )
			{
				var oDefaultInput = document.getElementById('chk_'+defaultInputs[inputCount][0]);

				// Set translation values
				for ( langCount = 0; langCount < languages.length; langCount++ )
				{
					if ( languages[langCount] != defaultLanguage )
					{
						// Get translation input, is it checked
						var translationInputId = defaultInputs[inputCount][0].substring(0, defaultInputs[inputCount][0].length-2)+languages[langCount];
						var oTranslationInput = document.getElementById('chk_'+translationInputId);

						// Set translation input
						if ( oDefaultInput.checked == true )
						{
							oTranslationInput.checked = true;
						}
						else
						{
							oTranslationInput.checked = false;
						}
					}
				}
			}
		}

		// Inputs of type 'select'
		else if ( defaultInputs[inputCount][1] == 'select' )
		{
			// Make sure default version of input exists
			if ( document.getElementById('sel_'+defaultInputs[inputCount][0]) )
			{
				var oDefaultInput = document.getElementById('sel_'+defaultInputs[inputCount][0]);

				// Set translation values
				for ( langCount = 0; langCount < languages.length; langCount++ )
				{
					if ( languages[langCount] != defaultLanguage )
					{
						// Get translation input
						var translationInputId = defaultInputs[inputCount][0].substring(0, defaultInputs[inputCount][0].length-2)+languages[langCount];
						var oTranslationInput = document.getElementById('sel_'+translationInputId);

						oTranslationInput.selectedIndex = oDefaultInput.selectedIndex;
					}
				}
			}
		}
	}
}

function toggleElementRequired(defaultReqInput, defaultMsgInput, defaultLanguage, languages)
{
	// defaultReqInput = string
	// defaultMsgInput = string


	// Make sure default version of inputs exist
	if ( document.getElementById('chk_'+defaultReqInput) && document.getElementById('txt_'+defaultMsgInput) )
	{
		var oDefaultReqInput = document.getElementById('chk_'+defaultReqInput);
		var oDefaultMsgInput = document.getElementById('txt_'+defaultMsgInput);

		// Set default message input accordingly
		if ( oDefaultReqInput.checked == true )
		{
			oDefaultMsgInput.disabled = false;
		}
		else
		{
			oDefaultMsgInput.disabled = true;
			// Don't remove value, would be a pain for user if they changed their mind
		}

		// Set translation message inputs accordingly
		for ( langCount = 0; langCount < languages.length; langCount++ )
		{
			if ( languages[langCount] != defaultLanguage )
			{
				// Get translation message input
				var translationMsgInputId = defaultMsgInput.substring(0, defaultMsgInput.length-2)+languages[langCount];
				var oTranslationMsgInput = document.getElementById('txt_'+translationMsgInputId);

				if ( oDefaultReqInput.checked == true )
				{
					oTranslationMsgInput.disabled = false;
				}
				else
				{
					oTranslationMsgInput.disabled = true;
					// Don't remove value, would be a pain for user if they changed their mind
				}
			}
		}
	}
}

function checkElementRangeValues(defaultMinInput, defaultMaxInput, defaultMsgInput, defaultLanguage, languages)
{
	// defaultMinInput = string
	// defaultMaxInput = string
	// defaultMsgInput = string


	// Make sure default version of inputs exist
	if ( document.getElementById('txt_'+defaultMinInput) && document.getElementById('txt_'+defaultMaxInput) && document.getElementById('txt_'+defaultMsgInput) )
	{
		var oDefaultMinInput = document.getElementById('txt_'+defaultMinInput);
		var oDefaultMaxInput = document.getElementById('txt_'+defaultMaxInput);
		var oDefaultMsgInput = document.getElementById('txt_'+defaultMsgInput);

		// Set message input accordingly
		if ( oDefaultMinInput.value != '' || oDefaultMaxInput.value != '' )
		{
			oDefaultMsgInput.disabled = false;
		}
		else
		{
			oDefaultMsgInput.disabled = true;
			// Don't remove value, would be a pain for user if they changed their mind
		}

		// Set translation message inputs accordingly
		for ( langCount = 0; langCount < languages.length; langCount++ )
		{
			if ( languages[langCount] != defaultLanguage )
			{
				// Get translation message input
				var translationMsgInputId = defaultMsgInput.substring(0, defaultMsgInput.length-2)+languages[langCount];
				var oTranslationMsgInput = document.getElementById('txt_'+translationMsgInputId);

				if ( oDefaultMinInput.value != '' || oDefaultMaxInput.value != '' )
				{
					oTranslationMsgInput.disabled = false;
				}
				else
				{
					oTranslationMsgInput.disabled = true;
					// Don't remove value, would be a pain for user if they changed their mind
				}
			}
		}
	}
}

function checkElementRegexValue(defaultExpInput, defaultMsgInput, defaultLanguage, languages)
{
	// defaultExpInput = string
	// defaultMsgInput = string

	// Make sure default version of inputs exist
	if ( document.getElementById('txt_'+defaultExpInput) && document.getElementById('txt_'+defaultMsgInput) )
	{
		var oDefaultExpInput = document.getElementById('txt_'+defaultExpInput);
		var oDefaultMsgInput = document.getElementById('txt_'+defaultMsgInput);

		// Set message input accordingly
		if ( oDefaultExpInput.value != '' )
		{
			oDefaultMsgInput.disabled = false;
		}
		else
		{
			oDefaultMsgInput.disabled = true;
			// Don't remove value, would be a pain for user if they changed their mind
		}

		// Set translation message inputs accordingly
		for ( langCount = 0; langCount < languages.length; langCount++ )
		{
			if ( languages[langCount] != defaultLanguage )
			{
				// Get translation message input
				var translationMsgInputId = defaultMsgInput.substring(0, defaultMsgInput.length-2)+languages[langCount];
				var oTranslationMsgInput = document.getElementById('txt_'+translationMsgInputId);

				if ( oDefaultExpInput.value != '' )
				{
					oTranslationMsgInput.disabled = false;
				}
				else
				{
					oTranslationMsgInput.disabled = true;
					// Don't remove value, would be a pain for user if they changed their mind
				}
			}
		}
	}
}


///////////////////////////////////////////////////////////////////////////
// Link selector
///////////////////////////////////////////////////////////////////////////

function toggleInternalOrExternal(oRadio, elementId)
{
	// Get link field and link msg objects
	var oLinkField = document.getElementById('txt_'+elementId);
	var oLinkInternalMsg = document.getElementById('msg_'+elementId+'_internal');
	var oLinkExternalMsg = document.getElementById('msg_'+elementId+'_external');

	// Set stuff
	if ( oRadio.value == 'internal' )
	{
		oLinkField.value = '';
		oLinkInternalMsg.style.display = 'block';
		oLinkExternalMsg.style.display = 'none';
	}
	else
	{
		oLinkField.value = '';
		oLinkInternalMsg.style.display = 'none';
		oLinkExternalMsg.style.display = 'block';
	}
}


///////////////////////////////////////////////////////////////////////////
// Publishing mail settings
///////////////////////////////////////////////////////////////////////////

function changeMailSetting(oSelect, inputId)
{
	// Get input object
	var oInput = document.getElementById(inputId);

	// Set input
	if ( oSelect.value != '' )
	{
		oInput.disabled = true;
		oInput.value = '';
	}
	else
	{
		oInput.disabled = false;
	}
}


///////////////////////////////////////////////////////////////////////////
// Confirms
///////////////////////////////////////////////////////////////////////////

function confirm_action()
{
	return (confirm('Are you sure?'));
}

// Try get rid of these!!!
function confirm_delete(what)
{
	return (confirm('Are you sure you want to delete this ' + what + '?'));
}
function confirm_archive(what)
{
	return (confirm('Please note:\nAny protection will be removed during archive process.\n\nAre you sure you want to archive this ' + what + '?'));
}

function confirm_block_delete(what)
{
	return (confirm('Are you sure you want to delete this ' + what + '?'));
}
function confirm_block_archive(what)
{
	return (confirm('Are you sure you want to archive this ' + what + '?'));
}
