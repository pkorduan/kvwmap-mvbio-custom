<?php
	# call this script with
  # php /var/www/apps/kvwmap/custom/tools/get_fotos_exif.php foto=/var/www/data/mvbio/fotos/56000/55369.jpg&original_name=0207-333B5004.jpg
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
		file_put_contents('/var/www/data/mvbio/fotos_verlustobjekte/exif_latlngs.csv', $content, FILE_APPEND);
	}
	else {
		echo $exif_data['err_msg'];
	}
?>