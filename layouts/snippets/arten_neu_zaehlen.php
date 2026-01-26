<?php
  $sql = "
    SELECT mvbio.update_num_pflanzen(" . $this->formvars['value_lrt_objekt_id'] . ")
  ";
  $ret = $this->pgdatabase->execSQL($sql);
  if (!$ret['success']) {
    $this->Fehlermeldung = 'Fehler bei der Aktualisierung der Anzahl der Arten!' . $ret['msg'];
  }
  else {
    $this->add_message('notice', 'Anzahl der Arten erfolgreich aktualisiert!');
  }
  $this->formvars['only_main'] = true;
  $this->GenerischeSuche_Suchen();
?>
