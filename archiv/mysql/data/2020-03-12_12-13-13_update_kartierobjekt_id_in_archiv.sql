# Lösche Layer Grundbögenfotos
# Lösche Layer Kurzbögenfotos
# Update pfad zur Abfrage der fotos in Layer Archivfotos
UPDATE
  layer
SET
  pfad = 'SELECT
  id,
  kartierobjekt_id,
  datei
FROM
  fotos
WHERE
  true'
WHERE
  Layer_ID = 158;

# Ändere den Attributnamen in den Attributeinstellungen
UPDATE
  layer_attributes
SET
  name = 'kartierobjekt_id',
  real_name = 'kartierobjekt_id',
  alias = 'Kartierobjekt ID',
  tooltip = 'ID des Kartierobjektes zu dem das Bild gehört.'
WHERE
  layer_id = 158 AND
  name = 'kartierung_id';

UPDATE
  layer_attributes2stelle
SET
  attributename = 'kartierobjekt_id'
WHERE
  layer_id = 158 AND
  attributename = 'kartierung_id';

# Passe die Verknüpfungen an
UPDATE
  layer_attributes
SET
  alias = 'Fotos',
  form_element_type = 'SubFormEmbeddedPK',
  options = '158,kartierobjekt_id,datei;embedded'
WHERE
  options LIKE '158,kartierung_id,datei;embedded';

UPDATE
  layer_attributes
SET
  alias = 'Fotos',
  form_element_type = 'SubFormEmbeddedPK',
  options = '158,kartierobjekt_id,datei;embedded'
WHERE
  options = '244,bogen_id,datei;embedded';

UPDATE
  layer_attributes
SET
  alias = 'Fotos',
  form_element_type = 'SubFormEmbeddedPK',
  options = '158,kartierobjekt_id,datei;embedded'
WHERE
  options = '245,bogen_id,datei;embedded';

UPDATE
  layer_attributes
SET
  alias = 'Fotos',
  form_element_type = 'SubFormEmbeddedPK',
  options = '158,kartierobjekt_id,datei;embedded'
WHERE
  options = '109,kartierung_id,datei;embedded' AND
  layer_id > 160;

UPDATE
  layer
SET
  pfad = replace(pfad, 'AS uebernehmen,', 'AS uebernehme, \'\' AS uebernehme_als_verlustbogen,')
WHERE
  pfad like '%AS uebernehmen,%' AND
  pfad NOT LIKE '%uebernehme_als_verlustbogen%';

DELETE FROM
  layer_attributes
WHERE
  name = 'uebernehmen';

INSERT INTO layer_attributes
SELECT
  l.layer_id,
  n.`name`, n.`real_name`, n.`tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `saveable`, `nullable`,
  `length`, `decimal_length`, `default`, `form_element_type`, `options`, n.`alias`, `alias_low-german`, `alias_english`,
  `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`,
  `mandatory`, `quicksearch`, `visible`, `kvp`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, n.`privileg`,
  n.`query_tooltip`
FROM
  layer l,
  (
    SELECT
      `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `saveable`, `nullable`, `length`,
      `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`,
      `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`,
      `visible`, `kvp`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`
    FROM
      `layer_attributes`
    WHERE
      Layer_ID = 169 AND
      name like '%uebern%'
  ) n
WHERE
  l.Gruppe IN (50, 53, 54, 55, 56) AND
  l.layer_ID != 169 AND
  l.Name NOT LIKE '%Kartier%'
ORDER BY
  l.Layer_ID