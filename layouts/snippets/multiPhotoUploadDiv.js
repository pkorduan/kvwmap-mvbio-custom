function createMultiPhotoUploadDiv(layerId, fotoTableName, fotoLayerId, joinAttributeName, joinAttributeId) {
	console.log('createMultiplePhotoUploadDiv');
	fotoLayerId_ = fotoLayerId; // make the var global
  fotoTableName_ = fotoTableName;

	$('#new_' + layerId + '_fotos_0').after('<div id="multiple_foto_upload_sperr_div" class="sperr-div" style="display: block">');
	$('#multiple_foto_upload_sperr_div').append('<div id="multiple_foto_upload_div" class="mutliple_upload_div">');
	$('#multiple_foto_upload_div').append('\
		<div class="contr">\
			<div style="float:left">\
				<h2>Upload mehrerer Fotos zum Datensatz ID: ' + joinAttributeId + '</h2>\
			</div>\
			<div style="float: right">\
				<img src="graphics/exit.png" onclick="closeMultiPhotoUploadDiv(' + layerId + ')">\
			</div>\
		</div>\
		<div style="clear: both"></div>\
		<div class="upload_form_cont">\
			<div id="dropArea">\
				Fotos hier reinziehen\
				<span style="font-size: 16px">\
					<p>\
					bis zu 50 Dateien pro Upload<br>\
					Dateigröße pro Bild unter 5Mb</p>\
				</span>\
			</div>\
			<div class="info">\
				<div>\
					Verbleibende Dateien zum Hochladen: <span id="count">0</span>\
				</div>\
				<input type="hidden" id="upload_foto_url" value="index.php?go=show_snippet&snippet=upload_foto&only_main=1&only_create=1&foto_table_name=' + fotoTableName + '&foto_layer_id=' + fotoLayerId + '&join_attribute_name=' + joinAttributeName + '&join_attribute_id=' + joinAttributeId + '&csrf_token=' + csrf_token + '"/>\
				<h2>Ergebnis:</h2>\
				<div id="result"></div>\
				<canvas width="500" height="20"></canvas>\
			</div>\
		</div>\
		<script src="custom/layouts/snippets/upload_multiple_files.js"></script>\
		<link href="custom/layouts/upload_multiple_files.css" rel="stylesheet" type="text/css">\
	');
};

function closeMultiPhotoUploadDiv(layerId) {
	$('#multiple_foto_upload_sperr_div').remove();
	reload_subform_list(layerId + '_fotos_0', 0, 0);
};