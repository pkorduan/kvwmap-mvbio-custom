<?
	include_once(CLASSPATH . 'PgObject.php');
	$success = true;
	$kartierung_ids = array();
	$sql = $err_sqls = $messages = '';
  $lock = 'false';
	if ($this->formvars['kartierung_ids'] != '') {
		# Beschränkung auf Kartierer wenn Änderungsberechtigte Stelle Kartierung ist
		$kartiererfilter = ($this->Stelle->Bezeichnung == 'Karterung' ? " AND k.user_id = " . $this->user->id : "");
		foreach (explode(',', $this->formvars['kartierung_ids']) AS $kartierung_id) {
			# Update Objekt
			$sql = "
				UPDATE
					mvbio." . ($this->formvars['objektart'] == 'Verlustobjekte' ? 'verlustobjekte' : 'kartierobjekte') . " k
				SET
					lock = " . $lock . ",
					bearbeitungsstufe = " . $this->formvars['stufe_neu'] . "
				FROM
					mvbio.code_bearbeitungsstufen b
				WHERE
					b.stufe = k.bearbeitungsstufe AND
					b.aenderungsberechtigte_stelle = '" . $this->Stelle->Bezeichnung . "' AND
					k.bearbeitungsstufe = " . $this->formvars['stufe_alt'] . " AND
					k.id = " . $kartierung_id .
					$kartiererfilter . "
				RETURNING
					k.id
			";
			#echo 'SQL zum Update der Bearbeitungsstände:<br>' . $sql; exit;
			$ret = $this->pgdatabase->execSQL($sql, 4, 0);
			if ($ret['success']) {
				$rs = pg_fetch_assoc($ret[1]);
				$kartierung_ids[] = $rs['id'];
				$messages .= '<br>' . 'Bearbeitungsstand von Kartierobjekt ' . $rs['id'] . ' erfolgreich geändert!';
			}
			else {
				$success = false;
				$messages .= '<p>Fehler:<br>' . $ret['msg'];
				$err_sqls .= '<p>SQL:<br>' . $sql;
			}
		}
	}
	else {
		$messages = 'Es wurden keine Kartierobjekt-IDs zur Änderung des Status übergeben!';
	}

	$this->qlayerset[0]['shape'][0] = array(
		'kartierung_ids' => implode(', ', $kartierung_ids),
		'success' => $success,
		'msg' => $messages,
		'sql' => $err_sqls
	);
?>