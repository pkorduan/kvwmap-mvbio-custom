<?php
	# call this script with
	# php custom/tools/get_fotos_verlustobjekte_exif.php foto=1000/140.JPG
	include('funktionen/allg_funktionen.php');
	$foto = explode('=', $argv[1])[1];
	$exif_data = get_exif_data_falsch('/var/www/data/mvbio/fotos_verlustobjekte/' . $foto);
	$content = "\"" . $foto . "\";\"" . $exif_data['LatLng'] . "\";\"" . $exif_data['LatLngFalsch'] . "\";\"" . $exif_data['Richtung'] .  "\"\n";
	file_put_contents('/var/www/data/mvbio/fotos_verlustobjekte/exif_latlngs.csv', $content, FILE_APPEND);
?>