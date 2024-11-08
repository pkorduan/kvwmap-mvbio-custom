<?php
	$this->sanitize([
		'bogen_id' => 'int',
		'bogenart_id' => 'int'
	]);
	# ToDo
	# Abfragen ob sich an dieser Stelle wo der neue Bogen eingetragen werden soll schon ein Kartierobjekt befindet
	# Wenn ja Abbruch mit aussagekräftiger Meldung
	# Bedingung für Abbruch wurde per E-Mail angefragt bei Dirk und Stefan Goen 2021-10-14.



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

		$sql = "
			SELECT
				*
			FROM
				archiv." . $archivtabelle . "
			WHERE
				id = " . $this->formvars['bogen_id'] . "
		";
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if ($ret['success']) {
			$altbogen = pg_fetch_assoc($ret[1]);
			#print_r($select_columns);
			# Übernahme von Auto-Werten für die neue Kartierung
			$select_columns['user_id'] = $this->user->id;
			$select_columns['created_from'] = "'" . $this->user->Vorname . ' ' . $this->user->Name . "'";
			$select_columns['created_at'] = "'" . date('Y-m-d H:i:s') . "'";
			$select_columns['bearbeitungsstufe'] = 1;
			$select_columns['kampagne_id'] = (rolle::$layer_params['kampagne_id'] == '' ? 0 : rolle::$layer_params['kampagne_id']);
			$select_columns['kartiergebiet_id'] = (rolle::$layer_params['kartiergebietfilter'] == '' ? 0 : rolle::$layer_params['kartiergebietfilter']);
			# Keine Übernahme des Datums, Anpassung S. Goen, alte Zeile:
			# $select_columns['e_datum'] = ($altbogen['e_datum'] == '01.01.1970' ? 'NULL' : "'" . $altbogen['e_datum'] . "'");
			$select_columns['e_datum'] = 'NULL';
			$select_columns['l_datum'] = 'NULL';
			$select_columns['foto'] = 0;
			$alt_datp20 = implode('-', array_reverse(explode('.', $altbogen['l_datum'] ?: ($altbogen['e_datum'] == '01.01.1970' ? 'NULL' : $altbogen['e_datum']) ?: 'NULL')));
			if ($alt_datp20 != 'NULL') {
				$alt_datp20 = "'" . $alt_datp20 . "'";
			}
#			$select_columns['alt_datp20'] = $alt_datp20;
			$select_columns['kartierer'] = "'" . $this->user->Vorname . ' ' . $this->user->Name . "'";


			if (rolle::$layer_params['kartierebenenfilter'] != '') {
				$select_columns['kartierebene_id'] = rolle::$layer_params['kartierebenenfilter'];
			}
			if ($this->formvars['bogenart_id'] == '') {
				if ($archivtabelle == 'bewertungsboegen') {
					$select_columns['bogenart_id'] = 2;
				}
			}
			else {
				$select_columns['bogenart_id'] = $this->formvars['bogenart_id'];
			}
			$select_columns['vorgaenger_id'] = $this->formvars['bogen_id'];
			# Eintragen des neuen Kartierobjektes mit den Daten des ausgewählten Bogens
			#print_r(implode(', ', array_keys($select_columns)));
			$sql = '
				INSERT INTO mvbio.kartierobjekte ("' . implode('", "', $insert_columns) . '", user_id, created_from, vorgaenger_id)
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
				$new_kartierobjekt_id = $rs['id'];

	/* Pflanzenvorkommen sollen nicht übernommen werden
				# Eintragen der Pflanzenvorkommen
				$sql = "
					INSERT INTO mvbio.pflanzenvorkommen (kartierobjekt_id, species_nr, valid_nr, dzv, fsk, rl, cf, tax, bav)
					SELECT
						" . $new_kartierobjekt_id . ", pv.species_nr, pv.valid_nr, pv.dzv, pv.fsk, pv.rl, pv.cf, pv.tax, pv.bav
					FROM
						archiv.erfassungsboegen eb JOIN
						archiv.pflanzenvorkommen pv ON eb.kartierobjekt_id = pv.kartierobjekt_id
					WHERE
						eb.id = " . $this->formvars['bogen_id'] . "
				";
				#echo '<br>SQL zum Eintragen der Pflanzenvorkommen: ' . $sql;
				#$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	*/
				if ($ret['success']) {

					# Eintragen der Nebencodes für das neue Kartierobjekt
					$sql = "
						INSERT INTO mvbio.biotoptypen_nebencodes (kartierung_id, code, flaechendeckung_prozent)
						SELECT
							" . $new_kartierobjekt_id . ", nc.code, nc.flaechendeckung_prozent
						FROM
							archiv.biotoptypen_nebencodes nc
						WHERE
							nc.kartierobjekt_id = " . $this->formvars['bogen_id'] . "
					";
					#echo '<br>SQL zum Eintragen der Nebencodes: ' . $sql;
					$ret = $this->pgdatabase->execSQL($sql, 4, 0);
					if ($ret['success']) {

						# Eintragen der Habitatvorkommen für das neue Kartierobjekt
						$sql = "
							INSERT INTO mvbio.habitatvorkommen (kartierung_id, code)
							SELECT
								" . $new_kartierobjekt_id . ", hv.code
							FROM
								archiv.habitatvorkommen hv
							WHERE
								hv.kartierobjekt_id = " . $this->formvars['bogen_id'] . "
						";
						#echo '<br>SQL zum Eintragen der Habitatvorkommen: ' . $sql;
						$ret = $this->pgdatabase->execSQL($sql, 4, 0);
						if ($ret['success']) {

							# Eintragen der Empfehlungen und Massnahmen für das neue Kartierobjekt
							$sql = "
								INSERT INTO mvbio.empfehlungen_massnahmen (kartierung_id, code)
								SELECT
									" . $new_kartierobjekt_id . ", em.code
								FROM
									archiv.empfehlungen_massnahmen em
								WHERE
									em.kartierobjekt_id = " . $this->formvars['bogen_id'] . "
							";
							#echo '<br>SQL zum Eintragen des Empfehlungen und Massnahmen: ' . $sql;
							$ret = $this->pgdatabase->execSQL($sql, 4, 0);
							if ($ret['success']) {

								# Eintragen der Beeintraechtigungen und Gefaehrdungen für das neue Kartierobjekt
								$sql = "
									INSERT INTO mvbio.beeintraechtigungen_gefaehrdungen (kartierung_id, code)
									SELECT
										" . $new_kartierobjekt_id . ", bg.code
									FROM
										archiv.beeintraechtigungen_gefaehrdungen bg
									WHERE
										bg.kartierobjekt_id = " . $this->formvars['bogen_id'] . "
								";
								#echo '<br>SQL zum Eintragen des Beeintraechtigungen und Gefaehrdungen: ' . $sql;
								$ret = $this->pgdatabase->execSQL($sql, 4, 0);
								if ($ret['success']) {

									#echo '<p>Alles erfolgreich eingetragen';
									$this->add_message('success', 'Neues Kartierobjekt mit id: ' . $new_kartierobjekt_id . ' erfolgreich aus Archivtabelle: ' . $archivtabelle . ' übernommen.<br>Sie können das Kartierobjekt jetzt bearbeiten.');

									/* Wenn die Archivtabelle ein Bewertungsbogen war, soll die Bewertung nicht in eine neue Bewertung übernommen werden.
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
											$mvbiotabelle = 'lrt_objekte_' . $rs['tabelle_postfix'];

											# Abfrage der zwischen Bewertungsbogen und Bewertung übereinstimmenden Spaltennamen
											$sql = "
												SELECT
													b.column_name
												FROM
													information_schema.columns b JOIN
													information_schema.columns k ON b.column_name = k.column_name
												WHERE
													b.table_schema = 'archiv' AND	b.table_name = '" . $archivtabelle . "' AND
													k.table_schema = 'mvbio' AND k.table_name = '" . $mvbiotabelle .	"' AND
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
												# Hinzufügen der kartierobjekt_id zu Abfrage- und Insertattributen
												$insert_columns['kartierung_id'] = 'kartierung_id';
												$select_columns['kartierung_id'] = $new_kartierobjekt_id;
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
													$this->add_message('success', 'Neue Bewertung für Kartierobjekt: ' . $new_kartierobjekt_id . ' angelegt.');
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
									} # end processing bewertungsbogen
									*/
								}
								else {
									$this->add_message('error', 'Fehler beim Eintragen der Empfehlungen.');
								}
							}
							else {
								$this->add_message('error', 'Fehler beim Eintragen der Gefahrencodes.');
							}
						}
						else {
							$this->add_message('error', 'Fehler beim Eintragen der Habitate und Stukturen.');
						}
					}
					else {
						$this->add_message('error', 'Fehler beim Eintragen der Nebencodes.');
					}
				}
			}
			else {
				$this->add_message('error', 'Fehler beim Eintragen des Bogens id: ' . $this->formvars['bogen_id'] . ' als neue Kartierung!<br>Hinweis: Wählen Sie immer vor der Übernahme unter Einstellungen eine Kampagne und ein Kartiergebiet.');
				$fehler = true;
			}
		}
		else {
			$this->add_message('error', 'Fehler bei der Abfrage des zu übernehmenden Bogens!');
			$fehler = true;
		}
	}
	else {
		$this->add_message('error', 'Fehler bei der Abfrage der Kartierobjekteigenschaften!');
		$fehler = true;
	}

	if ($fehler) { ?>
		<h2 style="margin-top: 20px; margin-bottom: 20px;">Datenübernahme von archivierten Bögen in neue Kartierung</h2>
		<a href="<? echo URL . APPLVERSION; ?>index.php?go=get_last_query">Zurück</a> zur letzten Sachdatenanzeige.<?php
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
		$this->formvars['value_kartierung_id'] = $new_kartierobjekt_id;
		$this->formvars['operator_kartierung_id'] = '=';
		$this->GenerischeSuche_Suchen();
		exit();
	}
/*
-- Angaben zur Erstkartierung von Kurzbögen und Kampagne 1 übernehmen an Hand einer Zuordnungstabelle
UPDATE
	mvbio.kartierobjekte ko
SET
	alt_giscod = b.alt_giscod,
	alt_lfd_nr = b.alt_lfd_nr,
	alt_bearb = b.alt_bearb,
	alt_datp20 = b.alt_datp20
FROM
	mvbio.zuordnungstabelle z JOIN
	archiv.grundboegen b ON z.alt_giscode = b.alt_giscode
WHERE
	z.label = ko.label AND
	b.kampagne_id = 1

-- Und das ganze noch mal für Grundbögen

UPDATE
	mvbio.kartierobjekte ko
SET
	alt_giscod = b.alt_giscod,
	alt_lfd_nr = b.alt_lfd_nr,
	see_nr = b.see_nr,
	alt_bearb = b.alt_bearb,
	alt_datp20 = b.alt_datp20,
	alt_datffh = b.alt_datffh
FROM
	mvbio.msommer m JOIN
	archiv.grundboegen b ON m.alt_giscode = b.alt_giscode
WHERE
	m.label = ko.label AND
	b.kampagne_id = 1
*/
?>