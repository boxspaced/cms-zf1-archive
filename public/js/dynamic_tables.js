function addRow(type, tableId, orderBy, inputs, numCurrentId, numNewId, defaultLanguage, language, languages)
{
	// type = string 'saved' or 'blank'
	// tableID = string
	// order_by = array [name, value]
	// inputs = array of arrays [[function, name, value, input_options = [[value, text], [value, text]], disabled], [function, name, value, options = [[value, text], [value, text]], disabled]]
	// numCurrentId = string
	// numNewId = string
	// defaultLanguage = string
	// language = string
	// languages = array of strings ['en', 'fr']

	var oTable = document.getElementById(tableId);
	var lastRow = oTable.rows.length;
	var oRow = oTable.insertRow(lastRow);

	// Set iteration, goes on end of all elements e.g. ...en_1
	if ( type == 'saved' )
	{
		// Adding saved row (from session)
		var iteration = lastRow;
	}
	else if ( type == 'blank' )
	{
		// Adding blank row
                var oNumCurrent = document.getElementById(numCurrentId);
		var oNumNew = document.getElementById(numNewId);
		oNumNew.value = parseInt(oNumNew.value)+1;
		var iteration = parseInt(oNumCurrent.value) + parseInt(oNumNew.value);
	}
	else
	{
		return;
	}

	for ( inputCount = 0; inputCount < inputs.length; inputCount++ )
	{
		// Create cell for input
		var oCell = oRow.insertCell(inputCount);

		// If it is first cell append order by hidden input
		if ( inputCount == 0 )
		{
			var order_by_input = document.createElement('input');
			order_by_input.type = 'hidden';
                        order_by_input.name = orderBy[0].replace('#', iteration);
			if ( type == 'blank' )
			{
				order_by_input.value = iteration;
			}
			else
			{
				order_by_input.value = orderBy[1];
			}
			oCell.appendChild(order_by_input);
		}

		// Create input
		var input = window[inputs[inputCount][0]](inputs[inputCount][1].replace('#', iteration), inputs[inputCount][2], inputs[inputCount][3], inputs[inputCount][4], defaultLanguage, language);

		// Append to cell
		oCell.appendChild(input);
	}

	if ( language == defaultLanguage )
	{
		// Up cell
		var up_cell = oRow.insertCell(inputCount);
		var up_link = document.createElement('a');
		var up_link_img = document.createElement('img');
		up_link_img.src = '/images/icons/arrow_up.png';
		up_link_img.alt = 'Arrow up icon';
		up_link_img.title = 'Shuffle up';
		up_link.appendChild(up_link_img);
		up_link.href = 'javascript:void(0)';
		up_link.onclick = function() { moveRowUp(tableId, languages, this); };
		up_link.onmouseover = function() { window.status = 'Move up'; return true; };
		up_link.onmouseout = function() { window.status = ''; return true; };
		up_cell.appendChild(up_link);

		// Down cell
		var down_cell = oRow.insertCell(inputCount+1);
		var down_link = document.createElement('a');
		var down_link_img = document.createElement('img');
		down_link_img.src = '/images/icons/arrow_down.png';
		down_link_img.alt = 'Arrow down icon';
		down_link_img.title = 'Shuffle down';
		down_link.appendChild(down_link_img);
		down_link.href = 'javascript:void(0)';
		down_link.onclick = function() { moveRowDown(tableId, languages, this); };
		down_link.onmouseover = function() { window.status = 'Move down'; return true; };
		down_link.onmouseout = function() { window.status = ''; return true; };
		down_cell.appendChild(down_link);

		// Delete cell
		var delete_cell = oRow.insertCell(inputCount+2);
		var delete_link = document.createElement('a');
		var delete_link_img = document.createElement('img');
		delete_link_img.src = '/images/icons/dustbin.png';
		delete_link_img.alt = 'Dustbin icon';
		delete_link_img.title = 'Delete';
		delete_link.appendChild(delete_link_img);
		delete_link.href = 'javascript:void(0)';
		delete_link.onclick = function() { removeRow(tableId, languages, this); };
		delete_link.onmouseover = function() { window.status = 'Delete'; return true; };
		delete_link.onmouseout = function() { window.status = ''; return true; };
		delete_cell.appendChild(delete_link);
	}
}

function addBlankRow(defaultTableId, maxNumRows, defaultOrderBy, defaultInputs, defaultNumCurrentId, defaultNumNewId, defaultLanguage, language, languages)
{
	var oDefaultTable = document.getElementById(defaultTableId);

	if ( oDefaultTable.rows.length < (maxNumRows+1) )
	{
		/*for ( langCount = 0; langCount < languages.length; langCount++ )
		{
			// Adjust element names to reflect language
			var langTableId = defaultTableId.substring(0, defaultTableId.length-2)+languages[langCount];
			var langOrderBy = defaultOrderBy;
			langOrderBy[0] = langOrderBy[0].substring(0, langOrderBy[0].length-2)+languages[langCount];
			var langInputs = new Array();
			for ( inputCount = 0; inputCount < defaultInputs.length; inputCount++ )
			{
				langInputs[inputCount] = defaultInputs[inputCount];
				langInputs[inputCount][1] = defaultInputs[inputCount][1].substring(0, defaultInputs[inputCount][1].length-2)+languages[langCount];
			}
			var langNumNewId = defaultNumNewId.substring(0, defaultNumNewId.length-2)+languages[langCount];

			// Add row
			addRow('blank', langTableId, langOrderBy, langInputs, langNumCurrentId, langNumNewId, defaultLanguage, languages[langCount], languages);
		}*/

                // Add row
                addRow('blank', defaultTableId, defaultOrderBy, defaultInputs, defaultNumCurrentId, defaultNumNewId, defaultLanguage, language, languages);
	}
}

function removeRow(defaultTableId, languages, oDeleteLink)
{
	// Get row number
	var rowNumber = oDeleteLink.parentNode.parentNode.rowIndex;

	for ( langCount = 0; langCount < languages.length; langCount++ )
	{
		var langTableId = defaultTableId.substring(0, defaultTableId.length-2)+languages[langCount];
		var oTable = document.getElementById(langTableId);

		// Delete row
		oTable.deleteRow(rowNumber);
	}
}

function setTranslationRows(defaultTableId, defaultInputs, defaultNumNewId, defaultLanguage, languages)
{
	// defaultTableId = string
	// defaultInputs = array of arrays [[name, type], [name, type]]
	// defaultNumNewId = string
	// defaultLanguage = string
	// languages = array of strings ['en', 'fr']

	var oDefaultTable = document.getElementById(defaultTableId);
	var oDefaultNumNew = document.getElementById(defaultNumNewId);


	// Go through rows
	for ( rowCount = 1; rowCount <= oDefaultNumNew.value; rowCount++ )
	{
		// Go through inputs
		for ( inputCount = 0; inputCount < defaultInputs.length; inputCount++ )
		{
			// Inputs of type 'text'
			if ( defaultInputs[inputCount][1] == 'text' )
			{
				// Make sure default version of input exists, could have been deleted
				if ( document.getElementById('txt_'+defaultInputs[inputCount][0]+'_'+rowCount) )
				{
					var oDefaultInput = document.getElementById('txt_'+defaultInputs[inputCount][0]+'_'+rowCount);

					// Set translation values
					for ( langCount = 0; langCount < languages.length; langCount++ )
					{
						if ( languages[langCount] != defaultLanguage )
						{
							// Get translation input
							var translationInputId = defaultInputs[inputCount][0].substring(0, defaultInputs[inputCount][0].length-2)+languages[langCount];
							var oTranslationInput = document.getElementById('txt_'+translationInputId+'_'+rowCount);

							oTranslationInput.value = oDefaultInput.value;
						}
					}
				}
			}

			// Inputs of type 'select'
			else if ( defaultInputs[inputCount][1] == 'select' )
			{
				// Make sure default version of input exists, could have been deleted
				if ( document.getElementById('sel_'+defaultInputs[inputCount][0]+'_'+rowCount) )
				{
					var oDefaultInput = document.getElementById('sel_'+defaultInputs[inputCount][0]+'_'+rowCount);

					// Set translation values
					for ( langCount = 0; langCount < languages.length; langCount++ )
					{
						if ( languages[langCount] != defaultLanguage )
						{
							// Get translation input
							var translationInputId = defaultInputs[inputCount][0].substring(0, defaultInputs[inputCount][0].length-2)+languages[langCount];
							var oTranslationInput = document.getElementById('sel_'+translationInputId+'_'+rowCount);

							oTranslationInput.selectedIndex = oDefaultInput.selectedIndex;
						}
					}
				}
			}
		}
	}
}

function setTranslationExistingRows(defaultTableId, defaultInputs, defaultLanguage, languages)
{
	// defaultTableId = string
	// defaultInputs = array of arrays [[name, type], [name, type]]
	// defaultLanguage = string
	// languages = array of strings ['en', 'fr']

	var oDefaultTable = document.getElementById(defaultTableId);


	// Go through inputs
	for ( inputCount = 0; inputCount < defaultInputs.length; inputCount++ )
	{
		// Inputs of type 'text'
		if ( defaultInputs[inputCount][1] == 'text' )
		{
			// Make sure default version of input exists, could have been deleted
			if ( document.getElementById('txt_'+defaultInputs[inputCount][0]) )
			{
				var oDefaultInput = document.getElementById('txt_'+defaultInputs[inputCount][0]);

				// Set translation values
				for ( langCount = 0; langCount < languages.length; langCount++ )
				{
					if ( languages[langCount] != defaultLanguage )
					{
						// Get row from default input
						var defaultInputArray = defaultInputs[inputCount][0].split('_');
						var defaultInputRow = defaultInputArray[defaultInputArray.length-1];

						// Get rid of language and row values from default input array
						var newArray = Array();
						for ( defaultInputArrayCount = 0; defaultInputArrayCount < defaultInputArray.length-2; defaultInputArrayCount++ )
						{
							newArray[defaultInputArrayCount] = defaultInputArray[defaultInputArrayCount];
						}

						// Get translation input
						var translationInputId = newArray.join('_')+'_'+languages[langCount]+'_'+defaultInputRow;
						var oTranslationInput = document.getElementById('txt_'+translationInputId);

						oTranslationInput.value = oDefaultInput.value;
					}
				}
			}
		}

		// Inputs of type 'select'
		else if ( defaultInputs[inputCount][1] == 'select' )
		{
			// Make sure default version of input exists, could have been deleted
			if ( document.getElementById('sel_'+defaultInputs[inputCount][0]) )
			{
				var oDefaultInput = document.getElementById('sel_'+defaultInputs[inputCount][0]);

				// Set translation values
				for ( langCount = 0; langCount < languages.length; langCount++ )
				{
					if ( languages[langCount] != defaultLanguage )
					{
						// Get row from default input
						var defaultInputArray = defaultInputs[inputCount][0].split('_');
						var defaultInputRow = defaultInputArray[defaultInputArray.length-1];

						// Get rid of language and row values from default input array (last two)
						var newArray = Array();
						for ( defaultInputArrayCount = 0; defaultInputArrayCount < defaultInputArray.length-2; defaultInputArrayCount++ )
						{
							newArray[defaultInputArrayCount] = defaultInputArray[defaultInputArrayCount];
						}

						// Get translation input
						var translationInputId = newArray.join('_')+'_'+languages[langCount]+'_'+defaultInputRow;
						var oTranslationInput = document.getElementById('sel_'+translationInputId);

						// Testing
						//var oTesting = document.getElementById('testing');
						//oTesting.innerHTML = translationInputId;

						oTranslationInput.selectedIndex = oDefaultInput.selectedIndex;
					}
				}
			}
		}
	}
}







function moveElements(fromRow, toRow)
{
	while ( fromRow.firstChild )
	{
		var n = fromRow.firstChild;
		fromRow.removeChild(n);
		toRow.appendChild(n);
	}
}

function setOrderByValues(defaultTableId, languages)
{
	for ( langCount = 0; langCount < languages.length; langCount++ )
	{
		var langTableId = defaultTableId.substring(0, defaultTableId.length-2)+languages[langCount];
		var oTable = document.getElementById(langTableId);

		//var test = document.getElementById(langTableId+'_test');
		//test.innerHTML = oTable.rows[1].cells[3].firstChild.value;

		for ( rowCount = 1; rowCount < oTable.rows.length; rowCount++ )
		{
			oTable.rows[rowCount].cells[0].firstChild.value = rowCount-1;
		}
	}
}

function moveRowDown(defaultTableId, languages, oDownLink)
{
	// Get from row number
	var fromRowNumber = oDownLink.parentNode.parentNode.rowIndex;

	for ( langCount = 0; langCount < languages.length; langCount++ )
	{
		var langTableId = defaultTableId.substring(0, defaultTableId.length-2)+languages[langCount];
		var oTable = document.getElementById(langTableId);
		var tableHeight = oTable.rows.length;
		var toRowNumber = fromRowNumber+2;

		if ( fromRowNumber == (tableHeight-1) )
		{
			toRowNumber = 1;
		}

		var oFromRow = oTable.rows[fromRowNumber];
		var oNewRow = oTable.insertRow(toRowNumber);

		moveElements(oFromRow, oNewRow);

		oTable.deleteRow(oFromRow.rowIndex);
	}

	// Set order_by values
	setOrderByValues(defaultTableId, languages);
}

function moveRowUp(defaultTableId, languages, oUpLink)
{
	// Get from row number
	var fromRowNumber = oUpLink.parentNode.parentNode.rowIndex;

	for ( langCount = 0; langCount < languages.length; langCount++ )
	{
		var langTableId = defaultTableId.substring(0, defaultTableId.length-2)+languages[langCount];

		var oTable = document.getElementById(langTableId);
		var tableHeight = oTable.rows.length;
		var toRowNumber = fromRowNumber-1;

		if ( fromRowNumber == 1 )
		{
			toRowNumber = tableHeight;
		}

		var oFromRow = oTable.rows[fromRowNumber];
		var oNewRow = oTable.insertRow(toRowNumber);

		moveElements(oFromRow, oNewRow);

		oTable.deleteRow(oFromRow.rowIndex);
	}

	// Set order_by values
	setOrderByValues(defaultTableId, languages);
}




function randomTable(tableId)
{
	var oTable = document.getElementById(tableId);
	if ( oTable.rows.length <= 1 )
	{
		return;
	}

	var newRows = new Array();

	var sortFunc = randomCompare;

	for ( rowCount = 1; rowCount < oTable.rows.length; rowCount++ )
	{
		newRows[rowCount-1] = oTable.rows[rowCount];
	}

	newRows.sort(sortFunc);

	// We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
	for ( newRowCount = 0; newRowCount < newRows.length; newRowCount++ )
	{
		oTable.tBodies[0].appendChild(newRows[newRowCount]);
	}
}

function randomCompare(a, b)
{
	var valueArray = new Array();
	valueArray = [-1, 0, 1];
	return parseInt(valueArray[Math.floor(Math.random()*valueArray.length)]);
}


function sortTable(tableId, columnIndex, childIndex, dataType, direction)
{
	var oTable = document.getElementById(tableId);
	if ( oTable.rows.length <= 1 )
	{
		return;
	}

	var newRows = new Array();

	SORT_COLUMN_INDEX = columnIndex;
	SORT_CHILD_INDEX = childIndex;
	SORT_DIRECTION = direction;

	if ( dataType == 'numeric' )
	{
		var sortFunc = numericCompare;
	}
	else
	{
		var sortFunc = stringCompare;
	}

	for ( rowCount = 1; rowCount < oTable.rows.length; rowCount++ )
	{
		newRows[rowCount-1] = oTable.rows[rowCount];
	}

	newRows.sort(sortFunc);

	// We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
	for ( newRowCount = 0; newRowCount < newRows.length; newRowCount++ )
	{
		oTable.tBodies[0].appendChild(newRows[newRowCount]);
	}
}

function numericCompare(a, b)
{
	var aa = parseInt(a.cells[SORT_COLUMN_INDEX].childNodes[SORT_CHILD_INDEX].value);
	var bb = parseInt(b.cells[SORT_COLUMN_INDEX].childNodes[SORT_CHILD_INDEX].value);

	if ( isNaN(aa) )
	{
		aa = 0;
	}
	if ( isNaN(bb) )
	{
		bb = 0;
	}

	if ( aa < bb )
	{
		var val = -1;
	}
  	else
	{
		if ( aa > bb )
		{
			var val = 1;
		}
		else
		{
			var val = 0;
		}
	}

	if ( SORT_DIRECTION == 'ASC' )
	{
		return val;
	}
	else
	{
		if ( val == -1 )
		{
			return 1;
		}
		else if ( val == 1 )
		{
			return -1;
		}
		else
		{
			return 0;
		}
	}
}

function stringCompare(a, b)
{
	var aa = a.cells[SORT_COLUMN_INDEX].childNodes[SORT_CHILD_INDEX].value.toLowerCase();
	var bb = b.cells[SORT_COLUMN_INDEX].childNodes[SORT_CHILD_INDEX].value.toLowerCase();

	if ( aa < bb )
	{
		var val = -1;
	}
  	else
	{
		if ( aa > bb )
		{
			var val = 1;
		}
		else
		{
			var val = 0;
		}
	}

	if ( SORT_DIRECTION == 'ASC' )
	{
		return val;
	}
	else
	{
		if ( val == -1 )
		{
			return 1;
		}
		else if ( val == 1 )
		{
			return -1;
		}
		else
		{
			return 0;
		}
	}
}




function groupOptions(sel)
{
	var opt, m, k = 0, tmp, grpar = new Array(), text = new Array(), grps = new Object();
	var re_opt = /group\d+/i;

	if ( sel.selectedIndex >= 0 )
	{
		tmp = sel.options[sel.selectedIndex];
		selidx = new Object();
		selidx.value = tmp.value;
		selidx.text = tmp.text;
	}

	for ( var i = sel.options.length-1; i >= 0; i-- )
	{
		text = sel.options[k].text.split(' ');
		m = text[0];

		if ( m )
		{
			opt = sel.removeChild(sel.options[k]);
			opt.text = text[1];

			if ( typeof grps[m] == 'undefined' )
			{
				grpar[grpar.length] = m;
				grps[m] = document.createElement('optgroup');
				grps[m].label = m;
			}

			grps[m].appendChild(opt);
		}
		else
		{
			k++;
		}
	}

	for ( var i = 0; i < grpar.length; i++ )
	{
		sel.appendChild(grps[grpar[i]]);
	}

	if ( typeof selidx == 'object' )
	{
		for ( var i = 0; i < sel.options.length; i++ )
		{
			tmp = sel.options[i];

			if ( ( tmp.value == selidx.value ) && ( tmp.text == selidx.text ) )
			{
				sel.selectedIndex = i;
				break;
			}
		}
	}
}




///////////////////////////////////////////////////////////////////////////
// Elements
///////////////////////////////////////////////////////////////////////////

function createCompareWithInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createCompareOperatorSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createCompareMessageInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;

	return oInput;
}

function createOtherFunctionSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createOtherMessageInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;

	return oInput;
}

function createOptionValueInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createOptionTextInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;

	return oInput;
}

function createOptionOperatorSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createOptionFigureInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 5;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createOptionSelectedSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createWorkflowNameInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createWorkflowActionInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createWorkflowAllowEditSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createWorkflowAllowDeleteSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	oSelect.id = 'sel_'+name;
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
		}
	}

	return oSelect;
}

function createBlockIdSelect(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oSelect = document.createElement('select');
	oSelect.name = name;
	//oSelect.id = 'sel_'+name;
	// oSelect.onchange = function() { populateTemplateIdSelect(this); };
	if ( language == defaultLanguage )
	{
		oSelect.disabled = disabled;
	}
	else
	{
		oSelect.disabled = true;
	}

	// Build options
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		oSelect.options[optionCount] = new Option(inputOptions[optionCount][1], inputOptions[optionCount][0]);
	}

	// Set selected index
	for ( optionCount = 0; optionCount < inputOptions.length; optionCount++ )
	{
		if ( value == inputOptions[optionCount][0] )
		{
			oSelect.selectedIndex = optionCount;
                        oSelect.options[optionCount].setAttribute('selected', 'selected');
		}
	}

	// Group options
	groupOptions(oSelect);

	return oSelect;
}

function createFormatMenuTagInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createFormatMenuTextInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createClassMenuClassInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createClassMenuTextInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 15;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createPriceBreaksQuantityInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 5;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createPriceBreaksPriceInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 5;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}

function createPriceBreaksSalePriceInput(name, value, inputOptions, disabled, defaultLanguage, language)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.className = 'textbox';
	oInput.name = name;
	oInput.id = 'txt_'+name;
	oInput.maxlength = 255;
	oInput.size = 5;
	oInput.value = value;
	if ( language == defaultLanguage )
	{
		oInput.disabled = disabled;
	}
	else
	{
		oInput.disabled = true;
	}

	return oInput;
}
