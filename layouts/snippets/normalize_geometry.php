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
			" . $attributes['the_geom'] . " = ST_Multi(
				gdi_normalize_geometry_v121(
					" . $attributes['the_geom'] . ",
					0.01,
					0.001,
					0.001,
					0.01,
					false
				)
			)
		WHERE
			" . $layerset[0]['oid'] . " = " . $this->formvars['oid'] . "
		RETURNING
			ST_AsText(
				ST_Multi(
					gdi_normalize_geometry_v121(
						" . $attributes['the_geom'] . ",
						0.01,
						0.001,
						0.001,
						0.01,
						false
					)
				)
			)
	";
	#echo '<p>SQL: ' . $sql;
	$ret = $layerdb->execSQL($sql);
	if (!$ret['success']) {
		$err_msg = 'Fehler in Datenbankabfrage!';
	}
	else {
		$rs = pg_fetch_row($ret[1]);
	}
?>
<h2 style="margin-top: 20px">Normalisierte Geometrie</h2><p><?
if ($err_msg) {
	echo $err_msg;
}
else { ?>
	Die Geometrie wurde erfolgreich normalisiert!<?
} ?><p>
<a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=<? echo $this->formvars['selected_layer_id']; ?>&value_kartierung_id=<?php echo $this->formvars['oid']; ?>&operator_kartierung_id==">zurück zum Kartierobjekt</a><p>
Layer ID: <? echo $this->formvars['selected_layer_id']; ?><br>
Layer Name: <? echo $layerset[0]['Name']; ?><br>
Tabelle: <? echo $table; ?><br>
ID-Spalte: <? echo $layerset[0]['oid']; ?><br>
Objekt-ID: <? echo $this->formvars['oid']; ?>
Geometrie-Spalte: <? echo $attributes['the_geom']; ?><br>
<!--SQL: <? echo $sql; ?><p//--><?
if (!$err_msg) { ?>
	Normalisierte Geometrie:<br><? echo $rs[0];
}