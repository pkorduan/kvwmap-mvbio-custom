<?php
	# call this script with
	# php custom/tools/get_fotos_exif.php foto=1000/140.JPG
	include('funktionen/allg_funktionen.php');
	$foto = explode('=', $argv[1])[1];
	$exif_data = get_exif_data('/var/www/data/mvbio/fotos/' . $foto);
	print_r($exif_data);
	$content = "\"" . $foto . "\";\"" . $exif_data['LatLng'] . "\";\"" . $exif_data['Richtung'] .  "\"\n";
#	file_put_contents('/var/www/data/mvbio/fotos/exif_latlngs.csv', $content); # Nicht anhängen mit FILE_APPEND
?>