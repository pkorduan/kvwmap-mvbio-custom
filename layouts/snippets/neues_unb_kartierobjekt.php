<?php
	# Frage Kartiergebiet des angemeldeten Benutzers ab
	$sql = "
		SELECT
			kg.id AS kartiergebiet_id
		FROM
			mvbio.kartiergebiete kg JOIN
			mvbio.kartierer k ON kg.id = k.kartiergebiet_id
		WHERE
			" . ($this->formvars['unb_kampagne_id'] != '' ? "kg.kampagne_id = " . $this->formvars['unb_kampagne_id'] . " AND" : "") . "
			nutzer_id = " . $this->user->id . "
		LIMIT 1
	";
	#echo '<p>SQL zum Abfragen des Kartiergebietes: ' . $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	$rs = pg_fetch_assoc($ret[1]);
	$this->formvars['selected_layer_id'] = 105;
	$this->formvars['go'] = 'neuer_Layer_Datensatz';
	if ($this->formvars['unb_kampagne_id'] != '' AND $rs['kartiergebiet_id'] != '') {
		$this->formvars['105;kampagne_id;kartierobjekte;;Auswahlfeld;1;int4;1'] = $this->formvars['unb_kampagne_id'];
		$this->formvars['105;kartiergebiet_id;kartierobjekte;;Auswahlfeld;1;int4;1'] = $rs['kartiergebiet_id'];
		$this->formvars['105;kartierebene_id;kartierobjekte;;Auswahlfeld;1;int4;1'] = $this->formvars['kartierebene_id'];
		$this->formvars['105;bogenart_id;kartierobjekte;;Auswahlfeld;1;int4;1'] = 2;
	}
	go_switch('neuer_Layer_Datensatz', true);
	$this->only_main = 1;
?>