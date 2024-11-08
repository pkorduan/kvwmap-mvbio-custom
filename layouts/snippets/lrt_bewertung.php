<?php include_once(SNIPPETS . 'generic_layer_editor_2.php'); ?>

<style>
	.frame {
		z-index: 10;
		position: fixed;
		border-width: 0px;
		top:3px;
		left:3px;
		background-color:#fff;
	}
	.frame > div:first-child {
		background: linear-gradient(#DAE4EC 0%, lightsteelblue 100%);
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 5px;
		padding-right:18px;
		border-width: 1px;
		border-bottom-width: 0px;
		border-color: gray;
		border-style: solid;
	}
	
	.frame > div:first-child span {
		margin-left: 4px;
	}
	
	.frame a:hover {
		cursor:hand;
	}
	
	.fett {
		/*white-space: pre;*/
	}
	
	.under_title {
		margin-left:20px;
		font-family: SourceSansPro1;
	}
	
	/*
	.bewertungsFrame {
		background-color:white;
		z-index:888;
	}
	*/
	.bewertungsFrame table {
		border-collapse: collapse;
		border-spacing: 0px;
	}
	
	.bewertungsFrame td {
		padding-left:5px;
		padding-top:5px;
		padding-bottom:5px;
		border-style:solid;
		border-width:1px;
		border-color:gray;
	}
	
	td.gle_attribute_value {
		white-space: nowrap;
	}
	
	#group144_101_0 td, #group145_92_0 td {
		text-align:center;
	}
	
	#group144_101_0 td:first-child, #group145_92_0 tr td:first-child {
		text-align:left;
	}

	/* Zentrierung Überschriften über Radiobutton Gesamt-Bewertungen */
	#tr_144_dummy1_0 td, #tr_145_dummy1_0 td, #tr_156_dummy1_0 td, #tr_157_dummy1_0 td {
		text-align:center !important;
	}

	
	#group156_131_0 td {
		text-align:center;
	}	
	#group156_131_0 td:first-child {
		text-align:left;
	}	
	
	#value_156_t325_1_0, #name_156_t325_1_0 {
		width:unset !important;
	}

	/* test */
	#group157_100_0 td {
		text-align:center;
	}	
	#group157_100_0 td:first-child {
		text-align:left;
	}	
	
	#value_157_t325_1_0, #name_157_sys_habit_0 {
		width:unset !important;
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
		(new BewertungApp()).start();
	}
	loadScript("<?php echo CUSTOM_PATH; ?>layouts/snippets/lrt_bewertung.js?v=1", callback);	
}


// document.addEventListener("DOMContentLoaded", loaded);
window.addEventListener('load', loaded);


</script>
