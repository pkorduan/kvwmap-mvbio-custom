<?php include_once(SNIPPETS . 'generic_layer_editor_2.php'); ?>

<style>
	.bewertungsFrame {
		background-color:white;
		z-index:888;
	}

	.bewertungsFrame td {
		padding-left:13px;
		padding-top:5px;
		padding-bottom:5px;
	}

	td.gle_attribute_value {
		white-space: nowrap;
	}

	#group145_92_0 td {
		text-align:center;
	}

	#group145_92_0 tr td:first-child {
		text-align:left;
	}

	#tr_145_dummy1_0 tr td {
		text-align:center !important;
	}

	.display_none {
		display: none;
	}

	.savebutton {
		font: bold 11px Arial;
		text-decoration: none;
		background-color: #EEEEEE;
		color: #333333;
		padding: 2px 6px 2px 6px;
		border-top: 1px solid #CCCCCC;
		border-right: 1px solid #333333;
		border-bottom: 1px solid #333333;
		border-left: 1px solid #CCCCCC;
	}

	.savebutton:hover {
		padding: 1px 5px 1px 5px;
		border-top: 2px solid #CCCCCC;
		border-right: 2px solid #333333;
		border-bottom: 2px solid #333333;
		border-left: 2px solid #CCCCCC;
	}
</style>

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
			if (!window.kvwmapApp105) {
				window.kvwmapApp105 = new App(105); 
				window.kvwmapApp105.start();
			}
		}
		loadScript("../../<?php echo CUSTOM_PATH; ?>layouts/snippets/kartierobjekte.js", callback);	
	}

	function unloaded() {
		if (window.kvwmapApp) {
			window.kvwmapApp = null;
		}
	}

	window.addEventListener('load', loaded);
	window.addEventListener('unload', unloaded);
</script>