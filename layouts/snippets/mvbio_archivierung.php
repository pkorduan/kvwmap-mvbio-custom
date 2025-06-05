<?php
/**
 * Use cases:
 * create_kampagne
 * show_kartiergebiete
 * 
 * ToDo:
 * - Datei mvbio_erfassungsstaende_ableiten.php prüfen und ggf. löschen
 * actions
 * - assign_kartiergebiet2archivkartiergebiet
 * - create_kampagne
 * - create_archivkartiergebiet
 * - undo_archivkartiergebiet
 * - show_kartiergebiete
 * 
*/
	define('KAMPAGNE_TEMPLATE_LAYER_ID', 205);
	define('KARTIERGEBIETE_TEMPLATE_LAYER_ID', 131);
	include_once(CLASSPATH . 'PgObject.php');

	switch ($this->formvars['action']) {
		case 'assign_kartiergebiet2archivkartiergebiet' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, array(1, 8, 47))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Sie haben keine Berechtigung, um Zuordnungen von Kartiergebieten im Archiv hinzuzufügen!'
				));
				exit;
			}

			$this->sanitize(array(
				'kartiergebiet_id' => 'int',
				'archivkartiergebiet_id' => 'int'
			));

			if (!(array_key_exists('kartiergebiet_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['kartiergebiet_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet-ID ist leer!'
				));
				exit;
			}

			$obj = new PgObject($this, 'mvbio', 'kartiergebiete');
			$kg = $obj->find_by('id', $this->formvars['kartiergebiet_id']);
			if (!$kg->get_id()) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet mit ID ' . $this->formvars['kartiergebiet_id'] . ' nicht gefunden!'
				));
				exit;
			}

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkartiergebiet nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['archivkartiergebiet_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkartiergebiet-ID ist leer!'
				));
				exit;
			}

			$obj = new PgObject($this, 'archiv', 'kartiergebiete');
			$kg = $obj->find_by('id', $this->formvars['archivkartiergebiet_id']);
			if (!$kg->get_id()) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkartiergebiet mit ID ' . $this->formvars['archivkartiergebiet_id'] . ' nicht gefunden!'
				));
				exit;
			}

			$result = assign_kartiergebiet2archivkartiergebiet($this, $this->formvars['kartiergebiet_id'], $this->formvars['archivkartiergebiet_id']);
			echo json_encode($result);
		} break;
		// Soll nicht mehr genutzt werden. Wird nicht mehr weiterentwickelt. Kampagnen und dazugehörige Layer für das Archiv werden manuell erstellt.
		case 'create_kampagne': {
			// global $admin_stellen;

			// // Frage Kampagne aus mvbio ab
			// $kk = get_kartierkampagne($this, $this->formvars['kampagne_id']);

			// // Für jede Kartierebene der Kampagne
			// //  eine Layergruppe mit Namen der Kampagne anlegen
			// //  und in der Layergruppe jeweils einen Layer für die Kartiergebiete und -objekte
			// //  und für Ebene LRT jeweils ein Layer für vorkommende Layergruppen anlegen.
			// foreach ($kk->kartierebenen as $ke) {
			// 	$layer_group = create_layer_group($this, $kk, $ke);
			// 	create_layers($this, $layer_group, $kk, $ke);
			// }

			// // Datensatz in der Tabelle kampagnen anlegen
			// $pg_obj = new PgObject($this, 'archiv', 'kampagnen');
			// $result = $pg_obj->create(array(
			// 	'id' => $kk->get('id'),
			// 	'kartierungsart' => strtolower($kk->get('art')),
			// 	'abk' => $kk->get('abk'),
			// 	'bezeichnung' => $kk->get('bezeichnung'),
			// 	'datenschichten' => $kk->get('datenschichten'),
			// 	'erfassungszeitraum' => $kk->get('erfassungszeitraum'),
			// 	'datenschichten' => $kk->get('datenschichten'),
			// 	'geom' => $kk->get('geom'),
			// 	'layer_kuerzel' => $archiv_layer->get('alias')
			// ))[0];

			// $this->formvars = array(
			// 	'selected_layer_id' => KAMPAGNE_TEMPLATE_LAYER_ID,
			// 	'value_id' => $kk->get('id'),
			// 	'operator_id' => '=',
			// 	'only_main' => 1
			// );
			// $this->GenerischeSuche_Suchen();
		} break;
		case 'create_archivkartiergebiet' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, array(1, 8, 47))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Sie haben keine Berechtigung, um Kartiergebiete im Archiv zu erstellen!'
				));
				exit;
			}

			$this->sanitize(array(
				'kartiergebiet_id' => 'int',
				'archivkampagne_id' => 'int',
				'akg_bezeichnung' => 'string'
			));

			if (!(array_key_exists('kartiergebiet_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['kartiergebiet_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet-ID ist leer!'
				));
				exit;
			}

			$obj = new PgObject($this, 'mvbio', 'kartiergebiete');
			$kg = $obj->find_by('id', $this->formvars['kartiergebiet_id']);
			if (!$kg->get_id()) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet mit ID ' . $this->formvars['kartiergebiet_id'] . ' nicht gefunden!'
				));
				exit;
			}

			if (!(array_key_exists('archivkampagne_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkampagne nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['archivkampagne_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkampagne-ID ist leer!'
				));
				exit;
			}
			$obj = new PgObject($this, 'archiv', 'kampagnen');
			$ak = $obj->find_by('id', $this->formvars['archivkampagne_id']);
			if (!$ak->get_id()) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkampagne mit ID ' . $this->formvars['archivkampagne_id'] . ' nicht gefunden!'
				));
				exit;
			}

			$akg = new PgObject($this, 'archiv', 'archivkartiergebiete');
			$akg->set('bezeichnung', $this->formvars['akg_bezeichnung']);
			$akg->set('losnummer', $this->formvars['akg_losnummer']);
			$akg->set('bemerkungen', $this->formvars['akg_bemerkungen']);

			$result = create_archivkartiergebiet($this, $kg, $ak, $akg);

			echo json_encode($result);
		} break;

		case 'undo_archivkartiergebiet' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, array(1, 8, 47))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Sie haben keine Berechtigung, um Zuordnungen von Kartiergebieten im Archiv zu entfernen!'
				));
				exit;
			}

			$this->sanitize(array(
				'kartiergebiet_id' => 'int',
				'archivkartiergebiet_id' => 'int'
			));

			if (!(array_key_exists('kartiergebiet_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['kartiergebiet_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Kartiergebiet-ID ist leer!'
				));
				exit;
			}

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkartiergebiet nicht angegeben!'
				));
				exit;
			}
			if ($this->formvars['archivkartiergebiet_id'] == '') {
				echo json_encode(array(
					'success' => false,
					'msg' => 'Archivkartiergebiet-ID ist leer!'
				));
				exit;
			}

			$result = undo_archivkartiergebiet($this, $this->formvars['kartiergebiet_id'], $this->formvars['archivkartiergebiet_id']);

			echo json_encode($result);
		} break;

		// Listet die Kartiergebiete einer Kampagne auf um sie Kampagnen oder Kartiergebieten im Archiv zuordnen zu können.
		case 'show_kartiergebiete': {
			$this->sanitize(array(
				'kampagne_id' => 'int'
			));
			$kk = get_kartierkampagne($this, $this->formvars['kampagne_id']);
			include(WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/snippets/mvbio_archivkartiergebiete.php');
		} break;
		default: {
			echo json_encode(array(
				'success' => false,
				'msg' => 'Unbekannte Aktion: ' . $this->formvars['action']
			));
		}
	}

	function assign_kartiergebiet2archivkartiergebiet($gui, $kg_id, $akg_id) {
		// Prüfe, ob die Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet bereits existiert
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		$obj->identifiers = array(
			array('column' => 'kartiergebiet_id', 'type' => 'integer'),
			array('column' => 'archivkartiergebiet_id', 'type' => 'integer')
		);
		$kg2akg = $obj->find_by_ids(array('kartiergebiet_id' => $kg_id, 'archivkartiergebiet_id' => $akg_id));
		if ($kg2akg->data) {
			return array(
				'success' => false,
				'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id . ' existiert bereits.'
			);
		}

		// Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet
		$data = array(
			'kartiergebiet_id' => $kg_id,
			'archivkartiergebiet_id' => $akg_id
		);

		$result = $obj->create($data);
		if (!$result['success']) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id
			);
		}

		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$kg = $obj->find_by('id', $kg_id);

		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');
		$sql = "
			UPDATE
				archiv.kartiergebiete akg
			SET
				geom = ST_Union(akg.geom, kg.geom)
			FROM
				mvbio.kartiergebiete kg JOIN 
				mvbio.kartiergebiete2archivkartiergebiete kg2akg ON kg.id = kg2akg.kartiergebiet_id
			WHERE
				kg2akg.archivkartiergebiet_id = akg.id AND
				kg.id = " . $kg_id . " AND
				akg.id = " . $akg_id . "
		";
		$obj->execSQL($sql);
		$akg = $obj->find_by('id', $akg_id);

		return array(
			'success' => true,
			'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet erfolgreich angelegt.',
			'data' => array(
				'id' => $akg->get('id'),
				'bezeichnung' => $akg->get('bezeichnung')
			)
		);
	}

	/**
	 * Liefert den template Layer für die LRT-Gruppe im Archiv.
	 * //ToDo richtige ID's raussuchen
	 * @param $gui
	 * @param $kampagne_id
	 * @return Layer
	 */
	function get_lrt_template_layer($gui, $lrt_gruppe_id) {
		switch($lrt_gruppe_id) {
			case 1: $layer = Layer::find_by_id($gui, 251); break; // Küste
			case 2: $layer = Layer::find_by_id($gui, 253); break; // Offenland
			case 3: $layer = Layer::find_by_id($gui, 250); break; // Fliessgewässer
			case 4: $layer = Layer::find_by_id($gui, 254); break; // Stillgewässer
			case 5: $layer = Layer::find_by_id($gui, 252); break; // Moore
			case 5: $layer = Layer::find_by_id($gui, 252); break; // Moore
			case 6: $layer = Layer::find_by_id($gui, 444); break; // Wald
			case 7: $layer = Layer::find_by_id($gui, 383); break; // Marine
			default: throw new Exception('Unbekannte LRT-Gruppe: ' . $lrt_gruppe_id);
		}
		return $layer;
	}

	/**
	 * Lädt die Kartierkampagne mit den zugehörigen Kartierebenen, Kartiergebieten und Archivkampagnen.
	 * @param $gui
	 * @param $kampagne_id
	 * @return Kampagne
	 */
	function get_kartierkampagne($gui, $kampagne_id) {
		$obj = new PgObject($gui, 'mvbio', 'kampagnen');
		$kk = $obj->find_by('id', $kampagne_id);
		// Kartierebenen der Kampagne
		$obj = new PgObject($gui, 'mvbio', 'kartierebenen');
		$kk->kartierebenen = $obj->find_by_sql(array(
			'select' => 'ke.id, ke.abk, ke.bezeichnung, ke.bogenarten',
			'from' => 'mvbio.kartierebenen ke JOIN mvbio.kartierebenen2kampagne k2k ON ke.id = k2k.kartierebene_id',
			'where' => 'k2k.kampagne_id = ' . $kk->get_id()
		));

		// Der Kampagne zugeordnete Archiv-Kampagnen mit dessen Archivkartiergebieten
		$kk->archivkampagnen = get_archivkampagnen($gui, $kk->get_id());

		// Kartiergebiete der Kampagne mit dessen zugehörigen Archiv-Kartiergebieten (assigned_archivkartiergebiet_ids)
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$kk->kartiergebiete = get_kartiergebiete($gui, $kk);
		return $kk;
	}

	/**
	 * Lädt die Kartiergebiete der Kampagne und die zugehörigen Archiv-Kartiergebiete.
	 * @param $gui
	 * @param $kampagne_id
	 * @return array of Kartiergebiet objects mit assigned_archivkartiergebiet_ids Associative Array with $archivkampagne_id als key and the assigned archivkartiergebiet_id as value.
	 */
	function get_kartiergebiete($gui, $kk) {
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$kartiergebiete = $obj->find_where('kampagne_id = ' . $kk->get_id());
		// Frage pro Kartierkampagne das zugehörige Archiv-Kartiergebiet ab
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		foreach ($kartiergebiete AS $kg) {
			foreach ($kk->archivkampagnen AS $akk) {
				$assigned_archivkartiergebiete = $obj->find_by_sql(array(
					'select' => 'akg.*',
					'from' => 'mvbio.kartiergebiete2archivkartiergebiete kg2akg JOIN archiv.kartiergebiete akg ON kg2akg.archivkartiergebiet_id = akg.id',
					'where' => "kg2akg.kartiergebiet_id = " . $kg->get_id() . " AND akg.kampagne_id = " . $akk->get_id()
				));
				$kg->assigned_archivkartiergebiet_ids[$akk->get_id()] = (count($assigned_archivkartiergebiete) == 0 ? null : $assigned_archivkartiergebiete[0]);
			}
		}
		return $kartiergebiete;
	}

	function get_archivkampagnen($gui, $kk_id) {
		$obj = new PgObject($gui, 'archiv', 'kampagnen');
		$archivkampagnen = $obj->find_where('mvbio_kampagne_id = ' . $kk_id);
		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');
		foreach ($archivkampagnen as $ak) {
			$ak->archivkartiergebiete = $obj->find_where("kampagne_id = " . $ak->get_id());
		}
		return $archivkampagnen;
	}

	function create_archivkartiergebiet($gui, $kg, $ak, $akg) {
		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');
		$data = array(
			'kampagne_id'      => $ak->get_id(),
			'losnummer'        => $akg->get('losnummer')    ?: $kg->get('losnummer'),
			'bezeichnung'      => $akg->get('bezeichnung')  ?: $kg->get('bezeichnung'),
			'bemerkungen'      => $akg->get('bemerkungen')  ?: $kg->get('bemerkungen'),
			'geom'             => $kg->get('geom')
		);
		if ($kg->get('bezeichnung'))   $obj->set('bezeichnung_orig', $kg->get('bezeichnung'));
		if ($kg->get('auftragnehmer')) $obj->set('auftragnehmer',    $kg->get('auftragnehmer'));
		if ($kg->get('auftraggeber'))  $obj->set('auftraggeber',     $kg->get('auftraggeber'));
		if ($kg->get('kart_start'))    $obj->set('kart_start',       $kg->get('kart_start'));
		if ($kg->get('kart_end'))      $obj->set('kart_end',         $kg->get('kart_end'));

		$result = $obj->create($data);
		if (is_array($result) AND !$result['succcess']) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Anlegen des Kartiergebiets im Archiv mit den Werten: ' . print_r($data, true)
			);
		}
		$akg = $obj->find_by('id', $result);

		$data = array(
			'kartiergebiet_id' => $kg->get_id(),
			'archivkartiergebiet_id' => $akg->get_id()
		);
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		$obj->identifier = 'kartiergebiet_id';
		$obj->identifiers = array(
			array(
				'column' => 'kartiergebiet_id',
				'type' => 'integer'
			),
			array(
				'column' => 'archivkartiergebiet_id',
				'type' => 'integer'
			)
		);
		$result = $obj->create($data);
		if (!$result['success']) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: ' . print_r($data, true)
			);
		}

		return array(
			'success' => true,
			'data' => array('id' => $akg->get_id(), 'bezeichnung' => $akg->get('bezeichnung')),
			'msg' => 'Kartiergebiet Id: ' . $akg->get_id() . ' erfolgreich im Archiv angelegt.'
		);
	}

	function undo_archivkartiergebiet($gui, $kg_id, $akg_id) {
		// Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet entfernen
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		$obj->identifiers = array(
			array('column' => 'kartiergebiet_id', 'type' => 'integer'),
			array('column' => 'archivkartiergebiet_id', 'type' => 'integer')
		);
		$kg2akg = $obj->find_by_ids(array('kartiergebiet_id' => $kg_id, 'archivkartiergebiet_id' => $akg_id));
		if (!$kg2akg->data) {
			return array(
				'success' => false,
				'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id . ' nicht gefunden.'
			);
		}

		$sql = "
			UPDATE
				archiv.kartiergebiete akg
			SET
				geom = ST_Multi(ST_Difference(akg.geom, kg.geom))
			FROM
				mvbio.kartiergebiete kg JOIN 
				mvbio.kartiergebiete2archivkartiergebiete kg2akg ON kg.id = kg2akg.kartiergebiet_id
			WHERE
				kg2akg.archivkartiergebiet_id = akg.id AND
				kg.id = " . $kg_id . " AND
				akg.id = " . $akg_id . "
		";

		$query = $obj->execSQL($sql);
		if ($query === false) {
			return array(
				'success' => false,
				'msg' => 'Fehler bei der Abfrage: ' . pg_last_error($this->database->dbConn)
			);
		}

		if (!$kg2akg->delete()) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Entfernen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id
			);
		}

		return array(
			'success' => true,
			'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet erfolgreich entfernt.'
		);
	}

	function create_layer_group($gui, $kk, $ke) {
		include_once(CLASSPATH . 'LayerGroup.php');
		switch (true) {
			case $ke->get('abk') == 'GB': $obergruppe_id = 40; break;
			case $ke->get('abk') == 'LRT': $obergruppe_id = 41; break;
			case $ke->get('abk') == 'LRT-FFH': $obergruppe_id = 42; break;
			case $ke->get('abk') == 'WLRT': $obergruppe_id = 45; break;
			default: $obergruppe_id = 65; // Sonstiges
		}
		$lg = new LayerGroup($gui);
		$result = $lg->create(array(
			'Gruppenname' => $kk->get('abk'),
			'obergruppe' => $obergruppe_id,
			'order' => $lg->get_next_order($obergruppe_id)
		))[0];
		if ($result['success'] == false) {
			throw new ErrorException($result['msg']);
		}
		return LayerGroup::find_by_id($gui, $result['id']);
	}

	/**
	 * Creates the layers for the layer_group.
	 * Layer für Kartiergruppen und
	 * Layer für Biotopbögen oder bei Ebene LRT für die LRT-Gruppen
	 */
	function create_layers($gui, $layer_group, $kk, $ke) {
		$layers = array();
		$alle_archivkartiergebiete_layer = Layer::find_by_id($gui, KARTIERGEBIETE_TEMPLATE_LAYER_ID);

		// Layer für Kartiergebiete
		$layers[] = $alle_archivkartiergebiete_layer->copy(array(
			'duplicate_criterion' => 'kg.kampagne_id = ' . $kk->get_id(),
			'Name' => 'Archiv ' . $ke->get('art') . ' ' . $kk->get('abk') . ' Kartiergebiete',
			'alias' => $ke->get('art') . '_' . $kk->get('abk') . '_KK',
			'Gruppe' => $layer_group->get_id(),
			'ows_srs' => 'EPSG:25833',
			'wms_server_version' => '1.3.0',
			'wms_format' => 'image/png'
		));

		// Layer für Biotopbögen
		if ($ke->get('abk') == 'GB') {
			$layers[] = $alle_archivkartiergebiete_layer->copy(array(
				'duplicate_criterion' => 'b.kampagne_id = ' . $kk->get_id(),
				'Name' => 'Archiv ' . $ke->get('art') . ' ' . $kk->get('abk') . ' Biotopbögen',
				'alias' => $ke->get('art') . '_' . $kk->get('abk') . '_bk',
				'Gruppe' => $layer_group->get_id(),
				'ows_srs' => 'EPSG:25833',
				'wms_server_version' => '1.3.0',
				'wms_format' => 'image/png'
			));
		}
		
		// Layer für LRT-Gruppen
		if ($ke->get('abk') == 'LRT') {
			$pg_obj = new PgObject($gui, 'mvbio', 'lrt_gruppen');
			$lrt_gruppen = $pg_obj->find_where("true");
			foreach ($lrt_gruppen as $lrt_gruppe) {
				// Der erste ist der required Layer und die andere beziehen sich auf den
				$template_layer = get_lrt_template_layer($gui, $lrt_gruppe->get('id')); // Noch implementieren
				$layers[] = $template_layer->copy(array(
					'duplicate_criterion' => 'kg.kampagne_id = ' . $kk->get_id() . ' AND kg.lrt_gruppe_id = ' . $lrt_gruppe->get('id'),
					'Name' => 'Archiv ' . ($lrt_gruppe->get('abk') == 'W' ? 'Wald' : 'LRT') . '-LRT ' . $kk->get('abk') . ' ' . $lrt_gruppe->get('bezeichnung'),
					'alias' => 'lrt_' . $kk->get('abk'),
					'Gruppe' => $layer_gruppe->get_id(),
					'ows_srs' => 'EPSG:25833',
					'wms_server_version' => '1.3.0',
					'wms_format' => 'image/png'
				));
			}
		}

		$this->addLayersToStellen(
			array_map(function($layer) { return $layer->get_id(); }, $layers),
			array($admin_stellen[0]),
			$filter = '',
			$assign_default_values = false,
			$privileg = 'editable_only_in_this_stelle'
		);
	}
?>