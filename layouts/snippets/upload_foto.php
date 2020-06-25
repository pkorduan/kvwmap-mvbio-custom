<?php
$this->only_main = 1;
// set error reporting level
if (version_compare(phpversion(), '5.3.0', '>=') == 1)
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
	error_reporting(E_ALL & ~E_NOTICE);

function bytesToSize1024($bytes, $precision = 2) {
	$unit = array('B','KB','MB');
	return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
}

if (isset($_FILES[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1'])) {
	$sFileName = $_FILES[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1']['name'];
	$sFileTmpName = $_FILES[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1']['tmp_name'];
	$sFileType = $_FILES[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1']['type'];
	$sFileSize = bytesToSize1024($_FILES[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1']['size'], 1);

	$this->formvars['go'] = 'neuer_Layer_Datensatz_speichern';
	$this->formvars['selected_layer_id'] = $this->formvars['foto_layer_id'];
	$this->formvars['form_field_names'] = $this->formvars['foto_layer_id'] . ';id;' . $this->formvars['foto_table_name'] . ';;Text;1;int4;1|' . $this->formvars['foto_layer_id'] . ';' . $this->formvars['join_attribute_name'] . ';' . $this->formvars['foto_table_name'] . ';;Text;0;int4;1|' . $this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1|' . $this->formvars['foto_layer_id'] . ';exif_latlng;' . $this->formvars['foto_table_name'] . ';;ExifLatLng;1;varchar;1|' . $this->formvars['foto_layer_id'] . ';exif_richtung;' . $this->formvars['foto_table_name'] . ';;ExifRichtung;1;float8;1|' . $this->formvars['foto_layer_id'] . ';exif_erstellungszeit;' . $this->formvars['foto_table_name'] . ';;ExifErstellungszeit;1;timestamp;1';
	$this->formvars[$this->formvars['foto_layer_id'] . ';id;' . $this->formvars['foto_table_name'] . ';;Text;1;int4;1'] = '';
	$this->formvars[$this->formvars['foto_layer_id'] . ';' . $this->formvars['join_attribute_name'] . ';' . $this->formvars['foto_table_name'] . ';;Text;0;int4;1'] = $this->formvars['join_attribute_id'];
	$this->formvars[$this->formvars['foto_layer_id'] . ';datei;' . $this->formvars['foto_table_name'] . ';;Dokument;1;varchar;1'] = 1;
	$this->formvars['only_create'] = true;
	$this->neuer_Layer_Datensatz_speichern();
	echo <<<EOF
<div class="s">
	<p>Das Foto: {$sFileName} wurde erfolgreich hochgeladen.</p>
	<p>Type: {$sFileType}</p>
	<p>Dateigröße: {$sFileSize}</p>
</div>
EOF;
}
else {
	echo '<div class="f">Es ist ein Fehler aufgetreten.</div>';
}