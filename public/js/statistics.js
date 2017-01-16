(function() {

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	YAHOO.namespace('boxspaced');

	// Axis formatter
	YAHOO.boxspaced.formatAxisLabel = function(value)
	{
		return YAHOO.util.Number.format(value,
		{
			thousandsSeparator: ','
		});
	}

	/*/ Data tips although won't work on pie charts
	YAHOO.boxspaced.getDataTipText = function(item, index, series)
	{
		var toolTipText = series.displayName+' for '+item.month;
		toolTipText += '\n'+YAHOO.boxspaced.formatAxisLabel(item[series.yField]);
		return toolTipText;
	}*/

	YAHOO.boxspaced.StatsApp = {

		init : function() {

			// Set swf path
			YAHOO.widget.Chart.SWFURL = '/yui/charts/assets/charts.swf';


			////////////////////////////////////////////////////////////
			// Unique visitor line chart
			////////////////////////////////////////////////////////////

			if ( scope == 'usage' )
			{
				// Get XML
				if ( year && month && day )
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/'+month+'/'+day+'/xml?');
				}
				else if ( year && month && !day )
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/'+month+'/xml?');
				}
				else
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/xml?');
				}
				reportXML.responseType = YAHOO.util.DataSource.TYPE_XML;
				reportXML.responseSchema = {
					resultNode: 'chart_row',
					fields: [ 'time', 'visitors' ]
				};

				var reportSeriesDef =
				[
					{
						yField: 'visitors',
						displayName: 'Unique visitors'
					}
				];

				var visitorsAxis = new YAHOO.widget.NumericAxis();
				visitorsAxis.labelFunction = YAHOO.boxspaced.formatAxisLabel;

				var reportChart = new YAHOO.widget.LineChart('report_chart', reportXML,
				{
					series: reportSeriesDef,
					xField: 'time',
					yAxis: visitorsAxis,
					expressInstall: 'assets/expressinstall.swf'
				});
			}


			////////////////////////////////////////////////////////////
			// Top 25 table
			////////////////////////////////////////////////////////////

			if ( scope == 'usage' )
			{
				// Get XML
				if ( year && month && day )
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/'+month+'/'+day+'/xml?');
				}
				else if ( year && month )
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/'+month+'/xml?');
				}
				else
				{
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/usage/'+year+'/xml?');
				}
				reportXML.responseType = YAHOO.util.DataSource.TYPE_XML;
				reportXML.responseSchema = {
					resultNode: 'table_row',
					fields: [ 'uri', 'views' ]
				};

				var top25TableColumns =
				[
					{key: 'uri', label: '', sortable: false, resizeable: true },
					{key: 'views', label: 'Views', sortable: false, resizeable: true }
				];
				var reportTable = new YAHOO.widget.DataTable('report_table', top25TableColumns, reportXML);
			}


			////////////////////////////////////////////////////////////
			// Client, traffic and search
			////////////////////////////////////////////////////////////

			if ( (scope == 'clients' || scope == 'traffic' || scope == 'search') && year && month )
			{
				// Create graph array
				if ( scope == 'clients' )
				{
					var graph_array = new Array('browser','primary_language','operating_system');
				}
				else if ( scope == 'traffic' )
				{
					var graph_array = new Array('referers_overview','refering_site','search_engine','search_engine_term');
				}
				else
				{
					var graph_array = new Array('internal_search_term');
				}

				for ( i = 0; i < graph_array.length; i++ )
				{
					// Visitors or something else...
					if ( graph_array[i] == 'internal_search_term' )
					{
						var key = 'searches';
						var label = 'Searches';
					}
					else
					{
						var key = 'visitors';
						var label = 'Unique visitors';
					}

					// Get XML
					var reportXML = new YAHOO.util.DataSource('/admin/utilities/statistics/'+scope+'/'+year+'/'+month+'/xml?');
					reportXML.responseType = YAHOO.util.DataSource.TYPE_XML;
					reportXML.responseSchema = {
						resultNode: graph_array[i]+'_row',
						fields: [ graph_array[i], key ]
					};

					var chart = new YAHOO.widget.PieChart('report_'+graph_array[i]+'_chart', reportXML,
					{
						dataField: key,
						categoryField: graph_array[i],
						expressInstall: 'assets/expressinstall.swf'
					});

					var tableColumns =
					[
						{key: graph_array[i], label: '', sortable: false, resizeable: true },
						{key: key, label: label, sortable: false, resizeable: true }
					];
					var table = new YAHOO.widget.DataTable('report_'+graph_array[i]+'_table', tableColumns, reportXML);
				}
			}
		}
	};

	Event.onDOMReady(YAHOO.boxspaced.StatsApp.init, YAHOO.boxspaced.StatsApp, true);
})();
