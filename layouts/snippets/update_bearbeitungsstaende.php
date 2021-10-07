<?
	include_once(CLASSPATH . 'PgObject.php');
	$success = false;
	$kartierung_ids = array();
	$sql = '';
	if ($this->formvars['kartierung_ids'] != '') {
		# Beschränkung auf Kartierer wenn Änderungsberechtigte Stelle Kartierung ist
		$kartiererfilter = ($this->Stelle->Bezeichnung == 'Karterung' ? " AND k.user_id = " . $this->user->id : "");
		# Update Objekte
		$sql = "
			UPDATE
				mvbio." . ($this->formvars['objektart'] == 'Verlustobjekte' ? 'verlustobjekte' : 'kartierobjekte') . " k
			SET
				lock = true,
				bearbeitungsstufe = " . $this->formvars['stufe_neu'] . "
			FROM
				mvbio.code_bearbeitungsstufen b
			WHERE
				b.stufe = k.bearbeitungsstufe AND
				b.aenderungsberechtigte_stelle = '" . $this->Stelle->Bezeichnung . "' AND
				k.bearbeitungsstufe = " . $this->formvars['stufe_alt'] . " AND
				k.id IN (" . $this->formvars['kartierung_ids'] . ")" .
				$kartiererfilter . "
			RETURNING
				k.id
		";
		#echo $sql;
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if ($ret['success']) {
			while ($rs = pg_fetch_assoc($ret[1])) {
				$kartierung_ids[] = $rs['id'];
			}
			if (count($kartierung_ids) > 0) {
				$success = true;
				$msg = 'Bearbeitungsstände der Kartierobjekte erfolgreich geändert.';
			}
			else {
				$msg = 'Es konnten keine Kartierobjekte geändert werden.<br>Prüfen Sie Ihre Rechte.';
			}
		}
		else {
			$msg = $ret['msg'];
		}
	}
	else {
		$msg = 'Es wurden keine Kartierobjekt-IDs zur Änderung des Abgabenamen übergeben!';
	}

	$this->qlayerset[0]['shape'][0] = array(
		'kartierung_ids' => implode(', ', $kartierung_ids),
		'success' => $success,
		'msg' => $msg,
		'sql' => $sql
	);
?>