<?php include_once(SNIPPETS . 'generic_layer_editor_2.php'); ?>

<script>
	function loadScript(url, callback) {
		// adding the script tag to the head as suggested before
		var head = document.getElementsByTagName('head')[0];
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = url;

		// then bind the event to the callback function 
		// there are several events for cross browser compatibility
		script.onreadystatechange = callback;
		script.onload = callback;

		// fire the loading
		head.appendChild(script);
	}

	function loaded() {
		var callback = function() {
		}
		loadScript("<?php echo CUSTOM_PATH; ?>layouts/snippets/verlustobjekte.js", callback);
		loadScript("<?php echo CUSTOM_PATH; ?>layouts/snippets/multiPhotoUploadDiv.js", callback);
	}

	function unloaded() {
		if (window.kvwmapApp) {
			window.kvwmapApp = null;
		}
	}

	window.addEventListener('load', loaded);
	window.addEventListener('unload', unloaded);
</script>