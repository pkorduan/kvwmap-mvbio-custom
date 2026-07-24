<?php
/*
* Diese Datei nach custom/layouts/snippets kopieren und auszuführen mit URL:
* https://gdi-service.de/kvwmap/index.php?go=show_snippet&snippet=check_migration_is_executed
* ggf. kvwmap ersetzen durch andere Applikationsversion
* Nach Bereinigung wieder aus custom-Verzeichnis löschen.
*/
if (isset($this)) {
  function check_if_migration_is_executed($database, $migration_file, $check_sql, $condition) {
    $ret = $database->execSQL($check_sql, 1, 0);
    $num_rows = mysqli_num_rows($ret['result']); ?>
    <div class="col1"><?php echo $migration_file; ?></div>
    <div class="col2"><?php
      $is_executed = ($condition == 'return_row' ? $num_rows > 0 : $num_rows == 0);
      if ($is_executed) {
        echo 'ja';
      }
      else {
        echo 'nein';
      } ?>
    </div>
    <div class="col3"><textarea col="3" style="width: 500px"><?php
      echo $check_sql;
    ?></textarea>
    </div>
    <div style="clear: both"></div><?php
  } ?>
  <p>
  <style>
    #container_paint {
      width: 1180px;
    }
    .col1 {
      float: left;
      width: 420px;
      margin-left: 50px;
      text-align: left;
    }
    .col2 {
      float: left;
      width: 50px;
      margin-left: 10px;
      text-align: left;
    }
    .col3 {
      float: left;
      width: 600px;
      margin-left: 50px;
      text-align: left;
      margin-bottom: 5px;
    }
  </style>
  <h2>Prüfung welche Migrationen schon ausgeführt wurden.</h2>
  <p>
  Aktueller Stand Software: <?php echo file_get_contents('version.txt'); ?>
  <p>
  Prüfe welche Migrationen ausgeführt sind in Datenbank: <?php echo $this->database->dbName; ?>
  <p>
  <div class="col1"><b>Migrationsdatei</b></div>
  <div class="col2"><b>Bereits ausgeführt</b></div>
  <div class="col3"><b>Prüfbedingung<b></div>
  <div style="clear: both"><?php

  check_if_migration_is_executed(
    $this->database,
    '2020-08-18_09-10-17_layer_use_geom.sql',
    "
      SELECT *
      FROM information_schema.COLUMNS
      WHERE
        TABLE_SCHEMA = '" . $this->database->dbName . "' AND
        TABLE_NAME = 'layer' AND
        COLUMN_NAME = 'use_geom'
    ",
    'return_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-08-19_09-20-04_layer_max_query_rows.sql',
    "
      SELECT *
      FROM information_schema.COLUMNS
      WHERE
        TABLE_SCHEMA = '" . $this->database->dbName . "' AND
        TABLE_NAME = 'layer' AND
        COLUMN_NAME = 'max_query_rows'
    ",
    'return_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-08-25_12-13-32_drop_filteritem.sql',
    "
      SELECT *
      FROM information_schema.COLUMNS
      WHERE
        TABLE_SCHEMA = '" . $this->database->dbName . "' AND
        TABLE_NAME = 'layer' AND
        COLUMN_NAME = 'filteritem'
    ",
    'return_no_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-10-02_13-56-39_Indizes',
    "
      SELECT *
      FROM information_schema.statistics
      WHERE
        table_schema = '" . $this->database->dbName . "' AND
        table_name = 'used_layer' AND
        column_name = 'Layer_ID' AND
        INDEX_NAME != 'PRIMARY'
    ",
    'return_row'
  );
	
		?>
		<div class="col1">2020-10-13_15-07-31_custom_kvwmap.php</div>
		<div class="col2"><?php
			if(is_dir(WWWROOT.APPLVERSION.CUSTOM_PATH.'class')){
				echo 'ja';
			}
			else {
				echo 'nein';
			} ?>
		</div>
		<div class="col3">
		</div>
    <div style="clear: both"></div>
		<br>
		<?

  check_if_migration_is_executed(
    $this->database,
    '2020-10-20_12-28-45_menue_get_last_query.sql',
    "
      SELECT *
      FROM u_menues
      WHERE
        links = 'javascript:void(0)' AND
        onclick = 'overlay_link(\'go=get_last_query\', true)'
    ",
    'return_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-10-23_13-02-43_Bug_layer_attributes_formelementtype.sql',
    "
      SELECT
        column_type
      FROM
        information_schema.COLUMNS
      WHERE
        TABLE_SCHEMA = '" . $this->database->dbName . "' AND
        TABLE_NAME = 'layer_attributes' AND
        COLUMN_NAME = 'form_element_type' AND
        column_type = \"enum('Text','Textfeld','Auswahlfeld','Autovervollständigungsfeld','Autovervollständigungsfeld_zweispaltig','Radiobutton','Checkbox','Geometrie','SubFormPK','SubFormFK','SubFormEmbeddedPK','Time','Dokument','Link','dynamicLink','User','UserID','Stelle','StelleID','Fläche','Länge','Zahl','mailto','Winkel','Style','Editiersperre','ExifLatLng','ExifRichtung','ExifErstellungszeit')\"
    ",
    'return_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-11-04_10-54-07_menues_Layer-Suche-Suchen.sql',
    "
      SELECT *
      FROM u_menues
      WHERE
        `links` like 'index.php?go=Layer-Suche_Suchen%'
    ",
    'return_no_row'
  );

  check_if_migration_is_executed(
    $this->database,
    '2020-11-04_13-00-17_fk_for_ddl.sql',
    "
      SELECT *
      FROM information_schema.REFERENTIAL_CONSTRAINTS
      WHERE
      CONSTRAINT_SCHEMA = '" . $this->database->dbName . "' AND
      REFERENCED_TABLE_NAME = 'druckfreirechtecke' AND
      CONSTRAINT_NAME = 'ddl2freirechtecke_ibfk_1'
    ",
    'return_row'
  );
}
else { ?>
  Diese Datei kann nur im kvwmap Context ausgeführt werden!<?php
}
?>