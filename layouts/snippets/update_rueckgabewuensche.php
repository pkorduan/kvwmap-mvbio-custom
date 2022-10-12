<?
if (!defined('CLASSPATH')) exit;
include_once(CLASSPATH . 'PgObject.php');
$success = true;
$messages = '';
$err_sqls = '';
if ($this->formvars['object_ids'] != '') {
	$this->sanitize([
		'object_ids' => 'text'
	]);
	$object_ids = array_map(
		function($object_id) {
			return sanitize($object_id, 'int');
		},
		explode(',', $this->formvars['object_ids'])
	);
	$table_name = ($this->formvars['objektart'] == 'Verlustobjekte' ? 'verlustobjekte' : 'kartierobjekte');
	# Update Objekte. Kartierobjekte die schon durch Prüfer für die Archivierung freigegeben wurden werden nicht mehr zurückgegeben.
	# Beschränkung auf Kartierer wenn Änderungsberechtigte Stelle Kartierung ist
	$kartiererfilter = ($this->Stelle->Bezeichnung == 'Karterung' ? " AND k.user_id = " . $this->user->id : "");
	$sql = "
		UPDATE
			mvbio." . $table_name . "
		SET
			lock = true,
			rueckgabewunsch = true
		WHERE
			(rueckgabewunsch IS NULL OR rueckgabewunsch = false) AND
			bearbeitungsstufe < 5 AND
			id IN (" . implode(', ', $object_ids) . ")
			" . $kartiererfilter . "
	";
	#echo 'SQL zum Update der Rückgabewünsche:<br>' . $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	if ($ret['success']) {
		$messages .= '<br>' . 'Rückgabewünsche für ' . ucfirst($table_name) . ' erfolgreich eingetragen!';
	}
	else {
		$success = false;
		$messages .= '<p>Fehler:<br>' . $ret['msg'];
		$err_sqls .= '<p>SQL:<br>' . $sql;
	}
}
else {
	$messages = 'Es wurden keine Kartierobjekt-IDs zur Änderung des Rückgabewunsches übergeben!';
}

$this->qlayerset[0]['shape'][0] = array(
	'object_ids' => implode(', ', $object_ids),
	'success' => $success,
	'msg' => $messages . ' ' . $sql,
	'sql' => $err_sqls
);
?>