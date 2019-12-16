<?php
if ($this->go == 'Stelle_waehlen') {
	include_once(CLASSPATH . 'PgObject.php');
	$kartiergebiet = new PgObject($this, 'mvbio', 'kartiergebiete');
	$kartierebene = new PgObject($this, 'mvbio', 'kartierebenen2kampagne');
	$bogenart = new PgObject($this, 'mvbio', 'bogenarten2kartierebenen');
	#$this->add_message('warning', 'kartiergebiete: ' . print_r(array_map(function($kg) { return $kg->get('kampagne_id'); }, $kartiergebiete), true)); ?>
	<script>
		
		var kartiergebiete = [<?php
			echo implode(
				', ',
				array_map (
					function ($kg) {
						return "'" . $kg->get('kampagne_id') . "-" . $kg->get('kartiergebiet_id') . "'";
					},
					$kartiergebiet->find_where(NULL, NULL, 'kampagne_id, id AS kartiergebiet_id')
				)
			); ?>
		];

		var kartierebenen = [<?php
			echo implode(
				', ',
				array_map (
					function ($kg) {
						return "'" . $kg->get('kampagne_id') . "-" . $kg->get('kartierebene_id') . "'";
					},
					$kartierebene->find_where(NULL, NULL, 'kampagne_id, kartierebene_id')
				)
			); ?>
		];

		var bogenarten = [<?php
			echo implode(
				', ',
				array_map (
					function ($kg) {
						return "'" . $kg->get('kartierebene_id') . "-" . $kg->get('bogenart_id') . "'";
					},
					$bogenart->find_where(NULL, NULL, 'kartierebene_id, bogenart_id')
				)
			); ?>
		];

		var onload_functionsCore = onload_functions;
		onload_functions = function() {
			onload_functionsCore.apply(this, arguments);

			filterKartiergebiete = function(kampagne_id) {
				//console.log('function filterKartiergebiete mit kampagne_id: ' + kampagne_id);
				if (kampagne_id == 0) {
					$('#layer_parameter_kartiergebietfilter option').show();
					$('#layer_parameter_kartiergebietfilter').val("0");
					$('#layer_parameter_kartiergebietfilter_tr').hide();
				}
				else {
					$('#layer_parameter_kartiergebietfilter_tr').show();
					$('#layer_parameter_kartiergebietfilter option').each(function(i, option) {
						if (option.value > 0 && $.inArray(kampagne_id + '-' + option.value, kartiergebiete) == -1) {
							if (option.selected) {
								$('#layer_parameter_kartiergebietfilter').val("0");
							}
							$(option).hide();
						}
						else {
							$(option).show();
						}
					});
				}
			};

			filterKartierebenen = function(kampagne_id) {
				//console.log('function filterKartierebene mit kampagne_id: ' + kampagne_id);
				if (kampagne_id == 0) {
					$('#layer_parameter_kartierebenenfilter option').show();
					$('#layer_parameter_kartierebenenfilter').val("0").trigger("change");
					$('#layer_parameter_kartierebenenfilter_tr').hide();
				}
				else {
					$('#layer_parameter_kartierebenenfilter_tr').show();
					$('#layer_parameter_kartierebenenfilter option').each(function(i, option) {
						if (option.value > 0 && $.inArray(kampagne_id + '-' + option.value, kartierebenen) == -1) {
							if (option.selected) {
								$('#layer_parameter_kartierebenenfilter').val("0").trigger("change");
							}
							$(option).hide();
						}
						else {
							$(option).show();
						}
					});
				}
			};

			filterBogenarten = function(kartierebene_id) {
				//console.log('function filterBogenarten mit kartierebene_id: ' + kartierebene_id);
				if (kartierebene_id == 0) {
					$('#layer_parameter_bogenartfilter option').show();
					$('#layer_parameter_bogenartfilter').val("0");
					$('#layer_parameter_bogenartfilter_tr').hide();
				}
				else {
					$('#layer_parameter_bogenartfilter_tr').show();
					$('#layer_parameter_bogenartfilter option').each(function(i, option) {
						if (option.value > 0 && $.inArray(kartierebene_id + '-' + option.value, bogenarten) == -1) {
							if (option.selected) {
								$('#layer_parameter_bogenartfilter').val("0");
							}
							$(option).hide();
						}
						else {
							$(option).show();
						}
					});
				}
			};

			onLayerParameterChanged = function(parameter) {
				switch (parameter.id) {
					case 'layer_parameter_kampagne_id':
						filterKartiergebiete(parameter.value);
						filterKartierebenen(parameter.value);
					break;
					case 'layer_parameter_kartierebenenfilter':
						filterBogenarten(parameter.value);
					break;
				}
			};
			filterKartiergebiete($('#layer_parameter_kampagne_id').val());
			filterKartierebenen($('#layer_parameter_kampagne_id').val());
			filterBogenarten($('#layer_parameter_kartierebenenfilter').val());
			$('#save_check_button').removeClass('red');
			$('#save_check_button').addClass('green');
		};
	</script><?
}
?>

<div style="
	width: 100%;
	height: 100%;
	background: linear-gradient(<? echo BG_GLEATTRIBUTE; ?> 0%, <? echo BG_DEFAULT ?> 100%);
">

	<div style="padding: 6px; float: left; width: 54%; text-align: left;"><?
		$params = $this->user->rolle->get_layer_params($this->Stelle->selectable_layer_params, $this->pgdatabase);
		$title = array();
		foreach ($params['kampagne_id']['options'] AS $param) {
			if ($param['value'] == rolle::$layer_params['kampagne_id']) {
				if ($param['output'] != 'alle') $title[] = $param['output'];
			}
		}
		foreach ($params['kartiergebietfilter']['options'] AS $param) {
			if ($param['value'] == rolle::$layer_params['kartiergebietfilter']) {
				if ($param['output'] != 'alle') $title[] = $param['output'];
			}
		}
		foreach ($params['kartierebenenfilter']['options'] AS $param) {
			if ($param['value'] == rolle::$layer_params['kartierebenenfilter']) {
				if ($param['output'] != 'alle') $title[] = $param['output'];
			}
		}
		foreach ($params['bogenartfilter']['options'] AS $param) {
			if ($param['value'] == rolle::$layer_params['bogenartfilter']) {
				if ($param['output'] != 'alle') $title[] = $param['output'];
			}
		} ?>
		<span class="fett px20"><?php
			echo $this->Stelle->Bezeichnung . ' ' . implode(', ', $title); ?>
		</span>
	</div>

	<div title="Einstellungen">
		<i class="fa fa-user header-button" aria-hidden="true" onclick="
		$('#user_options').toggle();
		$('#sperr_div').toggle()
	"></i>
		<div id="user_options" class="user-options">
			<div class="user-options-header">
				Angemeldet als: <?php echo $this->user->login_name; ?>
			</div>
			<div class="user-options-section-header">
				<i class="fa fa-tasks options-button"></i>in Stelle:
			</div><?php
			$this->user->Stellen = $this->user->getStellen(0);
			foreach (array_keys($this->user->Stellen['ID']) AS $id) { ?>
				<div
					class="user-option"
					style="margin-left: 0px" <?
					if ($this->user->Stellen['ID'][$id] != $this->user->stelle_id) { ?>
						onclick="window.location.href='index.php?Stelle_ID=<? echo $this->user->Stellen['ID'][$id]; ?>'" <?
					} ?>
				><? echo $this->user->Stellen['Bezeichnung'][$id];
				if ($this->user->Stellen['ID'][$id] == $this->user->stelle_id) {
					?> <i class="fa fa-check" aria-hidden="true" style="color: #9b2434; margin: 0px 0px 0px 7px"></i><?
				} ?>
				</div><?
			} ?>
			<div class="options-devider"></div>
			<div
				class="user-option"
				onclick="window.location.href='index.php?go=Stelle_waehlen&show_layer_parameter=1&hide_stellenwahl=1'"
			><i class="fa fa-ellipsis-v options-button"></i>Einstellungen</div>
		<div class="options-devider"></div>
		<div
			class="user-option"
			onclick="window.location.href='index.php?go=logout'"
		><i class="fa fa-sign-out options-button"></i>Logout</div>
		</div>
	</div>

	<div title="Kartenausschnitt Drucken">
		<a href="index.php?go=Druckausschnittswahl"><i class="fa fa-print header-button" style="font-size: 160%" aria-hidden="true"></i></a>
	</div>

	<div title="Karte Anzeigen">
		<a href="index.php?go=neu%20Laden"><i class="fa fa-map header-button" aria-hidden="true"></i></a>
	</div>

	<div title="Ortssuche">
		<i class="fa fa-search header-button" aria-hidden="true" style="font-size: 150%;" onclick="$('#search_div, .fa-search, .fa-times').toggle(); $('#geo_name_search_result_div').show()"></i>
		<i class="fa fa-times header-button" aria-hidden="true" style="font-size: 150%; display: none;" onclick="$('#search_div, .fa-search, .fa-times').toggle(); $('#geo_name_search_result_div').hide()"></i>
	</div>

	<div style="padding: 4px; float: right; width: 30%;"><?
		include(SNIPPETS . 'geo_name_search.php'); ?>
	</div>
</div>

<div id="sperr_div" class="sperr-div" onclick="
	$('#user_options').toggle();
	$('#sperr_div').toggle()
">
</div>