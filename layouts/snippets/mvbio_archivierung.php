<?php
/**
 * Use cases:
 * create_kampagne
 * show_kartiergebiete
 * 
 * ToDo:
 * - Datei mvbio_erfassungsstaende_ableiten.php prüfen und ggf. löschen
 * actions
 * - archiv_boegen
 * - assign_kartiergebiet2archivkartiergebiet
 * - create_kampagne
 * - create_archivkartiergebiet
 * - show_kartiergebiete
 * - show_kartierobjekte4archiv
 * - undo_archiv_boegen
 * - undo_archivkartiergebiet
 *
*/
	define('KAMPAGNE_TEMPLATE_LAYER_ID', 205);
	define('KARTIERGEBIETE_TEMPLATE_LAYER_ID', 131);
	include_once(CLASSPATH . 'PgObject.php');
	$allowed_users = array(1, 8, 47, 237);

	switch ($this->formvars['action']) {
		case 'archiv_boegen_einzeln_nach_bogenart' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
				throw new Exception('Sie haben keine Berechtigung, um Bögen zu archivieren!');
			}
			$this->sanitize(array(
				'archivkartiergebiet_id' => 'int',
				'set_active' => 'boolean',
				'pruefer' => 'text'
			));

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				throw new Exception('Archivkartiergebiet nicht angegeben!');
			}

			if ($this->formvars['archivkartiergebiet_id'] == '') {
				throw new Exception('Archivkartiergebiet-ID ist leer!');
			}

			$obj = new PgObject($this, 'archiv', 'kartiergebiete');
			$akg = $obj->find_by('id', $this->formvars['archivkartiergebiet_id']);
			if ($akg->data === false) {
				throw new Exception('Archivkartiergebiet mit id: ' . $this->formvars['archivkartiergebiet_id'] . ' nicht gefunden!');
			}

			if (!(array_key_exists('bogenart_id', $this->formvars))) {
				throw new Exception('Bogenart-ID nicht angegeben!');
			}

			if ($this->formvars['bogenart_id'] == '') {
				throw new Exception('Bogenart-ID ist leer!');
			}

			$obj = new PgObject($this, 'mvbio', 'bogenarten');
			$ba = $obj->find_by('id', $this->formvars['bogenart_id']);
			if ($ba->data === false) {
				throw new Exception('Bogenart mit id: ' . $this->formvars['bogenart_id'] . ' nicht gefunden!');
			}

			$ab = new PgObject($this, 'mvbio', $ba->get('archivtabelle') . '4archiv');
			if ($ba->get_id() == 3) {
				// Verlustbögen
				$obj = new PgObject($this, 'archiv', 'kartierebenen2kampagne');
				$ke = $obj->find_by('kampagne_id', $akg->get('kampagne_id'));
				if ($ke->data === false) {
					throw new Exception('Kartierebene von Kampagne id: ' . $akg->get('kampagne_id') . ' nicht gefunden!');
				}
				$akg->set('kartierebene_id', $ke->get('kartierebene_id'));
				$boegen = $ab->find_where("kartiergebiet_id = " . $this->formvars['archivkartiergebiet_id'] . " AND kartierebene_id = " . $ke->get('kartierebene_id'));
			}
			else{
				$boegen = $ab->find_where("kartiergebiet_id = " . $this->formvars['archivkartiergebiet_id']);
			};

			if (count($boegen) == 0) {
				throw new Exception('Keine zugeordneten Bögen zum Archivkartiergebiet Id: ' . $this->formvars['archivkartiergebiet_id'] . ' gefunden!');
			}

			if (!(array_key_exists('pruefer', $this->formvars))) {
				throw new Exception('Prüfer nicht angegeben!');
			}

			if ($this->formvars['pruefer'] == '') {
				throw new Exception('Prüfer ist leer!');
			}

			if (!(array_key_exists('set_active', $this->formvars))) {
				throw new Exception('Der Parameter set_active ist nicht angegeben!');
			}

			$archiv_function = 'archiv_' . $ba->get('archivtabelle');
			$result = $archiv_function($this, $akg, $this->formvars['pruefer'], $this->formvars['set_active']);

			echo json_encode($result);
		} break;

		case 'archiv_boegen' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
				throw new Exception('Sie haben keine Berechtigung, um Bögen zu archivieren!');
			}
			$this->sanitize(array(
				'archivkartiergebiet_id' => 'int',
				'set_active' => 'boolean',
				'pruefer' => 'text'
			));

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				throw new Exception('Archivkartiergebiet nicht angegeben!');
			}

			if ($this->formvars['archivkartiergebiet_id'] == '') {
				throw new Exception('Archivkartiergebiet-ID ist leer!');
			}

			$obj = new PgObject($this, 'archiv', 'kartiergebiete');
			$akg = $obj->find_by('id', $this->formvars['archivkartiergebiet_id']);
			if ($akg->data === false) {
				throw new Exception('Archivkartiergebiet mit id: ' . $this->formvars['archivkartiergebiet_id'] . ' nicht gefunden!');
			}

			if (!(array_key_exists('pruefer', $this->formvars))) {
				throw new Exception('Prüfer nicht angegeben!');
			}

			if ($this->formvars['pruefer'] == '') {
				throw new Exception('Prüfer ist leer!');
			}

			if (!(array_key_exists('set_active', $this->formvars))) {
				throw new Exception('Der Parameter set_active ist nicht angegeben!');
			}

			// Query bogenarten die archiviert werden müssen und gehe diese durch.
			$bogenarten = get_bogenarten($this, $akg);
			$num_archived_boegen = 0;
			$success = true;
			$msg = '';
			foreach ($bogenarten AS $ba) {
				$ab = new PgObject($this, 'mvbio', $ba->get('archivtabelle') . '4archiv');
				if ($ba->get_id() == 3) {
					// Verlustbögen
					$obj = new PgObject($this, 'archiv', 'kartierebenen2kampagne');
					$ke = $obj->find_by('kampagne_id', $akg->get('kampagne_id'));
					if ($ke->data === false) {
						throw new Exception('Kartierebene von Kampagne id: ' . $akg->get('kampagne_id') . ' nicht gefunden!');
					}
					$akg->set('kartierebene_id', $ke->get('kartierebene_id'));
					$boegen = $ab->find_where("kartiergebiet_id = " . $this->formvars['archivkartiergebiet_id'] . " AND kartierebene_id = " . $ke->get('kartierebene_id'));
				}
				else {
					$boegen = $ab->find_where("kartiergebiet_id = " . $this->formvars['archivkartiergebiet_id']);
				};

				if (count($boegen) > 0) {
					$archiv_function = 'archiv_' . $ba->get('archivtabelle');
					$result = $archiv_function($this, $akg, $this->formvars['pruefer'], $this->formvars['set_active']);
					if (!$result['success']) {
						$msg .= $result['msg'];
						$success = false;
					}
					else {
						$msg .= $result['msg'] . '<br>';
						$num_archived_boegen += $result['num_archived_boegen'];
					}
				}
			}

			if ($num_archived_boegen == 0) {
				throw new Exception('Keine zugeordneten Bögen zum Archivkartiergebiet Id: ' . $this->formvars['archivkartiergebiet_id'] . ' gefunden!');
			}

			echo json_encode(array(
				'success' => $success,
				'msg' => $msg,
				'num_archived_boegen' => $num_archived_boegen
			));
		} break;

		case 'assign_kartiergebiet2archivkartiergebiet' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
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
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
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
			$kg->show = true;
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

			$akg = new PgObject($this, 'archiv', 'kartiergebiete');
			$akg->set('bezeichnung', $this->formvars['akg_bezeichnung']);
			$akg->set('losnummer', $this->formvars['akg_losnummer']);
			$akg->set('bemerkungen', $this->formvars['akg_bemerkungen']);

			$result = create_archivkartiergebiet($this, $kg, $ak, $akg);

			echo json_encode($result);
		} break;

		// Listet die Kartiergebiete einer Kampagne auf um sie Kampagnen oder Kartiergebieten im Archiv zuordnen zu können.
		case 'show_kartiergebiete': {
			$this->sanitize(array(
				'kampagne_id' => 'int'
			));
			if (!(array_key_exists('kampagne_id', $this->formvars))) {
				throw new Exception('Kampagne-ID nicht angegeben!');
			}

			$kk = get_kartierkampagne($this, $this->formvars['kampagne_id']);
			if (!$kk->get_id()) {
				throw new Exception('Kampagne mit ID ' . $this->formvars['kampagne_id'] . ' nicht gefunden!');
			}
			include(WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/snippets/mvbio_archivkartiergebiete.php');
		} break;

		case 'show_kartierobjekte4archiv': {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
				echo 'Sie haben keine Berechtigung, um die zur Archivkampagne zugeordneten Kartierobjekte anzeigen zu lassen!';
			}

			$this->sanitize(array(
				'archivkartiergebiet_id' => 'int'
			));

			// archivkartiergebiet_id muss angegeben sein.
			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				throw new Exception('Archivkartiergebiet nicht angegeben!');
			}
			if ($this->formvars['archivkartiergebiet_id'] == '') {
				throw new Exception('Archivkartiergebiet-ID ist leer!');
			}

			$obj = new PgObject($this, 'mvbio', 'kartiergebiete2archivkartiergebiete');
			$obj->identifiers = array(
				array('column' => 'kartiergebiet_id', 'type' => 'integer'),
				array('column' => 'archivkartiergebiet_id', 'type' => 'integer')
			);

			// Frage das Archivkartiergebiet ab.
			$archivkartiergebiet = new PgObject($this, 'archiv', 'kartiergebiete');
			$archivkartiergebiet = $archivkartiergebiet->find_by('id', $this->formvars['archivkartiergebiet_id']);

			// Frage die zum Archivkartiergebiet zugeordneten Kartiergebiete ab.
			$kartiergebiete = $obj->find_where("archivkartiergebiet_id = " . $this->formvars['archivkartiergebiet_id']);
			if (count($kartiergebiete) == 0) {
				echo 'Keine zugeordneten Kartiergebiete zum Archivkartiergebiet Id: ' . $this->formvars['archivkartiergebiet_id'] . ' gefunden!';
				exit;
			}

			$obj = new PgObject($this, 'mvbio', 'kartiergebiete');
			$kampagne_id = $obj->find_by('id', $kartiergebiete[0]->get('kartiergebiet_id'))->get('kampagne_id');
			$bogenarten = get_bogenarten($this, $archivkartiergebiet);

			foreach ($bogenarten as $ba) {
				// mvbio.kurzboegen4archiv
				// mvbio.grundboegen4archiv
				// mvbio.gruenlandboegen4archiv
				// mvbio.verlustboegen4archiv
				$ba->assigned_boegen = get_assigned_boegen($this, $ba, $archivkartiergebiet->get_id());
				$ba->archived_boegen = get_archived_boegen($this, $ba, $archivkartiergebiet->get_id());
				$ba->kartiergebiete = $obj->find_by_sql(array(
					'select' => "
						kg.id,
						kg.losnummer,
						kg.bezeichnung,
						count(*) AS anzahl_boegen
					",
					'from' => "
						mvbio." . ($ba->get('id') == 3 ? "verlust" : "kartier") . "objekte AS ob JOIN
						mvbio.kartiergebiete2archivkartiergebiete AS kg2akg ON ob.kartiergebiet_id = kg2akg.kartiergebiet_id JOIN
						mvbio.kartiergebiete kg ON kg2akg.kartiergebiet_id = kg.id
					",
					'where' => "
						kg2akg.archivkartiergebiet_id = " . $archivkartiergebiet->get_id() .
						($ba->get('id') < 3 ? " AND ob.bogenart_id = " . $ba->get('id') : '') . "
					",
					'group' => "kg.id, kg.losnummer, kg.bezeichnung"
				));
				// Liste der assigned_boegen auf der Seite ausgeben.
				// In der Liste steht:
				// - Zielkampagne, Zielkartiergebiet, Kartierebene, Bogenart, Kartiergebiet-IDs, aus denen die Kartierobjekte stammen
				// - Anzahl der Objekte, Link zur Anzeige der Bögen. (Setzt voraus, dass es Layer gibt, die die Bögen anzeigen können.)
				// - Auswahlfeld oder Checkboxen zur Auswahl ob die Bögen auf aktuell gesetzt werden sollen oder erstmal noch nicht.
				// - Button zum Übernehmen der Bögen als Erfassungs-, bzw. Kurz-, Grund- etc. bögen.
				// Weitere ToDos: Wenn die Bögen übernommen wurden den Status dieser Kartierobjekte auf "archiviert" setzen.
				// Stefan fragen ob die Übernahme der Kartierobjekte nach und nach erfolgen soll oder alles über eine Seite, wo alle Bogenarten auftauchen. (Eine Kartierebene kann mehrere Bogenarten haben, die dann in der Liste auftauchen.)
				// Es könnte eine Auswahl der Bogenarten erscheinen bevor die zu übernehmenden Bögen angezeigt werden.
				// Wenn schon welche Archiviert worden sind, aber noch nicht gelöscht worden sind. Darf der Link zur Anzeige dieser Bogenart nicht mehr erscheinen, weil die ja dann nicht noch mal
				// archivert werden dürfen. Die Abfrage oben fragt also nicht nur die Bogenarten ab, sondern auch gleich die Bögen dazu und wenn keine gefunden werden,
				// stehen die auch nicht mehr zur Wahl zur Archivierung.
				// - Also frage alle Bögen der Bogenarten ab und wenn keine gefunden wurden, schreiben hin, dass nichts mehr zu archivieren ist.
				// - Sonst pro Bogenart jeweils die Angaben wie oben beschrieben.
				// - Wir brauchen pro Bogenart eigentlich nur die Links, Statistik und Buttons. Die Liste nicht. Die können über die Links angezeigt werden in Liste oder Karte.
				//   und diese Angabe sind für alle Bogenarten gleich, bis auf die verschiedenen Links wo jeweils die Bogenart/bogen4archivviewname oder die Layer_id und das Archivkartiergebiet angegeben ist. (Layer-Suche_Suchen&selected_layer_id=jeweils die id für die bogen4archivviewname, bzw. in Karte den Layer aktivieren und den Filter oder Layerparameter setzen) (gibt es eine Möglichkeit, alle Objekte eines Suchergebnisses anzeigen, als Suchergebnis-Layer oder als normaler Layer mit Filter? Eigentlich reicht ein Suchergebnis-Layer. Nach der Übernahme sind die ja wieder weg.)
				// include(WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/snippets/mvbio_kartierobjekte2archiv.php');
			}
			include(WWWROOT . APPLVERSION . CUSTOM_PATH . 'layouts/snippets/mvbio_boegen4archiv.php');
		} break;

		case 'undo_archiv_boegen_einzeln_nach_bogenart' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
				throw new Exception('Sie haben keine Berechtigung, um die Archivierung von Bögen rückgängig zu machen!');
			}

			$this->sanitize(array(
				'archivkartiergebiet_id' => 'int',
				'bogenart_id' => 'int'
			));

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				throw new Exception('Archivkartiergebiet nicht angegeben!');
			}

			if ($this->formvars['archivkartiergebiet_id'] == '') {
				throw new Exception('Archivkartiergebiet-ID ist leer!');
			}

			$obj = new PgObject($this, 'archiv', 'kartiergebiete');
			$akg = $obj->find_by('id', $this->formvars['archivkartiergebiet_id']);
			if ($akg->data === false) {
				throw new Exception('Archivkartiergebiet mit id: ' . $this->formvars['archivkartiergebiet_id'] . ' nicht gefunden!');
			}

			if (!(array_key_exists('bogenart_id', $this->formvars))) {
				throw new Exception('Bogenart-ID nicht angegeben!');
			}

			if ($this->formvars['bogenart_id'] == '') {
				throw new Exception('Bogenart-ID ist leer!');
			}

			$obj = new PgObject($this, 'mvbio', 'bogenarten');
			$ba = $obj->find_by('id', $this->formvars['bogenart_id']);
			if ($ba->data === false) {
				throw new Exception('Bogenart mit id: ' . $this->formvars['bogenart_id'] . ' nicht gefunden!');
			}

			if ($ba->get_id() == 3) {
				// Verlustbögen
				$obj = new PgObject($this, 'archiv', 'kartierebenen2kampagne');
				$ke = $obj->find_by('kampagne_id', $akg->get('kampagne_id'));
				if ($ke->data === false) {
					throw new Exception('Kartierebene von Kampagne id: ' . $akg->get('kampagne_id') . ' nicht gefunden!');
				}
				$akg->set('kartierebene_id', $ke->get('kartierebene_id'));
				$result = undo_archiv_verlustboegen($this, $akg, $ba);
			}
			else{
				$result = undo_archiv_boegen($this, $akg, $ba);
			};

			echo json_encode($result);
		} break;

		case 'undo_archiv_boegen' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
				throw new Exception('Sie haben keine Berechtigung, um die Archivierung von Bögen rückgängig zu machen!');
			}

			$this->sanitize(array(
				'archivkartiergebiet_id' => 'int',
				'bogenart_id' => 'int'
			));

			if (!(array_key_exists('archivkartiergebiet_id', $this->formvars))) {
				throw new Exception('Archivkartiergebiet nicht angegeben!');
			}

			if ($this->formvars['archivkartiergebiet_id'] == '') {
				throw new Exception('Archivkartiergebiet-ID ist leer!');
			}

			$obj = new PgObject($this, 'archiv', 'kartiergebiete');
			$akg = $obj->find_by('id', $this->formvars['archivkartiergebiet_id']);
			if ($akg->data === false) {
				throw new Exception('Archivkartiergebiet mit id: ' . $this->formvars['archivkartiergebiet_id'] . ' nicht gefunden!');
			}

			// Query bogenarten die archiviert werden müssen und gehe diese durch.
			$bogenarten = get_bogenarten($this, $akg);
			$num_assigned_boegen = 0;
			$success = true;
			$msg = '';
			foreach ($bogenarten AS $ba) {
				if ($ba->get_id() == 3) {
					// Verlustbögen
					$obj = new PgObject($this, 'archiv', 'kartierebenen2kampagne');
					$ke = $obj->find_by('kampagne_id', $akg->get('kampagne_id'));
					if ($ke->data === false) {
						throw new Exception('Kartierebene von Kampagne id: ' . $akg->get('kampagne_id') . ' nicht gefunden!');
					}
					$akg->set('kartierebene_id', $ke->get('kartierebene_id'));
					$result = undo_archiv_verlustboegen($this, $akg, $ba);
				}
				else {
					$result = undo_archiv_boegen($this, $akg, $ba);
				};

				if (!$result['success']) {
					$msg .= $result['msg'];
				}
				else {
					$msg .= $result['msg'] . '<br>';
					$num_assigned_boegen += $result['num_assigned_boegen'];
				}
			}

			echo json_encode(array(
				'success' => $success,
				'msg' => $msg,
				'num_assigned_boegen' => $num_assigned_boegen
			));
		} break;

		case 'undo_archivkartiergebiet' : {
			if (!$this->Stelle->id == 3 OR !in_array($this->user->id, $allowed_users)) {
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

		default: {
			echo json_encode(array(
				'success' => false,
				'msg' => 'Unbekannte Aktion: ' . $this->formvars['action']
			));
		}
	}

	function archiv_gruenlandboegen($gue, $akg, $pruefer, $set_active) {
		$current_timestamp = date('Y-m-d H:i:s');
		$select_aktuell = ($set_active ? "true" : "false");
		$select_aktuell_seit = ($set_active ? "'" . $current_timestamp . "'" : "NULL");
		$sql = "
			// gruenlandboegen
			INSERT INTO archiv.gruenlandboegen (
				id,
				kampagne_id,
				kartiergebiet_id,
				kartierebene_id, bogenart_id,
				lfd_nr_kr,
				label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, hc, hcp, uc1, uc2, vegeinheit,
				wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16,
				gefaehrdg, ohnegefahr, empfehlung,
				literatur,
				fauna, e_datum, l_datum, foto,
				created_at, created_from, updated_at, updated_from,
				archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				pruefer,
				import_table, kartierer,
				eu_nr,
				unb,
				gemeinde_schluessel,
				aktuell,
				aktuell_seit,
				geom
			)
			SELECT
				id,
				" . $akg->get('kampagne_id') . " AS kampagne_id,
				" . $akg->get_id() . " AS kartiergebiet_id,
				kartierebene_id, bogenart_id,
				COALESCE((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over () as lfd_nr_kr,
				CONCAT_WS('-', (SELECT abk FROM archiv.kampagnen WHERE id = " . $akg->get('kampagne_id') . "), (select bezeichnung from archiv.kartiergebiete where id = " . $akg->get_id() . "), LPAD((coalesce((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over ())::text, 4, '0') ) as label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, hc, hcp, uc1, uc2, vegeinheit,
				wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16,
				gefaehrdg, ohnegefahr, empfehlung,
				literatur,
				fauna, e_datum, l_datum, foto,
				created_at, created_from, updated_at, updated_from,
				'" . $current_timestamp . "' AS archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				'" . $pruefer .  "' AS pruefer,
				import_table, kartierer,
				eu_nr,
				unb,
				gemeinde_schluessel,
				" . $select_aktuell . " AS aktuell,
				" . $select_aktuell_seit . " AS aktuell_seit,
				geom
			FROM
				mvbio.gruenlandboegen4archiv
			WHERE
				kartiergebiet_id = " . $akg->get_id() . ";

			// biotoptypen_nebencodes
			INSERT INTO archiv.biotoptypen_nebencodes (
				code,
				bogen_id,
				flaechendeckung_prozent
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id,
				nt.flaechendeckung_prozent
			FROM
				mvbio.biotoptypen_nebencodes nt JOIN
				mvbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			// beeintraechtigungen_gefaehrdungen
			INSERT INTO archiv.beeintraechtigungen_gefaehrdungen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.beeintraechtigungen_gefaehrdungen nt JOIN
				mvbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			// empfehlungen_massnahmen
			INSERT INTO archiv.empfehlungen_massnahmen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.empfehlungen_massnahmen nt JOIN
				mvbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			// habitatvorkommen
			INSERT INTO archiv.habitatvorkommen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.habitatvorkommen nt JOIN
				mnbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			// pflanzenvorkommen
			INSERT INTO archiv.pflanzenvorkommen (
				species_nr,
				valid_name,
				dzv,
				fsk,
				rl,
				cf,
				tax,
				bav,
				gsl_version,
				valid_nr,
				uuid,
				species_nr_152,
				bogen_id
			)
			SELECT
				species_nr,
				nummer, -- prüfen ob das das richtige Feld ist für valid_name, muss wohl aus Pflanzentabelle geholt werden.
				dzv,
				fsk,
				rl,
				cf,
				tax,
				bav,
				valid_nr,
				uuid,
				species_nr_152,
				boegen.id AS bogen_id
			FROM
				mvbio.pflanzenvorkommen nt JOIN
				mvbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			// fotos
			INSERT INTO archiv.fotos (
				datei,
				bogen_id
			)
			SELECT
				nt.datei,
				boegen.id AS bogen_id, // ToDo welche Attribute vom Foto sollen ins Archiv übernommen werden?
			FROM
				mvbio.fotos nt JOIN
				mvbio.gruenlandboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			UPDATE
				mvbio.kartierobjekte ko
			SET
				lock = true, -- nichts mehr validieren oder ändern, so lassen wie es ins Archiv übernommen wurde.
				bearbeitungsstufe = 7
			FROM
				archiv.gruenlandboegen4archiv boegen
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . " AND
				boegen.id = ko.id
		";
		// ToDo Fotos verschieben in Archivordner
		$msg = 'Archiviere Grünlandbögen mit: ' . $sql;

		$obj = new PgObject($gui, 'archiv', 'erfassungsboegen');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}

		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id() . " AND bogenart_id = 5");

		return array(
			'success' => false,
			'msg' => 'Funktion zum Archivieren von Grünlandbögen in Kampagne: ' . $akg->get_id() . ' noch nicht fertig implementiert!<p>' . $msg,
			'num_archived_boegen' => count($archived_boegen)
		);
	}

	function archiv_grundboegen($gui, $akg, $pruefer, $set_active) {
		$sql = '';
		$current_timestamp = date('Y-m-d H:i:s');
		if ($set_active) {
			$select_aktuell = "true";
			$select_aktuell_seit = "'" . $current_timestamp . "'";
			// Setze existierende Erfassungsbögen im Archivkartiergebiet auf unaktuell und unaktuell_seit auf current_timestamp
			$sql .= "
				UPDATE
					archiv.grundboegen
				SET
					aktuell = false,
					unaktuell_seit = '" . $current_timestamp . "'
				WHERE
					ST_Within(ST_PointOnSurface(geom), (SELECT geom FROM archiv.kartiergebiete WHERE id = " . $akg->get_id() . "));
			";
		}
		else {
			$select_aktuell = "false";
			$select_aktuell_seit = "NULL";
		}

		$sql .= "
			-- grundboegen
			INSERT INTO archiv.grundboegen (
				id,
				kampagne_id,
				kartiergebiet_id,
				kartierebene_id, bogenart_id,
				lfd_nr_kr,
				label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, schutz_ffh, lrt_gr, lrt_code, lrt_nummer, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, ohnegefahr, empfehlung, beschreibg, substrat_1, substrat_2, substrat_3, substrat_4, substrat_5, substrat_6, substrat_7, substrat_8, substrat_9, substrat_10, trophie_1, trophie_2, trophie_3, trophie_4, trophie_5, wasser_1, wasser_2, wasser_3, wasser_4, wasser_5, wasser_6, wasser_7, wasser_8, wasser_9, relief_1, relief_2, relief_3, relief_4, relief_5, relief_6, relief_7, relief_8, relief_9, relief_10, relief_11, relief_12, exposition_1, exposition_2, exposition_3, exposition_4, exposition_5, exposition_6, exposition_7, exposition_8, nutzintens_1, nutzintens_2, nutzintens_3, nutzintens_4, nutzungsart_1, nutzungsart_2, nutzungsart_3, nutzungsart_4, nutzungsart_5, nutzungsart_6, nutzungsart_7, nutzungsart_8, nutzungsart_9, nutzungsart_10, nutzungsart_11, nutzungsart_12, nutzungsart_13, nutzungsart_14, nutzartzus, umgebung_1, umgebung_2, umgebung_3, umgebung_4, umgebung_5, umgebung_6, umgebung_7, umgebung_8, umgebung_9, umgebung_10, umgebung_11, umgebung_12, umgebung_13, umgebung_14, umgebung_15, umgebung_16, umgebung_17, umgebung_18, umgebung_19, umgebung_20, umgebung_21, umgebung_22, umgebung_23, umgebung_24, umgebung_25, umgebzus,
				literatur,
				fauna, e_datum, l_datum, foto, created_at, created_from, updated_at, updated_from,
				archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				lrt_objekt_id,
				pruefer,
				import_table, kartierer,
				eu_nr,
				unb,
				gemeinde_schluessel,
				aktuell,
				aktuell_seit,
				geom
			)
			SELECT
				id,
				" . $akg->get('kampagne_id') . " AS kampagne_id,
				" . $akg->get_id() . " AS kartiergebiet_id,
				kartierebene_id, bogenart_id,
				COALESCE((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over () as lfd_nr_kr,
				CONCAT_WS('-', (SELECT abk FROM archiv.kampagnen WHERE id = " . $akg->get('kampagne_id') . "), (select bezeichnung from archiv.kartiergebiete where id = " . $akg->get_id() . "), LPAD((coalesce((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over ())::text, 4, '0') ) as label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, schutz_ffh, lrt_gr, lrt_code, lrt_nummer, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, ohnegefahr, empfehlung, beschreibg, substrat_1, substrat_2, substrat_3, substrat_4, substrat_5, substrat_6, substrat_7, substrat_8, substrat_9, substrat_10, trophie_1, trophie_2, trophie_3, trophie_4, trophie_5, wasser_1, wasser_2, wasser_3, wasser_4, wasser_5, wasser_6, wasser_7, wasser_8, wasser_9, relief_1, relief_2, relief_3, relief_4, relief_5, relief_6, relief_7, relief_8, relief_9, relief_10, relief_11, relief_12, exposition_1, exposition_2, exposition_3, exposition_4, exposition_5, exposition_6, exposition_7, exposition_8, nutzintens_1, nutzintens_2, nutzintens_3, nutzintens_4, nutzungsart_1, nutzungsart_2, nutzungsart_3, nutzungsart_4, nutzungsart_5, nutzungsart_6, nutzungsart_7, nutzungsart_8, nutzungsart_9, nutzungsart_10, nutzungsart_11, nutzungsart_12, nutzungsart_13, nutzungsart_14, nutzartzus, umgebung_1, umgebung_2, umgebung_3, umgebung_4, umgebung_5, umgebung_6, umgebung_7, umgebung_8, umgebung_9, umgebung_10, umgebung_11, umgebung_12, umgebung_13, umgebung_14, umgebung_15, umgebung_16, umgebung_17, umgebung_18, umgebung_19, umgebung_20, umgebung_21, umgebung_22, umgebung_23, umgebung_24, umgebung_25, umgebzus,
				literatur,
				fauna, e_datum, l_datum, foto, created_at, created_from, updated_at, updated_from,
				'" . $current_timestamp . "' AS archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				lrt_objekt_id,
				'" . $pruefer .  "' AS pruefer,
				import_table, kartierer,
				eu_nr,
				unb,
				gemeinde_schluessel,
				" . $select_aktuell . " AS aktuell,
				" . $select_aktuell_seit . " AS aktuell_seit,
				geom
			FROM
				mvbio.grundboegen4archiv
			WHERE
				kartiergebiet_id = " . $akg->get_id() . ";

			-- biotoptypen_nebencodes
			INSERT INTO archiv.biotoptypen_nebencodes (
				code,
				bogen_id,
				flaechendeckung_prozent
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id,
				nt.flaechendeckung_prozent
			FROM
				mvbio.biotoptypen_nebencodes nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- beeintraechtigungen_gefaehrdungen
			INSERT INTO archiv.beeintraechtigungen_gefaehrdungen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.beeintraechtigungen_gefaehrdungen nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- empfehlungen_massnahmen
			INSERT INTO archiv.empfehlungen_massnahmen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.empfehlungen_massnahmen nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- habitatvorkommen
			INSERT INTO archiv.habitatvorkommen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.habitatvorkommen nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- pflanzenvorkommen
			INSERT INTO archiv.pflanzenvorkommen (
				species_nr,
				valid_name,
				dzv,
				fsk,
				rl,
				cf,
				tax,
				bav,
				gsl_version,
				valid_nr,
				uuid,
				species_nr_152,
				bogen_id
			)
			SELECT
				nt.species_nr,
				pa.valid_name AS valid_name,
				nt.dzv,
				nt.fsk,
				nt.rl,
				nt.cf,
				nt.tax,
				nt.bav,
				'1.5.2', -- prüfen ob das stimmt und wie man das dynamisch machen kann
				nt.valid_nr,
				nt.uuid,
				nt.species_nr_152,
				boegen.id AS bogen_id
			FROM
				mvbio.pflanzenvorkommen nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id JOIN
				mvbio.pflanzenarten_gsl pa ON nt.species_nr = pa.species_nr
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- fotos
			INSERT INTO archiv.fotos (
				datei,
				bogen_id,
				richtung,
				himmelsrichtung,
				beschreibung, geom, created_at,
				e_datum
			)
			SELECT
				nt.datei,
				boegen.id AS bogen_id,
				nt.richtung,
				nt.himmelsrichtung,
				nt.beschreibung, nt.geom, nt.created_at,
				nt.exif_erstellungszeit::date AS e_datum
			FROM
				mvbio.fotos nt JOIN
				mvbio.grundboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			INSERT INTO archiv.bogen_historie (vorgaenger_bogen_id, nachfolger_bogen_id, bemerkung, hauptvorgaenger)
			SELECT
				vorgaenger_id AS vorgaenger_bogen_id,
				id AS nachfolger_bogen_id,
				'Angabe durch Kartierer' AS bemerkung,
				true AS hauptvorgaenger
			FROM
				mvbio.grundboegen4archiv
			WHERE
				vorgaenger_id IS NOT NULL AND
				kartiergebiet_id = " . $akg->get_id() . ";

			INSERT INTO archiv.bogen_historie (vorgaenger_bogen_id, nachfolger_bogen_id, bemerkung, hauptvorgaenger)
			SELECT
				unnest(zusammengefasste_ids) AS vorgaenger_bogen_id,
				id AS nachfolger_bogen_id,
				'Angabe durch Kartierer' AS bemerkung,
				false AS hauptvorgaenger
			FROM
				mvbio.grundboegen4archiv
			WHERE
				zusammengefasste_ids IS NOT NULL AND
				kartiergebiet_id = " . $akg->get_id() . ";

			UPDATE
				mvbio.kartierobjekte ko
			SET
				lock = true, -- nichts mehr validieren oder ändern, so lassen wie es ins Archiv übernommen wurde.
				bearbeitungsstufe = 7
			FROM
				mvbio.grundboegen4archiv boegen
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . " AND
				boegen.id = ko.id
		";
		$obj = new PgObject($gui, 'archiv', 'erfassungsboegen');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}

		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id() . " AND bogenart_id = 2");

		// ToDo Fotos der Grundbögen in Archiv-Verzeichnis verschieben.
		// z.B. /var/www/data/mvbio/fotos/117000/116299.JPG&original_name=IMG_4611.JPG
		// nach /var/www/data/mvbio/archivfotos/Schulung-S01-0021_01.JPG&original_name=IMG_4611.JPG
		// Change or Remove and add existing metadata, see options of gdal_edit
		// -unsetmd
		// Remove existing metadata (in the default metadata domain). Can be combined with -mo.
		// -mo <META-TAG>=<VALUE>
		// Passes a metadata key and value to set on the output dataset if possible. This metadata is added to the existing metadata items, unless -unsetmd is also specified.
		// it seams that it is not possible with gdal_edit for jpeg thats why use image magick
		// convert 0209-134B5278_01.JPG -set comment "EXIF_xyz=value" 0209-134B5278_01_tmp.JPG && mv 0209-134B5278_01_tmp.JPG 0209-134B5278_01.JPG 

		$fotos = $obj->find_by_sql(array(
			'select' => "
				gb.id AS gb_id,
				f.datei AS kartierung_datei,
				Concat_ws('/', '/var/www/data/mvbio/archivfotos', kk.abk, kg.bezeichnung, gb.LABEL || '-' || LPAD((ROW_NUMBER() OVER (PARTITION BY gb.id ORDER BY f.id))::text, 3, '0') || '.' || REVERSE(SPLIT_PART(REVERSE(SPLIT_PART(f.datei, '&', 1)), '.', 1))) AS archiv_datei
			",
			'from' => "
				archiv.fotos f JOIN
				archiv.kurzboegen gb ON f.bogen_id = gb.id JOIN
				archiv.kartiergebiete kg ON gb.kartiergebiet_id = kg.id JOIN
				archiv.kampagnen kk ON kg.kampagne_id = kk.id
			",
			'where' => "b.kartiergebiet_id = " . $akg->get_id()
		));
		foreach ($fotos as $foto) {
			// hier fotos kopieren mit Anlegen von Verzeichnissen cp -p
		}

		return array(
			'success' => true,
			'msg' => 'Grundbögen erfolgreich in Archivkartiergebiet ' . $akg->get_id() . ' archiviert!<p>' . $msg,
			'num_archived_boegen' => count($archived_boegen)
		);
	}

	function archiv_kurzboegen($gui, $akg, $pruefer, $set_active) {
		$sql = '';
		$current_timestamp = date('Y-m-d H:i:s');
		if ($set_active) {
			$select_aktuell = "true";
			$select_aktuell_seit = "'" . $current_timestamp . "'";
			// Setze existierende Erfassungsbögen im Archivkartiergebiet auf unaktuell und unaktuell_seit auf current_timestamp
			$sql .= "
				UPDATE
					archiv.kurzboegen
				SET
					aktuell = false,
					unaktuell_seit = '" . $current_timestamp . "'
				WHERE
					ST_Within(ST_PointOnSurface(geom), (SELECT geom FROM archiv.kartiergebiete WHERE id = " . $akg->get_id() . "));
			";
		}
		else {
			$select_aktuell = "false";
			$select_aktuell_seit = "NULL";
		}

		$sql .= "
			-- kurzboegen
			INSERT INTO archiv.kurzboegen (
				id,
				kampagne_id,
				kartiergebiet_id,
				kartierebene_id, bogenart_id,
				lfd_nr_kr,
				label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, ohnegefahr, empfehlung, fauna, e_datum, l_datum, foto, created_at, created_from, updated_at, updated_from,
				archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				pruefer,
				import_table, kartierer,
				unb,
				gemeinde_schluessel,
				aktuell,
				aktuell_seit,
				geom
			)
			SELECT
				id,
				" . $akg->get('kampagne_id') . " AS kampagne_id,
				" . $akg->get_id() . " AS kartiergebiet_id,
				kartierebene_id, bogenart_id,
				COALESCE((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over () as lfd_nr_kr,
				CONCAT_WS('-', (SELECT abk FROM archiv.kampagnen WHERE id = " . $akg->get('kampagne_id') . "), (select bezeichnung from archiv.kartiergebiete where id = " . $akg->get_id() . "), LPAD((coalesce((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over ())::text, 4, '0') ) as label,
				label_erfassung,
				biotopname, standort, flaeche, schutz_bio, hc, hcp, uc1, uc2, vegeinheit, wert_krit_1, wert_krit_2, wert_krit_3, wert_krit_4, wert_krit_5, wert_krit_6, wert_krit_7, wert_krit_8, wert_krit_9, wert_krit_10, wert_krit_11, wert_krit_12, wert_krit_13, wert_krit_14, wert_krit_15, wert_krit_16, gefaehrdg, ohnegefahr, empfehlung, fauna, e_datum, l_datum, foto, created_at, created_from, updated_at, updated_from,
				'" . $current_timestamp . "' AS archived_at,
				nicht_begehbar, bearbeitungsinfo,
				keine_wert_krit, nutzintens_5,
				'" . $pruefer .  "' AS pruefer,
				import_table, kartierer,
				unb,
				gemeinde_schluessel,
				" . $select_aktuell . " AS aktuell,
				" . $select_aktuell_seit . " AS aktuell_seit,
				geom
			FROM
				mvbio.kurzboegen4archiv
			WHERE
				kartiergebiet_id = " . $akg->get_id() . ";

			-- biotoptypen_nebencodes
			INSERT INTO archiv.biotoptypen_nebencodes (
				code,
				bogen_id,
				flaechendeckung_prozent
			)
			SELECT
				nt.code,
				kb.id AS bogen_id,
				nt.flaechendeckung_prozent
			FROM
				mvbio.biotoptypen_nebencodes nt JOIN
				mvbio.kurzboegen4archiv kb ON nt.kartierung_id = kb.id
			WHERE
				kb.kartiergebiet_id = " . $akg->get_id() . ";

			-- beeintraechtigungen_gefaehrdungen
			INSERT INTO archiv.beeintraechtigungen_gefaehrdungen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				kb.id AS bogen_id
			FROM
				mvbio.beeintraechtigungen_gefaehrdungen nt JOIN
				mvbio.kurzboegen4archiv kb ON nt.kartierung_id = kb.id
			WHERE
				kb.kartiergebiet_id = " . $akg->get_id() . ";

			-- empfehlungen_massnahmen
			INSERT INTO archiv.empfehlungen_massnahmen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				kb.id AS bogen_id
			FROM
				mvbio.empfehlungen_massnahmen nt JOIN
				mvbio.kurzboegen4archiv kb ON nt.kartierung_id = kb.id
			WHERE
				kb.kartiergebiet_id = " . $akg->get_id() . ";

			-- habitatvorkommen
			INSERT INTO archiv.habitatvorkommen (
				code,
				bogen_id
			)
			SELECT
				nt.code,
				boegen.id AS bogen_id
			FROM
				mvbio.habitatvorkommen nt JOIN
				mvbio.kurzboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			-- fotos
			INSERT INTO archiv.fotos (
				datei,
				bogen_id,
				richtung,
				himmelsrichtung,
				beschreibung, geom, created_at,
				e_datum
			)
			SELECT
				nt.datei,
				boegen.id AS bogen_id,
				nt.richtung,
				nt.himmelsrichtung,
				nt.beschreibung, nt.geom, nt.created_at,
				nt.exif_erstellungszeit::date AS e_datum
			FROM
				mvbio.fotos nt JOIN
				mvbio.kurzboegen4archiv boegen ON nt.kartierung_id = boegen.id
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . ";

			INSERT INTO archiv.bogen_historie (vorgaenger_bogen_id, nachfolger_bogen_id, bemerkung, hauptvorgaenger)
			SELECT
				vorgaenger_id AS vorgaenger_bogen_id,
				id AS nachfolger_bogen_id,
				'Angabe durch Kartierer' AS bemerkung,
				true AS hauptvorgaenger
			FROM
				mvbio.kurzboegen4archiv
			WHERE
				vorgaenger_id IS NOT NULL AND
				kartiergebiet_id = " . $akg->get_id() . ";

			INSERT INTO archiv.bogen_historie (vorgaenger_bogen_id, nachfolger_bogen_id, bemerkung, hauptvorgaenger)
			SELECT
				unnest(zusammengefasste_ids) AS vorgaenger_bogen_id,
				id AS nachfolger_bogen_id,
				'Angabe durch Kartierer' AS bemerkung,
				false AS hauptvorgaenger
			FROM
				mvbio.kurzboegen4archiv
			WHERE
				zusammengefasste_ids IS NOT NULL AND
				kartiergebiet_id = " . $akg->get_id() . ";

			UPDATE
				mvbio.kartierobjekte ko
			SET
				lock = true, -- nichts mehr validieren oder ändern, so lassen wie es ins Archiv übernommen wurde.
				bearbeitungsstufe = 7
			FROM
				mvbio.kurzboegen4archiv boegen
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . " AND
				boegen.id = ko.id
		";
		$obj = new PgObject($gui, 'archiv', 'erfassungsboegen');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}

		// ToDo Fotos verschieben in Archivordner
		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id() . " AND bogenart_id = 1");
		return array(
			'success' => true,
			'msg' => 'Kurzbögen erfolgreich in Archivkartiergebiet ' . $akg->get_id() . ' archiviert!<p>' . $msg,
			'num_archived_boegen' => count($archived_boegen)
		);
	}

	function archiv_verlustboegen($gui, $akg, $pruefer, $set_active) {
		$current_timestamp = date('Y-m-d H:i:s');
		$sql .= "
			-- Setze Bezugsbögen im Archivkartiergebiet auf unaktuell und unaktuell_seit auf current_timestamp
			UPDATE
				archiv.erfassungsboegen eb
			SET
				aktuell = false,
				unaktuell_seit = '" . $current_timestamp . "'
			FROM
				mvbio.verlustboegen4archiv vb
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . " AND
				vb.bezugsbogen_id = eb.id;

			-- verlustboegen anlegen
			INSERT INTO archiv.verlustboegen (
				id,
				kampagne_id,
				kartiergebiet_id,
				kartierebene_id,
				bogenart_id,
				lfd_nr_kr,
				label,
				label_erfassung,
				biotopname,
				unb,
				e_datum,
				l_datum,
				geom,
				bezugsbogen_id,
				hc,
				uc1,
				uc2,
				hcp,
				vegeinheit,
				created_at,
				updated_at,
				created_from,
				updated_from,
				kartierer,
				foto,
				bearbeitungsinfo,
				eu_nr,
				gemeinde_schluessel,
				beschreibg,
				wiederherstellbar,
				massn_txt,
 				import_table,
				bestaetigt,
				bestaetigt_am,
				offiziell,
				archived_at,
				pruefer
			)
			SELECT
				id,
				" . $akg->get('kampagne_id') . " AS kampagne_id,
				" . $akg->get_id() . " AS kartiergebiet_id,
				kartierebene_id,
				bogenart_id,
				COALESCE((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over () as lfd_nr_kr,
				CONCAT_WS('-', (SELECT abk FROM archiv.kampagnen WHERE id = " . $akg->get('kampagne_id') . "), (select bezeichnung from archiv.kartiergebiete where id = " . $akg->get_id() . "), 'v', LPAD((coalesce((select max(lfd_nr_kr) from archiv.erfassungsboegen where kartiergebiet_id = " . $akg->get_id() . "), 0) + row_number() over ())::text, 4, '0') ) as label,
				label_erfassung,
				biotopname,
				unb,
				e_datum,
				l_datum,
				geom,
				bezugsbogen_id,
				hc,
				uc1,
				uc2,
				hcp,
				vegeinheit,
				created_at,
				updated_at,
				created_from,
				updated_from,
				kartierer,
				foto,
				bearbeitungsinfo,
				eu_nr,
				gemeinde_schluessel,
				beschreibg,
				wiederherstellbar,
				massn_txt,
 				import_table,
				FALSE AS bestaetigt,
				NULL AS bestaetigt_am,
				" . ($set_active ? 'true': 'false') . " AS offiziell,
				'" . $current_timestamp . "' AS archived_at,
				'" . $pruefer .  "' AS pruefer
			FROM
				mvbio.verlustboegen4archiv
			WHERE
				kartiergebiet_id = " . $akg->get_id() . " AND
				kartierebene_id = " . $akg->get('kartierebene_id') . ";

			-- biotoptypen_nebencodes_verlustboegen
			INSERT INTO archiv.biotoptypen_nebencodes_verlustboegen (
				verlustbogen_id,
				code,
				flaechendeckung_prozent
			)
			SELECT
				nt.verlustobjekt_id AS verlustbogen_id,
				nt.code,
				nt.flaechendeckung_prozent
			FROM
				mvbio.biotoptypen_nebencodes_verlustobjekte nt JOIN
				mvbio.verlustboegen4archiv vb ON nt.verlustobjekt_id = vb.id
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . ";

			-- empfehlungen_massnahmen_verlustboegen
			INSERT INTO archiv.empfehlungen_massnahmen_verlustboegen (
				verlustbogen_id,
				code
			)
			SELECT
				nt.verlustobjekt_id AS verlustbogen_id,
				nt.code
			FROM
				mvbio.empfehlungen_massnahmen_verlustobjekte nt JOIN
				mvbio.verlustboegen4archiv vb ON nt.verlustobjekt_id = vb.id
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . ";

			-- fotos_verlustboegen
			INSERT INTO archiv.fotos_verlustboegen (
				verlustbogen_id,
				datei,
				richtung,
				himmelsrichtung,
				beschreibung,
				geom
			)
			SELECT
				nt.verlustobjekt_id AS verlustbogen_id,
				nt.datei,
				nt.richtung,
				nt.himmelsrichtung,
				nt.beschreibung,
				nt.geom
			FROM
				mvbio.fotos_verlustobjekte nt JOIN
				mvbio.verlustboegen4archiv vb ON nt.verlustobjekt_id = vb.id
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . ";

			-- Anlegen der Verlustursachen
			INSERT INTO archiv.verlustursachen (
				verlustbogen_id,
				verlustart,
				begruendung
			)
			SELECT
				verlustobjekt_id,
				verlustart,
				begruendung
			FROM
				mvbio.verlustursachen vu JOIN
				mvbio.verlustboegen4archiv vb ON vu.verlustobjekt_id = vb.id
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . ";

			UPDATE
				mvbio.verlustobjekte vo
			SET
				lock = true, -- nichts mehr validieren oder ändern, so lassen wie es ins Archiv übernommen wurde.
				bearbeitungsstufe = 7
			FROM
				mvbio.verlustboegen4archiv boegen
			WHERE
				boegen.kartiergebiet_id = " . $akg->get_id() . " AND
				boegen.kartierebene_id = " . $akg->get('kartierebene_id') . " AND
				boegen.id = vo.id
		";
		$obj = new PgObject($gui, 'archiv', 'verlustboegen');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}

		// ToDo Fotos verschieben in Archivordner

		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id() . " AND bogenart_id = 3");
		return array(
			'success' => true,
			'msg' => 'Verlustbögen erfolgreich in Archivkartiergebiet ' . $akg->get_id() . ' archiviert!<p>' . $msg,
			'num_archived_boegen' => count($archived_boegen)
		);
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
				'msg' => 'Fehler beim Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id . ' Meldung: ' . $result['msg']
			);
		}

		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$kg = $obj->find_by('id', $kg_id);

		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');

		$sql = "
			UPDATE
				archiv.kartiergebiete akg
			SET
				geom = agg.geom_agg,
				bezeichnung_orig = agg.bezeichnung_agg
			FROM
				(
					SELECT
						kg2akg.archivkartiergebiet_id,
						string_agg(kg.bezeichnung, ', ') AS bezeichnung_agg,
						ST_Multi(gdi_normalize_geometry(ST_Union(kg.geom), 100, 0.1, 0.1, 1, true)) AS geom_agg
					FROM
						mvbio.kartiergebiete2archivkartiergebiete kg2akg JOIN
						mvbio.kartiergebiete kg ON kg2akg.kartiergebiet_id = kg.id
					WHERE
						kg2akg.archivkartiergebiet_id = " . $akg_id . "
					GROUP BY
						kg2akg.archivkartiergebiet_id
				) agg
			WHERE
				akg.id = agg.archivkartiergebiet_id
		";

		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			// Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet löschen, da die Abfrage fehlgeschlagen ist
			$kg2akg->delete();
			return array(
				'success' => false,
				'msg' => 'Fehler bei der Abfrage sql: ' . $sql . ' Fehler: ' . $err_msg
			);
		}
		$akg = $obj->find_by('id', $akg_id);

		return array(
			'success' => true,
			'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet erfolgreich angelegt!',
			'data' => array(
				'id' => $akg->get('id'),
				'bezeichnung' => $akg->get('bezeichnung')
			)
		);
	}

	function create_archivkartiergebiet($gui, $kg, $ak, $akg) {
		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');
		$akg->data = array(
			'kampagne_id'      => $ak->get_id(),
			'losnummer'        => $akg->get('losnummer')    ?: $kg->get('losnummer'),
			'bezeichnung'      => $akg->get('bezeichnung')  ?: $kg->get('bezeichnung'),
			'bemerkungen'      => $akg->get('bemerkungen')  ?: $kg->get('bemerkungen'),
			'bezeichnung_orig' => $kg->get('bezeichnung'),
			'auftragnehmer'    => $kg->get('auftragnehmer'),
			'auftraggeber'     => $kg->get('auftraggeber'),
			'kart_start'       => $kg->get('kart_start') ?: null,
			'kart_end'         => $kg->get('kart_end') ?: null,
			'geom'             => $kg->get('geom')
		);

		$result = $akg->create();
		if (is_array($result) AND !$result['success']) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Anlegen des Kartiergebiets im Archiv mit den Werten: ' . print_r($data, true) . ' Meldung: ' . $result['msg']
			);
		}
		$akg = $obj->find_by_ids($result['ids']);

		$data = array(
			'kartiergebiet_id' => $kg->get_id(),
			'archivkartiergebiet_id' => $akg->get_id()
		);
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		$obj->identifier = 'kartiergebiet_id';
		$obj->identifiers = array(
			array('column' => 'kartiergebiet_id', 'type' => 'integer'),
			array('column' => 'archivkartiergebiet_id', 'type' => 'integer')
		);
		$result = $obj->create($data);
		if (!$result['success']) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: ' . print_r($data, true) . ' Meldung: ' . $result['msg']
			);
		}

		return array(
			'success' => true,
			'data' => array('id' => $akg->get_id(), 'bezeichnung' => $akg->get('bezeichnung')),
			'msg' => 'Kartiergebiet Id: ' . $akg->get_id() . ' erfolgreich im Archiv angelegt.'
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

	function get_assigned_boegen($gui, $ba, $akg_id) {
		$obj = new PgObject($gui, 'mvbio', $ba->get('archivtabelle') . '4archiv');
		return $obj->find_where("kartiergebiet_id = " . $akg_id);
	}

	function get_archived_boegen($gui, $ba, $akg_id) {
		$obj = new PgObject($gui, 'archiv', $ba->get('archivtabelle'));
		return $obj->find_where("kartiergebiet_id = " . $akg_id . " AND bogenart_id = " . $ba->get('id'));
	}

	/**
	 * Fragt die im Archivkartiergebiet $akg vorkommenden Bogenarten der Kartier- und Verlustobjekte ab.
	 */
	function get_bogenarten($gui, $akg) {
		// Frage die Bogenarten der Kartierobjekte ab.
		$obj = new PgObject($gui, 'mvbio', 'bogenarten');
		$bogenarten = $obj->find_by_sql(array(
			'select' => "
				ke2kk.kartierebene_id,
				ba.id,
				ba.bezeichnung,
				ba.archivtabelle,
				ba.layer_name_part AS name_plural,
				ba.bogen4archiv_layer_id
			",
			'from' => "
				mvbio.bogenarten2kartierebenen ba2ke JOIN
				archiv.kartierebenen2kampagne ke2kk ON ba2ke.kartierebene_id = ke2kk.kartierebene_id JOIN
				mvbio.bogenarten ba ON ba2ke.bogenart_id = ba.id
			",
			'where' => "ke2kk.kampagne_id = " . $akg->get('kampagne_id'),
			'order' => 'ba.id'
		));

		// Frage Bogenart der Verlustobjekte ab.
		$result = $obj->find_by_sql(array(
			'select' => "
				(SELECT kartierebene_id FROM archiv.kartierebenen2kampagne WHERE kampagne_id = " . $akg->get('kampagne_id') . ") AS kartierebene_id,
				ba.id,
				ba.bezeichnung,
				ba.archivtabelle,
				ba.layer_name_part AS name_plural,
				ba.bogen4archiv_layer_id
			",
			'from' => "
				mvbio.bogenarten ba
			",
			'where' => "ba.id = 3"
		));
		array_push($bogenarten, $result[0]);
		return $bogenarten;
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

		// Kartiergebiete der Kampagne mit dessen zugehörigen Archiv-Kartiergebieten (assigned_archivkartiergebiet)
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$kk->kartiergebiete = get_kartiergebiete($gui, $kk);
		return $kk;
	}

	/**
	 * Lädt die Kartiergebiete der Kampagne und die zugehörigen Archiv-Kartiergebiete.
	 * @param $gui
	 * @param $kampagne_id
	 * @return array of Kartiergebiet objects mit assigned_archivkartiergebiet Associative Array with $archivkampagne_id als key and the assigned archivkartiergebiet_id as value.
	 */
	function get_kartiergebiete($gui, $kk) {
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete');
		$order = value_of($gui->formvars, 'order') ?: 'losnummer, bezeichnung';
		$direction = value_of($gui->formvars, 'direction') ?: 'ASC';
		$kartiergebiete = $obj->find_where('kampagne_id = ' . $kk->get_id(), $order . ' ' . $direction);
		// Frage pro Kartierkampagne die zugehörigen Archiv-Kartiergebiete ab
		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		foreach ($kartiergebiete AS $kg) {
			$kg->sum_kartierobjekte = $obj->find_by_sql(array(
				'select' => "
					sum(CASE WHEN bearbeitungsstufe < 6 THEN 1 ELSE 0 END) AS nicht_archivierbar
				",
				'from' => 'mvbio.kartierobjekte',
				'where' => "kartiergebiet_id = " . $kg->get_id(),
				'group_by' => 'kartiergebiet_id'
			))[0];
			$kg->sum_verlustobjekte = $obj->find_by_sql(array(
				'select' => "
					sum(CASE WHEN bearbeitungsstufe < 6 THEN 1 ELSE 0 END) AS nicht_archivierbar
				",
				'from' => 'mvbio.verlustobjekte',
				'where' => "kartiergebiet_id = " . $kg->get_id(),
				'group_by' => 'kartiergebiet_id'
			))[0];
			$kg->archivierbar = ($kg->sum_kartierobjekte->get('nicht_archivierbar') + $kg->sum_verlustobjekte->get('nicht_archivierbar')) === 0;
			if ($kg->archivierbar) {
				foreach ($kk->archivkampagnen AS $akk) {
					$assigned_archivkartiergebiete = $obj->find_by_sql(array(
						'select' => 'akg.*',
						'from' => 'mvbio.kartiergebiete2archivkartiergebiete kg2akg JOIN archiv.kartiergebiete akg ON kg2akg.archivkartiergebiet_id = akg.id',
						'where' => "kg2akg.kartiergebiet_id = " . $kg->get_id() . " AND akg.kampagne_id = " . $akk->get_id()
					));
					if (count($assigned_archivkartiergebiete) == 0) {
						$kg->assigned_archivkartiergebiet[$akk->get_id()] = null;
					}
					else {
						$assigned_archivkartiergebiete[0]->is_archiviert = is_archiviert('archivkartiergebiet', $assigned_archivkartiergebiete[0]);
						$kg->assigned_archivkartiergebiet[$akk->get_id()] = $assigned_archivkartiergebiete[0];
					}
				}
			}
		}
		return $kartiergebiete;
	}

	function get_archivkampagnen($gui, $kk_id) {
		$obj = new PgObject($gui, 'archiv', 'kampagnen');
		$archivkampagnen = $obj->find_where('mvbio_kampagne_id = ' . $kk_id, 'id');
		$obj = new PgObject($gui, 'archiv', 'kartiergebiete');
		foreach ($archivkampagnen as $ak) {
			$ak->archivkartiergebiete = $obj->find_where("kampagne_id = " . $ak->get_id());
			foreach ($ak->archivkartiergebiete as $akg) {
				$akg->is_archiviert = is_archiviert('archivkartiergebiet', $akg);
			}
		}
		return $archivkampagnen;
	}

	function is_archiviert($type, $akg) {
		echo '<br>Prüfe, ob das Kartiergebiet ' . $akg->get_id() . ' archiviert ist.<br>';
		// Prüfe, ob das Archiv-Kartiergebiet archiviert ist
		if ($type == 'archivkartiergebiet') {
			$pg_obj = new PgObject($akg->gui, 'archiv', 'erfassungsboegen');
			echo '<br>Frage erfassungsbögen für Kartiergebiet ' . $akg->get_id() . ' ab.<br>';
			$erfassungsboegen = $pg_obj->find_where("kartiergebiet_id = " . $akg->get_id());
			echo ' Anzahl: ' . count($erfassungsboegen) . '<br>';
			return count($erfassungsboegen) > 0;
		}
	}

	/**
	 * Entfernt die Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet.
	 * Wenn das Archiv-Kartiergebiet keine Zuordnungen mehr hat, wird es gelöscht.
	 * @param GUI $gui
	 * @param Integer $kg_id
	 * @param Integer $akg_id
	 * @return array with success and msg
	 */
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

		if (!$kg2akg->delete()) {
			return array(
				'success' => false,
				'msg' => 'Fehler beim Entfernen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: kartiergebiet_id=' . $kg_id . ', archivkartiergebiet_id=' . $akg_id
			);
		}

		$sql = "
			UPDATE
				archiv.kartiergebiete akg
			SET
				geom = agg.geom_agg,
				bezeichnung_orig = agg.bezeichnung_agg
			FROM
				(
					SELECT
						kg2akg.archivkartiergebiet_id,
						string_agg(kg.bezeichnung, ', ') AS bezeichnung_agg,
						ST_Multi(gdi_normalize_geometry(ST_Union(kg.geom), 100, 0.1, 0.1, 1, true)) AS geom_agg
					FROM
						mvbio.kartiergebiete2archivkartiergebiete kg2akg JOIN
						mvbio.kartiergebiete kg ON kg2akg.kartiergebiet_id = kg.id
					WHERE
						kg2akg.archivkartiergebiet_id = " . $akg_id . "
					GROUP BY
						kg2akg.archivkartiergebiet_id
				) agg
			WHERE
				akg.id = agg.archivkartiergebiet_id
		";

		// echo 'SQL zum Abziehen der Geometrie des Kartiergebiets: ' . $sql . '<br>';
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$result = $kg2akg->create(); // Zuordnung wiederherstellen, da die Abfrage fehlgeschlagen ist
			if (!$result['success']) {
				return array(
					'success' => false,
					'msg' => 'Fehler beim Anlegen der Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet mit den Werten: ' . print_r($data, true) . ' Meldung: ' . $result['msg']
				);
			}

			return array(
				'success' => false,
				'msg' => 'Fehler bei der Abfrage: ' . pg_last_error($gui->database->dbConn)
			);
		}

		$obj = new PgObject($gui, 'mvbio', 'kartiergebiete2archivkartiergebiete');
		$obj->identifiers = array(
			array('column' => 'kartiergebiet_id', 'type' => 'integer'),
			array('column' => 'archivkartiergebiet_id', 'type' => 'integer')
		);

		$kartiergebietzuordnungen = $obj->find_where("archivkartiergebiet_id = " . $akg_id);
		if (count($kartiergebietzuordnungen) == 0) {
			$sql = "
				DELETE FROM
					archiv.kartiergebiete
				WHERE
					id = " . $akg_id . "
			";
			$query = $obj->execSQL($sql);
			if ($query === false) {
				$err_msg = pg_last_error($obj->database->dbConn);
				return array(
					'success' => false,
					'msg' => 'Fehler beim Löschen des Archiv-Kartiergebiets mit der ID: ' . $akg_id . '. Fehler: ' . $err_msg
				);
			}
			$deleted_archivkartiergebiet_id = $akg_id;
		}
		else {
			$deleted_archivkartiergebiet_id = null;
		}
		return array(
			'success' => true,
			'msg' => 'Zuordnung zwischen Kartiergebiet und Archiv-Kartiergebiet erfolgreich entfernt!',
			'deleted_archivkartiergebiet_id' => $deleted_archivkartiergebiet_id
		);
	}

	function undo_archiv_boegen($gui, $akg, $ba) {
		$sql = "
			-- Setze die Bögen die vorher aktuell waren (letztes unaktuell_seit)
			-- zurück auf aktuell, wenn sie nicht schon aktuell waren und
			-- die Bögen die aus dem Archiv entfernt werden sollen aktuell waren
			UPDATE
				archiv." . $ba->get('archivtabelle') . " b
			SET
				aktuell = true,
				unaktuell_seit = NULL
			FROM
				(
					SELECT
						max(unaktuell_seit) AS max_datum
					FROM
						archiv." . $ba->get('archivtabelle') . " b JOIN
						archiv.kartiergebiete kg ON ST_Within(b.geom, kg.geom)
					WHERE
						b.kartiergebiet_id != " . $akg->get_id() . " AND
						kg.id = " . $akg->get_id() . "
				) last_aktuell
			WHERE
				NOT b.aktuell AND
				b.unaktuell_seit = last_aktuell.max_datum AND
				EXISTS (
					SELECT
						* 
					FROM
						archiv." . $ba->get('archivtabelle') . "
					WHERE
						kartiergebiet_id = " . $akg->get_id() . " AND
						aktuell
					LIMIT 1
				);

			DELETE FROM
				archiv.beeintraechtigungen_gefaehrdungen nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			DELETE FROM
				archiv.biotoptypen_nebencodes nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			DELETE FROM
				archiv.empfehlungen_massnahmen nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			DELETE FROM
				archiv.fotos nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			DELETE FROM
				archiv.habitatvorkommen nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			DELETE FROM
				archiv.pflanzenvorkommen nt
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				nt.bogen_id = b.id;

			-- Lösche Bogenhistorie
			DELETE FROM
				archiv.bogen_historie h
			USING
				archiv." . $ba->get('archivtabelle') . " b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				h.nachfolger_bogen_id = b.id;

			-- Lösche die Bögen
			DELETE FROM
				archiv." . $ba->get('archivtabelle') . "
			WHERE
				kartiergebiet_id = " . $akg->get_id() . ";

			UPDATE
				mvbio.kartierobjekte ko
			SET
				lock = true,
				bearbeitungsstufe = 6
			FROM
				mvbio." . $ba->get('archivtabelle') . "4archiv ab
			WHERE
				ko.id = ab.id AND
				ab.kartiergebiet_id = " . $akg->get_id() . "
		";
		$obj = new PgObject($gui, 'mvbio', $ba->get('archivtabelle') . '4archiv');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}
		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id());
		return array(
			'success' => true,
			'msg' => 'Archivierung der ' . $ba->get('layer_name_part') . ' in Archivkartiergebiet Id: ' . $akg->get_id() . ' erfolgreich rückgängig gemacht!<p>' . $msg,
			'num_assigned_boegen' => count($archived_boegen)
		);
	}

	function undo_archiv_verlustboegen($gui, $akg, $ba) {
		$sql = "
			-- Lösche die Nebentabellen der Verlustbögen
			DELETE FROM
				archiv.biotoptypen_nebencodes_verlustboegen vu
			USING
				archiv.verlustboegen b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				vu.verlustbogen_id = b.id;

			DELETE FROM
				archiv.empfehlungen_massnahmen_verlustboegen vu
			USING
				archiv.verlustboegen b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				vu.verlustbogen_id = b.id;

			DELETE FROM
				archiv.fotos_verlustboegen vu
			USING
				archiv.verlustboegen b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				vu.verlustbogen_id = b.id;

			DELETE FROM
				archiv.verlustursachen vu
			USING
				archiv.verlustboegen b
			WHERE
				b.kartiergebiet_id = " . $akg->get_id() . " AND
				vu.verlustbogen_id = b.id;

			-- Setze die Bögen, die durch die Verlustbögen auf unaktuelle gesetzt wurden wieder auf aktuell
			UPDATE
				archiv.erfassungsboegen eb
			SET
				aktuell = true,
				unaktuell_seit = NULL
			FROM
				archiv.verlustboegen vb
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				eb.id = vb.bezugsbogen_id;

			-- Lösche die Verlustbögen im Archiv
			DELETE FROM
				archiv.verlustboegen
			WHERE
				kartiergebiet_id = " . $akg->get_id() . ";

			-- Setze die Bearbeitungsstufe der Verlustobjekte dessen Archivierung rückgängig gemacht wurde auf 6 zurück
			UPDATE
				mvbio.verlustobjekte vo
			SET
				lock = true,
				bearbeitungsstufe = 6
			FROM
				mvbio.verlustboegen4archiv vb
			WHERE
				vb.kartiergebiet_id = " . $akg->get_id() . " AND
				vb.kartierebene_id = " . $akg->get('kartierebene_id') . " AND
				vo.id = vb.id;
		";
		$obj = new PgObject($gui, 'mvbio', $ba->get('archivtabelle') . '4archiv');
		$query = $obj->execSQL($sql);
		if ($query === false) {
			$err_msg = pg_last_error($obj->database->dbConn);
			return array(
				'success' => false,
				'msg' => $err_msg . ' in SQL ' . $sql
			);
		}
		$archived_boegen = $obj->find_where("kartiergebiet_id = " . $akg->get_id());
		return array(
			'success' => true,
			'msg' => 'Archivierung der ' . $ba->get('layer_name_part') . ' in Archivkartiergebiet Id: ' . $akg->get_id() . ' erfolgreich rückgängig gemacht!<p>' . $msg,
			'num_assigned_boegen' => count($archived_boegen)
		);
	}
?>