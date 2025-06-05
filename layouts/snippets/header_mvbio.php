<?php
include_once(CLASSPATH . 'PgObject.php');
$kampagne = new PgObject($this, 'mvbio', 'kampagnen');
if ($this->go == 'Stelle_waehlen' OR in_array($this->user->id, array(1, 47))) {
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
				if (kampagne_id == 0) {
					$('#layer_parameter_kartiergebietfilter option').show();
					$('#layer_parameter_kartiergebietfilter').val("0");
					$('#layer_parameter_kartiergebietfilter_tr').hide();
				}
				else {
					var num_option_char = 4;
					$('#layer_parameter_kartiergebietfilter_tr').show();
					$('#layer_parameter_kartiergebietfilter option').each(
						function(i, option) {
							if (option.value > 0 && $.inArray(kampagne_id + '-' + option.value, kartiergebiete) == -1) {
								if (option.selected) {
									$('#layer_parameter_kartiergebietfilter').val("0");
								}
								$(option).hide();
							}
							else {
								if (num_option_char < $(option).html().length) {
									num_option_char = $(option).html().length;
								}
								$(option).show();
							}
						}
					);
					$('#layer_parameter_kartiergebietfilter').css('width', (num_option_char + 4) + 'ch');
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
					var num_option_char = 4;
					$('#layer_parameter_kartierebenenfilter_tr').show();
					$('#layer_parameter_kartierebenenfilter option').each(function(i, option) {
						if (option.value > 0 && $.inArray(kampagne_id + '-' + option.value, kartierebenen) == -1) {
							if (option.selected) {
								$('#layer_parameter_kartierebenenfilter').val("0").trigger("change");
							}
							$(option).hide();
						}
						else {
							if (num_option_char < $(option).html().length) {
								num_option_char = $(option).html().length;
							}
							$(option).show();
						}
					});
					$('#layer_parameter_kartierebenenfilter').css('width', (num_option_char + 4) + 'ch');
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
					var num_option_char = 4;
					$('#layer_parameter_bogenartfilter_tr').show();
					$('#layer_parameter_bogenartfilter option').each(function(i, option) {
						if (option.value > 0 && $.inArray(kartierebene_id + '-' + option.value, bogenarten) == -1) {
							if (option.selected) {
								$('#layer_parameter_bogenartfilter').val("0");
							}
							$(option).hide();
						}
						else {
							if (num_option_char < $(option).html().length) {
								num_option_char = $(option).html().length;
							}
							$(option).show();
						}
					});
					$('#layer_parameter_bogenartfilter').css('width', (num_option_char + 4) + 'ch');
				}
			};

			filterTest = function(param_value) {
				if (param_value == 'Test') {
					// Set value of Testgebiet
					$('#layer_parameter_kartiergebietfilter').val($('#layer_parameter_kartiergebietfilter option:contains(Testgebiet)').val());
				}
				else {
					$('#layer_parameter_kartiergebietfilter').val("0");
				}
			};

			onLayerParameterChanged = function(parameter) {
				switch (parameter.id) {
					case 'layer_parameter_kampagne_id' : {
						filterKartiergebiete(parameter.value);
						filterKartierebenen(parameter.value);
					}
					break;
					case 'layer_parameter_kartierebenenfilter':
						filterBogenarten(parameter.value);
					break;
					case 'layer_parameter_umgebung' :
					  filterTest(parameter.value);
					break;
				}
				var updateLayerParameterButton = $('#update_layer_parameter_button');
				updateLayerParameterButton.fadeIn();
			};

			var num_option_char = 4;
			$('#layer_parameter_kampagne_id option').each(function(i, option) {
				if (num_option_char < $(option).html().length) {
					num_option_char = $(option).html().length;
				}
			});
			$('#layer_parameter_kampagne_id').css('width', (num_option_char + 4) + 'ch');

			filterKartiergebiete($('#layer_parameter_kampagne_id').val());
			filterKartierebenen($('#layer_parameter_kampagne_id').val());
			filterBogenarten($('#layer_parameter_kartierebenenfilter').val());
			$('#save_check_button').removeClass('red');
			$('#save_check_button').addClass('green');
		};
	</script><?
}
else { ?>
	<script>
		var onload_functionsCore = onload_functions;
		onload_functions = function() {
			onload_functionsCore.apply(this, arguments);
		}
	</script><?
} ?>

<div style="
	width: 100%;
	height: 100%;
	background: linear-gradient(<? echo BG_GLEATTRIBUTE; ?> 0%, <? echo BG_DEFAULT ?> 100%);
"><?
	$title_width = (in_array($this->user->id, array(1, 47)) ? '203px' : '54%'); ?>
	<div style="padding: 6px; float: left; width: <? echo $title_width; ?>; text-align: left;"><?
		$params = $this->user->rolle->get_layer_params($this->Stelle->selectable_layer_params, $this->pgdatabase);
		$title = array();
		if ($params['kampagne_id']) {
			foreach ($params['kampagne_id']['options'] AS $param) {
				if ($param['value'] == rolle::$layer_params['kampagne_id']) {
					if ($param['output'] != 'alle') {
						$title[] = $param['output'];
					}
				}
			}
		}
		if ($params['kartiergebietfilter']) {
			foreach ($params['kartiergebietfilter']['options'] AS $param) {
				if ($param['value'] == rolle::$layer_params['kartiergebietfilter']) {
					if ($param['output'] != 'alle') {
						$title[] = $param['output'];
					}
				}
			}
		}
		if ($params['kartierebenenfilter']) {
			foreach ($params['kartierebenenfilter']['options'] AS $param) {
				if ($param['value'] == rolle::$layer_params['kartierebenenfilter']) {
					if ($param['output'] != 'alle') {
						$title[] = $param['output'];
					}
				}
			}
		}
		if ($params['bogenartfilter']) {
			foreach ($params['bogenartfilter']['options'] AS $param) {
				if ($param['value'] == rolle::$layer_params['bogenartfilter']) {
					if ($param['output'] != 'alle') {
						$title[] = $param['output'];
					}
				}
			}
		} ?>
		<span class="fett px20"><?php
			echo $this->Stelle->Bezeichnung . (rolle::$layer_params['umgebung'] == 'Test' ? ' <span style="color: red">Testumgebung</span>' : '') . ' ' . implode(', ', $title); ?>
		</span>
	</div><?
	if (in_array($this->user->id, array(1, 47))) { ?>
		<div style="float: left; margin-top: 6px;"><?
			include(WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/snippets/params_mvbio.php'); ?>
		</div><?
	} ?>
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
						onclick="window.location.href='index.php?browserwidth=' + $('input[name=browserwidth]').val() + '&browserheight=' + $('input[name=browserheight]').val() + '&Stelle_ID=<? echo $this->user->Stellen['ID'][$id]; ?>&csrf_token=<? echo $_SESSION['csrf_token']; ?>'" <?
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
				onclick="window.location.href='index.php?go=Stelle_waehlen&show_layer_parameter=<? echo (in_array($this->user->id, array(1, 47)) ? '0' : '1');?>&hide_stellenwahl=1&csrf_token=<? echo $_SESSION['csrf_token']; ?>'"
			><i class="fa fa-ellipsis-v options-button"></i>Einstellungen</div>
		<div class="options-devider"></div>
		<div
			class="user-option"
			onclick="window.location.href='index.php?go=logout'"
		><i class="fa fa-sign-out options-button"></i>Logout</div>
		</div>
	</div>

	<div title="Benachrichtigungen" style="float: right; display: block;">
		<script>
			function delete_user2notification(notification_id) {
				let formData = new FormData();
				formData.append('go', 'delete_user2notification');
				formData.append('notification_id', notification_id);
				formData.append('csrf_token', '<? echo $_SESSION['csrf_token']; ?>')
				let response = fetch('index.php', {
				  method: 'POST',
				  body: formData
				})
				.then(response => response.text())
				.then(text => {
					try {
						const data = JSON.parse(text);
						if (data.success) {
							$('#notification_box_' + notification_id).remove();
							let num_notifications = $('#num_notification_div').html() - 1;
							if (num_notifications == 0) {
								$('#num_notification_div').hide();
							}
							else {
								$('#num_notification_div').html(num_notifications);
							}
						}
						else {
							message([{ 'type': 'error', 'msg' : 'Fehler beim Löschen Benachrichtigung für den Nutzer: ' + data.err_msg + ' ' + text}]);
						}
					} catch(err) {
						message([{ 'type': 'error', 'msg' : err.name + ': ' + err.message + ' in Zeile: ' + err.lineNumber + ' Response: ' + text}]);
					}
				});
			}
		</script><?
		include_once(CLASSPATH . 'Notification.php');
		$result = Notification::find_for_user($this); ?>
		<a href="#" onclick="if ($('#user_notifications').is(':visible') && $('.notification-box').filter(':visible').length > 0) { $('#user_notifications').hide('swing'); } else {
			<? if (count($result['notifications']) == 0) { echo 'message([{ type: \'notice\', msg: \'Keine neuen Benachrichtigungen vorhanden.\'}]);'; } ?> $('.notification-box').show(); $('#user_notifications').show('swing'); }">
			<i class="fa fa-bell" aria-hidden="true" style="
				font-size: 150%;
				padding: 5px 0px 4px 0;
			"></i><?
			if ($result['success'] AND count($result['notifications']) > 0) { ?>
				<div id="num_notification_div" style="
					margin: -27 0 0 14;
					width: 12;
					height: 12;
					border-radius: 8px;
					background-color: orange;
					font-size: 10px;
					font-weight: bold;
					font-family: arial;
					color: white;
					padding: 0px 2px 3px 2px;
					position: relative;
					text-align: center;"
				><?
					echo count($result['notifications']); ?>
				</div><?
			} ?>
		</a>
		<div id="user_notifications" style="display: none; position: absolute;right: 30px; z-index: 9999;padding: 5px;top: 30px;"><?
			if ($result['success']) {
				foreach($result['notifications'] AS $notification) { ?>
					<div id="notification_box_<? echo $notification['id']; ?>" class="notification-box">
						<div style="float: left"><a href="#" class="notification-hide-icon"><i class="fa fa-times" aria-hidden="true" style="font-size: 100%" onclick="if ($('#notification_delete_checkbox_<? echo $notification['id']; ?>').is(':checked')) { delete_user2notification(<? echo $notification['id']; ?>); } else { $(this).parent().parent().parent().hide('swing'); }"></i></a></div>
						<div style="float: left; margin-left: 5px; max-width: 200px"><?
							echo $notification['notification']; ?>
						</div>
						<div style="clear: both"></div>
						<div style="margin-top: 2px; width: 100%; text-align: center;"><input id="notification_delete_checkbox_<? echo $notification['id']; ?>" type="checkbox"/> nicht mehr anzeigen</div>
					</div><?
				}
			} ?>
		</div>
	</div>

	<div title="Kartenausschnitt Drucken">
		<a href="index.php?go=Druckausschnittswahl"><i class="fa fa-print header-button" style="font-size: 160%" aria-hidden="true"></i></a>
	</div>

	<div title="Karte Anzeigen">
		<a href="index.php?go=neu%20Laden"><i class="fa fa-map header-button" aria-hidden="true"></i></a>
	</div>

	<div title="Ortssuche">
		<i id="search_icon" class="fa fa-search header-button" aria-hidden="true" style="font-size: 150%;" onclick="$('#search_div, #search_icon, #close_icon').toggle(); $('#geo_name_search_result_div').show()"></i>
		<i id="close_icon" class="fa fa-times header-button" aria-hidden="true" style="font-size: 150%; display: none;" onclick="$('#search_div, #search_icon, #close_icon').toggle(); $('#geo_name_search_result_div').hide()"></i>
	</div>

	<div style="padding: 4px; float: right; width: 30%;"><?
		include(SNIPPETS . 'geo_name_search.php'); ?>
	</div>

	<div><? 
		if (array_key_exists('prev_login_name', $_SESSION)) {
			echo '<a href="index.php?go=als_voriger_Nutzer_anmelden" class="fett" style="white-space: nowrap;">zurück zum vorigen Nutzer wechseln</a>';
		} ?>
	</div>
</div>

<div id="sperr_div" class="sperr-div" onclick="
	$('#user_options').toggle();
	$('#sperr_div').toggle()
">
</div>