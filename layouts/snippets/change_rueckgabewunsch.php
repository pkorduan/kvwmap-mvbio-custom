<?php
	$layerset = $this->user->rolle->getLayer($this->formvars['selected_layer_id']);
	$mapDB = new db_mapObj($this->Stelle->id,$this->user->id);
	$layerdb = $mapDB->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
	$attributes = $mapDB->read_layer_attributes($this->formvars['selected_layer_id'], $layerdb, NULL);
	#echo '<br>layer: ' . print_r($layerset[0], true);
	#echo '<p>Attributes: ' . print_r($attributes, true);
 	$table = $layerset[0]['schema'] . "." . $layerset[0]['maintable'];
	$sql = "
		UPDATE
			" . $table . "
		SET
			lock = true,
			rueckgabewunsch = NOT coalesce(rueckgabewunsch, false)
		WHERE
			user_id = " . $this->user->id . " AND
			" . $layerset[0]['oid'] . " = " . $this->formvars['value_kartierung_id'] . "
		RETURNING
			id
	";
	#echo '<p>SQL: ' . $sql;
	$ret = $layerdb->execSQL($sql);
	if (!$ret['success']) {
		$type = 'error';
		$err = 'Fehler in Datenbankabfrage!';
	}
	else {
		if (pg_num_rows($ret[1]) > 0) {
			$type = 'notice';
			$msg = "Rückgabewunsch geändert!";
		}
		else {
			$type = 'warning';
			$msg = "Rückgabewunsch konnte nicht geändert werden. Datensatz nicht gefunden oder kein Recht zur Änderung!";
		};
	}
	$this->add_message($type, $msg);
  $this->formvars['only_main'] = true;
	go_switch('Layer-Suche_Suchen');
?>