<? include_once(CLASSPATH . 'MyObject.php'); ?>
<h1 style="margin-top: 20px">Nutzersynchronisation</h1>
<div style="text-align: left; margin-left: 50px">
<?php
if ($this->user->funktion == 'admin') {
	/*
	$sql = "
		SELECT
			SCHEMA_NAME
		FROM
			INFORMATION_SCHEMA.SCHEMATA
		WHERE
			SCHEMA_NAME LIKE 'kvwmapdb_%' AND
			SCHEMA_NAME NOT LIKE '" . $this->database->dbName . "'
	";
	#echo 'SQL zur Abfrage der anderen Datenbanken, die mit kvwmapdb_ anfangen: ' . $sql;
	$ret = $this->database->execSQL($sql, 4, 0);
	while ($rs = mysql_fetch_assoc($ret[1])) {
		$databases[] = $rs['SCHEMA_NAME'];
	}
	*/

	/*
	* Wenn andere Datenbanken als die, die mit kvwmapdb_ anfangen synchronisiert werden, hier ein Array mit anderen Datenbanknamen befüllen.
	* $databases = array('webgis', 'demo', 'test')
	*/
	$databases = array('kvwmapdb', 'kvwmapdb_dev', 'kvwmapdb_test', 'kvwmapdb_demo'); ?>

	<p>Diese Funktion synchronisiert die Passwörter und das Datum der letzten Passwortänderung der Anwendung in der Sie jetzt angemeldet sind auf die folgenden Datenbanken:<br>
	<ul>
		<li><?
			echo implode('</li><li>', $databases); ?>
		</li>
	</ul>

	<p>Die Synchronisierung <a href="index.php?go=show_snippet&snippet=Nutzersynchronisierung&los=1">jetzt</a> vornehmen.<?php
	if ($this->formvars['los'] == 1) { ?>
		<p>los gehts ...<?php
		$myobject = new MyObject($this, 'user');
		$users = $myobject->find_where('1=1');

		foreach ($databases AS $dbname) {
			if ($this->database->dbName != $dbname) {
				$userDb = new database();
				$userDb->host = MYSQL_HOST;
				$userDb->user = MYSQL_USER;
				$userDb->passwd = MYSQL_PASSWORD;
				$userDb->dbName = $dbname;
				$userDb->open();?>
				<p>Syncronisiere mit Datenbank <? echo $dbname; ?><br><?
				foreach($users AS $user) {
					$sql = "
						UPDATE
							user
						SET
							passwort = '" . $user->get('passwort') . "',
							password_setting_time = '" . $user->get('password_setting_time') . "',
							ips = '" . $user->get('ips') . "'
						WHERE
							login_name = '" . $user->get('login_name') . "'
					";
					$userDb->execSQL($sql, 4, 0); ?> ... <?php echo $user->get('login_name'); ?><?php
				} ?><br>... fertig<?
			}
		}
	}
} else { ?>
	<p>Sie sind nicht berechtigt zum Ausführen dieser Funktion.<?php
} ?>
</div>