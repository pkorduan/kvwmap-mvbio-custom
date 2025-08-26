async function archiv_boegen(e) { 
  const akgId = document.getElementById('archivkartiergebiet_id').value;
  const setActive = document.getElementById('set_active').checked;
  const pruefer = document.getElementById('pruefer').value;
  // console.log('Archiv kurzbögen für archivkartiergebiet_id: %s', akgId);
  if (!pruefer) {
    message([{ 'type':'error', 'msg' : 'Sie müssen erst einen Prüfer auswählen!'}]);
    return false;
  }

  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: `archiv_boegen`,
    archivkartiergebiet_id: akgId,
    pruefer: pruefer,
    set_active: (setActive ? 1 : 0),
    only_main : 1,
    csrf_token: csrf_token
  };

  try {
    console.log('url: ', url + new URLSearchParams(params).toString());
    document.getElementById('waitingdiv').style.display = 'inline-block';

    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const result = await response.json();
    console.log('Result: %o', result);
    if (result.success) {
      // Anzeige der zu archivierenden Bögen umschalten.
      console.log(document.getElementsByClassName(`assigned-div`));
      Array.from(document.getElementsByClassName(`assigned-div`)).forEach(el => el.style.display = 'none');
      document.getElementById(`num_archived_boegen`).innerHTML = result.num_archived_boegen;
      Array.from(document.getElementsByClassName(`archived-div`)).forEach(el => el.style.display = 'block');
 
      // Text "Archiviert" sichtbar setzen.
      // document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      // document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
      // document.getElementById(`archivkartiergebiete_${kgId}_${akId}`).value = '';
      // document.getElementById(`select_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
      // document.getElementById(`create_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';

      message([{ 'type': 'info', 'msg': result.msg }]);
    } else {
      message([{ 'type': 'error', 'msg': result.msg }]);
      console.error(result.msg);
    }
    document.getElementById('waitingdiv').style.display = 'none';
  } catch (error) {
    message([{ 'type' : 'error', 'msg': error.message }]);
    console.error(error.message);
    document.getElementById('waitingdiv').style.display = 'none';
  }
}

async function archiv_boegen_einzeln_nach_bogenart(e) {
  const akgId = document.getElementById('archivkartiergebiet_id').value;
  const setActive = document.getElementById('set_active').checked;
  const pruefer = document.getElementById('pruefer').value;
  // console.log('Archiv kurzbögen für archivkartiergebiet_id: %s', akgId);
  if (!pruefer) {
    message([{ 'type':'error', 'msg' : 'Sie müssen erst einen Prüfer auswählen!'}]);
    return false;
  }

  // Iteriere über die Bogenarten
  const url = 'index.php?';
  let params;
  document.getElementById('waitingdiv').style.display = 'inline-block';
  for (const bogenartId of bogenarten) {
    params = {
      go: 'show_snippet',
      snippet: 'mvbio_archivierung',
      action: `archiv_boegen`,
      archivkartiergebiet_id: akgId,
      pruefer: pruefer,
      set_active: (setActive ? 1 : 0),
      bogenart_id: bogenartId,
      only_main : 1,
      csrf_token: csrf_token
    };

    console.log('url: ', url + new URLSearchParams(params).toString());

  //   try {
  //     const response = await fetch(url + new URLSearchParams(params).toString());
  //     if (!response.ok) {
  //       throw new Error(`Response status: ${response.status}`);
  //     }
  //     const json = await response.json();
  //     // console.log(json);
  //     if (json.success) {
  //       // Anzeige der zu archivierenden Kurzbögen auf nicht sichtbar setzen.
  //       document.getElementById(`assigned-div_${bogenartId}`).style.display = 'none';
  //       document.getElementById(`num_archived_boegen_${bogenartId}`).innerHTML = json.num_archived_boegen;
  //       document.getElementById(`archived-div_${bogenartId}`).style.display = 'block';
  //       // Text "Archiviert" sichtbar setzen.

  // /*
  //       document.getElementById(`undo_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
  //       document.getElementById(`selected_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'none';
  //       document.getElementById(`archivkartiergebiete_${kgId}_${akId}`).value = '';
  //       document.getElementById(`select_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
  //       document.getElementById(`create_archivkartiergebiet_div_${kgId}_${akId}`).style.display = 'inline-block';
  // */
  //       message([{ 'type': 'info', 'msg': json.msg }]);
  //     } else {

  //       message([{ 'type': 'error', 'msg': json.msg }]);
  //       console.error(json.msg);
  //     }
  //   } catch (error) {
  //     message([{ 'type' : 'error', 'msg': error.message }]);
  //     console.error(error.message);
  //   }
  }
  document.getElementById('waitingdiv').style.display = 'none';
}

async function undo_archiv_boegen(e) {
  const akgId = document.getElementById('archivkartiergebiet_id').value;
  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: `undo_archiv_boegen`,
    archivkartiergebiet_id: akgId,
    only_main : 1,
    csrf_token: csrf_token
  };

  console.log('url: ', url + new URLSearchParams(params).toString());
  try {
    document.getElementById('waitingdiv').style.display = 'inline-block';
    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const result = await response.json();
    // console.log(result);
    if (result.success) {
      // Anzeige der zu archivierenden Kurzbögen auf nicht sichtbar setzen.
      Array.from(document.getElementsByClassName('assigned-div')).forEach(el => el.style.display = 'block');
      document.getElementById('num_assigned_boegen').innerHTML = result.num_assigned_boegen;
      Array.from(document.getElementsByClassName('archived-div')).forEach(el => el.style.display = 'none');
      message([{ 'type': 'info', 'msg': result.msg }]);
    } else {
      message([{ 'type': 'error', 'msg': result.msg }]);
      console.error(result.msg);
    }
    document.getElementById('waitingdiv').style.display = 'none';
  } catch (error) {
    message([{ 'type' : 'error', 'msg': error.message }]);
    console.error(error.message);
  }
}

async function undo_archiv_boegen_einzeln_nach_bogenart(e) {
  const akgId = document.getElementById('archivkartiergebiet_id').value;
  const bogenartId = e.dataset.bogenart_id;
  const url = 'index.php?';
  const params = {
    go: 'show_snippet',
    snippet: 'mvbio_archivierung',
    action: `undo_archiv_boegen`,
    archivkartiergebiet_id: akgId,
    bogenart_id: bogenartId,
    only_main : 1,
    csrf_token: csrf_token
  };

  console.log('url: ', url + new URLSearchParams(params).toString());
  try {
    document.getElementById('waitingdiv').style.display = 'inline-block';
    const response = await fetch(url + new URLSearchParams(params).toString());
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }
    const json = await response.json();
    // console.log(json);
    if (json.success) {
      // Anzeige der zu archivierenden Kurzbögen auf nicht sichtbar setzen.
      document.getElementById(`num_assigned_boegen_${bogenartId}`).innerHTML = json.num_assigned_boegen;
      document.getElementById(`assigned-div_${bogenartId}`).style.display = 'block';
      document.getElementById(`archived-div_${bogenartId}`).style.display = 'none';
      message([{ 'type': 'info', 'msg': json.msg }]);
    } else {
      message([{ 'type': 'error', 'msg': json.msg }]);
      console.error(json.msg);
    }
    document.getElementById('waitingdiv').style.display = 'none';
  } catch (error) {
    message([{ 'type' : 'error', 'msg': error.message }]);
    console.error(error.message);
  }
}