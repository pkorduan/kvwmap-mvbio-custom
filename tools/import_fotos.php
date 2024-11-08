<?php
	echo "Script zum import von Fotos. Zur Zeit noch nicht fertig und nicht aktiviert."
	exit;

	$upload_verzeichnis = '/var/www/data/mvbio/fotos/xy_upload';

	# Lege Foto-Datensatz an über Verknüpfung zum Kartierobjekt
	$sql = "
		INSERT INTO mvbio.fotos (kartierung_id, datei, beschreibung)
		SELECT
			ko.id,
			'/var/www/data/mvbio/fotos/' || ((((SELECT max(id) FROM mvbio.fotos) + row_number() over(ORDER BY f.datei)) / 1000) + 1) * 1000 || '/' || (SELECT max(id) FROM mvbio.fotos) + row_number() over(ORDER BY f.datei) || '.jpg&original_name=' || split_part(f.datei,'./', 2) datei,
			'Bilderimport vom 22.03.2023 durch P. Korduan' beschreibung
		FROM
			import.fotos_upload f LEFT JOIN
			mvbio.kartierobjekte ko ON lower(replace(CASE WHEN datei LIKE '%#%' THEN split_part(datei, '#', 1) ELSE split_part(datei, '.jpg', 1) END, './', '')) = lower(ko.arbeits_id)
		WHERE
			ko.kartiergebiet_id = (SELECT id FROM mvbio.kartiergebiete WHERE bezeichnung LIKE $losnr_zb_Los1_2) AND
			ko.kartierer = $bedinung
		ORDER BY
			f.datei
	";
	# Liefer verwendete id zurück und kopiere das Foto in das Zielverzeichnis
	echo "Kopiert Fotos aus Upload-Verzeichnis"
	$sql = "
			SELECT
			  'cp ' || f.datei || ' /home/gisadmin/www/data/mvbio/fotos/' || ((((SELECT max(id) FROM mvbio.fotos) + row_number() over(ORDER BY f.datei)) / 1000) + 1) * 1000 || '/' || (SELECT max(id) FROM mvbio.fotos) + row_number() over(ORDER BY f.datei) || '.jpg' cp
			FROM
			  import.fotos_upload f LEFT JOIN
			  mvbio.kartierobjekte ko ON lower(replace(CASE WHEN datei LIKE '%#%' THEN split_part(datei, '#', 1) ELSE split_part(datei, '.jpg', 1) END, './', '')) = lower(ko.arbeits_id)
			WHERE
			  ko.kartiergebiet_id = (SELECT id FROM mvbio.kartiergebiete WHERE bezeichnung LIKE $losnr_zb_Los1_2)  AND
			  ko.kartierer = $bedinung
			ORDER BY
			  f.datei
			"
	# Frage exif_date ab und lese diese falls vorhanden ein.
	include('/var/www/apps/kvwmap/funktionen/allg_funktionen.php');
	echo "
argv1: " . $argv[1]. "
";
	$foto = explode('=', $argv[1])[1];
	echo "
foto: " . $foto . "
";
	$foto_datei = explode('&', $foto)[0];
	$exif_data = get_exif_data($foto_datei);
	echo "
foto_datei: " . $foto_datei . "
";
	if ($exif_data['success']) {
		echo print_r($exif_data, true);
		$content = "\"" . $foto . "\";\"" . $exif_data['LatLng'] . "\";\"" . $exif_data['Richtung'] .  "\";\"" . $exif_data['Erstellungszeit'] .  "\"\n";
		file_put_contents('/var/www/data/mvbio/fotos/exif_latlngs.csv', $content, FILE_APPEND);
	}
	else {
		echo $exif_data['err_msg'];
	}
	
	$sql = "

		UPDATE
		  mvbio.fotos f
		SET
		  exif_latlng = n.exif_latlng,
		  exif_richtung = n.exif_richtung,
		  exif_erstellungszeit = n.exif_erstellungszeit,
		  geom = n.geom
		FROM
		  (
		    SELECT
		      ko.id AS kartierobjekt_id,
		      CASE WHEN i.exif_latlng != '' THEN i.exif_latlng ELSE NULL END AS exif_latlng,
		      CASE WHEN i.exif_richtung != '' THEN i.exif_richtung::double precision ELSE NULL END AS exif_richtung,
		      i.exif_erstellungszeit,
		      CASE WHEN i.exif_latlng != '' THEN ST_Transform(ST_SetSrid(ST_MakePoint(split_part(i.exif_latlng, ' ', 2)::float, split_part(i.exif_latlng, ' ', 1)::float), 4326), 5650) ELSE NULL END AS geom,
		      ST_Distance(CASE WHEN i.exif_latlng != '' THEN ST_Transform(ST_SetSrid(ST_MakePoint(split_part(i.exif_latlng, ' ', 2)::float, split_part(i.exif_latlng, ' ', 1)::float), 4326), 5650) ELSE NULL END, ko.geom) AS dist
		    FROM
		      import.fotos_exif_latlngs i JOIN
		      mvbio.fotos f ON f.datei LIKE i.datei || '%' JOIN
		      mvbio.kartierobjekte ko ON f.kartierung_id = ko.id
		    WHERE
		      ST_Distance(CASE WHEN i.exif_latlng != '' THEN ST_Transform(ST_SetSrid(ST_MakePoint(split_part(i.exif_latlng, ' ', 2)::float, split_part(i.exif_latlng, ' ', 1)::float), 4326), 5650) ELSE NULL END, ko.geom) > 200
		  ) n
		WHERE
		  n.dist <= 200 AND
		  f.kartierung_id = n.kartierobjekt_id
		";
?>