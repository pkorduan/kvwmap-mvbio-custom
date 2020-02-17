<?php
	$log = new LogFile('/var/www/logs/mvbio/archivierung.log', 'text', date("Y:m:d H:i:s", time()));
	foreach ($kampagne['kartiergebiete'] AS $kartiergebiet) {
		# übernehme Kartiergebiet in das archiv
		$sql = "
			INSERT INTO archiv.kartiergebiete
			SELECT * FROM mvbio.kartiergebiete
			WHERE id = " . $kartiergebiet['id'] . "
		";
		$log->write($sql);

		# setze Status der unter dem Kartiergebiet liegenden Bögen zurück 
		$sql = "
			UPDATE archiv.grundboegen gb
			SET status = 0
			FROM
				mvbio.kartiergebiete kg
			WHERE
				kg.id = " . $kartiergebiet['id'] . " AND
				st_intersects(geom, kg.geom)
		";

		# Liest die im View aufbereiteten Kartierobjekte mit Status = 1 als Bögen in die Archivtabellen ein
		$sql = "
			INSERT INTO archiv.grundboegen
			SELECT * FROM mvbio.grundboegen
			WHERE
				kartiergebiet_id = " . $kartiergebiet['id'] . "
		";

		# Listen werden nach Änderung archiviert listenname_datum
		# gsl_version mit in archiv.pflanzenvorkommen übenehmen aus mvbio.pflanzenarten_gsl
		
		# festlegung einer biotopverzeichnisnummer

		# Löscht die Kartierungen im mvbio Schema
		$sql = "
			DELETE FROM mvbio.kartierobjekte
			WHERE kartiergebiet_id = " . $kartiergebiet['id'] . "
		";

		# Löscht das Kartiergebiet im mvbio Schema
		$sql = "
			DELETE FROM mvbio.kartiergebiete
			WHERE id = " . $kartiergebiet['id'] . "
		";
	}
	$log->close();
?>