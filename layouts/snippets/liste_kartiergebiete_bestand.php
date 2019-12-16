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

	function jaNeinFormatter(value, row) {
		return (value == 't' ? 'Ja' : 'Nein');
	}

	function kartiergebieteEditFunctionsFormatter(value, row) {
		var icon = 'pencil',
				output;

				output = '<a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=131&value_kartiergebiet_id=' + value + '&operator_kartiergebiet_id==">ansehen</a>';
		return output;
	}
</script>
<div style="min-height: 500px">
<?php
	if ($this->formvars['kampagne_id'] != '') {
		$kampagne_filter = '&value_kampagne_id=' . $this->formvars['kampagne_id'] . '&operator_kampagne_id==';
		$subtitle = 'Gefiltert nach Kampagne_id: ' . $this->formvars['kampagne_id'];
	}
?>
<h2>Kartiergebiete Bestand</h2>
<?php echo $subtitle; ?>
<table
	id="kartierungen_table"
	data-toggle="table"
	data-url="index.php"
	data-height="100%"
	data-click-to-select="true"
	data-filter-control="true" 
	data-sort-name="kartiergebiet_id"
	data-sort-order="asc"
	data-search="true"
	data-show-export="false"
	data-export_types=['json', 'xml', 'csv', 'txt', 'sql', 'excel']
	data-show-refresh="false"
	data-show-toggle="true"
	data-show-columns="true"
	data-query-params="go=Layer-Suche_Suchen&selected_layer_id=131<?php echo $kampagne_filter; ?>&anzahl=10000&mime_type=formatter&format=json"
	data-pagination="true"
	data-page-size="25"
	data-toggle="table"
	data-toolbar="#toolbar"
>
	<thead>
		<th
			data-field="kampagne_id"
			data-sortable="true"
			data-visible="false"
			data-switchable="true"
		>Kampagne ID</th>
		<th
			data-field="abk"
			data-sortable="true"
			data-visible="true"
			data-switchable="true"
		>Kampagne</th>
		<th
			data-field="kartiergebiet_id"
			data-sortable="true"
			data-visible="true"
			data-switchable="true"
		>Kartiergebiet ID</th>
		<th
			data-field="bezeichnung"
			data-sortable="true"
			data-visible="true"
			data-switchable="true"
		>Kartiergebiet</th>
		<th
			data-field="losnummer"
			data-sortable="true"
			data-visible="true"
			data-switchable="true"
		>Losnummer</th>
		<th
			data-field="kartiergebiet_id"
			data-visible="true"
			data-formatter="kartiergebieteEditFunctionsFormatter"
			data-switchable="false"
		>Details</th>
	</thead>
</table>
</div>