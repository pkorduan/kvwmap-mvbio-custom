<h2>Korrigierte Fotopositionen</h2>
<?
  $sql = "
		SELECT
		  id,
			verlustobjekt_id,
		  split_part(datei, '&' , 1) datei,
			exif_latlng
		FROM
		  mvbio.fotos_verlustobjekte
		WHERE
		  datei IS NOT NULL AND
			exif_latlng IS NOT NULL
		ORDER BY datei
	";

	$ret = $this->pgdatabase->execSQL($sql);
	$fotos = pg_fetch_all($ret[1]);
	$lats = array();
	$update_sql = array();
	error_reporting(E_ERROR);
	foreach ($fotos AS $foto) {
		$exif_data = get_exif_data($foto['datei']);
		if (
			$exif_data['success'] AND
			$exif_data['LatLng'] != '' AND
			$exif_data['LatLng6000'] != ''
		) {
			$parts = explode(' ', $exif_data['LatLng']);
			#var_dump($parts);
			if (
				$parts[0] > 53 AND
				$parts[0] < 55 AND
				$parts[1] > 10 AND
				$parts[1] < 15 AND
				$exif_data['LatLng6000'] == $foto['exif_latlng']
			) {
				#var_dump($exif_data);
				#$lats[] = $parts[0] . ' ' . $foto['datei'] . ' ' . $exif_data['LatLng'];
				$update_sql[] = "
					UPDATE
						mvbio.fotos_verlustobjekte
					SET
						exif_latlng = '" . $exif_data['LatLng'] . "'
					WHERE
						id = " . $foto['id'] . ";
				";
				$update_sql[] = "
					UPDATE
						mvbio.fotos_verlustobjekte
					SET
						geom = gdi_geomfromexiflatlng('" . $exif_data['LatLng'] . "', 5650)
					WHERE
						ST_Distance(geom, gdi_geomfromexiflatlng('" . $exif_data['LatLng6000'] . "', 5650)) < 1 AND
						id = " . $foto['id'] . ";
				";
			}
		}
	}
	error_reporting(E_WARNING);
	echo '<br>Anzahl korrigierte Fotos: ' . count($update_sql);
	#asort($lats);
?>
<p><textarea cols="100" rows="50"><? echo implode("", $update_sql); ?></textarea>