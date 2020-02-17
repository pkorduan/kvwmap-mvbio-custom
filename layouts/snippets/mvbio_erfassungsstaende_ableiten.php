<?php
	$log = new LogFile('/var/www/logs/mvbio/erfassungsstaende_ableiten.log', 'text', date("Y:m:d H:i:s", time()));
	# Lege alle Kampagnen im Archiv an, die noch nicht vorhanden sind und für die es aber abzuleitende Kartierobjekte gibt
	$sql = "
		INSERT INTO archiv.kampagnen (
			id, abk, bezeichnung, erfassungszeitraum, umfang, datenschichten, erstellt_am, erstellt_von, abgeschlossen, geom
		)
		SELECT DISTINCT
			kk.id, kk.abk, kk.bezeichnung, kk.erfassungszeitraum, kk.umfang, kk.datenschichten, kk.erstellt_am, kk.erstellt_von, false, kk.geom
		FROM
			mvbio.kartierobjekte ko JOIN
			mvbio.kampagnen kk ON ko.kampagne_id = kk.id LEFT JOIN
			archiv.kampagnen ak ON kk.id = ak.id
		WHERE
			ko.bearbeitungsstufe = 6 AND
			ak.id IS NULL
	";
	$log->write($sql);

	# Lege alle Kartiergebiete im Archiv an, die noch nicht vorhanden sind und für die es aber abzuleitende Kartierobjekte gibt
	$sql = "
		INSERT INTO archiv.kartiergebiete (
			id, kampagne_id, losnummer, bezeichnung, bemerkungen, geom
		)
		SELECT DISTINCT
			kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.bemerkungen, kg.geom
		FROM
			mvbio.kartierobjekte ko JOIN
			mvbio.kartiergebiete kg ON ko.kartiergebiet_id = kg.id LEFT JOIN
			archiv.kartiergebiete ag ON kg.id = ag.id
		WHERE
			ko.bearbeitungsstufe = 6 AND
			ag.id IS NULL
	";
	$log->write($sql);

	/*
	# ToDo: Das folgende Ersetzen durch eine andere Herangehensweise
	# Nicht die Kartierobjekte abfragen und jeweils entscheiden was erzeugt werden soll, sondern
	# Pro Archivbogenart abfragen welche Kartierobjekte in Bearbeitungsstufe 6 sind und entsprechend
	# der Beschreibung https://mvbio.de/wiki/doku.php?id=aktualisierung#ersetzung_von_alten_durch_neue_boegen
	# als Bogen im Archiv abgelegt werden sollen. 
*/

	# Frage die abzuleitenden Kurzbögen ab
	$sql = "
		INSERT INTO archiv.kurzboegen(
			kartierobjekt_id,
			kampagne_id, kartiergebiet_id, kartierebene_id, bogenart_id, stelle_id, user_id,
			giscode, label,
			lfd_nr_kr,
			biotopname, standort, unb, flaeche, la_sper, la_sp_txt, schutz_bio, altbestand, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, fb_id, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, gefcode, ohnegefahr, empfehlung, fauna, bearbeiter, e_datum, l_datum, foto, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, legende, version, import_table, bearbeitungsstufe, geom
		)
		SELECT
			id AS kartierobjekt_id,
			kampagne_id, kartiergebiet_id, kartierebene_id, bogenart_id, stelle_id, user_id,
			giscode, label,
			lfd_nr_kr, biotopname, standort, unb, flaeche, la_sper, la_sp_txt, schutz_bio, altbestand, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, fb_id, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, gefcode, ohnegefahr, empfehlung, fauna, bearbeiter, e_datum, l_datum, foto, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, legende, version, import_table, bearbeitungsstufe, geom
		FROM
			mvbio.kartierobjekte
		WHERE
			bearbeitungsstufe = 6
	";
	$log->write($sql);
/*
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	while ($rs = pg_fetch_assoc($ret[1])) {
		switch ($rs['bogenart_id']) {
			case 1 : {
				ableitung_kurzbogen($rs);
			} break;
			case 2 : {
				ableitung_grundbogen($rs);
			} break;
			case 3 : {
				ableitung_verlustbogen($rs);
			} break;
			case 4 : {
				ableitung_bewertungsbogen($rs);
			} break;
			case 5 : {
				ableitung_gruenlandbogen($rs);
			} break;
			case 6 : {
				ableitung_zustandsbewertung($rs);
			} break;
		}
	}

	function ableitung_kurzbogen($ko) {
		sql = "
			INSERT INTO archiv.kurzboegen
			VALUES (
				id = " . $ko['id'] . ",
				kartierobjekt_id = " . $ko['kartierobjekt_id'] . ",
				kampagne_id = " . $ko['kampagne_id'] . ",
				kampagne = '" . $ko['kampagne'] . "',
				kartiergebiet_id = " . $ko['kartiergebiet_id'] . ",
				kartierebene_id = " . $ko['kartierebene_id'] . ",
				kartiergebiet_name = '" . $ko['kartiergebiet_name'] . "',
				bogenart_id = " . $ko['bogenart_id'] . ",
				stelle_id = " . $ko['stelle_id'] . ",
				user_id = " . $ko['user_id'] . ",
				nummer = '" . $ko['nummer'] . "',
				giscode = '" . $ko['giscode'] . "',
				label = '" . $ko['label'] . "',
				lfd_nr = '" . $ko['lfd_nr'] . "',
				lfd_nr_kr = " . $ko['lfd_nr_kr'] . ",
				userid = '" . $ko['userid'] . "',
				biotopname = '" . $ko['biotopname'] . "',
				standort = '" . $ko['standort'] . "',
				unb = '" . $ko['unb'] . "',
				flaeche = " . $ko['flaeche'] . ",
				la_sper = " . $ko['la_sper'] . ",
				la_sp_txt = '" . $ko['la_sp_txt'] . "',
				schutz_bio = " . $ko['schutz_bio'] . ",
				altbestand = " . $ko['altbestand'] . ",
				alt_giscod = '" . $ko['alt_giscod'] . "',
				alt_lfd_nr = '" . $ko['alt_lfd_nr'] . "',
				alt_bearb = '" . $ko['alt_bearb'] . "',
				alt_datp20 = '" . $ko['alt_datp20'] . "',
				fb_id = '" . $ko['fb_id'] . "',
				hc = '" . $ko['hc'] . "',
				hcp = " . $ko['hcp'] . ",
				uc1 = '" . $ko['uc1'] . "',
				uc2 = '" . $ko['uc2'] . "',
				vegeinheit = '" . $ko['vegeinheit'] . "',
				wert_krit_1 = " . $ko['wert_krit_1'] . ",
				wert_krit_2 = " . $ko['wert_krit_2'] . ",
				wert_krit_3 = " . $ko['wert_krit_3'] . ",
				wert_krit_4 = " . $ko['wert_krit_4'] . ",
				wert_krit_5 = " . $ko['wert_krit_5'] . ",
				wert_krit_6 = " . $ko['wert_krit_6'] . ",
				wert_krit_7 = " . $ko['wert_krit_7'] . ",
				wert_krit_8 = " . $ko['wert_krit_8'] . ",
				wert_krit_9 = " . $ko['wert_krit_9'] . ",
				wert_krit_10 = " . $ko['wert_krit_10'] . ",
				wert_krit_11 = " . $ko['wert_krit_11'] . ",
				wert_krit_12 = " . $ko['wert_krit_12'] . ",
				wert_krit_13 = " . $ko['wert_krit_13'] . ",
				wert_krit_14 = " . $ko['wert_krit_14'] . ",
				wert_krit_15 = " . $ko['wert_krit_15'] . ",
				wert_krit_16 = " . $ko['wert_krit_16'] . ",
				gefaehrdg = '" . $ko['gefaehrdg'] . "',
				gefcode = '" . $ko['gefcode'] . "',
				ohnegefahr = " . $ko['ohnegefahr'] . ",
				empfehlung = '" . $ko['empfehlung'] . "',
				fauna = '" . $ko['fauna'] . "',
				bearbeiter = '" . $ko['bearbeiter'] . "',
				e_datum = '" . $ko['e_datum'] . "',
				l_datum = '" . $ko['l_datum'] . "',
				foto = '" . $ko['foto'] . "',
				loeschen = " . $ko['loeschen'] . ",
				druck = " . $ko['druck'] . ",
				aend_datum = '" . $ko['aend_datum'] . "',
				korrektur = " . $ko['korrektur'] . ",
				geprueft = " . $ko['geprueft'] . ",
				pruefer = '" . $ko['pruefer'] . "',
				pruefdatum = '" . $ko['pruefdatum'] . "',
				lock = " . $ko['lock'] . ",
				status = " . $ko['status'] . ",
				legende = " . $ko['legende'] . ",
				version = '" . $ko['version'] . "',
				import_table = '" . $ko['import_table'] . "',
				bearbeitungsstufe = " . $ko['bearbeitungsstufe'] . ",
				geom = '" . $ko['geom'] . "'
			)
		";
		
	}

	function ableitung_grundbogen($ko) {
		sql = "
			INSERT INTO archiv.grundboegen
			VALUES (
				id = " . $ko['id'] . ",
				kartierobjekt_id = " . $ko['kartierobjekt_id'] . ",
				kampagne_id = " . $ko['kampagne_id'] . ",
				kampagne = '" . $ko['kampagne'] . "',
				kartiergebiet_id = " . $ko['kartiergebiet_id'] . ",
				kartierebene_id = " . $ko['kartierebene_id'] . ",
				kartiergebiet_name = '" . $ko['kartiergebiet_name'] . "',
				bogenart_id = " . $ko['bogenart_id'] . ",
				stelle_id = " . $ko['stelle_id'] . ",
				user_id = " . $ko['user_id'] . ",
				nummer = '" . $ko['nummer'] . "',
				giscode = '" . $ko['giscode'] . "',
				label = '" . $ko['label'] . "',
				lfd_nr = '" . $ko['lfd_nr'] . "',
				lfd_nr_kr = " . $ko['lfd_nr_kr'] . ",
				userid = '" . $ko['userid'] . "',
				biotopname = '" . $ko['biotopname'] . "',
				standort = '" . $ko['standort'] . "',
				unb = '" . $ko['unb'] . "',
				flaeche = " . $ko['flaeche'] . ",
				la_sper = " . $ko['la_sper'] . ",
				la_sp_txt = '" . $ko['la_sp_txt'] . "',
				schutz_bio = " . $ko['schutz_bio'] . ",
				altbestand = " . $ko['altbestand'] . ",
				alt_giscod = '" . $ko['alt_giscod'] . "',
				alt_lfd_nr = '" . $ko['alt_lfd_nr'] . "',
				alt_bearb = '" . $ko['alt_bearb'] . "',
				alt_datp20 = '" . $ko['alt_datp20'] . "',
				fb_id = '" . $ko['fb_id'] . "',
				hc = '" . $ko['hc'] . "',
				hcp = " . $ko['hcp'] . ",
				uc1 = '" . $ko['uc1'] . "',
				uc2 = '" . $ko['uc2'] . "',
				vegeinheit = '" . $ko['vegeinheit'] . "',
				wert_krit_1 = " . $ko['wert_krit_1'] . ",
				wert_krit_2 = " . $ko['wert_krit_2'] . ",
				wert_krit_3 = " . $ko['wert_krit_3'] . ",
				wert_krit_4 = " . $ko['wert_krit_4'] . ",
				wert_krit_5 = " . $ko['wert_krit_5'] . ",
				wert_krit_6 = " . $ko['wert_krit_6'] . ",
				wert_krit_7 = " . $ko['wert_krit_7'] . ",
				wert_krit_8 = " . $ko['wert_krit_8'] . ",
				wert_krit_9 = " . $ko['wert_krit_9'] . ",
				wert_krit_10 = " . $ko['wert_krit_10'] . ",
				wert_krit_11 = " . $ko['wert_krit_11'] . ",
				wert_krit_12 = " . $ko['wert_krit_12'] . ",
				wert_krit_13 = " . $ko['wert_krit_13'] . ",
				wert_krit_14 = " . $ko['wert_krit_14'] . ",
				wert_krit_15 = " . $ko['wert_krit_15'] . ",
				wert_krit_16 = " . $ko['wert_krit_16'] . ",
				gefaehrdg = '" . $ko['gefaehrdg'] . "',
				gefcode = '" . $ko['gefcode'] . "',
				ohnegefahr = " . $ko['ohnegefahr'] . ",
				empfehlung = '" . $ko['empfehlung'] . "',
				fauna = '" . $ko['fauna'] . "',
				bearbeiter = '" . $ko['bearbeiter'] . "',
				e_datum = '" . $ko['e_datum'] . "',
				l_datum = '" . $ko['l_datum'] . "',
				foto = '" . $ko['foto'] . "',
				loeschen = " . $ko['loeschen'] . ",
				druck = " . $ko['druck'] . ",
				aend_datum = '" . $ko['aend_datum'] . "',
				korrektur = " . $ko['korrektur'] . ",
				geprueft = " . $ko['geprueft'] . ",
				pruefer = '" . $ko['pruefer'] . "',
				pruefdatum = '" . $ko['pruefdatum'] . "',
				lock = " . $ko['lock'] . ",
				status = " . $ko['status'] . ",
				legende = " . $ko['legende'] . ",
				version = '" . $ko['version'] . "',
				import_table = '" . $ko['import_table'] . "',
				bearbeitungsstufe = " . $ko['bearbeitungsstufe'] . ",
				geom = '" . $ko['geom'] . "',
				schutz_ffh = " . $ko['schutz_ffh'] . ",
				see_nr = '" . $ko['see_nr'] . "',
				alt_datffh = '" . $ko['alt_datffh'] . "',
				lrt = '" . $ko['lrt'] . "',
				eu_nr = '" . $ko['eu_nr'] . "',
				erhaltung = '" . $ko['erhaltung'] . "',
				beschreibg = '" . $ko['beschreibg'] . "',
				substrat_1 = " . $ko['substrat_1'] . ",
				substrat_2 = " . $ko['substrat_2'] . ",
				substrat_3 = " . $ko['substrat_3'] . ",
				substrat_4 = " . $ko['substrat_4'] . ",
				substrat_5 = " . $ko['substrat_5'] . ",
				substrat_6 = " . $ko['substrat_6'] . ",
				substrat_7 = " . $ko['substrat_7'] . ",
				substrat_8 = " . $ko['substrat_8'] . ",
				substrat_9 = " . $ko['substrat_9'] . ",
				substrat_10 = " . $ko['substrat_10'] . ",
				trophie_1 = " . $ko['trophie_1'] . ",
				trophie_2 = " . $ko['trophie_2'] . ",
				trophie_3 = " . $ko['trophie_3'] . ",
				trophie_4 = " . $ko['trophie_4'] . ",
				trophie_5 = " . $ko['trophie_5'] . ",
				wasser_1 = " . $ko['wasser_1'] . ",
				wasser_2 = " . $ko['wasser_2'] . ",
				wasser_3 = " . $ko['wasser_3'] . ",
				wasser_4 = " . $ko['wasser_4'] . ",
				wasser_5 = " . $ko['wasser_5'] . ",
				wasser_6 = " . $ko['wasser_6'] . ",
				wasser_7 = " . $ko['wasser_7'] . ",
				wasser_8 = " . $ko['wasser_8'] . ",
				wasser_9 = " . $ko['wasser_9'] . ",
				relief_1 = " . $ko['relief_1'] . ",
				relief_2 = " . $ko['relief_2'] . ",
				relief_3 = " . $ko['relief_3'] . ",
				relief_4 = " . $ko['relief_4'] . ",
				relief_5 = " . $ko['relief_5'] . ",
				relief_6 = " . $ko['relief_6'] . ",
				relief_7 = " . $ko['relief_7'] . ",
				relief_8 = " . $ko['relief_8'] . ",
				relief_9 = " . $ko['relief_9'] . ",
				relief_10 = " . $ko['relief_10'] . ",
				relief_11 = " . $ko['relief_11'] . ",
				relief_12 = " . $ko['relief_12'] . ",
				exposition_1 = " . $ko['exposition_1'] . ",
				exposition_2 = " . $ko['exposition_2'] . ",
				exposition_3 = " . $ko['exposition_3'] . ",
				exposition_4 = " . $ko['exposition_4'] . ",
				exposition_5 = " . $ko['exposition_5'] . ",
				exposition_6 = " . $ko['exposition_6'] . ",
				exposition_7 = " . $ko['exposition_7'] . ",
				exposition_8 = " . $ko['exposition_8'] . ",
				nutzintens_1 = " . $ko['nutzintens_1'] . ",
				nutzintens_2 = " . $ko['nutzintens_2'] . ",
				nutzintens_3 = " . $ko['nutzintens_3'] . ",
				nutzintens_4 = " . $ko['nutzintens_4'] . ",
				nutzungsart_1 = " . $ko['nutzungsart_1'] . ",
				nutzungsart_2 = " . $ko['nutzungsart_2'] . ",
				nutzungsart_3 = " . $ko['nutzungsart_3'] . ",
				nutzungsart_4 = " . $ko['nutzungsart_4'] . ",
				nutzungsart_5 = " . $ko['nutzungsart_5'] . ",
				nutzungsart_6 = " . $ko['nutzungsart_6'] . ",
				nutzungsart_7 = " . $ko['nutzungsart_7'] . ",
				nutzungsart_8 = " . $ko['nutzungsart_8'] . ",
				nutzungsart_9 = " . $ko['nutzungsart_9'] . ",
				nutzungsart_10 = " . $ko['nutzungsart_10'] . ",
				nutzungsart_11 = " . $ko['nutzungsart_11'] . ",
				nutzungsart_12 = " . $ko['nutzungsart_12'] . ",
				nutzungsart_13 = " . $ko['nutzungsart_13'] . ",
				nutzungsart_14 = " . $ko['nutzungsart_14'] . ",
				nutzartzus = '" . $ko['nutzartzus'] . "',
				umgebung_1 = " . $ko['umgebung_1'] . ",
				umgebung_2 = " . $ko['umgebung_2'] . ",
				umgebung_3 = " . $ko['umgebung_3'] . ",
				umgebung_4 = " . $ko['umgebung_4'] . ",
				umgebung_5 = " . $ko['umgebung_5'] . ",
				umgebung_6 = " . $ko['umgebung_6'] . ",
				umgebung_7 = " . $ko['umgebung_7'] . ",
				umgebung_8 = " . $ko['umgebung_8'] . ",
				umgebung_9 = " . $ko['umgebung_9'] . ",
				umgebung_10 = " . $ko['umgebung_10'] . ",
				umgebung_11 = " . $ko['umgebung_11'] . ",
				umgebung_12 = " . $ko['umgebung_12'] . ",
				umgebung_13 = " . $ko['umgebung_13'] . ",
				umgebung_14 = " . $ko['umgebung_14'] . ",
				umgebung_15 = " . $ko['umgebung_15'] . ",
				umgebung_16 = " . $ko['umgebung_16'] . ",
				umgebung_17 = " . $ko['umgebung_17'] . ",
				umgebung_18 = " . $ko['umgebung_18'] . ",
				umgebung_19 = " . $ko['umgebung_19'] . ",
				umgebung_20 = " . $ko['umgebung_20'] . ",
				umgebung_21 = " . $ko['umgebung_21'] . ",
				umgebung_22 = " . $ko['umgebung_22'] . ",
				umgebung_23 = " . $ko['umgebung_23'] . ",
				umgebung_24 = " . $ko['umgebung_24'] . ",
				umgebung_25 = " . $ko['umgebung_25'] . ",
				umgebzus = '" . $ko['umgebzus'] . "',
				literatur = '" . $ko['literatur'] . "',
				old_id = " . $ko['old_id'] . "
			)
		";
	}

	function ableitung_verlustbogen($ko) {
		sql = "
			INSERT INTO archiv.verlustboegen
			VALUES (
				id = " . $ko['id'] . ",
				kartierobjekt_id = " . $ko['kartierobjekt_id'] . ",
				kampagne_id = " . $ko['kampagne_id'] . ",
				kampagne = '" . $ko['kampagne'] . "',
				kartiergebiet_id = " . $ko['kartiergebiet_id'] . ",
				kartierebene_id = " . $ko['kartierebene_id'] . ",
				kartiergebiet_name = '" . $ko['kartiergebiet_name'] . "',
				bogenart_id = " . $ko['bogenart_id'] . ",
				stelle_id = " . $ko['stelle_id'] . ",
				user_id = " . $ko['user_id'] . ",
				nummer = '" . $ko['nummer'] . "',
				giscode = '" . $ko['giscode'] . "',
				label = '" . $ko['label'] . "',
				lfd_nr = '" . $ko['lfd_nr'] . "',
				lfd_nr_kr = " . $ko['lfd_nr_kr'] . ",
				userid = '" . $ko['userid'] . "',
				biotopname = '" . $ko['biotopname'] . "',
				standort = '" . $ko['standort'] . "',
				unb = '" . $ko['unb'] . "',
				flaeche = " . $ko['flaeche'] . ",
				la_sper = " . $ko['la_sper'] . ",
				la_sp_txt = '" . $ko['la_sp_txt'] . "',
				schutz_bio = " . $ko['schutz_bio'] . ",
				altbestand = " . $ko['altbestand'] . ",
				alt_giscod = '" . $ko['alt_giscod'] . "',
				alt_lfd_nr = '" . $ko['alt_lfd_nr'] . "',
				alt_bearb = '" . $ko['alt_bearb'] . "',
				alt_datp20 = '" . $ko['alt_datp20'] . "',
				fb_id = '" . $ko['fb_id'] . "',
				hc = '" . $ko['hc'] . "',
				hcp = " . $ko['hcp'] . ",
				uc1 = '" . $ko['uc1'] . "',
				uc2 = '" . $ko['uc2'] . "',
				vegeinheit = '" . $ko['vegeinheit'] . "',
				wert_krit_1 = " . $ko['wert_krit_1'] . ",
				wert_krit_2 = " . $ko['wert_krit_2'] . ",
				wert_krit_3 = " . $ko['wert_krit_3'] . ",
				wert_krit_4 = " . $ko['wert_krit_4'] . ",
				wert_krit_5 = " . $ko['wert_krit_5'] . ",
				wert_krit_6 = " . $ko['wert_krit_6'] . ",
				wert_krit_7 = " . $ko['wert_krit_7'] . ",
				wert_krit_8 = " . $ko['wert_krit_8'] . ",
				wert_krit_9 = " . $ko['wert_krit_9'] . ",
				wert_krit_10 = " . $ko['wert_krit_10'] . ",
				wert_krit_11 = " . $ko['wert_krit_11'] . ",
				wert_krit_12 = " . $ko['wert_krit_12'] . ",
				wert_krit_13 = " . $ko['wert_krit_13'] . ",
				wert_krit_14 = " . $ko['wert_krit_14'] . ",
				wert_krit_15 = " . $ko['wert_krit_15'] . ",
				wert_krit_16 = " . $ko['wert_krit_16'] . ",
				gefaehrdg = '" . $ko['gefaehrdg'] . "',
				gefcode = '" . $ko['gefcode'] . "',
				ohnegefahr = " . $ko['ohnegefahr'] . ",
				empfehlung = '" . $ko['empfehlung'] . "',
				fauna = '" . $ko['fauna'] . "',
				bearbeiter = '" . $ko['bearbeiter'] . "',
				e_datum = '" . $ko['e_datum'] . "',
				l_datum = '" . $ko['l_datum'] . "',
				foto = '" . $ko['foto'] . "',
				loeschen = " . $ko['loeschen'] . ",
				druck = " . $ko['druck'] . ",
				aend_datum = '" . $ko['aend_datum'] . "',
				korrektur = " . $ko['korrektur'] . ",
				geprueft = " . $ko['geprueft'] . ",
				pruefer = '" . $ko['pruefer'] . "',
				pruefdatum = '" . $ko['pruefdatum'] . "',
				lock = " . $ko['lock'] . ",
				status = " . $ko['status'] . ",
				legende = " . $ko['legende'] . ",
				version = '" . $ko['version'] . "',
				import_table = '" . $ko['import_table'] . "',
				bearbeitungsstufe = " . $ko['bearbeitungsstufe'] . ",
				geom = '" . $ko['geom'] . "',
				verl_ursa = '" . $ko['verl_ursa'] . "',
				verl_nat = " . $ko['verl_nat'] . ",
				verl_ant = " . $ko['verl_ant'] . ",
				verl_wf = " . $ko['verl_wf'] . ",
			)
		";
	}

	function ableitung_bewertungsbogen($ko) {
		switch ($ko) {
			case 'offenland' : {
				sql = "
					INSERT INTO archiv.bewertungsboegen
					VALUES (
						id = " . $ko['id'] . ",
						kartierobjekt_id = " . $ko['kartierobjekt_id'] . ",
						kampagne_id = " . $ko['kampagne_id'] . ",
						kampagne = '" . $ko['kampagne'] . "',
						kartiergebiet_id = " . $ko['kartiergebiet_id'] . ",
						kartierebene_id = " . $ko['kartierebene_id'] . ",
						kartiergebiet_name = '" . $ko['kartiergebiet_name'] . "',
						bogenart_id = " . $ko['bogenart_id'] . ",
						stelle_id = " . $ko['stelle_id'] . ",
						user_id = " . $ko['user_id'] . ",
						nummer = '" . $ko['nummer'] . "',
						giscode = '" . $ko['giscode'] . "',
						label = '" . $ko['label'] . "',
						lfd_nr = '" . $ko['lfd_nr'] . "',
						lfd_nr_kr = " . $ko['lfd_nr_kr'] . ",
						userid = '" . $ko['userid'] . "',
						biotopname = '" . $ko['biotopname'] . "',
						standort = '" . $ko['standort'] . "',
						unb = '" . $ko['unb'] . "',
						flaeche = " . $ko['flaeche'] . ",
						la_sper = " . $ko['la_sper'] . ",
						la_sp_txt = '" . $ko['la_sp_txt'] . "',
						schutz_bio = " . $ko['schutz_bio'] . ",
						altbestand = " . $ko['altbestand'] . ",
						alt_giscod = '" . $ko['alt_giscod'] . "',
						alt_lfd_nr = '" . $ko['alt_lfd_nr'] . "',
						alt_bearb = '" . $ko['alt_bearb'] . "',
						alt_datp20 = '" . $ko['alt_datp20'] . "',
						fb_id = '" . $ko['fb_id'] . "',
						hc = '" . $ko['hc'] . "',
						hcp = " . $ko['hcp'] . ",
						uc1 = '" . $ko['uc1'] . "',
						uc2 = '" . $ko['uc2'] . "',
						vegeinheit = '" . $ko['vegeinheit'] . "',
						wert_krit_1 = " . $ko['wert_krit_1'] . ",
						wert_krit_2 = " . $ko['wert_krit_2'] . ",
						wert_krit_3 = " . $ko['wert_krit_3'] . ",
						wert_krit_4 = " . $ko['wert_krit_4'] . ",
						wert_krit_5 = " . $ko['wert_krit_5'] . ",
						wert_krit_6 = " . $ko['wert_krit_6'] . ",
						wert_krit_7 = " . $ko['wert_krit_7'] . ",
						wert_krit_8 = " . $ko['wert_krit_8'] . ",
						wert_krit_9 = " . $ko['wert_krit_9'] . ",
						wert_krit_10 = " . $ko['wert_krit_10'] . ",
						wert_krit_11 = " . $ko['wert_krit_11'] . ",
						wert_krit_12 = " . $ko['wert_krit_12'] . ",
						wert_krit_13 = " . $ko['wert_krit_13'] . ",
						wert_krit_14 = " . $ko['wert_krit_14'] . ",
						wert_krit_15 = " . $ko['wert_krit_15'] . ",
						wert_krit_16 = " . $ko['wert_krit_16'] . ",
						gefaehrdg = '" . $ko['gefaehrdg'] . "',
						gefcode = '" . $ko['gefcode'] . "',
						ohnegefahr = " . $ko['ohnegefahr'] . ",
						empfehlung = '" . $ko['empfehlung'] . "',
						fauna = '" . $ko['fauna'] . "',
						bearbeiter = '" . $ko['bearbeiter'] . "',
						e_datum = '" . $ko['e_datum'] . "',
						l_datum = '" . $ko['l_datum'] . "',
						foto = '" . $ko['foto'] . "',
						loeschen = " . $ko['loeschen'] . ",
						druck = " . $ko['druck'] . ",
						aend_datum = '" . $ko['aend_datum'] . "',
						korrektur = " . $ko['korrektur'] . ",
						geprueft = " . $ko['geprueft'] . ",
						pruefer = '" . $ko['pruefer'] . "',
						pruefdatum = '" . $ko['pruefdatum'] . "',
						lock = " . $ko['lock'] . ",
						status = " . $ko['status'] . ",
						legende = " . $ko['legende'] . ",
						version = '" . $ko['version'] . "',
						import_table = '" . $ko['import_table'] . "',
						bearbeitungsstufe = " . $ko['bearbeitungsstufe'] . ",
						geom = '" . $ko['geom'] . "',
						schutz_ffh = " . $ko['schutz_ffh'] . ",
						see_nr = '" . $ko['see_nr'] . "',
						alt_datffh = '" . $ko['alt_datffh'] . "',
						lrt = '" . $ko['lrt'] . "',
						eu_nr = '" . $ko['eu_nr'] . "',
						erhaltung = '" . $ko['erhaltung'] . "',
						beschreibg = '" . $ko['beschreibg'] . "',
						substrat_1 = " . $ko['substrat_1'] . ",
						substrat_2 = " . $ko['substrat_2'] . ",
						substrat_3 = " . $ko['substrat_3'] . ",
						substrat_4 = " . $ko['substrat_4'] . ",
						substrat_5 = " . $ko['substrat_5'] . ",
						substrat_6 = " . $ko['substrat_6'] . ",
						substrat_7 = " . $ko['substrat_7'] . ",
						substrat_8 = " . $ko['substrat_8'] . ",
						substrat_9 = " . $ko['substrat_9'] . ",
						substrat_10 = " . $ko['substrat_10'] . ",
						trophie_1 = " . $ko['trophie_1'] . ",
						trophie_2 = " . $ko['trophie_2'] . ",
						trophie_3 = " . $ko['trophie_3'] . ",
						trophie_4 = " . $ko['trophie_4'] . ",
						trophie_5 = " . $ko['trophie_5'] . ",
						wasser_1 = " . $ko['wasser_1'] . ",
						wasser_2 = " . $ko['wasser_2'] . ",
						wasser_3 = " . $ko['wasser_3'] . ",
						wasser_4 = " . $ko['wasser_4'] . ",
						wasser_5 = " . $ko['wasser_5'] . ",
						wasser_6 = " . $ko['wasser_6'] . ",
						wasser_7 = " . $ko['wasser_7'] . ",
						wasser_8 = " . $ko['wasser_8'] . ",
						wasser_9 = " . $ko['wasser_9'] . ",
						relief_1 = " . $ko['relief_1'] . ",
						relief_2 = " . $ko['relief_2'] . ",
						relief_3 = " . $ko['relief_3'] . ",
						relief_4 = " . $ko['relief_4'] . ",
						relief_5 = " . $ko['relief_5'] . ",
						relief_6 = " . $ko['relief_6'] . ",
						relief_7 = " . $ko['relief_7'] . ",
						relief_8 = " . $ko['relief_8'] . ",
						relief_9 = " . $ko['relief_9'] . ",
						relief_10 = " . $ko['relief_10'] . ",
						relief_11 = " . $ko['relief_11'] . ",
						relief_12 = " . $ko['relief_12'] . ",
						exposition_1 = " . $ko['exposition_1'] . ",
						exposition_2 = " . $ko['exposition_2'] . ",
						exposition_3 = " . $ko['exposition_3'] . ",
						exposition_4 = " . $ko['exposition_4'] . ",
						exposition_5 = " . $ko['exposition_5'] . ",
						exposition_6 = " . $ko['exposition_6'] . ",
						exposition_7 = " . $ko['exposition_7'] . ",
						exposition_8 = " . $ko['exposition_8'] . ",
						nutzintens_1 = " . $ko['nutzintens_1'] . ",
						nutzintens_2 = " . $ko['nutzintens_2'] . ",
						nutzintens_3 = " . $ko['nutzintens_3'] . ",
						nutzintens_4 = " . $ko['nutzintens_4'] . ",
						nutzungsart_1 = " . $ko['nutzungsart_1'] . ",
						nutzungsart_2 = " . $ko['nutzungsart_2'] . ",
						nutzungsart_3 = " . $ko['nutzungsart_3'] . ",
						nutzungsart_4 = " . $ko['nutzungsart_4'] . ",
						nutzungsart_5 = " . $ko['nutzungsart_5'] . ",
						nutzungsart_6 = " . $ko['nutzungsart_6'] . ",
						nutzungsart_7 = " . $ko['nutzungsart_7'] . ",
						nutzungsart_8 = " . $ko['nutzungsart_8'] . ",
						nutzungsart_9 = " . $ko['nutzungsart_9'] . ",
						nutzungsart_10 = " . $ko['nutzungsart_10'] . ",
						nutzungsart_11 = " . $ko['nutzungsart_11'] . ",
						nutzungsart_12 = " . $ko['nutzungsart_12'] . ",
						nutzungsart_13 = " . $ko['nutzungsart_13'] . ",
						nutzungsart_14 = " . $ko['nutzungsart_14'] . ",
						nutzartzus = '" . $ko['nutzartzus'] . "',
						umgebung_1 = " . $ko['umgebung_1'] . ",
						umgebung_2 = " . $ko['umgebung_2'] . ",
						umgebung_3 = " . $ko['umgebung_3'] . ",
						umgebung_4 = " . $ko['umgebung_4'] . ",
						umgebung_5 = " . $ko['umgebung_5'] . ",
						umgebung_6 = " . $ko['umgebung_6'] . ",
						umgebung_7 = " . $ko['umgebung_7'] . ",
						umgebung_8 = " . $ko['umgebung_8'] . ",
						umgebung_9 = " . $ko['umgebung_9'] . ",
						umgebung_10 = " . $ko['umgebung_10'] . ",
						umgebung_11 = " . $ko['umgebung_11'] . ",
						umgebung_12 = " . $ko['umgebung_12'] . ",
						umgebung_13 = " . $ko['umgebung_13'] . ",
						umgebung_14 = " . $ko['umgebung_14'] . ",
						umgebung_15 = " . $ko['umgebung_15'] . ",
						umgebung_16 = " . $ko['umgebung_16'] . ",
						umgebung_17 = " . $ko['umgebung_17'] . ",
						umgebung_18 = " . $ko['umgebung_18'] . ",
						umgebung_19 = " . $ko['umgebung_19'] . ",
						umgebung_20 = " . $ko['umgebung_20'] . ",
						umgebung_21 = " . $ko['umgebung_21'] . ",
						umgebung_22 = " . $ko['umgebung_22'] . ",
						umgebung_23 = " . $ko['umgebung_23'] . ",
						umgebung_24 = " . $ko['umgebung_24'] . ",
						umgebung_25 = " . $ko['umgebung_25'] . ",
						umgebzus = '" . $ko['umgebzus'] . "',
						literatur = '" . $ko['literatur'] . "',
						old_id = " . $ko['old_id'] . ",
						geb_name = '" . $ko['geb_name'] . "',
						lrt_gr = '" . $ko['lrt_gr'] . "',
						lrt_gr_tex = '" . $ko['lrt_gr_tex'] . "',
						lrt_code = '" . $ko['lrt_code'] . "',
						lrt_nummer = '" . $ko['lrt_nummer'] . "',
						seegroesse = '" . $ko['seegroesse'] . "',
						bereich = '" . $ko['bereich'] . "',
						uc = '" . $ko['uc'] . "',
						sys_habit = '" . $ko['sys_habit'] . "',
						sys_leben = '" . $ko['sys_leben'] . "',
						sys_beein = '" . $ko['sys_beein'] . "',
						sys_erhalt = '" . $ko['sys_erhalt'] . "',
						bea_habit = '" . $ko['bea_habit'] . "',
						bea_leben = '" . $ko['bea_leben'] . "',
						bea_beein = '" . $ko['bea_beein'] . "',
						bea_erhalt = '" . $ko['bea_erhalt'] . "',
						bemerkung = '" . $ko['bemerkung'] . "',
						bew_datum = '" . $ko['bew_datum'] . "',
						t111_1 = " . $ko['t111_1'] . ",
						t112_1 = " . $ko['t112_1'] . ",
						t113_1 = " . $ko['t113_1'] . ",
						t121_1 = " . $ko['t121_1'] . ",
						t121_2 = " . $ko['t121_2'] . ",
						t131_1_1 = " . $ko['t131_1_1'] . ",
						t131_1_2 = " . $ko['t131_1_2'] . ",
						t131_2_1 = " . $ko['t131_2_1'] . ",
						t131_2_2 = " . $ko['t131_2_2'] . ",
						t131_3 = " . $ko['t131_3'] . ",
						t132_1 = " . $ko['t132_1'] . ",
						t132_2 = " . $ko['t132_2'] . ",
						t211_1_1 = " . $ko['t211_1_1'] . ",
						t211_1_2 = " . $ko['t211_1_2'] . ",
						t211_1_3 = " . $ko['t211_1_3'] . ",
						t211_3_1 = " . $ko['t211_3_1'] . ",
						t212_1 = " . $ko['t212_1'] . ",
						t22_1 = " . $ko['t22_1'] . ",
						t22_2 = '" . $ko['t22_2'] . "',
						t311_1_1 = " . $ko['t311_1_1'] . ",
						t311_1_2 = " . $ko['t311_1_2'] . ",
						t311_1_3 = " . $ko['t311_1_3'] . ",
						t311_1_4 = " . $ko['t311_1_4'] . ",
						t311_1_5 = " . $ko['t311_1_5'] . ",
						t321_1 = " . $ko['t321_1'] . ",
						t321_2 = " . $ko['t321_2'] . ",
						t322_1 = " . $ko['t322_1'] . ",
						t322_2 = " . $ko['t322_2'] . ",
						t322_3 = " . $ko['t322_3'] . ",
						t323_1 = " . $ko['t323_1'] . ",
						t324_1 = " . $ko['t324_1'] . ",
						t325_1 = " . $ko['t325_1'] . ",
						t326_1 = " . $ko['t326_1'] . ",
						t327_1 = " . $ko['t327_1'] . ",
						t328_1_1 = " . $ko['t328_1_1'] . "
					)
				";
			} break;
		}
	}

	function ableitung_gruenlandbogen($ko) {
		sql = "
			INSERT INTO archiv.bewertungsboegen
			VALUES (
				id = " . $ko['id'] . ",
				kartierobjekt_id = " . $ko['kartierobjekt_id'] . ",
				kampagne_id = " . $ko['kampagne_id'] . ",
				kampagne = '" . $ko['kampagne'] . "',
				kartiergebiet_id = " . $ko['kartiergebiet_id'] . ",
				kartierebene_id = " . $ko['kartierebene_id'] . ",
				kartiergebiet_name = '" . $ko['kartiergebiet_name'] . "',
				bogenart_id = " . $ko['bogenart_id'] . ",
				stelle_id = " . $ko['stelle_id'] . ",
				user_id = " . $ko['user_id'] . ",
				nummer = '" . $ko['nummer'] . "',
				giscode = '" . $ko['giscode'] . "',
				label = '" . $ko['label'] . "',
				lfd_nr = '" . $ko['lfd_nr'] . "',
				lfd_nr_kr = " . $ko['lfd_nr_kr'] . ",
				userid = '" . $ko['userid'] . "',
				biotopname = '" . $ko['biotopname'] . "',
				standort = '" . $ko['standort'] . "',
				unb = '" . $ko['unb'] . "',
				flaeche = " . $ko['flaeche'] . ",
				la_sper = " . $ko['la_sper'] . ",
				la_sp_txt = '" . $ko['la_sp_txt'] . "',
				schutz_bio = " . $ko['schutz_bio'] . ",
				altbestand = " . $ko['altbestand'] . ",
				alt_giscod = '" . $ko['alt_giscod'] . "',
				alt_lfd_nr = '" . $ko['alt_lfd_nr'] . "',
				alt_bearb = '" . $ko['alt_bearb'] . "',
				alt_datp20 = '" . $ko['alt_datp20'] . "',
				fb_id = '" . $ko['fb_id'] . "',
				hc = '" . $ko['hc'] . "',
				hcp = " . $ko['hcp'] . ",
				uc1 = '" . $ko['uc1'] . "',
				uc2 = '" . $ko['uc2'] . "',
				vegeinheit = '" . $ko['vegeinheit'] . "',
				wert_krit_1 = " . $ko['wert_krit_1'] . ",
				wert_krit_2 = " . $ko['wert_krit_2'] . ",
				wert_krit_3 = " . $ko['wert_krit_3'] . ",
				wert_krit_4 = " . $ko['wert_krit_4'] . ",
				wert_krit_5 = " . $ko['wert_krit_5'] . ",
				wert_krit_6 = " . $ko['wert_krit_6'] . ",
				wert_krit_7 = " . $ko['wert_krit_7'] . ",
				wert_krit_8 = " . $ko['wert_krit_8'] . ",
				wert_krit_9 = " . $ko['wert_krit_9'] . ",
				wert_krit_10 = " . $ko['wert_krit_10'] . ",
				wert_krit_11 = " . $ko['wert_krit_11'] . ",
				wert_krit_12 = " . $ko['wert_krit_12'] . ",
				wert_krit_13 = " . $ko['wert_krit_13'] . ",
				wert_krit_14 = " . $ko['wert_krit_14'] . ",
				wert_krit_15 = " . $ko['wert_krit_15'] . ",
				wert_krit_16 = " . $ko['wert_krit_16'] . ",
				gefaehrdg = '" . $ko['gefaehrdg'] . "',
				gefcode = '" . $ko['gefcode'] . "',
				ohnegefahr = " . $ko['ohnegefahr'] . ",
				empfehlung = '" . $ko['empfehlung'] . "',
				fauna = '" . $ko['fauna'] . "',
				bearbeiter = '" . $ko['bearbeiter'] . "',
				e_datum = '" . $ko['e_datum'] . "',
				l_datum = '" . $ko['l_datum'] . "',
				foto = '" . $ko['foto'] . "',
				loeschen = " . $ko['loeschen'] . ",
				druck = " . $ko['druck'] . ",
				aend_datum = '" . $ko['aend_datum'] . "',
				korrektur = " . $ko['korrektur'] . ",
				geprueft = " . $ko['geprueft'] . ",
				pruefer = '" . $ko['pruefer'] . "',
				pruefdatum = '" . $ko['pruefdatum'] . "',
				lock = " . $ko['lock'] . ",
				status = " . $ko['status'] . ",
				legende = " . $ko['legende'] . ",
				version = '" . $ko['version'] . "',
				import_table = '" . $ko['import_table'] . "',
				bearbeitungsstufe = " . $ko['bearbeitungsstufe'] . ",
				geom = '" . $ko['geom'] . "',
				schutz_ffh = " . $ko['schutz_ffh'] . ",
				see_nr = '" . $ko['see_nr'] . "',
				alt_datffh = '" . $ko['alt_datffh'] . "',
				lrt = '" . $ko['lrt'] . "',
				eu_nr = '" . $ko['eu_nr'] . "',
				erhaltung = '" . $ko['erhaltung'] . "',
				beschreibg = '" . $ko['beschreibg'] . "',
				substrat_1 = " . $ko['substrat_1'] . ",
				substrat_2 = " . $ko['substrat_2'] . ",
				substrat_3 = " . $ko['substrat_3'] . ",
				substrat_4 = " . $ko['substrat_4'] . ",
				substrat_5 = " . $ko['substrat_5'] . ",
				substrat_6 = " . $ko['substrat_6'] . ",
				substrat_7 = " . $ko['substrat_7'] . ",
				substrat_8 = " . $ko['substrat_8'] . ",
				substrat_9 = " . $ko['substrat_9'] . ",
				substrat_10 = " . $ko['substrat_10'] . ",
				trophie_1 = " . $ko['trophie_1'] . ",
				trophie_2 = " . $ko['trophie_2'] . ",
				trophie_3 = " . $ko['trophie_3'] . ",
				trophie_4 = " . $ko['trophie_4'] . ",
				trophie_5 = " . $ko['trophie_5'] . ",
				wasser_1 = " . $ko['wasser_1'] . ",
				wasser_2 = " . $ko['wasser_2'] . ",
				wasser_3 = " . $ko['wasser_3'] . ",
				wasser_4 = " . $ko['wasser_4'] . ",
				wasser_5 = " . $ko['wasser_5'] . ",
				wasser_6 = " . $ko['wasser_6'] . ",
				wasser_7 = " . $ko['wasser_7'] . ",
				wasser_8 = " . $ko['wasser_8'] . ",
				wasser_9 = " . $ko['wasser_9'] . ",
				relief_1 = " . $ko['relief_1'] . ",
				relief_2 = " . $ko['relief_2'] . ",
				relief_3 = " . $ko['relief_3'] . ",
				relief_4 = " . $ko['relief_4'] . ",
				relief_5 = " . $ko['relief_5'] . ",
				relief_6 = " . $ko['relief_6'] . ",
				relief_7 = " . $ko['relief_7'] . ",
				relief_8 = " . $ko['relief_8'] . ",
				relief_9 = " . $ko['relief_9'] . ",
				relief_10 = " . $ko['relief_10'] . ",
				relief_11 = " . $ko['relief_11'] . ",
				relief_12 = " . $ko['relief_12'] . ",
				exposition_1 = " . $ko['exposition_1'] . ",
				exposition_2 = " . $ko['exposition_2'] . ",
				exposition_3 = " . $ko['exposition_3'] . ",
				exposition_4 = " . $ko['exposition_4'] . ",
				exposition_5 = " . $ko['exposition_5'] . ",
				exposition_6 = " . $ko['exposition_6'] . ",
				exposition_7 = " . $ko['exposition_7'] . ",
				exposition_8 = " . $ko['exposition_8'] . ",
				nutzintens_1 = " . $ko['nutzintens_1'] . ",
				nutzintens_2 = " . $ko['nutzintens_2'] . ",
				nutzintens_3 = " . $ko['nutzintens_3'] . ",
				nutzintens_4 = " . $ko['nutzintens_4'] . ",
				nutzungsart_1 = " . $ko['nutzungsart_1'] . ",
				nutzungsart_2 = " . $ko['nutzungsart_2'] . ",
				nutzungsart_3 = " . $ko['nutzungsart_3'] . ",
				nutzungsart_4 = " . $ko['nutzungsart_4'] . ",
				nutzungsart_5 = " . $ko['nutzungsart_5'] . ",
				nutzungsart_6 = " . $ko['nutzungsart_6'] . ",
				nutzungsart_7 = " . $ko['nutzungsart_7'] . ",
				nutzungsart_8 = " . $ko['nutzungsart_8'] . ",
				nutzungsart_9 = " . $ko['nutzungsart_9'] . ",
				nutzungsart_10 = " . $ko['nutzungsart_10'] . ",
				nutzungsart_11 = " . $ko['nutzungsart_11'] . ",
				nutzungsart_12 = " . $ko['nutzungsart_12'] . ",
				nutzungsart_13 = " . $ko['nutzungsart_13'] . ",
				nutzungsart_14 = " . $ko['nutzungsart_14'] . ",
				nutzartzus = '" . $ko['nutzartzus'] . "',
				umgebung_1 = " . $ko['umgebung_1'] . ",
				umgebung_2 = " . $ko['umgebung_2'] . ",
				umgebung_3 = " . $ko['umgebung_3'] . ",
				umgebung_4 = " . $ko['umgebung_4'] . ",
				umgebung_5 = " . $ko['umgebung_5'] . ",
				umgebung_6 = " . $ko['umgebung_6'] . ",
				umgebung_7 = " . $ko['umgebung_7'] . ",
				umgebung_8 = " . $ko['umgebung_8'] . ",
				umgebung_9 = " . $ko['umgebung_9'] . ",
				umgebung_10 = " . $ko['umgebung_10'] . ",
				umgebung_11 = " . $ko['umgebung_11'] . ",
				umgebung_12 = " . $ko['umgebung_12'] . ",
				umgebung_13 = " . $ko['umgebung_13'] . ",
				umgebung_14 = " . $ko['umgebung_14'] . ",
				umgebung_15 = " . $ko['umgebung_15'] . ",
				umgebung_16 = " . $ko['umgebung_16'] . ",
				umgebung_17 = " . $ko['umgebung_17'] . ",
				umgebung_18 = " . $ko['umgebung_18'] . ",
				umgebung_19 = " . $ko['umgebung_19'] . ",
				umgebung_20 = " . $ko['umgebung_20'] . ",
				umgebung_21 = " . $ko['umgebung_21'] . ",
				umgebung_22 = " . $ko['umgebung_22'] . ",
				umgebung_23 = " . $ko['umgebung_23'] . ",
				umgebung_24 = " . $ko['umgebung_24'] . ",
				umgebung_25 = " . $ko['umgebung_25'] . ",
				umgebzus = '" . $ko['umgebzus'] . "',
				literatur = '" . $ko['literatur'] . "',
				old_id = " . $ko['old_id'] . "
			)
		";
	}

	function ableitung_zustandsbewertung($ko) {
		
	}
*/
	$log->close();
?>