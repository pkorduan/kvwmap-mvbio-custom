$('#tr_240_fotos_0').on(
  'change',
  function() {
    var num_fotos = $('#240_fotos_0 div.raster_record,div.raster_record_open').length;
    $('#240_foto_0').val(num_fotos);
    $('#240_foto_0').next().html(num_fotos);
  }
);