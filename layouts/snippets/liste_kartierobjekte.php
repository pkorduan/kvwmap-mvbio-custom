<?
	$this->sanitize([
		'user_id' => 'int',
		'bearbeitungsstufe' => 'int'
	]);
	include_once(CLASSPATH . 'PgObject.php');
	include_once(CLASSPATH . 'FormObject.php');
	include_once(CLASSPATH . 'LayerAttributeRolleSetting.php');
	$layer_id = 146;
	$larsObj = new LayerAttributeRolleSetting($this, $this->Stelle->id, $this->user->id, $layer_id);
	$rolle_attribute_settings = $larsObj->read_layer_attributes2rolle($layer_id, $this->Stelle->id, $this->user->id);
	if (count($rolle_attribute_settings) > 0) {
		$sort_attribute = array_values(
			array_filter(
				$rolle_attribute_settings,
				function ($attribute) {
					return $attribute['sort_order'];
				}
			)
		)[0];
	}
	else {
		$sort_attribute = array(
			'attributename' => 'label',
			'sort_direction' => 'asc'
		);
	}

#	echo '<p>' . print_r($sort_attribute, true);
#	echo '<p>Sortierattribute: ' . $sort_attribute['attributename'];
#	echo '<br>Sortierrichtung:' . $sort_attribute['sort_direction'];
?>

<script src="<? echo JQUERY_PATH; ?>jquery.min.js"></script>

<link rel="stylesheet" href="<? echo BOOTSTRAP_PATH; ?>css/bootstrap.min.css">
<script src="<? echo BOOTSTRAP_PATH; ?>lib/popper-1.16.1.min.js"></script>
<script src="<? echo BOOTSTRAP_PATH; ?>js/bootstrap.min.js"></script>

<link rel="stylesheet" href="<? echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.css">
<script src="<? echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.js"></script>
<script src="<? echo BOOTSTRAPTABLE_PATH; ?>locale/bootstrap-table-de-DE.min.js"></script>
<script type="text/javascript" src="<? echo BOOTSTRAPTABLE_PATH; ?>/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<link rel="stylesheet" href="<? echo BOOTSTRAPTABLE_PATH; ?>/extensions/filter-control/bootstrap-table-filter-control.min.css">
<!--
<script type="text/javascript" src="<?php echo BOOTSTRAPTABLE_PATH; ?>libs/FileSaver/FileSaver.min.js"></script>
<script type="text/javascript" src="<?php echo BOOTSTRAPTABLE_PATH; ?>libs/js-xlsx/xlsx.core.min.js"></script>
//-->




<!--
<link rel="stylesheet" href="<?php echo BOOTSTRAP_PATH; ?>css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.css" type="text/css">

<script src="<?php echo JQUERY_PATH; ?>jquery-1.12.0.min.js"></script>
<script src="<?php echo JQUERY_PATH; ?>jquery.base64.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap.min.js"></script>
<script src="<?php echo BOOTSTRAP_PATH; ?>js/bootstrap-table-flatJSON.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>extensions/export/bootstrap-table-export.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="<?php echo BOOTSTRAPTABLE_PATH; ?>locale/bootstrap-table-de-DE.min.js"></script>

//-->
<script src="funktionen/bootstrap-table-settings.js"></script>

<div id="waiting_div">
	<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color: gray"></i>
	<br><br><br><span>Lade Daten ...</span>
</div>
<div style="min-height: 500px; height: 100%"><?php
	# Filter auf nur eigene, wenn user_id belegt ist oder bearbeitungsstufe auf in Erfassung
	if ($this->formvars['user_id'] != '' OR $this->formvars['bearbeitungsstufe'] == 1) {
		$user_id_filter = '&value_user_id=' . $this->user->id . '&operator_user_id==';
		$subtitle = 'nur eigene';
	}

	$bearbeitungsstufe = new PgObject($this, 'mvbio', 'code_bearbeitungsstufen');
	$bearbeitungsstufen = $bearbeitungsstufe->getSQLResults("
		SELECT DISTINCT
			b.stufe AS value,
			b.stand AS output
		FROM
			mvbio.sichtbarkeit_bearbeitungsstufen s JOIN
			mvbio.code_bearbeitungsstufen b ON s.stufe = b.stufe
		WHERE
			stelle_id = " . $this->Stelle->id . " OR
			(
				3 = " . $this->Stelle->id . " AND
				b.stufe IN (4, 5)
			) OR
			(
				4 = " . $this->Stelle->id . " AND
				b.stufe IN (1, 2, 5)
			)
	");
	if ($this->formvars['bearbeitungsstufe'] == '') {
		array_unshift($bearbeitungsstufen, array('value' => '', 'output' => '--- Bearbeitungsstufe wählen ---'));
	}
	$bearbeitungsstufe_filter = '';
	if ($this->formvars['bearbeitungsstufe'] != '') {
		if ($this->Stelle->Bezeichnung == 'Kartierung' AND $this->formvars['bearbeitungsstufe'] == 3) {
			$bearbeitungsstufe_filter = '&value_bearbeitungsstufe=3,4&operator_bearbeitungsstufe=IN';
		}
		else {
			$bearbeitungsstufe_filter = '&value_bearbeitungsstufe=' . $this->formvars['bearbeitungsstufe'] . '&operator_bearbeitungsstufe==';
		}
		$bearbeitungsstufe->data = $bearbeitungsstufe->getSQLResults("
			SELECT
				*
			FROM
				(
					SELECT
						stufe,
						stand,
						aenderungsberechtigte_stelle,
						LAG(stufe) OVER (ORDER BY stufe) prev_stufe,
						LAG(stand) OVER (ORDER BY stufe) prev_stand,
						LEAD(stufe) OVER (ORDER BY stufe) next_stufe,
						LEAD(stand) OVER (ORDER BY stufe) next_stand
					FROM
						mvbio.code_bearbeitungsstufen
				) AS stufen
			WHERE
				stufe = " . $this->formvars['bearbeitungsstufe'] . "
		")[0];

		$subtitle = $bearbeitungsstufe->get('stand');

		/* ToDo
			Die Listen werden wie folgt mit Funktion zum Standwechsel ausgestattet.
			Nur die Seiten, die jeweils nur einen Stand anzeigen erhalten Funktion zum Wechsel.
			Man kann die angezeigten nur eine Stufe hoch oder runter wechseln
			und nur wenn der Bearbeiter mit seinen Rechten das darf.
			Der Kartierer z.B. darf nur 1 nach 2, 2 nach 1 und 2 nach 3 aber nicht 3 nach 2 etc.
			Je nach dem welche Rolle der Nutzer hat darf er also hoch, runter, hoch und runter oder nichts
			Pro Rolle und eingestelltem Stand gibt es also das Recht stand_weiter_allowed true/false und stand_zurueck_allowed true/false
			Stand 6 "Ergebnisstände abgeleitet" ändern in "zur Archivierung freigegeben"
			Stand 7 "Erfassungsstand aktualisiert" ändern in "in Archiv überführt"
			In Liste "zur Archivierung freigegeben" und "in Archiv überführt" kann man keinen Status mehr ändern.
			Alle, die Stand "zur Archivierung freigegeben haben" werden durch einen Cron-Job in das Archiv überführt und bekommen den Stand "in Archiv überführt"
			Die Prüfer dürfen Ändern von "4 nach 5, 5 nach 6, aber nicht von 6 nach 5 und auch nicht von 6 nach 7 (das geht automatisch) und auch nicht von 7 nach 6 zurück (ein mal im Archiv geht es nicht zurück)"
			Wenn Stand 6 erreicht ist kann man höchstens als Admin den Job stoppen, der die Überführung ausführt und dann noch welche manuell von 6 auf 5 oder gar niedriger zurücksetzen für eine erneute Prüfung durch die Prüfer oder weiter runter.
			Die Kartierobjekte werden erst gelöscht wenn der Prüfer das dazugehörige Kartiergebiet löscht.
			Mit der Funktion werden die alten im Archiv historisch und die neu in das Archiv überführten aktuell.
			Die Kartierobjekte erscheinen dann in keiner Liste von Kartierobjekten oder Verlustobjekten mehr.
		*/
	}

	$gesperrt_fuer_kartierer = (in_array($this->formvars['bearbeitungsstufe'], array(3, 4)) AND $this->Stelle->Bezeichnung == 'Kartierung');

	# Stellen den Filter so ein, dass Kartierer keine Kartierungen von anderen sehen, die noch in der Erfassung sind
	if ($bearbeitungsstufe_filter == '' AND $user_id_filter == '') {
		if ($this->user->id == 1) {
			echo '<br>filter leer:';
		}
		$filter = '&searchmask_count=1&value_user_id=' . $this->user->id . '&operator_user_id==&boolean_operator_1=OR&1_value_bearbeitungsstufe=1&1_operator_bearbeitungsstufe=%3E';
	}
	else {
		$filter = $user_id_filter . $bearbeitungsstufe_filter;
	}
	#echo '<p>go=Layer-Suche_Suchen&selected_layer_id=146' . $filter . '&anzahl=100000&mime_type=formatter&format=json';
	
	 ?>
	<h2 style="display: inline">Kartierobjekte</h2><?
	echo FormObject::createSelectField(
		'bearbeitungsstand_filter_selector',
		$bearbeitungsstufen,
		$this->formvars['bearbeitungsstufe'] ? $bearbeitungsstufe->get('stufe') : '',
		1,
		'float: right; margin-right: 10px; margin-top: 9px; width: 248px;',
		"window.location.href='index.php?go=show_snippet&snippet=liste_kartierobjekte&bearbeitungsstufe=' + $(this).val() + '&user_id=" . $this->formvars['user_id'] . "&csrf_token=" . $_SESSION['csrf_token'] . "'"
	);
	if ($this->Stelle->Bezeichnung == 'Kartierung' AND $bearbeitungsstufe->get('stufe') > 1) {
		echo FormObject::createSelectField(
			'user_filter_selector',
			array(
				array('value' => '', 'output' => 'alle Kartierer'),
				array('value' => 'nureigene', 'output' => 'nur eigene')
			),
			($this->formvars['user_id'] == $this->user->id ? 'nureigene' : ''),
			1,
			'float: right; margin-right: 8px; margin-top: 9px;',
			'',
			'',
			'',
			'',
			''
		);
	} ?>
	<div style="padding-left:10px;padding-right:10px;">
		<div style="margin-top: 23px; margin-left: 13px; float:left"><?php
			if (($this->formvars['bearbeitungsstufe'] ? $bearbeitungsstufe->get('aenderungsberechtigte_stelle') : '') == $this->Stelle->Bezeichnung) { ?>
				<span style="font-size: 25px"><sub>&#8625;</sub></span>Bearbeitungsstand für ausgewählte Datensätze ändern in:<?
				if ($this->formvars['bearbeitungsstufe'] > 1) {
					if ($this->Stelle->id == 5 AND $this->formvars['bearbeitungsstufe'] == 4) { ?>
						<input
							class="change-bearbeitungsstand"
							type="button"
							name="prev_stufe"
							stufe="2"
							value="Zur Info freigegeben"
						><?
					} ?>
					<input
						class="change-bearbeitungsstand"
						type="button"
						name="prev_stufe"
						stufe="<? echo $bearbeitungsstufe->get('prev_stufe'); ?>"
						value="<? echo $bearbeitungsstufe->get('prev_stand'); ?>"
					><?
				} ?>
				<input
					class="change-bearbeitungsstand"
					type="button"
					name="next_stufe"
					stufe="<? echo $bearbeitungsstufe->get('next_stufe'); ?>"
					value="<? echo $bearbeitungsstufe->get('next_stand'); ?>"
				><?
			}
			if ($gesperrt_fuer_kartierer) { ?>
				<span style="font-size: 25px"><sub>&#8625;</sub></span> für ausgewählte Datensätze:
				<input
					id="rueckgabewuensche_button"
					type="button"
					name="rueckgabewuensche"
					value="Rückgabewunsch setzen"
				><?
			} 
			if (in_array($this->Stelle->id, array(1, 4, 5)) AND $this->formvars['bearbeitungsstufe'] == 3) { ?>
				<input id="abgabe_name_field" type="text" size="12" placeholder="Abgabename" style="margin-left: 10px">
				<input
				  id="update_abgabe_name_button"
					type="button"
					value="übernehmen für ausgewählte"
				><?
			} ?>
		</div>
		<div class="table-wrapper">
			<table
				id="kartierungen_table"
				data-unique-id="kartierung_id"
				data-toggle="table"
				data-url="index.php"
				data-height="100%"
				data-click-to-select="true"
				data-filter-control="true" 
				data-sort-name="<?php echo $sort_attribute['attributename']; ?>"
				data-sort-order="<?php echo $sort_attribute['sort_direction']; ?>"
				data-search="true"
				data-show-export="false"
				data-export_types=['json', 'xml', 'csv', 'txt', 'sql', 'excel']
				data-show-refresh="false"
				data-show-toggle="true"
				data-show-columns="true"
				data-query-params="go=Layer-Suche_Suchen&selected_layer_id=<?php echo $layer_id; ?><?php echo $filter; ?>&anzahl=100000&mime_type=formatter&format=json"
				data-pagination="true"
				data-page-list=[10,25,50,100,250,500,1000,all]
				data-page-size="100"
				data-toggle="table"
				data-toolbar="#toolbar"	
				style="position:relative;overflow:auto;height:50%"
			>
				<thead>
					<tr><?
						if (
							($this->formvars['bearbeitungsstufe'] ? $bearbeitungsstufe->get('aenderungsberechtigte_stelle') : '') == $this->Stelle->Bezeichnung OR
							$gesperrt_fuer_kartierer
						) { ?>
							<th
								data-sortable="false"
								data-visible="true"
								data-switchable="true"
								data-checkbox="true"
								data-formatter="checkboxFormatter"
							></th><?
						} ?>
						<th
							data-field="kartierung_id"
							data-visible="true"
							data-sortable="true"
							data-formatter="kartierungEditFunctionsFormatter"
							data-switchable="false"
							data-filter-control="input"
						>ID</th>
						<th
							data-field="lfd_nr_kr"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('lfd_nr_kr', $rolle_attribute_settings) OR $rolle_attribute_settings['lfd_nr_kr']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
						>Lfd Nr KG</th>
						<th
							data-field="label"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('label', $rolle_attribute_settings) OR $rolle_attribute_settings['label']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
						>Objekt-Code</th>
						<th
							data-field="arbeits_id"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('arbeits_id', $rolle_attribute_settings) OR $rolle_attribute_settings['arbeits_id']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
						>Arbeits-ID</th>
						<th
							data-field="hat_geom"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('hat_geom', $rolle_attribute_settings) OR $rolle_attribute_settings['hat_geom']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-formatter="boolTypeFormatter"
							data-switchable="true"
							data-filter-control="select"
						>hat Geom</th>
						<th
							data-field="flaeche"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('flaeche', $rolle_attribute_settings) OR $rolle_attribute_settings['flaeche']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-switchable="true"
						>Fläche</th>
						<th
							data-field="hat_fotos"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('hat_fotos', $rolle_attribute_settings) OR $rolle_attribute_settings['hat_fotos']['switched_on'] == 1) ? 'true' : 'false'); ?>"
							data-formatter="boolTypeFormatter"
							data-switchable="true"
							data-filter-control="select"
						>hat Fotos</th>
						<th
							data-field="kampagne_id"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['kampagne_id']['switched_on'] == 1 ? 'true' : 'false'); ?>"
							data-switchable="true"
						>Kampagne ID</th>
						<th
							data-field="kampagne_abk"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('kampagne_abk', $rolle_attribute_settings) OR $rolle_attribute_settings['kampagne_abk']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kampagne</th>
						<th
							data-field="kampagne"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['kampagne']['switched_on'] == 1 ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kampagne Bezeichnung</th>
						<th
							data-field="kartiergebiet_id"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['kartiergebiet_id']['switched_on'] == 1 ? 'true': 'false'); ?>"
							data-switchable="true"
						>Kartiergebiet ID</th>
						<th
							data-field="kartiergebiet"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('kartiergebiet', $rolle_attribute_settings) OR $rolle_attribute_settings['kartiergebiet']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kartiergebiet</th>
						<th
							data-field="kartierebene_id"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['kartierebene_id']['switched_on'] == 1 ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kartierebene Id</th>
						<th
							data-field="kartierebene"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('kartierebene', $rolle_attribute_settings) OR $rolle_attribute_settings['kartierebene']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kartierebene</th>
						<th
							data-field="bogenart_id"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['bogenart_id']['switched_on'] == 1 ? 'true': 'false'); ?>"
							data-switchable="true"
						>Bogenart Id</th>
						<th
							data-field="bogenart"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('bogenart', $rolle_attribute_settings) OR $rolle_attribute_settings['bogenart']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Bogenart</th>
						<th
							data-field="hc"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('hc', $rolle_attribute_settings) OR $rolle_attribute_settings['hc']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
							data-filter-order-by="asc"
						>Hauptcode</th>
						<th
							data-field="lrt_info_code"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('lrt_info_code', $rolle_attribute_settings) OR $rolle_attribute_settings['lrt_info_code']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
							data-formatter="lrtCodeFormatter"
						>LRT-Code</th>
						<th
							data-field="biotopname"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('biotopname', $rolle_attribute_settings) OR $rolle_attribute_settings['biotopname']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-width="40px"
							data-filter-control="input"
						>Biotopname</th>
						<th
							data-field="anz_habitatvorkommen"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('anz_habitatvorkommen', $rolle_attribute_settings) OR $rolle_attribute_settings['anz_habitatvorkommen']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
						>Habitate</th>
						<th
							data-field="koordinator_rueckweisung"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('koordinator_rueckweisung', $rolle_attribute_settings) OR $rolle_attribute_settings['koordinator_rueckweisung']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-formatter="boolTypeFormatter"
							data-filter-control="select"
						>Rückweisung durch Koordinator</th>
						<th
							data-field="abgabe_name"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('abgabe_name', $rolle_attribute_settings) OR $rolle_attribute_settings['abgabe_name']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Abgabename</th>
						<th
							data-field="pruefer_rueckweisung"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('pruefer_rueckweisung', $rolle_attribute_settings) OR $rolle_attribute_settings['pruefer_rueckweisung']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-formatter="boolTypeFormatter"
							data-filter-control="select"
						>Rückweisung durch Prüfer</th>
						<th
							data-field="pruefer_pruefhinweis"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('pruefer_pruefhinweis', $rolle_attribute_settings) OR $rolle_attribute_settings['pruefer_pruefhinweis']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="input"
						>Prüfhinweise</th>
						<th
							data-field="korrektur_nr"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('korrektur_nr', $rolle_attribute_settings) OR $rolle_attribute_settings['korrektur_nr']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Rückgabe</th>
						<th
							data-field="kartierer_name"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('kartierer_name', $rolle_attribute_settings) OR $rolle_attribute_settings['kartierer_name']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Kartierer</th>
						<th
							data-field="user_id"
							data-sortable="true"
							data-visible="<? echo ($rolle_attribute_settings['user_id']['switched_on'] == 1 ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>User-ID</th>
						<th
							data-field="stand"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('stand', $rolle_attribute_settings) OR $rolle_attribute_settings['stand']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-filter-control="select"
						>Bearbeitungsstand</th>
						<th
							data-field="rueckgabewunsch"
							data-sortable="true"
							data-visible="<? echo ((!array_key_exists('rueckgabewunsch', $rolle_attribute_settings) OR $rolle_attribute_settings['rueckgabewunsch']['switched_on'] == 1) ? 'true': 'false'); ?>"
							data-switchable="true"
							data-formatter="boolTypeFormatter"
							data-filter-control="select"
						>Rueckgabewunsch</th>
					</tr>
				</thead>
			</table>
		</div>
		<script language="javascript" type="text/javascript">
			$('#gui-table').css('width', '100%');
			$('#container_paint').css('height', 'auto');
			resizeBootstrapTable = function() {
				//console.log('browserwidth: ' + document.body.width);
				$('.bootstrap-table').css('width', (document.body.offsetWidth - 240) + 'px');
			};
			window.addEventListener('resize', resizeBootstrapTable);

			$(function () {
				result = $('#eventsResult');
				result.success = function(text) {
					message([{ type: 'notice', msg: text}], 1000, 500);
		/*			result.text(text);
					result.removeClass('alert-danger');
					result.addClass('alert-success');*/
				};
				result.error = function(text) {
					message([{ type: 'error', msg: text}]);
		/*			result.text(text);
					result.removeClass('alert-success');
					result.addClass('alert-danger');*/
				};

				// event handler
				$('#kartierungen_table')
				.one('load-success.bs.table', function (e, data) {
					resizeBootstrapTable();
					result.success('Tabelle erfolgreich geladen');
					registerEventHandler();
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
/*
				.on('all.bs.table', function(e, name, args) {
					console.log('event: %s', name);
				})
				.on('sort.bs.table', function(e, spalte, richtung) {
					//$('#waiting_div').show();
					//console.log('Sortiere Spalte: %s Richtung: %s', spalte, richtung);
					//message([{ type: 'notice', msg: 'Sortiere Spalte: ' + spalte + ' Richtung: ' + (richtung == 'desc' ? 'absteigend' : 'aufsteigend')}])
				})
*/
				// more examples for register events on data tables: http://jsfiddle.net/wenyi/e3nk137y/36/

				$('.change-bearbeitungsstand').on(
					'click',
					function (e) {
						//console.log('Ändere Bearbeitungsstand');
						updateBearbeitungsstand(e.target.getAttribute('stufe'), e.target.value);
					}
				);

				$('#user_filter_selector').on(
					'change',
					function(e) {
						window.location.href = 'index.php?go=show_snippet&snippet=liste_kartierobjekte&bearbeitungsstufe=<? echo $this->formvars['bearbeitungsstufe']; ?>' + (e.target.value == 'nureigene' ? '&user_id=<? echo $this->user->id; ?>' : '') + '&csrf_token=<?php echo $_SESSION['csrf_token']; ?>';
					}
				);

				$('#update_abgabe_name_button').on(
					'click',
					function(e) {
						updateAbgabename($('#abgabe_name_field').val());
					}
				); <?
				if ($gesperrt_fuer_kartierer) { ?>
					$('#rueckgabewuensche_button').on(
						'click',
						function(e) {
							set_rueckgabewuensche();
						}
					); <?
				} ?>

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

			function updateBearbeitungsstand(stufe, stand) { <?
				if ($this->formvars['bearbeitungsstufe'] != '') { ?>
					var selected_rows = $('#kartierungen_table').bootstrapTable('getSelections'),
							kartierung_ids = $.map(
								selected_rows,
								function(row) {
									if (row.stand != stand) {
										return row.kartierung_id;
									}
								}
							);

					if (kartierung_ids.length > 0) {
						//console.log('Update selected kartierung_ids: ' + kartierung_ids)
						$.ajax({
							type: "POST",
							url: 'index.php',
							data: {
								go: "show_snippet",
								snippet: "update_bearbeitungsstaende",
								mime_type: "application/json",
								format: "json",
								stufe_alt: <? echo $this->formvars['bearbeitungsstufe']; ?>,
								stufe_neu: stufe,
								objektart: 'Kartierobjekte',
								kartierung_ids: kartierung_ids.join(','),
								csrf_token: '<? echo $_SESSION['csrf_token']; ?>'
							},
							success: function(response) {
								debug_resp = response;
//								console.log('response: %s', response);
								var result = JSON.parse(response)[0];
//								console.log('result: %o', result);
								if (result.kartierung_ids != '') {
									$('#kartierungen_table').bootstrapTable('refresh', {silent: true});
									message([
										{ type: 'notice', msg: 'Bearbeitungsstufe erfolgreich geändert für Objekte mit Id: ' + result.kartierung_ids },
										{ type: 'notice', msg: 'Bitte Warten. Tabelle wird neu geladen!'}
									]);
								}
								if (result.success == false) {
									message([{ type: 'error', msg: result.msg}]);
								}
							},
							error: function(xhr) {
								alert('Fehler beim Ändern des Bearbeitungsstandes von kartierung_ids: ' + kartierung_ids + ' Fehlerstatus: ' + xhr.status + ' Meldung: ' + xhr.statusText);
							}
						});
					}
					else {
						message('Keine änderbaren Kartierobjekte ausgewählt!');
					} <?
				} ?>
			}

			function updateAbgabename(abgabe_name) {
				//console.log('update Abgabename: %s', abgabe_name);

				var selected_rows = $('#kartierungen_table').bootstrapTable('getSelections'),
						objectIds = $.map(
							selected_rows,
							function(row) {
								return row.kartierung_id;
							}
						);

				if (objectIds.length > 0) {
					//console.log('Update selected objectIds: ' + objectIds);

					$.ajax({
						type: "Post",
						url: 'index.php',
						data: {
							go: "show_snippet",
							snippet: "update_abgabe_name",
							mime_type: "application/json",
							format: "json",
							abgabe_name: abgabe_name,
							objektart: 'Kartierobjekte',
							object_ids: objectIds.join(','),
							csrf_token: '<? echo $_SESSION['csrf_token']; ?>'
						},
						success: function(response) {
							var result = JSON.parse(response)[0];
							//console.log('Die Antwort vom Server ist da: %o', result);
							if (result.object_ids != '') {
								$('#kartierungen_table').bootstrapTable('refresh', {silent: true});
								message([
									{ type: 'notice', msg: 'Abgabename erfolgreich geändert für Objekte mit Id: ' + result.object_ids},
									{ type: 'notice', msg: 'Bitte Warten. Tabelle wird neu geladen!'}
								]);
							}
							if (result.success == false) {
								message([{ type: 'error', msg: result.msg}]);
							}
						},
						error: function(xhr) {
							alert('Fehler beim Ändern des Abgabenamen der Objekte mit ID: ' + objectIds.join(', ') + ' Fehlerstatus: ' + xhr.status + ' Meldung: ' + xhr.statusText);
						}
					});
				}
				else {
					message('Keine änderbaren Objekte ausgewählt!');
				}
			}

			function set_rueckgabewuensche() {
				//console.log('set_rueckgabewuensche');

				var selected_rows = $('#kartierungen_table').bootstrapTable('getSelections'),
						objectIds = $.map(
							selected_rows,
							function(row) {
								return row.kartierung_id;
							}
						);

				if (objectIds.length > 0) {
					//console.log('Update selected objectIds: ' + objectIds);

					$.ajax({
						type: "Post",
						url: 'index.php',
						data: {
							go: "show_snippet",
							snippet: "update_rueckgabewuensche",
							mime_type: "application/json",
							format: "json",
							objektart: 'Kartierobjekte',
							object_ids: objectIds.join(','),
							csrf_token: '<? echo $_SESSION['csrf_token']; ?>'
						},
						success: function(response) {
							//console.log(response);
							var result = JSON.parse(response)[0];
							//console.log('Die Antwort vom Server ist da: %o', result);
							if (result.object_ids != '') {
								$('#kartierungen_table').bootstrapTable('refresh', {silent: true});
								message([
									{ type: 'notice', msg: 'Rückgabewünsche erfolgreich eingetragen für Objekte mit Id: ' + result.object_ids},
									{ type: 'notice', msg: 'Bitte Warten. Tabelle wird neu geladen!'}
								]);
							}
							if (result.success == false) {
								message([{ type: 'error', msg: result.msg}]);
							}
						},
						error: function(xhr) {
							alert('Fehler beim Eintragen der Rückgabewünsche der Objekte mit ID: ' + objectIds.join(', ') + ' Fehlerstatus: ' + xhr.status + ' Meldung: ' + xhr.statusText);
						}
					});
				}
				else {
					message('Keine Objekte ausgewählt!');
				}
			}

			function checkboxFormatter(value, row, index) {
				if (
					(
						'<? echo $this->Stelle->Bezeichnung; ?>' == 'Kartierung' &&
						row.user_id != <? echo $this->user->id; ?>
					) ||
					row.stand != '<? echo $bearbeitungsstufe->get('stand'); ?>' ||
					(
						<? echo ($gesperrt_fuer_kartierer ? 'true' : 'false'); ?> &&
						row.rueckgabewunsch == 't'
					)
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

			// formatter functions
			function boolTypeFormatter(value, row) {
				//console.log(value);
				return (value == 't' ? 'ja' : 'nein');
			}

			function lrtCodeFormatter(value, row) {
				return (row.lrt_info_code ? row.lrt_info_code : row.lrt_code);
			}

			function kartierungEditFunctionsFormatter(value, row) {
				var output = '<a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=105&value_kartierung_id=' + value + '&operator_kartierung_id==&csrf_token=<? echo $_SESSION['csrf_token']; ?>">' + row.kartierung_id + '</a>';
				//console.log(output);
				return output;
			}
		</script>
	</div>
</div>