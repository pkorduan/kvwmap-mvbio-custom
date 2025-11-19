<?
  if (!$kk OR !$kk->get_id()) {
    echo 'Das Kampagne-Objekt fehlt.'; exit;
  }
  include_once(CLASSPATH . 'FormObject.php');
?>
<script src="<? echo CUSTOM_PATH; ?>layouts/snippets/mvbio_archivkartiergebiete.js"></script>
<link rel="stylesheet" href="<?php echo BOOTSTRAP_PATH; ?>css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo BOOTSTRAPTABLE_PATH; ?>bootstrap-table.min.css" type="text/css">
<h2 style="margin: 10px">Archivierung von Kartiergebieten</h2>
<div style="margin-bottom: 10px">
  Kartiergebiete der Kampagne <span style="font-size: 16px; font-weight: bold">"<?php echo $kk->get('bezeichnung'); ?> (ID: <?php echo $kk->get_id(); ?>)"</span>
</div>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>ID<br><i class="fa fa-caret-up" aria-hidden="true" data-order="id" data-direction="ASC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i><i class="fa fa-caret-down" aria-hidden="true" data-order="id" data-direction="DESC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i></th>
      <th>Losnummer<br><i class="fa fa-caret-up" aria-hidden="true" data-order="losnummer" data-direction="ASC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i><i class="fa fa-caret-down" aria-hidden="true" data-order="losnummer" data-direction="DESC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i></th>
      <th>Kartiergebiet<br><i class="fa fa-caret-up" aria-hidden="true" data-order="bezeichnung" data-direction="ASC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i><i class="fa fa-caret-down" aria-hidden="true" data-order="bezeichnung" data-direction="DESC" onclick="sort_archivkartiergebiete(this, <? echo $kk->get_id(); ?>)"></i></th><?php
      foreach ($kk->archivkampagnen as $archivkampagne) { ?>
        <th><?php echo $archivkampagne->get('bezeichnung') . ' (' . $archivkampagne->get('abk') . ')'; ?><!--br><i class="fa fa-caret-up" aria-hidden="true" data-order="<? echo $archivkampagne->get('abk'); ?>" data-direction="ASC" onclick="sort_archivkartiergebiete(this, <? echo $archivkampagne->get_id(); ?>)"></i><i class="fa fa-caret-down" aria-hidden="true" data-order="<? echo $archivkampagne->get('abk'); ?>" data-direction="DESC" onclick="sort_archivkartiergebiete(this, <? echo $archivkampagne->get_id(); ?>)"></i//--></th><?php
      } ?>
    </tr>
  </thead>
  <tbody><?php
    foreach ($kk->kartiergebiete as $kg) { ?>
      <tr>
        <td><a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=302&value_kartiergebiet_id=<? echo $kg->get_id(); ?>&operator_kartiergebiet_id==&csrf_token=<? echo $_SESSION['csrf_token']; ?>" title="Kartiergebiet anzeigen"><?php echo $kg->get('id'); ?></a></td>
        <td><?php echo $kg->get('losnummer'); ?></td>
        <td><?php echo $kg->get('bezeichnung'); ?></td><?php
        if ($kg->archivierbar) {
          foreach ($kk->archivkampagnen as $archivkampagne) { ?>
            <td><?
              $assigned_archivkartiergebiet = $kg->assigned_archivkartiergebiet[$archivkampagne->get_id()];
              if ($assigned_archivkartiergebiet AND $assigned_archivkartiergebiet->is_archiviert) {
                // Kartiergebiet ist in dieser Archivkampagne bereits archiviert
                echo '<span style="color: green; font-weight: bold">Bereits archiviert in Archivkartiergebiet ' . $assigned_archivkartiergebiet->get('bezeichnung') . ' (ID: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=' . KARTIERGEBIETE_TEMPLATE_LAYER_ID . '&value_kartiergebiet_id=' . $assigned_archivkartiergebiet->get_id() . '&operator_kartiergebiet_id==&csrf_token=' . $_SESSION['csrf_token'] . '">' . $assigned_archivkartiergebiet->get_id() . '</a>)</span><br>';
              }
              else {
                // Kartiergebiet ist in dieser Archivkampagne noch nicht archiviert
                // echo 'archivkampagne_id: ' . $archivkampagne->get_id() . '<br>';
                // echo 'Num Kurzbögen: ' . $kg->sum_kartierobjekte->get('kurzboegen') . ' Grundbögen: ' . $kg->sum_kartierobjekte->get('grundboegen') . ' Grünlandbögen: ' . $kg->sum_kartierobjekte->get('gruenlandboegen') . '<br>';
                $unarchived_boegen = get_unarchived_boegen($this, $kg, $archivkampagne);
                if ($unarchived_boegen[0]->get('num_boegen') == 0) {
                  echo '<span style="color: orange; font-weight: bold">Keine Bögen zum Archivieren vorhanden!</span><br>';
                }
                else {
                  echo 'Anzahl zu archivierende Objekte: ' . $unarchived_boegen[0]->get('num_boegen') . '<br>';
                  echo archivkartiergebietauswahl($kg, $archivkampagne);
                }
              } ?>
            </td><?
          }
        }
        else { ?>
          <td colspan="<?php echo count($kk->archivkampagnen); ?>" style="text-align: left;">
            <span style="color: red; font-weight: bold">Archivierung nicht möglich.</span><br>
            Anzahl noch nicht zur Archivierung freigegebener Objekte<?
            if ($kg->sum_kartierobjekte->get('nicht_archivierbar') > 0) { ?>
              <br>Kartierobjekte: <? echo $kg->sum_kartierobjekte->get('nicht_archivierbar');
            }
            if ($kg->sum_verlustobjekte->get('nicht_archivierbar') > 0) { ?>
              <br>Verlustobjekte: <? echo $kg->sum_verlustobjekte->get('nicht_archivierbar');
            } ?>
          </td><?php
        } ?>
      </tr><?php
    } ?>
  </tbody>
</table><?

function archivkartiergebietauswahl($kg, $archivkampagne) {
  $html = '<div style="width: 273px">';
  $assigned_archivkartiergebiet = $kg->assigned_archivkartiergebiet[$archivkampagne->get_id()];
  $html .= '<div id="selected_archivkartiergebiet_div_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'inline-block' : 'none') . '">';
  $html .= '<span id="archivkartiergebiet_span_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '">
    ' . ($assigned_archivkartiergebiet ? $assigned_archivkartiergebiet->get('bezeichnung') . ' (id: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=' . KARTIERGEBIETE_TEMPLATE_LAYER_ID . '&value_kartiergebiet_id=' . $assigned_archivkartiergebiet->get_id() . '&operator_kartiergebiet_id==&csrf_token=' . $_SESSION['csrf_token'] . '">' . $assigned_archivkartiergebiet->get_id() . '</a>)' : '')  . '
  </span>';
  $html .= '</div>';

  $html .= '<div id="select_archivkartiergebiet_div_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'none' : 'inline-block') . '">';
  $html .= FormObject::createSelectField(
    'archivkartiergebiet_id', // name
    array_map(
      function($archivkartiergebiet) {
        return array(
          'value' => $archivkartiergebiet->get('id'),
          'output' => $archivkartiergebiet->get('bezeichnung') . ' (' . $archivkartiergebiet->get_id() . ')'
        );
      },
      array_filter($archivkampagne->archivkartiergebiete, function($archivkartiergebiet) { return !$archivkartiergebiet->is_archiviert; })
    ), // options
    $assigned_archivkartiergebiet ? $assigned_archivkartiergebiet->get_id() : null, // value
    1, // size
    'margin-top: 5px; width: 250px; margin-right: 10px;', // style
    'change_archivkartiergebiet(this)', // onchange
    'archivkartiergebiete_' . $kg->get_id() . '_' . $archivkampagne->get_id(), // id
    '', // multiple
    'archivkartiergebiete-select', // class
    '-- Kartiergebiet wählen --', // first option
    '', // option_style
    '', // option_class
    '', // onclick
    '', // onmouseenter
    'Kartiergebiet auswählen', // title
    array(
      'kg_id' => $kg->get_id(),
      'ak_id' => $archivkampagne->get_id()
    ) // data attributes
  );

  $html .= '<span data-tooltip="Zuordnung zum Kartiergebiet in der Archivkampagne oder Kartiergebiet als neues Kartiergebiet in dieser Kampagne anlegen."></span>';
  $html .= '</div>';

  $html .= '<div id="create_archivkartiergebiet_div_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'none' : 'inline-block') . '">';
  $html .= '<button id="create_archivkartiergebiet_button_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-kg_bezeichnung="' . $kg->get('bezeichnung') . '"  data-kg_losnummer="' . $kg->get('losnummer') . '" data-kg_bemerkungen="' . $kg->get('bemerkungen') . '" data-ak_id="' . $archivkampagne->get_id() . '" type="button" class="btn-primary"  style="margin-top: 5px;" title="Kartiergebiet im Archiv anlegen" onclick="create_archivkartiergebiet(this)">
    Neu anlegen
  </button>';
  $html .= '</div>';

  $html .= '<div id="assign_archivkartiergebiet_div_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" style="display: none">';
  $html .= '<button id="assign_archivkartiergebiet_button_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-ak_id="' . $archivkampagne->get_id() . '" type="button" class="btn-primary" style="margin-top: 5px;" title="Kartiergebiet zu ausgewähltem Archivkartiergebiet zuordnen." onclick="assign_kartiergebiet2archivkartiergebiet(this)">
    Zuordung speichern
  </button>';
  $html .= '</div>';

  $html .= '<div id="undo_archivkartiergebiet_div_' . $kg->get_id() . '_' . $archivkampagne->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'inline-block' : 'none') . '">';
  $html .= '<button id="undo_archivkartiergebiet_button_' . $archivkampagne->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-ak_id="' . $archivkampagne->get_id() . '" type="button" class="btn-primary" style="margin-top: 5px; margin-right: 5px; margin-left: 5px;" title="Zuordnung des Kartiergebietes im Archiv rückgängig machen." onclick="undo_archivkartiergebiet(this)">
    Zuordnung löschen
  </button>';
  $html .= '<span data-tooltip="Zuordnung von Kartiergebiet zum Archiv entfernen."></span>';
  $html .= '</div>';

  $hmtl .= '</div>';
  return $html;
}
?>