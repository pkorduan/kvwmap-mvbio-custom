<?php
	# Frage Bogenart ab
	$sql = "
		SELECT
		  l.layer_id
		FROM
		  archiv.erfassungsboegen eb
		  JOIN archiv.kampagnen kk ON eb.kampagne_id = kk.id
		  JOIN mvbio.bogenarten ba ON eb.bogenart_id = ba.id
		  LEFT JOIN archiv.bewertungsboegen bb ON eb.id = bb.id
		  LEFT JOIN mvbio.layer l ON l.name = 'Archiv '
		  || kk.layer_kuerzel
		  || ' '
		  || CASE
		    WHEN eb.bogenart_id = 4
			  THEN bb.lrt_gr_tex
			  ELSE ba.layer_name_part
		  END
		WHERE
			eb.id = " . $this->formvars['bogen_id'] . "
	";
	$ret = $this->pgdatabase->execSQL($sql, 4, 0);
	$rs = pg_fetch_assoc($ret[1]);
	$this->formvars['selected_layer_id'] = $rs['layer_id'];
	$this->formvars['go'] = 'Layer-Suche_Suchen';
	$this->formvars['operator_id'] = '=';
	$this->formvars['value_id'] = $this->formvars['bogen_id'];
	$this->formvars['only_main'] = 1;
	$this->GenerischeSuche_Suchen();
?>