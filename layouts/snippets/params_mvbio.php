<?php
	$params = $this->user->rolle->get_layer_params($this->Stelle->selectable_layer_params, $this->pgdatabase);
	if (value_of($params, 'error_message') != '') {
		$this->add_message('error', $params['error_message']);
	}
	else { ?>
		<script language="javascript" type="text/javascript">
			function toggleLayerParamsBar() {
				var openLayerParamBarIcon = $('#openLayerParamBarIcon'),
						layerParamsBar = $('#layerParamsBar');
						if (layerParamsBar.is(':visible'))
							layerParamsBar.fadeOut()
						else
							layerParamsBar.fadeIn()
			}

			function updateLayerParams() {
				var data = 'go=setLayerParams<?php
				foreach($params AS $param) {
					echo '&layer_parameter_' . $param['key'] . "=' + document.getElementById('layer_parameter_" . $param['key'] . "').value + '";
				} ?>';
				ahah('index.php', data, [''], ['execute_function']);
				document.GUI.legendtouched.value = 1;
				neuLaden();
			}

			function onLayerParameterChanged(parameter) {
				var updateLayerParameterButton = $('#update_layer_parameter_button');
				updateLayerParameterButton.fadeIn();
			}

			function onLayerParamsUpdated(status) {
				var updateLayerParameterButton = $('#update_layer_parameter_button'),
						layerParamsBar = $("#layerParamsBar");
				updateLayerParameterButton.fadeOut();
				layerParamsBar.fadeOut();
			}
		</script><?
		if (!empty($params)) {
			include_once(CLASSPATH . 'FormObject.php');
			foreach($params AS $param) { ?>
				<div id="layer_parameter_<? echo $param['key']; ?>_tr" style="float: left;"><?
					echo $param['alias'] . ': ';
					echo FormObject::createSelectField(
						'layer_parameter_' . $param['key'],		# name
						$param['options'],										# options
						rolle::$layer_params[$param['key']],	# value
						1,																		# size
						'margin-right: 5px;',									# style
						'onLayerParameterChanged(this);',			# onchange
						'layer_parameter_' . $param['key'],		# id
						'',																		# multiple
						'',																		# class
						''																		# first option
					); ?>
				</div><?
			} ?>
			<input type="button" id="update_layer_parameter_button" value="Speichern" style="display: none" onclick="updateLayerParams();"><?
		}
	}
?>