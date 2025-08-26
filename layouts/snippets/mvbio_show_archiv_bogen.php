<?php
	# Frage Layer-id der Bogenart ab
	$sql = "
		SELECT
		  l.layer_id
		FROM
		  archiv.erfassungsboegen eb
		  JOIN archiv.kampagnen kk ON eb.kampagne_id = kk.id
		  JOIN mvbio.bogenarten ba ON eb.bogenart_id = ba.id
		  LEFT JOIN archiv.grundboegen gb ON eb.id = gb.id
			LEFT JOIN mvbio.lrt_gruppen lrg ON lrg.id = gb.lrt_gr::integer
		  LEFT JOIN mvbio.layer l ON l.name = 'Archiv '
		  || kk.layer_kuerzel
		  || ' '
		  || CASE
		    WHEN eb.bogenart_id = 4
			  THEN lrg.bezeichnung
			  ELSE ba.layer_name_part
		  END
		WHERE
			eb.id = " . $this->formvars['bogen_id'] . "
	";
	#echo '<p>SQL zum Abfragen der Datensätze: ' . $sql;
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	$rs = pg_fetch_assoc($ret[1]);
	$this->formvars['selected_layer_id'] = $rs['layer_id'];
	$this->formvars['go'] = 'Layer-Suche_Suchen';
	$this->formvars['operator_bogen_id'] = '=';
	$this->formvars['value_bogen_id'] = $this->formvars['bogen_id'];
	$this->formvars['only_main'] = true;
	$this->GenerischeSuche_Suchen();
?>