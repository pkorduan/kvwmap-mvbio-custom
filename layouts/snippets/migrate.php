<?php
	if ($this != NULL) {
		#https://mvbio.de/kvwmap_dev/index.php?go=show_snippet&snippet=migrate
		define('MIGRATIONS_FILE', WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/db/migrations.txt');
		function find_new_migration_files() {
			$search_path = WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/db/*/*/';
			$migration_files = glob($search_path . '*.sql');
			#echo '<br>Durchsuche das Verzeichnis: ' . $search_path;
			#echo '<p>Existierende Migrationsdateien:';
			#show_migration_files($migration_files);
			$executed_migration_files = array_map(
				function($line) {
					return trim($line);
				},
				file(MIGRATIONS_FILE)
			);
			#echo '<p>Schon ausgeführte Migrationsdateien:';
			#show_migration_files($executed_migration_files);
			$diff = array_diff($migration_files, $executed_migration_files);
			# ToDo Sort $diff by timestamp instead of name
			usort($diff, function($a, $b) {
				return basename($a) > basename($b);
			});
			return $diff;
		}

		function show_migration_files($migration_files) {
			if (count($migration_files) == 0) { ?>
				<br>keine<?
			}
			else { ?>
				<ul><?
					echo implode("\n", array_map(
						function($migration_file) {
							return '<li>' . $migration_file . '</li>';
						},
						$migration_files
					)); ?>
				</ul><?
			}
		}

		function migrate_files($files) {
			$migrated_files = array();
			foreach ($files AS $migration_file) {
				if (migrate($migration_file)) {
					$migrated_files[] = $migration_file;
				}
				else {
					break;
				}
			}
			return $migrated_files;
		}

		function migrate($file) {
			GLOBAL $GUI;
			if (strpos($file, 'layouts/db/mysql') !== false) {
				# migrate to mysql
				$db = $GUI->database;
			}
			if (strpos($file, 'layouts/db/postgresql') !== false) {
				# migrate to postgresql
				$db = $GUI->pgdatabase;
			}

			$sql = file_get_contents($file);
			#echo "<br>Exec SQL: " . $sql;
			$ret = $db->execSQL($sql, 4, 0);
			if ($ret['success']) {
				register_migration_file($file);
				return true;
			}
			else {
				echo '<p>Fehler beim Ausführen der Migration ' . $file;
				return false;
			}
		}

		function register_migration_file($file) {
			file_put_contents(MIGRATIONS_FILE, $file  . "\n", FILE_APPEND | LOCK_EX);
		}

		function show_complete_msg() {
			global $GUI;
			$GUI->add_message('info', 'Alles sauber. Keine Dateien zu migrieren.');
		}
	?>
	<h1 style="margin: 15px; width: 1000px">
		Custom Migrations
		<i
			class="fa fa-info-circle"
			aria-hidden="true"
			style="color: orange; cursor: pointer"
			onclick="message([{
				'type': 'info',
				'msg': 'Es werden alle Dateien ausgeführt, die unter\
								<ul>\
									<li>db/mysql/data</li>\
									<li>db/postgresql/data</li>\
									<li>db/postgresql/schema</li>\
								</ul>\
								stehen und noch nicht in der Datei <? echo MIGRATIONS_FILE ?> aufgeführt sind.'
			}])"></i>
	</h1>
	<div style="text-align: left; margin-left: 25px"><?php
			$new_migration_files = find_new_migration_files();
			if (count($new_migration_files) > 0) {
				if ($this->formvars['migrate'] == 'Migrationen jetzt ausführen') {
					$migrated_files = migrate_files($new_migration_files);
					echo '<p>Ausgeführte Migrationsdateien:';
					show_migration_files($migrated_files);
					if (count($new_migration_files) == count($migrated_files)) {
						show_complete_msg();
					}
					else { ?>
						<p>Korrigiere Migrationsdatei!<p><a href="index.php?go=show_snippet&snippet=migrate">Zurück zur Liste</a><?
					}
				}
				else { ?>
					<p>Noch nicht ausgeführte Migrationsdateien:<?
					show_migration_files($new_migration_files); ?>
					<input type="hidden" name="go" value="show_snippet">
					<input type="hidden" name="snippet" value="migrate">
					<p><input type="submit" name="migrate" value="Migrationen jetzt ausführen"><?
				}
			}
			else {
				show_complete_msg();
			} ?>
	</div><?
}
else { ?>
	login<?
}