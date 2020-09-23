<?php include_once(SNIPPETS . 'generic_layer_editor_2.php'); ?>

<script>
	function loadScript(url, callback) {
		var head = document.getElementsByTagName('head')[0];
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = url;
		script.onreadystatechange = callback;
		if (callback) {
			script.onload = callback;
		}
		head.appendChild(script);
	}

	function loaded() {
		var callback = function() {
		}
		loadScript("<?php echo CUSTOM_PATH; ?>layouts/snippets/verlustobjekte.js", callback);
		loadScript("<?php echo CUSTOM_PATH; ?>layouts/snippets/multiPhotoUploadDiv.js");
	}

	function unloaded() {
		if (window.kvwmapApp) {
			window.kvwmapApp = null;
		}
	}

	window.addEventListener('load', loaded);
	window.addEventListener('unload', unloaded);
</script>