<link rel="stylesheet" href="<?php echo BOOTSTRAP_PATH; ?>css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.css" type="text/css">
<link rel="stylesheet" href="plugins/xplankonverter/styles/design.css" type="text/css">
<link rel="stylesheet" href="plugins/xplankonverter/styles/styles.css" type="text/css">

<script src="<?php echo JQUERY_PATH; ?>jquery-1.12.0.min.js"></script>
<script src="<?php echo JQUERY_PATH; ?>jquery.base64.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap.min.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap-table-flatJSON.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>extension/bootstrap-table-export.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>locale/bootstrap-table-de-DE.min.js"></script>

<script language="javascript" type="text/javascript">
	$('#gui-table').css('width', '100%');
	$(function () {
		result = $('#eventsResult');
		result.success = function(text) {
			message([{ type: 'notice', msg: text}], 1000, 500, '13%');
/*			result.text(text);
			result.removeClass('alert-danger');
			result.addClass('alert-success');*/
		};
		result.error = function(text){
			message([{ type: 'error', msg: text}]);
/*			result.text(text);
			result.removeClass('alert-success');
			result.addClass('alert-danger');*/
		};

		// event handler
		$('#kartierungen_table')
		.one('load-success.bs.table', function (e, data) {
			result.success('Tabelle erfolgreich geladen');
		})
		.on('post-body.bs.table', function (e, data) {
			$('.xpk-func-del-konvertierung').click(
				loescheKartierung
			);
		})
		.on('load-error.bs.table', function (e, status) {
			console.log('loaderror');
			result.error('Event: load-error.bs.table');
		});
		// more examples for register events on data tables: http://jsfiddle.net/wenyi/e3nk137y/36/
	});

	loescheKartierung = function(e) {
		message('Lösche Kartierung noch nicht implementiert.');
/*		e.preventDefault();
		var plan_name = $(e.target).parent().parent().attr('plan_name'),
				plan_oid = $(e.target).parent().parent().attr('plan_oid'),
				r = confirm("Soll der Plan " + plan_oid + " wirklich gelöscht werden?");

		if (r == true) {
			$(this).closest('tr').remove();
			result.text('Lösche Plan: ' + plan_name);
			$.ajax({
				url: 'index.php',
				data: {
					go: 'xplankonverter_konvertierung_loeschen',
					planart: '<?php echo $this->formvars['planart']; ?>',
					plan_oid: plan_oid
				},
				success: function(response) {
					message([response]);
				}
			});
		}*/
	};

	// formatter functions
	function kartierungFormatter(value, row) {
/*		var kartierung = JSON.parse(value);
		return $.map(
			gemeinden,
			function(gemeinde) {
				return gemeinde.gemeindename;
			}
		).join(', ')
*/
	}

	function jaNeinFormatter(value, row) {
		return (value == 't' ? 'Ja' : 'Nein');
	}

	function kartiergebieteFormatter(value, row) {
		var output = '<a href="index.php?go=show_snippet&snippet=liste_kartiergebiete_bestand&kampagne_id=' + value + '&csrf_token=<? echo $_SESSION['csrf_token']; ?>">anzeigen</a>';
		return output;
	}

	function kartiergebieteDetailsFormatter(value, row) {
		var output;

				output = '<a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=122&value_id=' + value + '&operator_id==&csrf_token=<? echo $_SESSION['csrf_token']; ?>">ansehen</a>';
		return output;
	}
</script>
<div style="min-height: 500px">
<h2>Kampagnen</h2>
<table
	id="kartierungen_table"
	data-toggle="table"
	data-url="index.php"
	data-height="100%"
	data-click-to-select="true"
	data-filter-control="true" 
	data-sort-name="erfassungszeitraum"
	data-sort-order="asc"
	data-search="true"
	data-show-export="false"
	data-export_types=['json', 'xml', 'csv', 'txt', 'sql', 'excel']
	data-show-refresh="false"
	data-show-toggle="true"
	data-show-columns="true"
	data-query-params="go=Layer-Suche_Suchen&selected_layer_id=122&anzahl=10000&mime_type=formatter&format=json"
	data-pagination="true"
	data-page-size="100"
	data-toggle="table"
	data-toolbar="#toolbar"
>
	<thead>
		<tr>
			<th
				data-field="id"
				data-sortable="true"
				data-visible="true"
				data-switchable="true"
			>ID</th>
			<th
				data-field="abk"
				data-sortable="true"
				data-visible="true"
				data-switchable="true"
			>Kampagne</th>
			<th
				data-field="bezeichnung"
				data-sortable="true"
				data-visible="true"
				data-switchable="true"
			>Bezeichnung</th>
			<th
				data-field="erfassungszeitraum"
				data-sortable="true"
				data-visible="true"
				data-switchable="true"
			>Erfassungszeitraum</th>
			<th
				data-field="datenschichten"
				data-sortable="true"
				data-visible="true"
				data-switchable="true"
			>Datenschichten</th>
			<th
				data-field="id"
				data-visible="true"
				data-formatter="kartiergebieteDetailsFormatter"
				data-switchable="false"
			>Details</th>
			<th
				data-field="id"
				data-visible="true"
				data-formatter="kartiergebieteFormatter"
				data-switchable="false"
			>Kartiergebiete</th>
	</thead>
</table>
</div>