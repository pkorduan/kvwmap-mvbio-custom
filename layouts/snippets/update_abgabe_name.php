<?
	include_once(CLASSPATH . 'PgObject.php');
	$success = false;
	$object_ids = array();
	$sql = '';
	if ($this->formvars['object_ids'] != '') {
		# Update Objekte
		$sql = "
			UPDATE
				mvbio." . ($this->formvars['objektart'] == 'Verlustobjekte' ? 'verlustobjekte' : 'kartierobjekte') . " k
			SET
				lock = true,
				abgabe_name = " . ($this->formvars['abgabe_name'] != '' ? "'" . $this->formvars['abgabe_name'] . "'" : "NULL") . "
			WHERE
				k.id IN (" . $this->formvars['object_ids'] . ")" .
				$kartiererfilter . "
			RETURNING
				k.id
		";
		#echo $sql;
		$ret = $this->pgdatabase->execSQL($sql, 4, 0);
		if ($ret['success']) {
			while ($rs = pg_fetch_assoc($ret[1])) {
				$object_ids[] = $rs['id'];
			}
			if (count($object_ids) > 0) {
				$success = true;
				$msg = 'Abgabename der Objekte erfolgreich geändert.';
			}
			else {
				$msg = 'Es konnten keine Objekte geändert werden.<br>Prüfen Sie Ihre Rechte.';
			}
		}
		else {
			$msg = $ret['msg'];
		}
	}
	else {
		$msg = 'Es wurden keine Objekt-IDs zur Änderung des Abgabenamen übergeben!';
	}

	$this->qlayerset[0]['shape'][0] = array(
		'object_ids' => implode(', ', $object_ids),
		'success' => $success,
		'msg' => $msg,
		'sql' => $sql
	);
?>