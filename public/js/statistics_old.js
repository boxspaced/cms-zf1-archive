(function() {

	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;

	YAHOO.namespace('boxspaced');

	YAHOO.boxspaced.StatsApp = {

		init : function() {

			// Set swf path
			YAHOO.widget.Chart.SWFURL = '/yui/charts/assets/charts.swf';


			////////////////////////////////////////////////////////////
			// Daily report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_dayrep_row',
				fields: [ 'datespan_from', 'reqs', 'preqs', 'pages', 'ppages', 'bytes', 'pbytes' ]
			};

			var dayrepSeriesDef =
			[
				{
					yField: 'pages',
					displayName: 'Pages Served'
				},
				{
					yField: 'reqs',
					displayName: 'Requests Served'
				}
			];

			var dayrepChart = new YAHOO.widget.ColumnChart('rep_dayrep_chart', analogXML,
			{
				series: dayrepSeriesDef,
				xField: 'datespan_from',
				expressInstall: 'assets/expressinstall.swf'
			});

			var dayrepTableColumns =
			[
				{key: 'datespan_from', label: 'Date', sortable: true, resizeable: true },
				{key: 'pages', label: 'Pages Served', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Requests Served', sortable: true, resizeable: true },
				{key: 'bytes', label: 'MBytes Served', sortable: true, resizeable: true }
			];
			var dayrepTable = new YAHOO.widget.DataTable('rep_dayrep_table', dayrepTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// Most popular pages
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_req_row',
				fields: [ 'name', 'timespan_from', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var reqChart = new YAHOO.widget.PieChart('rep_req_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var reqTableColumns =
			[
				{key: 'name', label: 'Page', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Served', sortable: true, resizeable: true },
				{key: 'timespan_from', label: 'Last Served', sortable: true, resizeable: true }

			];
			var reqTable = new YAHOO.widget.DataTable('rep_req_table', reqTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// External search query report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_searchrep_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var searchrepChart = new YAHOO.widget.PieChart('rep_searchrep_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var searchrepTableColumns =
			[
				{key: 'name', label: 'Search Term', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Searched', sortable: true, resizeable: true }
			];
			var searchrepTable = new YAHOO.widget.DataTable('rep_searchrep_table', searchrepTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// Internal search query report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_intsearchrep_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var intsearchrepChart = new YAHOO.widget.PieChart('rep_intsearchrep_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var intsearchrepTableColumns =
			[
				{key: 'name', label: 'Search Term', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Searched', sortable: true, resizeable: true }
			];
			var intsearchrepTable = new YAHOO.widget.DataTable('rep_intsearchrep_table', intsearchrepTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// Refering site report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_refsite_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var refsiteChart = new YAHOO.widget.PieChart('rep_refsite_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var refsiteTableColumns =
			[
				{key: 'name', label: 'Refering Site', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Requests Generated', sortable: true, resizeable: true }
			];
			var refsiteTable = new YAHOO.widget.DataTable('rep_refsite_table', refsiteTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// Domain report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_domain_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var domainChart = new YAHOO.widget.PieChart('rep_domain_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var domainTableColumns =
			[
				{key: 'name', label: 'Domain', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Requests Generated', sortable: true, resizeable: true }
			];
			var domainTable = new YAHOO.widget.DataTable('rep_domain_table', domainTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// Browser report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_browsum_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var browsumChart = new YAHOO.widget.PieChart('rep_browsum_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var browsumTableColumns =
			[
				{key: 'name', label: 'Domain', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Requests Generated', sortable: true, resizeable: true }
			];
			var browsumTable = new YAHOO.widget.DataTable('rep_browsum_table', browsumTableColumns, analogXML);



			////////////////////////////////////////////////////////////
			// OS report
			////////////////////////////////////////////////////////////

			// Get XML
			var analogXML = new YAHOO.util.DataSource('/admin/utilities/statistics/data/proxy.php?');
			analogXML.responseType = YAHOO.util.DataSource.TYPE_XML;
			analogXML.responseSchema = {
				resultNode: 'rep_os_row',
				fields: [ 'name', 'reqs', 'preqs', 'bytes', 'pbytes' ]
			};

			var osChart = new YAHOO.widget.PieChart('rep_os_chart', analogXML,
			{
				dataField: 'reqs',
				categoryField: 'name',
				expressInstall: 'assets/expressinstall.swf'
			});

			var osTableColumns =
			[
				{key: 'name', label: 'Domain', sortable: true, resizeable: true },
				{key: 'reqs', label: 'Requests Generated', sortable: true, resizeable: true }
			];
			var osTable = new YAHOO.widget.DataTable('rep_os_table', osTableColumns, analogXML);
		}
	};

	Event.onDOMReady(YAHOO.boxspaced.StatsApp.init, YAHOO.boxspaced.StatsApp, true);
})();
