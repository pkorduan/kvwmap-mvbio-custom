<?
	include(CLASSPATH . 'PgObject.php');
	$success = false;
	# Beschränkung auf Kartierer wenn Änderungsberechtigte Stelle Kartierung ist
	$kartiererfilter = ($this->Stelle->Bezeichnung == 'Karterung' ? " AND k.user_id = " . $this->user->id : "");
	# Update Kartierobjekte
	$sql = "
		UPDATE
			mvbio.kartierobjekte k
		SET
			bearbeitungsstufe = " . $this->formvars['stufe_neu'] . "
		FROM
			mvbio.code_bearbeitungsstufen b
		WHERE
			b.stufe = k.bearbeitungsstufe AND
			b.aenderungsberechtigte_stelle = '" . $this->Stelle->Bezeichnung . "' AND
			k.bearbeitungsstufe = " . $this->formvars['stufe_alt'] . " AND
			k.id IN (" . implode(', ', $this->formvars['kartierung_ids']) . ")" .
			$kartiererfilter . "
		RETURNING
			k.id
	";
	#echo $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	$kartierung_ids = array();
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

	$this->qlayerset[0]['shape'][0] = array(
		'kartierung_ids' => $kartierung_ids,
		'success' => $success,
		'msg' => $msg,
		'sql' => $sql
	);
?>