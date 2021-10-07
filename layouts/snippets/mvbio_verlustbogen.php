<?php
	# Erzeugt neuen Verlustbogen und verknüpft mit übergebener bogen_id
	$bogenart_id = 3;

	# Übernahme der Daten des Boges mit der ID $bogen_id in den neuen Verlustbogen
	# Es werden alle Felder übernommen, die passen.
	$fehler = false;
	# Abfrage der zwischen Bogen und Verlustobjekt übereinstimmenden Spaltennamen
	$sql = "
		SELECT
			k.column_name,
			ba.archivtabelle
		FROM
			information_schema.columns b
			JOIN information_schema.columns k ON b.column_name = k.column_name
			JOIN mvbio.bogenarten ba ON b.table_name = ba.archivtabelle
			JOIN archiv.erfassungsboegen eb ON ba.id = eb.bogenart_id
		WHERE
			b.table_schema = 'archiv'
			AND	eb.id = " . $this->formvars['bogen_id'] . "
			AND k.table_schema = 'mvbio'
			AND k.table_name = 'verlustobjekte'
			AND k.column_name NOT IN (
				'id',
				'kartierobjekt_id',
				'kampagne',
				'kartiergebiet_name',
				'userid',
				'unb'
			)
	";
	#echo '<br>SQL zum Abfragen der Spaltennamen, die übernommen werden sollen: ' . $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	if ($ret['success']) {
		$insert_columns = $select_columns = array();
		while ($rs = pg_fetch_assoc($ret[1])) {
			$insert_columns[$rs['column_name']] = $rs['column_name'];
			$select_columns[$rs['column_name']] = $rs['column_name'];
			$archivtabelle = $rs['archivtabelle'];
		}

		# Übernahme von Auto-Werten für die neue Kartierung
		$insert_columns['user_id'] = 'user_id';
		$insert_columns['stelle_id'] = 'stelle_id';
		$select_columns['user_id'] = $this->user->id;
		$select_columns['stelle_id'] = $this->Stelle->id;
		$select_columns['bearbeitungsstufe'] = 1;
		$select_columns['kampagne_id'] = (rolle::$layer_params['kampagne_id'] == '' ? 0 : rolle::$layer_params['kampagne_id']);
		$select_columns['kartiergebiet_id'] = (rolle::$layer_params['kartiergebietfilter'] == '' ? 0 : rolle::$layer_params['kartiergebietfilter']);
#    $select_columns['e_datum'] = 'NULL';
    $select_columns['l_datum'] = 'NULL';
		$select_columns['bogenart_id'] = 3;
		$insert_columns['bogen_id'] = 'bogen_id';
		$select_columns['bogen_id'] = $this->formvars['bogen_id'];
		$select_columns['foto'] = 0;

		# Eintragen des neuen Verlustbogens mit den Daten des ausgewählten Bogens
		$sql = "
			INSERT INTO mvbio.verlustobjekte (" . implode(', ', $insert_columns) . ")
			SELECT
				" . implode(', ', $select_columns) . "
			FROM
				archiv." . $archivtabelle . "
			WHERE
				id = " . $this->formvars['bogen_id'] . "
			RETURNING id
		";
		#echo '<br>SQL zum Eintragen des Verlustobjektes: ' . $sql;
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if ($ret['success']) {
			$rs = pg_fetch_assoc($ret[1]);
			$new_verlustobjekt_id = $rs['id'];

			# Eintragen der Nebencodes für das neue Kartierobjekt
			$sql = "
				INSERT INTO mvbio.biotoptypen_nebencodes_verlustobjekte (verlustobjekt_id, code, flaechendeckung_prozent)
				SELECT
					" . $new_verlustobjekt_id . ", nc.code, nc.flaechendeckung_prozent
				FROM
					archiv.biotoptypen_nebencodes nc
				WHERE
					nc.kartierobjekt_id = " . $this->formvars['bogen_id'] . "
			";
			#echo '<br>SQL zum Eintragen der Nebencodes für Verlustobjekt: ' . $sql;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if ($ret['success']) {
				$this->add_message('success', 'Neues Verlustobjekt mit id: ' . $new_verlustobjekt_id . ' erfolgreich aus Archivtabelle: ' . $archivtabelle . ' übernommen.<br>Sie können das Verlustobjekt jetzt bearbeiten.');
				$fehler = false;
			}
			else {
				$this->add_message('error', 'Fehler beim Eintragen der Nebencodes.');
				$fehler = true;
			}
		}
		else {
			$this->add_message('error', 'Fehler beim Eintragen des Datensatzes als neues Verlustobjekt!<br>Hinweis: Wählen Sie immer vor der Übernahme unter Einstellungen die Kampagne und Kartiergebiet in die übernommen werden soll.');
			$fehler = true;
		}
	}
	else {
		$this->add_message('error', 'Fehler bei der Abfrage der Kartierobjekteigenschaften!');
		$fehler = true;
	}

	if ($fehler) { ?>
		<h2 style="margin-top: 20px; margin-bottom: 20px;">Datenübernahme von archivierten Bögen als neues Verlustobjekt</h2>
		<a href="https://mvbio.de/kvwmap/index.php?go=get_last_query">Zurück</a> zur letzten Sachdatenanzeige.<?php
		/*
		Das geht nicht weil das includiert dargestellt werden würde
		$this->last_query = $this->user->rolle->get_last_query();
		$this->formvars['go'] = $this->last_query['go'];
		if ($this->formvars['go'] == 'Layer-Suche_Suchen') {
			$this->GenerischeSuche_Suchen();
		}
		else {
			$this->queryMap();
		}*/
	}
	else {
		ob_end_clean();
		ob_start();
		header('Content-Type: text/html; charset=utf-8');
		include(CLASSPATH . 'Layer.php');
		$layer = Layer::find_by_name($this, 'Verlustobjekte');
		$this->formvars['selected_layer_id'] = $layer->get('Layer_ID');
		$this->formvars['value_verlustobjekt_id'] = $new_verlustobjekt_id;
		$this->formvars['operator_verlustobjekt_id'] = '=';
		$this->GenerischeSuche_Suchen();
		exit();
	}
?>