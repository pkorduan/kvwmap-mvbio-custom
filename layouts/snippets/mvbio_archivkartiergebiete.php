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
      foreach ($kk->archivkampagnen as $ak) { ?>
        <th><?php echo $ak->get('bezeichnung') . ' (' . $ak->get('abk') . ')'; ?></th><?php
      } ?>
    </tr>
  </thead>
  <tbody><?php
    foreach ($kk->kartiergebiete as $kg) { ?>
      <tr>
        <td><?php echo $kg->get('id'); ?></td>
        <td><?php echo $kg->get('losnummer'); ?></td>
        <td><?php echo $kg->get('bezeichnung'); ?></td><?php
        if ($kg->archivierbar) {
          foreach ($kk->archivkampagnen as $ak) { ?>
            <td><?
              $assigned_archivkartiergebiet = $kg->assigned_archivkartiergebiet[$ak->get_id()];
              if ($assigned_archivkartiergebiet AND $assigned_archivkartiergebiet->is_archiviert) {
                echo '<span style="color: green; font-weight: bold">Bereits archiviert in Archivkartiergebiet ' . $assigned_archivkartiergebiet->get('bezeichnung') . ' (ID: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=' . KARTIERGEBIETE_TEMPLATE_LAYER_ID . '&value_kartiergebiet_id=' . $assigned_archivkartiergebiet->get_id() . '&operator_kartiergebiet_id==&csrf_token=' . $_SESSION['csrf_token'] . '">' . $assigned_archivkartiergebiet->get_id() . '</a>)</span><br>';
              }
              else {
                echo archivkartiergebietauswahl($kg, $ak); 
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

function archivkartiergebietauswahl($kg, $ak) {
  $html = '<div style="width: 273px">';
  $assigned_archivkartiergebiet = $kg->assigned_archivkartiergebiet[$ak->get_id()];
  $html .= '<div id="selected_archivkartiergebiet_div_' . $kg->get_id() . '_' . $ak->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'inline-block' : 'none') . '">';
  $html .= '<span id="archivkartiergebiet_span_' . $kg->get_id() . '_' . $ak->get_id() . '">
    ' . ($assigned_archivkartiergebiet ? $assigned_archivkartiergebiet->get('bezeichnung') . ' (id: <a href="index.php?go=Layer-Suche_Suchen&selected_layer_id=' . KARTIERGEBIETE_TEMPLATE_LAYER_ID . '&value_kartiergebiet_id=' . $assigned_archivkartiergebiet->get_id() . '&operator_kartiergebiet_id==&csrf_token=' . $_SESSION['csrf_token'] . '">' . $assigned_archivkartiergebiet->get_id() . '</a>)' : '')  . '
  </span>';
  $html .= '</div>';

  $html .= '<div id="select_archivkartiergebiet_div_' . $kg->get_id() . '_' . $ak->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'none' : 'inline-block') . '">';
  $html .= FormObject::createSelectField(
    'archivkartiergebiet_id', // name
    array_map(
      function($akg) {
        return array(
          'value' => $akg->get('id'),
          'output' => $akg->get('bezeichnung') . ' (' . $akg->get_id() . ')'
        );
      },
      $ak->archivkartiergebiete
    ), // options
    $assigned_archivkartiergebiet ? $assigned_archivkartiergebiet->get_id() : null, // ,
    1, // size
    'margin-top: 5px; width: 250px; margin-right: 10px', // style
    'change_archivkartiergebiet(this)', // onchange
    'archivkartiergebiete_' . $kg->get_id() . '_' . $ak->get_id(), // id
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
      'ak_id' => $ak->get_id()
    ) // data attributes
  );
  $html .= '<span data-tooltip="Zuordnung zum Kartiergebiet in der Archivkampagne oder Kartiergebiet als neues Kartiergebiet in dieser Kampagne anlegen."></span>';
  $html .= '</div>';

  $html .= '<div id="create_archivkartiergebiet_div_' . $kg->get_id() . '_' . $ak->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'none' : 'inline-block') . '">';
  $html .= '<button id="create_archivkartiergebiet_button_' . $kg->get_id() . '_' . $ak->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-kg_bezeichnung="' . $kg->get('bezeichnung') . '"  data-kg_losnummer="' . $kg->get('losnummer') . '" data-kg_bemerkungen="' . $kg->get('bemerkungen') . '" data-ak_id="' . $ak->get_id() . '" type="button" class="btn-primary"  style="margin-top: 5px;" title="Kartiergebiet im Archiv anlegen" onclick="create_archivkartiergebiet(this)">
    Neu anlegen
  </button>';
  $html .= '</div>';

  $html .= '<div id="assign_archivkartiergebiet_div_' . $kg->get_id() . '_' . $ak->get_id() . '" style="display: none">';
  $html .= '<button id="assign_archivkartiergebiet_button_' . $kg->get_id() . '_' . $ak->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-ak_id="' . $ak->get_id() . '" type="button" class="btn-primary" style="margin-top: 5px;" title="Kartiergebiet zu ausgewähltem Archivkartiergebiet zuordnen." onclick="assign_kartiergebiet2archivkartiergebiet(this)">
    Zuordung speichern
  </button>';
  $html .= '</div>';

  $html .= '<div id="undo_archivkartiergebiet_div_' . $kg->get_id() . '_' . $ak->get_id() . '" style="display: ' . ($assigned_archivkartiergebiet ? 'inline-block' : 'none') . '">';
  $html .= '<button id="undo_archivkartiergebiet_button_' . $ak->get_id() . '" data-kg_id="' . $kg->get_id() . '" data-ak_id="' . $ak->get_id() . '" type="button" class="btn-primary" style="margin-top: 5px; margin-right: 5px; margin-left: 5px;" title="Zuordnung des Kartiergebietes im Archiv rückgängig machen." onclick="undo_archivkartiergebiet(this)">
    Zuordnung löschen
  </button>';
  $html .= '<span data-tooltip="Zuordnung von Kartiergebiet zum Archiv entfernen."></span>';
  $html .= '</div>';

  $hmtl .= '</div>';
  return $html;
}
?>