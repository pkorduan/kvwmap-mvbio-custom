--
-- Benenne vorhandenen Layer Kartierer um in Nutzer und trage anderen pfad ein
--
UPDATE
  `layer`
SET
  `name` = 'Nutzer',
  `pfad` = 'SELECT
  id,
  id AS nutzer_id,
  user_id,
  name,
  '' AS kartierer
FROM
  nutzer
WHERE true'
WHERE
  `name` LIKE 'Kartierer';

SET @layer_id_nutzer = (SELECT `Layer_ID` FROM `layer` WHERE `name` LIKE 'Nutzer');

--
-- Lege Layer für Kartierer an als Zuordnung zwischen Nutzern und Kartiergebieten
--
SET @layer_id_kartierer = 243;
SET @stelle_id_1 = (SELECT ID FROM stelle WHERE Bezeichnung = 'Administration');
SET @stelle_id_4 = (SELECT ID FROM stelle WHERE Bezeichnung = 'Koordinierung');
SET @stelle_id_5 = (SELECT ID FROM stelle WHERE Bezeichnung = 'Prüfung');
SET @connection = 'host=pgsql user=kvwmap password=xxxxxx dbname=kvwmapsp';

-- Layer Kartierer
INSERT INTO layer (`Layer_ID`, `Name`, `alias`, `Datentyp`, `Gruppe`, `pfad`, `maintable`, `Data`, `schema`, `document_path`, `tileindex`, `tileitem`, `labelangleitem`, `labelitem`, `labelmaxscale`, `labelminscale`, `labelrequires`, `connection`, `connection_id`, `printconnection`, `connectiontype`, `classitem`, `filteritem`, `tolerance`, `toleranceunits`, `epsg_code`, `template`, `queryable`, `transparency`, `drawingorder`, `minscale`, `maxscale`, `offsite`, `ows_srs`, `wms_name`, `wms_server_version`, `wms_format`, `wms_connectiontimeout`, `wms_auth_username`, `wms_auth_password`, `wfs_geom`, `selectiontype`, `querymap`, `logconsume`, `processing`, `kurzbeschreibung`, `datenherr`, `metalink`, `privileg`, `trigger_function`, `sync`) VALUES (@layer_id_kartierer, 'Kartierer', '', '5', '23', 'SELECT
  kartiergebiet_id,
  nutzer_id
FROM
  kartierer
WHERE
  true', 'kartierer', '', 'mvbio', '', '', '', '', '', NULL, NULL, '', @connection, '2', '', '6', '', '', '3', 'pixels', '5650', '', '1', NULL, NULL, '-1', '-1', '', '', '', '', '', '60', '', '', '', '', '0', '0', '', '', '', '', '2', '', '0');

-- Zuordnung Layer Kartierer zu Stelle 1
INSERT INTO used_layer (`Layer_ID`, `Stelle_ID`, `queryable`, `drawingorder`, `legendorder`, `minscale`, `maxscale`, `offsite`, `transparency`, `postlabelcache`, `Filter`, `template`, `header`, `footer`, `symbolscale`, `requires`, `logconsume`, `privileg`, `export_privileg`, `start_aktiv`, `use_geom`) VALUES (@layer_id_kartierer, @stelle_id_1, '1', '0', NULL, '-1', '-1', '', NULL, '', '', '', '', '', NULL, NULL, '0', '2', '1', '0', '1');

-- Zuordnung Layer Kartierer zu Stelle 4
INSERT INTO used_layer (`Layer_ID`, `Stelle_ID`, `queryable`, `drawingorder`, `legendorder`, `minscale`, `maxscale`, `offsite`, `transparency`, `postlabelcache`, `Filter`, `template`, `header`, `footer`, `symbolscale`, `requires`, `logconsume`, `privileg`, `export_privileg`, `start_aktiv`, `use_geom`) VALUES (@layer_id_kartierer, @stelle_id_4, '1', '0', NULL, '-1', '-1', '', NULL, '', '', '', '', '', NULL, NULL, '0', '2', '1', '0', '1');

-- Zuordnung Layer Kartierer zu Stelle 5
INSERT INTO used_layer (`Layer_ID`, `Stelle_ID`, `queryable`, `drawingorder`, `legendorder`, `minscale`, `maxscale`, `offsite`, `transparency`, `postlabelcache`, `Filter`, `template`, `header`, `footer`, `symbolscale`, `requires`, `logconsume`, `privileg`, `export_privileg`, `start_aktiv`, `use_geom`) VALUES (@layer_id_kartierer, @stelle_id_5, '1', '0', NULL, '-1', '-1', '', NULL, '', '', '', '', '', NULL, NULL, '0', '2', '1', '0', '1');

-- Attribut kartiergebiet_id des Layers Kartierer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_kartierer, 'kartiergebiet_id', 'kartiergebiet_id', 'kartierer', 'kartierer', 'int4', '', 'PRIMARY KEY', '0', '32', '0', '', 'Auswahlfeld', 'SELECT
  kg.id AS value,
  kk.abk || \' - \' || kg.bezeichnung AS output
FROM
  mvbio.kartiergebiete kg JOIN
  mvbio.kampagnen kk ON kg.kampagne_id = kk.id
ORDER BY
  kk.abk || \' - \' || kg.bezeichnung', 'Kartiergebiete', '', '', '', '', '', '', '0', '0', NULL, '0', '0', NULL, '1', '', '', '', '0', '1', '0');

-- Attribut nutzer_id des Layers Kartierer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_kartierer, 'nutzer_id', 'nutzer_id', 'kartierer', 'kartierer', 'int4', '', 'PRIMARY KEY', '0', '32', '0', '', 'Auswahlfeld', 'SELECT
  id AS value,
  name AS output
FROM
  mvbio.nutzer', 'Nutzer', '', '', '', '', '', '', '1', '0', NULL, '0', '0', NULL, '1', '', '', '', '1', '0', '0');

-- Zuordnung der Layerattribute des Layers Kartierer zur Stelle 1
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_1, 'kartiergebiet_id', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_1, 'nutzer_id', '0', '0');

-- Zuordnung der Layerattribute des Layers Kartierer zur Stelle 4
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_4, 'kartiergebiet_id', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_4, 'nutzer_id', '0', '0');

-- Zuordnung der Layerattribute des Layers Kartierer zur Stelle 5
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_5, 'kartiergebiet_id', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_kartierer, @stelle_id_5, 'nutzer_id', '0', '0');

--
-- Nehme Änderungen an Layer Kartierer vor
--

DELETE FROM `layer_attributes` WHERE `layer_id` = @layer_id_nutzer;
-- Attribut id des Layers Nutzer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_nutzer, 'id', 'id', 'nutzer', 'nutzer', 'int4', '', 'PRIMARY KEY', '1', '32', '0', '', 'Text', '', '', '', '', '', '', '', '', '0', '0', NULL, '0', '0', NULL, '0', '', '', '', '0', '0', '0');

-- Attribut kartierer des Layers Nutzer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_nutzer, 'kartierer', '', '', '', 'not_saveable', '', '', NULL, NULL, NULL, '', 'SubFormEmbeddedPK', concat(@layer_id_kartierer, ',nutzer_id, kartiergebiet_id;embedded'), 'Eingesetzt als Kartier in Kartiergebiet', '', '', '', '', '', '', '0', '0', NULL, '0', '0', NULL, '1', '', '', '', '4', '0', '0');

-- Attribut name des Layers Nutzer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_nutzer, 'name', 'name', 'nutzer', 'nutzer', 'varchar', '', '', '1', NULL, NULL, '', 'Text', '', 'Vor- und Zuname', '', '', '', '', 'Vor und Zuname können frei gewählt und ggf. auch annonymisiert werden.', '', '0', '0', NULL, '0', '0', NULL, '1', '', '', '', '3', '0', '0');

-- Attribut nutzer_id des Layers Nutzer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_nutzer, 'nutzer_id', 'id', 'nutzer', 'nutzer', 'int4', '', 'PRIMARY KEY', '1', '32', '0', '', 'Text', '', 'ID des Nutzers', '', '', '', '', 'Die ID des Nutzers für Kartierungen. Sie muss übereinstimmen mit der ID des kvwmap login, damit an Hand der richtigen id gefiltert werden kann.', '', '0', '0', NULL, '0', '0', NULL, '1', '', '', '', '1', '0', '0');

-- Attribut user_id des Layers Nutzer
INSERT INTO layer_attributes (`layer_id`, `name`, `real_name`, `tablename`, `table_alias_name`, `type`, `geometrytype`, `constraints`, `nullable`, `length`, `decimal_length`, `default`, `form_element_type`, `options`, `alias`, `alias_low-german`, `alias_english`, `alias_polish`, `alias_vietnamese`, `tooltip`, `group`, `arrangement`, `labeling`, `raster_visibility`, `dont_use_for_new`, `mandatory`, `quicksearch`, `visible`, `vcheck_attribute`, `vcheck_operator`, `vcheck_value`, `order`, `privileg`, `query_tooltip`) VALUES (@layer_id_nutzer, 'user_id', 'user_id', 'nutzer', 'nutzer', 'int4', '', '', '1', '32', '0', '', 'Text', '', 'ID des kvwmap login', '', '', '', '', 'Die ID des für kvwmap registrierten Nutzers. Die ID findet man in der Nutzerverwaltung unter Nutzer anzeigen.', '', '0', '0', NULL, '0', '0', NULL, '1', '', '', '', '2', '0', '0');

DELETE FROM layer_attributes2stelle WHERE `layer_id` = @layer_id_nutzer;
-- Zuordnung der Layerattribute des Layers Nutzer zur Stelle 1
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_1, 'id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_1, 'kartierer', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_1, 'name', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_1, 'nutzer_id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_1, 'user_id', '1', '0');

-- Zuordnung der Layerattribute des Layers Nutzer zur Stelle 4
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_4, 'id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_4, 'kartierer', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_4, 'name', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_4, 'nutzer_id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_4, 'user_id', '1', '0');

-- Zuordnung der Layerattribute des Layers Nutzer zur Stelle 5
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_5, 'id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_5, 'kartierer', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_5, 'name', '1', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_5, 'nutzer_id', '0', '0');
INSERT INTO layer_attributes2stelle (`layer_id`, `stelle_id`, `attributename`, `privileg`, `tooltip`) VALUES (@layer_id_nutzer, @stelle_id_5, 'user_id', '1', '0');