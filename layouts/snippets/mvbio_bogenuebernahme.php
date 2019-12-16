<?php
	# Übernahme der Daten des Boges mit der ID $bogen_id in ein neues Kartierobjekt
	# Wenn es ein Bewertungsbogen ist, wird sowohl eine Kartierung und eine Bewertung angelegt
	# Es werden alle Felder übernommen, die passen, außer bei Bewertungen. Dabei werden
	# die Ergebnisse der Bewertung nicht übernommen, da sie immer neu berechnet werden müssen
	$fehler = false;
	# Abfrage der zwischen Bogen und Kartierobjekt übereinstimmenden Spaltennamen
	$sql = "
		SELECT
			k.column_name,
			ba.archivtabelle
		FROM
			information_schema.columns b JOIN
			information_schema.columns k ON b.column_name = k.column_name JOIN
			mvbio.bogenarten ba ON b.table_name = ba.archivtabelle JOIN
			archiv.erfassungsboegen eb ON ba.id = eb.bogenart_id
		WHERE
			b.table_schema = 'archiv' AND	eb.id = " . $this->formvars['bogen_id'] . " AND
			k.table_schema = 'mvbio' AND k.table_name = 'kartierobjekte' AND
			k.column_name NOT IN (
				'id',
				'kartierobjekt_id',
				'kampagne',
				'kartiergebiet_name',
				'userid',
				'unb'
			)
	";
	#echo '<br>SQL zum Abfragen der Spaltennamen, die übernommen werden sollen.' . $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	if ($ret['success']) {
		$insert_columns = $select_columns = array();
		while ($rs = pg_fetch_assoc($ret[1])) {
			$insert_columns[$rs['column_name']] = $rs['column_name'];
			$select_columns[$rs['column_name']] = $rs['column_name'];
			$archivtabelle = $rs['archivtabelle'];
		}

		# Übernahme von Auto-Werten für die neue Kartierung
		$select_columns['stelle_id'] = $this->Stelle->id;
		$select_columns['user_id'] = $this->user->id;
		$select_columns['bearbeitungsstufe'] = 1;
		$select_columns['kampagne_id'] = (rolle::$layer_params['kampagne_id'] == '' ? 0 : rolle::$layer_params['kampagne_id']);
		$select_columns['kartiergebiet_id'] = (rolle::$layer_params['kartiergebietfilter'] == '' ? 0 : rolle::$layer_params['kartiergebietfilter']);
		if ($archivtabelle == 'bewertungsboegen') {
			$select_columns['bogenart_id'] = 2; # Bogenart von Kartierungsobjekten mit Bewertungsbögen ist 2
		}

		$sql = '
			INSERT INTO mvbio.kartierobjekte ("' . implode('", "', $insert_columns) . '")
			SELECT
				' . implode(', ', $select_columns) . '
			FROM
				archiv.' . $archivtabelle . '
			WHERE
				id = ' . $this->formvars['bogen_id'] . '
			RETURNING id
		';
		#echo '<br>SQL zum Eintragen des Kartierobjektes' . $sql;
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if ($ret['success']) {
			$rs = pg_fetch_assoc($ret[1]);
			$new_kartierung_id = $rs['id'];
			$sql = "
					INSERT INTO mvbio.pflanzenvorkommen (kartierung_id, pflanzenart_id, nummer, dzv, fsk, rl, cf, tax, bav)
					SELECT
						" . $new_kartierung_id . ", pv.pflanzenart_id, pv.nummer, pv.dzv, pv.fsk, pv.rl, pv.cf, pv.tax, pv.bav
					FROM
						archiv.erfassungsboegen eb JOIN
						archiv.pflanzenvorkommen pv ON eb.kartierobjekt_id = pv.kartierung_id
					WHERE
						eb.id = " . $this->formvars['bogen_id'] . "
			";
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if ($ret['success']) {
				$this->add_message('success', 'Neues Kartierobjekt mit id: ' . $new_kartierung_id . ' erfolgreich aus Archivtabelle: ' . $archivtabelle . ' übernommen.');

				if ($archivtabelle == 'bewertungsboegen') {
				# Abfrage der LRT-Gruppe
				$sql = "
					SELECT
						gr.tabelle_postfix
					FROM
						archiv.bewertungsboegen bb JOIN
						mvbio.lrt_gruppen gr ON bb.lrt_gr::integer = gr.id
					WHERE
						bb.id = " . $this->formvars['bogen_id'] . "
				";
				#echo '<br>SQL zum Abfragen des Bewertungsbogentabellennamenzusatzes für Tabelle aus der übernommen und in die geschrieben werden sollen: ' . $sql;
				$ret = $this->pgdatabase->execSQL($sql, 4, 0);
				if ($ret['success']) {
					$rs = pg_fetch_assoc($ret[1]);
					$archivtabelle = 'bewertungsboegen_' . $rs['tabelle_postfix'];
					$mvbiotabelle = 'bewertungen_' . $rs['tabelle_postfix'];

					# Abfrage der zwischen Bewertungsbogen und Bewertung übereinstimmenden Spaltennamen
					$sql = "
						SELECT
							b.column_name
						FROM
							information_schema.columns b JOIN
							information_schema.columns k ON b.column_name = k.column_name
						WHERE
							b.table_schema = 'archiv' AND	b.table_name = '" . $archivtabelle . "' AND
							k.table_schema = 'mvbio' AND k.table_name = '" . $mvbiotabelle .  "' AND
							k.column_name NOT IN (
								'id',
								'kartierobjekt_id',
								'kampagne_id',
								'kampagne',
								'kartiergebiet_id',
								'kartiergebiet_name',
								'userid',
								'sys_habit',
								'sys_leben',
								'sys_beein',
								'sys_erhalt',
								'bea_habit',
								'bea_leben',
								'bea_beein',
								'bea_erhalt'
							)
					";
					#echo '<br>SQL zum Abfragen der Spaltennamen, die übernommen werden sollen.' . $sql;
					$ret = $this->pgdatabase->execSQL($sql, 4, 0);
					if ($ret['success']) {
						$insert_columns = $select_columns = array();
						while ($rs = pg_fetch_assoc($ret[1])) {
							$insert_columns[$rs['column_name']] = $rs['column_name'];
							$select_columns[$rs['column_name']] = $rs['column_name'];
						}
						$select_columns['bearbeitungsstufe'] = 1;
						# Hinzufügen der Kartierobjekt_id, die in den Bewertungen kartierung_id heißt zu Abfrage- und Insertattributen
						$insert_columns['kartierobjekt_id'] = 'kartierung_id';
						$select_columns['kartierobjekt_id'] = $new_kartierung_id;
						$sql = '
							INSERT INTO mvbio.' . $mvbiotabelle . ' ("' . implode('", "', $insert_columns) . '")
							SELECT
								' . implode(', ', $select_columns) . '
							FROM
								archiv.' . $archivtabelle . '
							WHERE
								id = ' . $this->formvars['bogen_id'] . '
						';
						#echo '<br>SQL zum Eintragen des Kartierobjektes' . $sql;
						$ret = $this->pgdatabase->execSQL($sql, 4, 0);
						if ($ret['success']) {
							$this->add_message('success', 'Neue Bewertung für Kartierobjekt: ' . $new_kartierung_id . ' angelegt.');
						}
						else {
							$this->add_message('error', 'Fehler bei dem Eintragen der neuen Bewertung auf der Basis des Bewertungsbogens der Kartierung: ' . $this->formvars['bogen_id'] . '!');
							$fehler = true;
						}
					}
				}
				else {
					$this->add_message('error', 'Fehler bei der Abfrage des Namen der Bewertungsbogenarchivtabelle!');
					$fehler = true;
				}
			}
			}
		}
		else {
			$this->add_message('error', 'Fehler beim Eintragen des Datensatzes als neue Kartierung!<br>Wählen Sie vor der Übernahme unter Einstellungen eine Kampagne und ein Kartiergebiet.');
			$fehler = true;
		}
	}
	else {
		$this->add_message('error', 'Fehler bei der Abfrage der Kartierobjekteigenschaften!');
		$fehler = true;
	}

	if ($fehler) { ?>
		<h2 style="margin-top: 20px; margin-bottom: 20px;">Datenübernahme von archivierten Bögen in neue Kartierung</h2>
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
		$this->formvars['selected_layer_id'] = 105;
		$this->formvars['value_kartierung_id'] = $new_kartierung_id;
		$this->formvars['operator_kartierung_id'] = '=';
		$this->GenerischeSuche_Suchen();
		exit();
	}
?>