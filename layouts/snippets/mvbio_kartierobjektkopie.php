<?php
	$kartierobjekt_ids = array();
	if ($this->formvars['kartierobjekt_id']) {
		$kartierobjekt_ids[] = $this->formvars['kartierobjekt_id'];
	}

	foreach ($kartierobjekt_ids as $kartierobjekt_id) {
		# Abfragen der Attribute der Tabelle mvbio.kartierobjekte
		# Attribute, die nicht übernommen werden sollen oder deren Werte per
		# Default-Werte gesetzt werden sollen, sind hier in der NOT IN Klausel
		# mit aufgeführt.
		$sql = "
			SELECT
				column_name
			FROM
				information_schema.columns b
			WHERE
				table_schema = 'mvbio' AND
				table_name = 'kartierobjekte' AND
				column_name NOT IN (
					'erhaltung',
					'eu_nr', -- wird im trigger tr_60_update_eu_nr gesetzt
					'flaeche_geom_20201113',
					'flaeche_kvwmap_20201113',
					'foto',
					'gemeinde_id', -- wird im trigger tr_62_update_gemeinde_id gesetzt
					'geom',
					'geom_topo',
					'id', -- wird automatisch über serial gesetzt
					'kommentar_zum_korrekturhinweis',
					'kommentar_zum_pruefhinweis',
					'koordinator_korrekturhinweis',
					'koordinator_rueckweisung',
					'korrektur_nr',
					'label', -- wird im trigger tr_20_lfd_nr_label gesetzt
					'lfd_nr_kr', -- wird im trigger tr_20_lfd_nr_label gesetzt
					'lrt'
					'pdf_document',
					'pruefdatum',
					'pruefer',
					'pruefer_pruefhinweis',
					'pruefer_rueckweisung',
					'rueckgabewunsch',
					'updated_at',
					'updated_from',
					'uuid', -- wird automatisch über uuid_generate_v1mc() erzeugt
					'zur_pruefung_freigegeben',
					'zusammengefasste_boegen',
					'qs_ausnahme',
					'vorgaenger_id',
					'zusammengefasste_ids',
					'lrt_objekt_id'
				)
		";
		# echo '<br>SQL zum Abfragen der Spaltennamen der Tabelle Kartierobjekte: ' . $sql;
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if (!$ret['success']) {
			show_error_page('Fehler bei der Abfrage der Attribute des Kartierobjekte!');
			break;
		}
		$insert_columns = $select_columns = array();
		while ($rs = pg_fetch_assoc($ret[1])) {
			$insert_columns[$rs['column_name']] = $rs['column_name'];
			$select_columns[$rs['column_name']] = $rs['column_name'];
		}

		$sql = "
			SELECT
				k.id,
				k.kampagne_id,
				k.kartiergebiet_id,
				k.kartierebene_id,
				CASE
					WHEN " . $this->Stelle->id . " IN (1, 8) THEN false
					ELSE
						" . $this->Stelle->id . " NOT IN (ba.aenderungsberechtigte_stelle_id, 6) OR
						CASE
							WHEN " . $this->Stelle->id . " IN (3, 6) THEN " . $this->user->id . " <> ALL (SELECT nutzer_id FROM mvbio.kartierer WHERE kartiergebiet_id = kg.id)
							ELSE false
						END
				END AS editiersperre
			FROM
				mvbio.kartierobjekte k JOIN
				mvbio.code_bearbeitungsstufen ba ON k.bearbeitungsstufe = ba.stufe JOIN
				mvbio.kartiergebiete kg ON k.kartiergebiet_id = kg.id
			WHERE
				k.id = " . $kartierobjekt_id . "
			";
			#echo 'SQL zur Abfrage des Kartierobjektes: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Fehler bei der Abfrage des Kartierobjektes welches kopiert werden soll.');
				break;
			}

			if (pg_num_rows($ret[1]) == 0) {
				show_error_page('Das zu kopierende Kartierobjekt mit der ID: ' . $kartierobjekt_id . ' existiert nicht und kann deswegen nicht kopiert werden.');
				breaak;
			}

			$kartierobjekt = pg_fetch_assoc($ret[1]);
			if ($kartierobjekt['editiersperre'] === 0) {
				show_error_page('Das Kartierobjekt ist für den Benutzer ID: ' . $this->user->id . ' in der Stelle ID: ' . $this->Stelle->id . ' nicht editierbar und kann deshalb nicht kopiert werden.');
				break;
			}

			if ($kartierobjekt['kampagne_id'] != rolle::$layer_params['kampagne_id']) {
				show_error_page('Das Kartierobjekt gehört nicht zur ausgewählten Kampagne ID: ' . rolle::$layer_params['kampagne_id'] . ' und darf deshalb nicht für die Kopie verwendet werden.');
				break;
			}

			if ($kartierobjekt['kartiergebiet_id'] != rolle::$layer_params['kartiergebietfilter']) {
				echo 'lp: ' . print_r($layer_params, true);
				show_error_page('Das Kartierobjekt gehört nicht zum ausgewählten Kartiergebiet ID: ' . rolle::$layer_params['kartiergebietfilter'] . ' und darf deshalb nicht für die Kopie verwendet werden.');
				break;
			}

			if ($kartierobjekt['kartierebene_id'] != rolle::$layer_params['kartierebenenfilter']) {
				show_error_page('Das Kartierobjekt gehört nicht zur ausgewählten Kartierebene ID: ' . rolle::$layer_params['kartierebenenfilter'] . ' und darf deshalb nicht für die Kopie verwendet werden.');
				break;
			}
			# Übernahme von Auto-Werten für die neue Kartierung
			$select_columns['bearbeitungsstufe'] = 1;
			$select_columns['created_at'] = "'" . date('Y-m-d H:i:s') . "'";
			$select_columns['created_from'] = "'" . $this->user->Vorname . ' ' . $this->user->Name . "'";
			$select_columns['kartierer'] = "'" . $this->user->Vorname . ' ' . $this->user->Name . "'";
			$select_columns['user_id'] = $this->user->id;
			# Eintragen des neuen Kartierobjektes mit den Daten des ausgewählten Kartierobjektes
			#print_r(implode(', ', array_keys($select_columns)));
			$sql = '
				INSERT INTO mvbio.kartierobjekte ("' . implode('", "', $insert_columns) . '")
				SELECT
					' . implode(', ', $select_columns) . '
				FROM
					mvbio.kartierobjekte
				WHERE
					id = ' . $kartierobjekt_id . '
				RETURNING id
			';
			#echo '<br>SQL zum Kopieren des Kartierobjektes' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('SQL-Fehler');
				break;
			}
			if (pg_num_rows($ret[1]) !== 1) {
				show_error_page('Die neue Objekt ID konnte nicht ermittelt werden!');
				break;
			}
			$rs = pg_fetch_assoc($ret[1]);
			if (!array_key_exists('id', $rs)) {
				show_error_page('Die neue Objekt ID konnte nicht abgefragt werden!');
				break;
			}
			$new_kartierobjekt_id = $rs['id'];
			if ($new_kartierobjekt_id === '' OR $new_kartierobjekt_id < 1) {
				show_error_page('Die abgefragte neue Kartierobjekt ID ist fehlerhaft!');
				break;
			}

			# Eintragen der Pflanzenvorkommen
			$sql = "
				INSERT INTO mvbio.pflanzenvorkommen (kartierung_id, species_nr, valid_nr, dzv, fsk, rl, cf, tax, bav)
				SELECT
					" . $new_kartierobjekt_id . ", pv.species_nr, pv.valid_nr, pv.dzv, pv.fsk, pv.rl, pv.cf, pv.tax, pv.bav
				FROM
					mvbio.kartierobjekte k JOIN
					mvbio.pflanzenvorkommen pv ON k.id = pv.kartierung_id
				WHERE
					k.id = " . $kartierobjekt_id . "
			";
			#echo '<br>SQL zum Eintragen der Pflanzenvorkommen: ' . $sql; exit;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Die Pflanzenvorkommen konnten nicht übernommen werden.');
				break;
			}

			# Eintragen der Nebencodes für das neue Kartierobjekt
			$sql = "
				INSERT INTO mvbio.biotoptypen_nebencodes (kartierung_id, code, flaechendeckung_prozent)
				SELECT
					" . $new_kartierobjekt_id . ", nc.code, nc.flaechendeckung_prozent
				FROM
					mvbio.biotoptypen_nebencodes nc
				WHERE
					nc.kartierung_id = " . $kartierobjekt_id. "
			";
			echo '<br>SQL zum Eintragen der Nebencodes: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Fehler beim Eintragen der Nebencodes.');
				break;
			}

			# Eintragen der Habitatvorkommen für das neue Kartierobjekt
			$sql = "
				INSERT INTO mvbio.habitatvorkommen (kartierung_id, code)
				SELECT
					" . $new_kartierobjekt_id . ", hv.code
				FROM
					mvbio.habitatvorkommen hv
				WHERE
					hv.kartierung_id = " . $kartierobjekt_id . "
			";
			echo '<br>SQL zum Eintragen der Habitatvorkommen: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Fehler beim Eintragen der Habitate und Stukturen.');
				break;
			}

			# Eintragen der Empfehlungen und Massnahmen für das neue Kartierobjekt
			$sql = "
				INSERT INTO mvbio.empfehlungen_massnahmen (kartierung_id, code)
				SELECT
					" . $new_kartierobjekt_id . ", em.code
				FROM
					mvbio.empfehlungen_massnahmen em
				WHERE
					em.kartierung_id = " . $kartierobjekt_id . "
			";
			echo '<br>SQL zum Eintragen des Empfehlungen und Massnahmen: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Fehler beim Eintragen der Empfehlungen.');
				break;
			}

			# Eintragen der Beeintraechtigungen und Gefaehrdungen für das neue Kartierobjekt
			$sql = "
				INSERT INTO mvbio.beeintraechtigungen_gefaehrdungen (kartierung_id, code)
				SELECT
					" . $new_kartierobjekt_id . ", bg.code
				FROM
					mvbio.beeintraechtigungen_gefaehrdungen bg
				WHERE
					bg.kartierung_id = " . $kartierobjekt_id . "
			";
			echo '<br>SQL zum Eintragen des Beeintraechtigungen und Gefaehrdungen: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if (!$ret['success']) {
				show_error_page('Fehler beim Eintragen der Beeintraechtigungen und Gefährdungen');
				break;
			}

			#echo '<p>Alles erfolgreich eingetragen';
			$this->add_message('success', 'Kartierobjekt ID: ' . $kartierobjekt_id . ' erfolgreich in das neue Kartierobjekt ID: ' . $new_kartierobjekt_id . ' kopiert.<br>Sie können das Kartierobjekt jetzt bearbeiten.');

			ob_end_clean();
			ob_start();
			header('Content-Type: text/html; charset=utf-8');
			$this->formvars['selected_layer_id'] = 105;
			$this->formvars['value_kartierung_id'] = $new_kartierobjekt_id;
			$this->formvars['operator_kartierung_id'] = '=';
			$this->GenerischeSuche_Suchen();
			exit();
	}

	function show_error_page($err_msg) {
		global $GUI;
		$GUI->add_message('error', 'Fehler beim Kopieren des Kartierobjektes ID: ' . $GUI->formvars['kartierobjekt_id'] . '! ' . $err_msg); ?>
		<h2 style="margin-top: 20px; margin-bottom: 20px;">Kopie des Kartierobjektes</h2>
		<a href="<? echo URL . APPLVERSION; ?>index.php?go=get_last_query">Zurück</a> zur letzten Sachdatenanzeige.<?
	}

?>