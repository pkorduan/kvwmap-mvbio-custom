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

if (isset($_FILES['109;datei;fotos;;Dokument;1;varchar;1'])) {
  $sFileName = $_FILES['109;datei;fotos;;Dokument;1;varchar;1']['name'];
  $sFileTmpName = $_FILES['109;datei;fotos;;Dokument;1;varchar;1']['tmp_name'];
  $sFileType = $_FILES['109;datei;fotos;;Dokument;1;varchar;1']['type'];
  $sFileSize = bytesToSize1024($_FILES['109;datei;fotos;;Dokument;1;varchar;1']['size'], 1);

	$this->formvars['go'] = 'neuer_Layer_Datensatz_speichern';
	$this->formvars['selected_layer_id'] = 109;
	$this->formvars['form_field_names'] = '109;id;fotos;;Text;1;int4;1|109;kartierung_id;fotos;;Text;0;int4;1|109;datei;fotos;;Dokument;1;varchar;1';
	$this->formvars['109;id;fotos;;Text;1;int4;1'] = '';
	$this->formvars['109;kartierung_id;fotos;;Text;0;int4;1'] = $this->formvars['kartierobjekt_id'];
	$this->formvars['109;datei;fotos;;Dokument;1;varchar;1'] = 1;
	$this->formvars['only_create'] = true;
	$this->neuer_Layer_Datensatz_speichern();
  echo <<<EOF
<div class="s">
    <p>Das Foto: {$sFileName} wurde erfolgreich hochgeladen.</p>
    <p>Type: {$sFileType}</p>
    <p>Dateigröße: {$sFileSize}</p>
</div>
EOF;
} else {
    echo '<div class="f">Es ist ein Fehler aufgetreten.</div>';
}