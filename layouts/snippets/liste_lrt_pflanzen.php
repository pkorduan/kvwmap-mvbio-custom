<?
	include(CLASSPATH . 'PgObject.php');
	include(CLASSPATH . 'FormObject.php');
?>
<link rel="stylesheet" href="<?php echo BOOTSTRAP_PATH; ?>css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.css" type="text/css">

<script src="<?php echo JQUERY_PATH; ?>jquery-1.12.0.min.js"></script>
<script src="<?php echo JQUERY_PATH; ?>jquery.base64.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap.min.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap-table-flatJSON.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>extension/bootstrap-table-export.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>locale/bootstrap-table-de-DE.min.js"></script>
<script>
			function anzeigePflanzenListeFormatter(value, row) {
				var output = '<a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=203&value_lrt_code=' + value + '&operator_lrt_code==">ansehen</a>';
				return output;
			}
</script>
<div style="min-height: 500px; height: 100%">
	
	<h2 style="display: inline">LRT und Pflanzenlisten</h2>
	
	<div style="padding-left:10px;padding-right:10px;">
		<table
			id="lrt_pflanzen_table"
			data-unique-id="lrt_code"
			data-toggle="table"
			data-url="index.php"
			data-height="100%"
			data-click-to-select="true"
			data-filter-control="true" 
			data-sort-name="lrt_code"
			data-sort-order="asc"
			data-search="true"
			data-show-export="false"
			data-export_types=['json', 'xml', 'csv', 'txt', 'sql', 'excel']
			data-show-refresh="false"
			data-show-toggle="true"
			data-show-columns="true"
			data-query-params="go=Layer-Suche_Suchen&selected_layer_id=203&anzahl=10000&mime_type=formatter&format=json"
			data-pagination="true"
			data-page-size="100"
			data-toggle="table"
			data-toolbar="#toolbar"	
			style="position:relative;overflow:auto;height:50%"
		>
			<thead>
				<tr>
					<th
						data-field="lrt_code"
						data-sortable="true"
						data-visible="true"
						data-switchable="true"
					>LRT-Code</th>
					<th
						data-field="lrt_name"
						data-sortable="true"
						data-visible="true"
						data-switchable="true"
					>Name</th>
					<th
						data-field="lrt_code"
						data-visible="true"
						data-formatter="anzeigePflanzenListeFormatter"
						data-switchable="false"
					>&nbsp;</th>
				</tr>
			</thead>
		</table>
		<script language="javascript" type="text/javascript">
			$('#gui-table').css('width', '100%');
			$('#container_paint').css('height', 'auto');
			
			/*
			$(function () {
				result = $('#eventsResult');
				result.success = function(text) {
					message([{ type: 'notice', msg: text}], 1000, 500, '13%');
				};
				result.error = function(text){
					message([{ type: 'error', msg: text}]);
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

				$('.change-bearbeitungsstand').on(
					'click',
					function (e) {
						console.log('Ändere Bearbeitungsstand');
						updateBearbeitungsstand(e.target.getAttribute('stufe'), e.target.value);
					}
				);

			});
			*/


			/*

			function checkboxFormatter(value, row, index) {
				if (
					(
						'<? echo $this->Stelle->Bezeichnung; ?>' == 'Kartierung' &&
						row.user_id != <? echo $this->user->id; ?>
					) ||
					row.stand != '<? echo $bearbeitungsstufe->get('stand'); ?>'
				) {
					return { disabled: true }
				}
				return value
			}

			function getIdSelections() {
				return $.map($('#kartierungen_table').bootstrapTable('getSelections'), function (row) {
					return row.kartierung_id
				})
			}

			*/


		</script>
	</div>
</div>