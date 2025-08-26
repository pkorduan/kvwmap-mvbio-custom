<? include_once(CLASSPATH . 'FormObject.php'); ?>
<script src="<? echo CUSTOM_PATH; ?>layouts/snippets/mvbio_boegen4archiv.js"></script>
<h2 style="margin: 10px">Archivierung von Kartier- und Verlustobjekten</h2>
<div style="
	margin-bottom: 10px;
	line-height: 21px;
	text-align: left;
	padding-left: 50px
">
	Bögen die dem Archivkartiergebiet <span style="font-size: 16px; font-weight: bold">"<?php echo $archivkartiergebiet->get('bezeichnung'); ?> (ID: <?php echo $archivkartiergebiet->get_id(); ?>)"</span> zur Archivierung zugeordnet sind:<br>
	<input id="archivkartiergebiet_id" type="hidden" name="kampagne_kartiergebiet_id" value="<?php echo $archivkartiergebiet->get_id(); ?>"/><?
	foreach ($bogenarten AS $bogenart) { ?>
		<script>
			bogenarten.push(<? echo $bogenart->get('id'); ?>);
		</script><?
		$num_assigned_boegen += count($bogenart->assigned_boegen);
		$num_archived_boegen += count($bogenart->archived_boegen); ?>
		<br><span style="font-size: 18px; font-weight: bold"><? echo $bogenart->get('name_plural'); ?> (bogenart_id: <? echo $bogenart->get('id'); ?>)</span>
		<div id="assigned-div_<? echo $bogenart->get('id'); ?>" class="assigned-div" style="margin-top: 0px; display: <? echo (count($bogenart->archived_boegen) > 0 ? "none" : "block"); ?>">
			Anzahl zugeordnete Bögen gesamt: <span id="num_assigned_boegen_<? echo $bogenart->get('id'); ?>"><? echo count($bogenart->assigned_boegen); ?></span><?
			if (count($bogenart->assigned_boegen) > 0) { ?>
				=> <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=<? echo $bogenart->get('bogen4archiv_layer_id'); ?>&operator_kartiergebiet_id==&value_kartiergebiet_id=<? echo $archivkartiergebiet->get_id(); ?>" title="Anzeige der zu archivierenden Kartierobjekte in der Suchergebnisanzeige in einem neuen Tab" target="_blank" class="link"><? echo $bogenart->get('bezeichnung'); ?>daten anzeigen</a><?
				foreach ($bogenart->kartiergebiete AS $kartiergebiet) { ?>
					<br>Anzahl aus Kartiergebiet <? echo $kartiergebiet->get('bezeichnung'); ?> (<? echo $kartiergebiet->get('id'); ?>): <? echo $kartiergebiet->get('anzahl_boegen'); ?> => <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=105&operator_kartiergebiet_id==&value_kartiergebiet_id=<? echo $kartiergebiet->get('id'); ?>&operator_bogenart_id==&value_bogenart_id=<? echo $bogenart->get('id'); ?>" title="Anzeige der zu archivierenden Kartierobjekte in der Suchergebnisanzeige in einem neuen Tab" target="_blank" class="link">Kartierobjekte für <? echo $bogenart->get('name_plural'); ?> anzeigen</a><?
				} ?>
				<!--br>
				Archivbögen nach Übernahme gleich aktiv setzen? <input id="set_active_<?php echo $bogenart->get('id'); ?>" type="checkbox" name="set_active_<?php echo $bogenart->get('id'); ?>" style="vertical-align: text-top"> <span data-tooltip="Wenn die Option gewählt wird, werden alle existierenden <? echo $bogenart->get('name_plural'); ?> im Archiv, die sich innerhalb des Archivkartiergebietes befinden auf unaktuell gesetzt und die neuen <? echo $bogenart->get('name_plural'); ?> auf aktuell." style="vertical-align: -2px"></span><br>
				Prüfer <?
				echo FormObject::createSelectField(
					'pruefer_' . $bogenart->get('id'),
					array_map(
						function($user) {
							$parts = explode(',', $user);
							return array(
								'value' => trim($parts[1]) . ' ' . $parts[0],
								'output' => $user
								);
						},
						$this->user->getall_Users("Name, Vorname")['Bezeichnung']
					),
					null, // selected,
					1, // size
					'margin-top: 5px; width: 250px; margin-right: 10px', // style
					null, // onchange
					'pruefer_' . $bogenart->get('id'), // id
					'', // multiple
					'', // class
					'-- Prüfer wählen --', // first option
					'', // option_style
					'', // option_class
					'', // onclick
					'', // onmouseenter
					'Prüfer auswählen', // title
					array() // data attributes
				);
				?><br>
				<input type="button" value="Jetzt Archivieren" data-bogenart_id="<?php echo $bogenart->get('id'); ?>" data-bogenart_archivtabelle="<? echo $bogenart->get('archivtabelle'); ?>" onclick="archiv_boegen(this);" style="margin-top: 7px"/>--><?
			} ?>
		</div>

		<div id="archived-div_<? echo $bogenart->get('id'); ?>" class="archived-div" style="display: <? echo (count($bogenart->archived_boegen) > 0 ? "block" : "none"); ?>">
			Bereits archivierte Bögen: <span id="num_archived_boegen_<? echo $bogenart->get('id'); ?>"><? echo count($bogenart->assigned_boegen); ?></span>
		</div><?
	} ?>
	<br>
	<br>
	<div class="assigned-div" style="display: <? echo ($num_archived_boegen == 0 ? "block" : "none"); ?>">
		Gesamtzahl der zur Archivierung zugeordneten Bögen: <span id="num_assigned_boegen"><? echo $num_assigned_boegen; ?></span><br>
		Archivbögen nach Übernahme gleich aktiv setzen? <input id="set_active" type="checkbox" name="set_active" style="vertical-align: text-top"> <span data-tooltip="Wenn die Option gewählt wird, werden alle existierenden Bögen im Archiv, die sich innerhalb des Archivkartiergebietes befinden auf unaktuell gesetzt und die neuen Bögen auf aktuell." style="vertical-align: -2px"></span><br>
		Prüfer <? echo FormObject::createSelectField(
			'pruefer',
			array_map(
				function($user) {
					$parts = explode(',', $user);
					return array(
						'value' => trim($parts[1]) . ' ' . $parts[0],
						'output' => $user
						);
				},
				$this->user->getall_Users("Name, Vorname")['Bezeichnung']
			),
			null, // selected,
			1, // size
			'margin-top: 5px; width: 250px; margin-right: 10px', // style
			null, // onchange
			'pruefer', // id
			'', // multiple
			'', // class
			'-- Prüfer wählen --', // first option
			'', // option_style
			'', // option_class
			'', // onclick
			'', // onmouseenter
			'Prüfer auswählen', // title
			array() // data attributes
		);
		?><br>
		<input type="button" value="Jetzt Archivieren" onclick="archiv_boegen(this);" style="margin-top: 7px"/>
	</div>

	<div id="archived-div" class="archived-div" style="display: <? echo ($num_archived_boegen > 0 ? "block" : "none"); ?>">
		Gesamtzahl der bereits archivierten Bögen: <span id="num_archived_boegen"><? echo $num_archived_boegen; ?></span><br>
		<input type="button" value="Rückgängig machen" onclick="undo_archiv_boegen(this);" style="margin-top: 7px"/>
	</div>
	<br>
	<a href="index.php?go=show_snippet&snippet=mvbio_archivierung&action=show_kartiergebiete&kampagne_id=<? echo $kampagne_id; ?>&csrf_token=<? echo $_SESSION['csrf_token']; ?>" title="Anzeige der Zuordnung der Kartiergebiete zu den Archivkartiergebieten" class="link">zur Zuordnung der Kartiergebiete zu den Archivkartiergebieten</a>
</div>