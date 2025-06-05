async function create_archivkartiergebiet(e) {
  const kgId = e.dataset.kg_id;
  const kgBezeichnung = e.dataset.kg_bezeichnung;
  const kgLosnummer = e.dataset.kg_losnummer;
  const kgBemerkungen = e.dataset.kg_bemerkungen;
  const akId = e.dataset.ak_id;
  console.log('Creating archive kartiergebiet von mvbio_kartiergebiet_id: %s in archivekampagne_id: %s', kgId, akId);

  // ToDo: Request custom bezeichnung, losnummer and bemerkung of archivkartiergebiet in one single form
  const akg_bezeichnung = prompt("Bezeichnung des Kartiergebietes im Archiv", kgBezeichnung);
  const akg_losnummer = prompt("Losnummer des Kartiergebietes im Archiv", kgLosnummer);
  const akg_bemerkungen = prompt("Bemerkungen zum Kartiergebiet im Archiv", kgBemerkungen);

  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: 'create_archivkartiergebiet',
    kartiergebiet_id: kgId,
    akg_bezeichnung: akg_bezeichnung,
    akg_losnummer: akg_losnummer,
    akg_bemerkungen: akg_bemerkungen,
    archivkampagne_id: akId,
    only_main : 1,
    csrf_token: csrf_token
  };

  // console.log('url: ', url + new URLSearchParams(params).toString())

  try {
    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const json = await response.json();
    console.log(json);
    if (json.success) {
      const akg = json.data;
      // Add new created archivkartiergebiet to all archiv select lists 
      document.querySelectorAll(`.archivkartiergebiete-select[data-ak_id="${akId}"]`).forEach(function(selectField) {
        const option = document.createElement('option');
        option.value = akg.id;
        option.textContent = `${akg.bezeichnung} (${akg.id})`;
        selectField.appendChild(option);
        if (selectField.dataset.kg_id == kgId) { selectField.value = akg.id; }
      });

      // const selectField = document.getElementById(`archivkartiergebiete_${kgId}_${akId}`);
      // if (selectField) {
      //   const option = document.createElement('option');
      //   option.value = akg.id;
      //   option.textContent = `${akg.bezeichnung} (${akg.id})`;
      //   selectField.appendChild(option);
      //   selectField.value = akg.id;
      // }

      // Set selected archivkartiergebiet
      document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).innerHTML = akg.bezeichnung + ' (id: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=131&value_kartiergebiet_id=' + akg.id + '&operator_kartiergebiet_id==&csrf_token=' + csrf_token + '">' + akg.id + '</a>)';
      // hide select field and create button
      document.getElementById(`select_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      document.getElementById(`create_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      // show selected archivkartiergebiet and undo button
      document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';

      message([{'type': 'notice', 'msg': json.msg}]);
    } else {
      message([{'type': 'error', 'msg': json.msg}]);
    }
  } catch (error) {
    console.error(error.message);
  }
}

async function undo_archivkartiergebiet(e) {
  const kgId = e.dataset.kg_id;
  const akId = e.dataset.ak_id;
  const akgId = document.getElementById(`archivkartiergebiete_${kgId}_${akId}`).value;
  // console.log('Undo archiv kartiergebiet von mvbio_kartiergebiet_id: %s zu archivekartiergebiet_id: %s', kgId, akgId);

  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: 'undo_archivkartiergebiet',
    kartiergebiet_id: kgId,
    archivkartiergebiet_id: akgId,
    only_main : 1,
    csrf_token: csrf_token
  };

  // console.log('url: ', url + new URLSearchParams(params).toString())

  try {
    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const json = await response.json();
    // console.log(json);
    if (json.success) {
      document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      document.getElementById(`archivkartiergebiete_${kgId}_${akId}`).value = '';
      document.getElementById(`select_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      document.getElementById(`create_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      message([{'type': 'notice', 'msg': json.msg}]);
    } else {
      message([{'type': 'error', 'msg': json.msg}]);
    }
  } catch (error) {
    console.error(error.message);
  }
}

async function assign_kartiergebiet2archivkartiergebiet(e) {
  const kgId = e.dataset.kg_id;
  const akId = e.dataset.ak_id;
  const akgId = document.getElementById(`archivkartiergebiete_${kgId}_${akId}`).value;

  // console.log('Assign mvbio_kartiergebiet_id: %s zu archivekartiergebiet_id: %s in archivkampagne_id: %s', kgId, akgId, akId);

  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: 'assign_kartiergebiet2archivkartiergebiet',
    kartiergebiet_id: kgId,
    archivkartiergebiet_id: akgId,
    archivkampagne_id: akId,
    only_main : 1,
    csrf_token: csrf_token
  };

  // console.log('url: ', url + new URLSearchParams(params).toString())

  try {
    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const json = await response.json();
    // console.log(json);
    if (json.success) {
      const akg = json.data;
      document.getElementById(`select_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      document.getElementById(`assign_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      document.getElementById(`archivkartiergebiet_span_${kgId}_${akId}`).innerHTML = akg.bezeichnung + ' (id: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=131&value_kartiergebiet_id=' + akg.id + '&operator_kartiergebiet_id==&csrf_token=' + csrf_token + '">' + akg.id + '</a>)';
      document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      message([{'type': 'notice', 'msg': json.msg}]);
    } else {
      message([{'type': 'error', 'msg': json.msg}]);
    }
  } catch (error) {
    console.error(error.message);
  }
}

/**
 * Handle the change of the archivkartiergebiet select field.
 * @param {*} e 
 */
function change_archivkartiergebiet(e) {
  const kgId = e.dataset.kg_id;
  const akId = e.dataset.ak_id;
  const akgId = e.value;
  // console.log('Change archiv kartiergebiet von mvbio_kartiergebiet_id: %s zu archivekartiergebiet_id: %s in archivkampagne_id: %s', kgId, akgId, akId);

  document.getElementById(`assign_archivkartiergebiet_div_${kgId}_${akId}`).style.display = (akgId ? 'inline-block' : 'none');
  document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
  document.getElementById(`create_archivkartiergebiet_div_${kgId}_${akId}`).style.display = (akgId ? 'none' : 'inline-block');
}