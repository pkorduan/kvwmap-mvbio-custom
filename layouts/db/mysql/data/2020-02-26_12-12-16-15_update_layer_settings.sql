/*
SELECT
  concat('UPDATE layer
SET
  drawingorder = ', drawingorder, ',
  pfad = \'', pfad, '\',
  data = \'', data, '\'
WHERE Layer_ID = ', Layer_ID, ';')
FROM
  layer
WHERE
  drawingorder IS NOT NULL
*/

UPDATE layer
SET
  drawingorder = 0,
  pfad = '',
  data = ''
WHERE Layer_ID = 1;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 2 THEN -1 ELSE coalesce(bo.kartierung_id, 0) END as bewertung_id_offenland, '' as bewerten_offenland, '' as bewertung_offenland, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 5 THEN -1 ELSE coalesce(bm.kartierung_id, 0) END as bewertung_id_moore, '' as bewerten_moore, '' as bewertung_moore, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 1 THEN -1 ELSE coalesce(bk.kartierung_id, 0) END as bewertung_id_kueste, '' as bewerten_kueste, '' as bewertung_kueste, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 4 THEN -1 ELSE coalesce(bs.kartierung_id, 0) END as bewertung_id_still, '' as bewerten_still, '' as bewertung_still, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 3 THEN -1 ELSE coalesce(bf.kartierung_id, 0) END as bewertung_id_fliess, '' as bewerten_fliess, '' as bewertung_fliess, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.koordinator_rueckweisung,b.koordinator_korrekturhinweis,b.kommentar_zum_korrekturhinweis,b.pruefer_rueckweisung,b.pruefer_pruefhinweis,b.kommentar_zum_pruefhinweis, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id,  lrt_gr, lrt_code,
  label,
  CASE
    WHEN $stelle_id = 1
    THEN false
    ELSE
      ba.aenderungsberechtigte_stelle_id != $stelle_id OR
      CASE
        WHEN $stelle_id = 3
        THEN b.user_id != $user_id
        ELSE false
      END
  END AS editiersperre,
  nicht_begehbar,
  '' as speichern_link,
  biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, b.id as kartierung_id, schutz_bio, schutz_ffh, bntk_code, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' as nc, vegeinheit, uc1, uc2, ost_west_lage, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23, nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' AS pflanzen, fauna, literatur, e_datum, l_datum, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, old_id, unb, altbestand,
  b.stelle_id,
  user_id,
  kartierer,
  kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter,
  b.created_at,
  b.created_from,
  b.updated_at,
  b.updated_from,
  b.geom
FROM
  kartierobjekte b JOIN
  code_bearbeitungsstufen ba ON b.bearbeitungsstufe = ba.stufe JOIN
  kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN
  kampagnen kk ON kg.kampagne_id = kk.id JOIN
  kartierebenen ke ON b.kartierebene_id = ke.id LEFT JOIN
  bewertungen_stillgewaesser bs ON bs.kartierung_id = b.id LEFT JOIN
  bewertungen_fliessgewaesser bf ON bf.kartierung_id = b.id LEFT JOIN
  bewertungen_kueste bk ON bk.kartierung_id = b.id LEFT JOIN
  bewertungen_moore bm ON bm.kartierung_id = b.id LEFT JOIN
  bewertungen_offenland bo ON bo.kartierung_id = b.id
WHERE
  ($kampagne_id = 0 OR $kampagne_id = kg.kampagne_id) AND
  ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.bogenart_id, b.hc, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, CASE WHEN b.nicht_begehbar IS NOT NULL AND b.nicht_begehbar then 'nicht_begehbar' ELSE 'kartiert' END AS nicht_begehbar, b.stelle_id, b.user_id, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE  ($kampagne_id = 0 OR $kampagne_id = kg.kampagne_id) AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 105;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT
  kartierung_id,
  pflanzenart_id,
  gsl.abbreviat,
  dzv, cf, fsk, rl, tax, bav
FROM
  pflanzenvorkommen pv JOIN
  pflanzenarten_gsl gsl ON pv.pflanzenart_id = gsl.species_nr
WHERE
  true
ORDER BY (CASE WHEN dzv = 'D' THEN 1 ELSE CASE WHEN dzv = 'Z' THEN 2 ELSE 3 END END)',
  data = ''
WHERE Layer_ID = 106;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT
  kartierung_id,
  pflanzenart_id,
  pflanzenart_name || CASE WHEN cf = 'C' THEN ' (cf)' ELSE '' END AS abbreviat,
  fsk, rl, bav,
  gsl_version
FROM
  pflanzenvorkommen
WHERE
  dzv = 'Z'
ORDER BY
  abbreviat',
  data = ''
WHERE Layer_ID = 107;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM fotos WHERE true',
  data = ''
WHERE Layer_ID = 109;	
UPDATE layer
SET
  drawingorder = 3010,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche,  schutz_bio, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, hc, uc1, uc2, hcp, '' as dummy1, '' as dummy2, vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna, e_datum, l_datum, bearbeiter, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.id, b.kartierobjekt_id, b.kartierer, userid, unb, altbestand, b.geom FROM kurzboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE 1=1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter FROM archiv.kurzboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id) as foo using unique oid using srid=5650 '
WHERE Layer_ID = 110;	
UPDATE layer
SET
  drawingorder = 0,
  pfad = '',
  data = ''
WHERE Layer_ID = 119;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM pflanzenarten WHERE true ORDER BY art',
  data = ''
WHERE Layer_ID = 120;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  true',
  data = 'geom from (SELECT id, abk, geom FROM archiv.kampagnen) as foo using unique id using srid=5650'
WHERE Layer_ID = 122;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, ba.bezeichnung, b.label, b.giscode, b.biotopname, b.nummer, b.standort, b.la_sper, b.la_sp_txt, b.fb_id, b.flaeche, b.lfd_nr, b.schutz_bio, b.alt_giscod, b.alt_lfd_nr, b.alt_bearb, b.alt_datp20, b.hc, b.hcp, '' nc, (nc.code)[1] AS nc1, (nc.flaechendeckung_prozent)[1] AS ncp1,   (nc.code)[2] AS nc2, (nc.flaechendeckung_prozent)[2] AS ncp2,   (nc.code)[3] AS nc3, (nc.flaechendeckung_prozent)[3] AS ncp3,   (nc.code)[4] AS nc4, (nc.flaechendeckung_prozent)[4] AS ncp4,   (nc.code)[5] AS nc5, (nc.flaechendeckung_prozent)[5] AS ncp5,   (nc.code)[6] AS nc6, (nc.flaechendeckung_prozent)[6] AS ncp6,   (nc.code)[7] AS nc7, (nc.flaechendeckung_prozent)[7] AS ncp7,   (nc.code)[8] AS nc8, (nc.flaechendeckung_prozent)[8] AS ncp8, b.uc1, b.uc2,  '' as dummy1, '' as dummy2, b.vegeinheit, b.wert_krit_1, b.wert_krit_9, b.wert_krit_2, b.wert_krit_10, b.wert_krit_3, b.wert_krit_11, b.wert_krit_4, b.wert_krit_12, b.wert_krit_5, b.wert_krit_13, b.wert_krit_6, b.wert_krit_14, b.wert_krit_7, b.wert_krit_15, b.wert_krit_8, b.wert_krit_16, b.gefaehrdg, '' AS gefcode, b.ohnegefahr, b.empfehlung, '' AS empfcode, b.fauna, b.e_datum, b.l_datum, b.bearbeiter, b.loeschen, b.druck, b.aend_datum, b.korrektur, b.geprueft, b.pruefer, b.pruefdatum, b.lock, b.status, b.version, b.import_table, b.id, b.kartierobjekt_id AS kartierung_id, b.kartierer, b.userid, b.unb, b.altbestand, b.geom FROM erfassungsboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id LEFT JOIN archiv.biotoptypen_nebencodes_agg nc ON b.id = nc.kartierung_id JOIN mvbio.bogenarten ba ON b.bogenart_id = ba.id WHERE 1=1',
  data = 'geom from (SELECT geom, oid, id, kartierobjekt_id, giscode, biotopname FROM archiv.erfassungsboegen) as foo using unique id using srid=5650'
WHERE Layer_ID = 124;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT '' as zum_kartierobjekt, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, lrt_gr, lrt_code, label, b.bearbeitungsstufe AS bearbeitungsstufe_bogen, bs.bearbeitungsstufe AS bearbeitungsstufe_kartierer, bs.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  bs.bearbeitungsstufe AS bearbeitungsstufe_pruefer, bs.bearbeitungsstufe > 2 OR b.user_id != $user_id AS editiersperre_kartierer, bs.bearbeitungsstufe != 3 AS editiersperre_koordinator, bs.bearbeitungsstufe != 4 AS editiersperre_pruefer, bs.pruefhinweis_koordinator, bs.pruefhinweis_pruefer, bs.rueckweisung, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, ''  gefcode, beschreibg, nutzintens_1, nutzintens_2, nutzintens_3, nutzintens_4,e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, old_id, unb, altbestand, bs.kartierung_id, b.stelle_id, b.user_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom, '' as t1, t111_1, t112_1, t113_1, t121_1, t121_2, t131_1_1, t131_1_2,        t131_2_1, t131_2_2, t131_3, t132_1, t132_2, '' as t2, t211_1_1, t211_1_2, t211_1_3, t211_3_1,        t212_1, t22_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4,        t311_1_5, t311_1_6 as t311_1_6_1, t321_1, t321_2, t322_1, t322_2, t322_3,t322_4, t323_1,        t324_1, t325_1, t326_1, t327_1,t328_1_2, t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungen_offenland bs JOIN kartierobjekte b ON b.id = bs.kartierung_id JOIN kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN kartierebenen ke ON b.kartierebene_id = ke.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.stelle_id, b.user_id, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 160;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM code_habitat_strukturen WHERE true ORDER BY code',
  data = ''
WHERE Layer_ID = 125;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT kartierung_id, code FROM habitatvorkommen WHERE true',
  data = ''
WHERE Layer_ID = 126;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM code_beeintraechtigungen_gefaehrdungen WHERE true ORDER BY code',
  data = ''
WHERE Layer_ID = 127;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT kartierung_id, code FROM beeintraechtigungen_gefaehrdungen WHERE true',
  data = ''
WHERE Layer_ID = 128;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT kartierung_id, code FROM empfehlungen_massnahmen WHERE true',
  data = ''
WHERE Layer_ID = 129;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM code_empfehlungen_massnahmen WHERE true ORDER BY code',
  data = ''
WHERE Layer_ID = 130;	
UPDATE layer
SET
  drawingorder = 3010,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  true',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id) as foo using unique id using srid=5650'
WHERE Layer_ID = 131;	
UPDATE layer
SET
  drawingorder = 3010,
  pfad = 'SELECT kg.id AS kartiergebiet_id, kg.kampagne_id, '' AS zur_kampagne, kg.losnummer, kg.bezeichnung, '' kartierer, kg.bemerkungen,  kk.abk || kg.bezeichnung AS filter, kk.abk, kg.geom FROM  kartiergebiete kg JOIN  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  ($kampagne_id = 0 OR $kampagne_id = kg.kampagne_id) AND
  ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung AS kartiergebiet, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM mvbio.kartiergebiete kg JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kampagne_id = 0 OR $kampagne_id = kg.kampagne_id) AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique id using srid=5650'
WHERE Layer_ID = 147;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT b.kartierung_id, b.code, b.flaechendeckung_prozent, b.vegeinheit FROM biotoptypen_nebencodes b WHERE true',
  data = ''
WHERE Layer_ID = 132;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM code_biotoptypen WHERE true ORDER BY code',
  data = ''
WHERE Layer_ID = 133;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT abk, bezeichnung, bogenarten FROM kartierebenen WHERE true',
  data = ''
WHERE Layer_ID = 134;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT stufe, stand, beschreibung FROM code_bearbeitungsstufen WHERE true',
  data = ''
WHERE Layer_ID = 135;	
UPDATE layer
SET
  drawingorder = 10,
  pfad = 'SELECT * FROM tk10av WHERE true',
  data = 'the_geom from (SELECT gid, name, the_geom from mvbio.tk10av) as foo using unique gid using srid=5650'
WHERE Layer_ID = 136;	
UPDATE layer
SET
  drawingorder = 3080,
  pfad = 'SELECT CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 2 THEN -1 ELSE coalesce(bo.kartierung_id, 0) END as bewertung_id_offenland, '' as bewerten_offenland, '' as bewertung_offenland, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 5 THEN -1 ELSE coalesce(bm.kartierung_id, 0) END as bewertung_id_moore, '' as bewerten_moore, '' as bewertung_moore, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 1 THEN -1 ELSE coalesce(bk.kartierung_id, 0) END as bewertung_id_kueste, '' as bewerten_kueste, '' as bewertung_kueste, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 4 THEN -1 ELSE coalesce(bs.kartierung_id, 0) END as bewertung_id_still, '' as bewerten_still, '' as bewertung_still, CASE WHEN kartierebene_id != 2 OR lrt_gr::integer != 3 THEN -1 ELSE coalesce(bf.kartierung_id, 0) END as bewertung_id_fliess, '' as bewerten_fliess, '' as bewertung_fliess, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.koordinator_rueckweisung,b.koordinator_korrekturhinweis,b.kommentar_zum_korrekturhinweis,b.pruefer_rueckweisung,b.pruefer_pruefhinweis,b.kommentar_zum_pruefhinweis, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id,  lrt_gr, lrt_code,
  label,
  CASE
    WHEN $stelle_id = 1
    THEN false
    ELSE
      ba.aenderungsberechtigte_stelle_id != $stelle_id OR
      CASE
        WHEN $stelle_id = 3
        THEN b.user_id != $user_id
        ELSE false
      END
  END AS editiersperre,
  nicht_begehbar,
  '' as speichern_link,
  biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, b.id as kartierung_id, schutz_bio, schutz_ffh, bntk_code, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' as nc, vegeinheit, uc1, uc2, ost_west_lage, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23, nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' AS pflanzen, fauna, literatur, e_datum, l_datum, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, old_id, unb, altbestand,
  b.stelle_id,
  user_id,
  kartierer,
  kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter,
  b.created_at,
  b.created_from,
  b.updated_at,
  b.updated_from,
  b.geom
FROM
  kartierobjekte b JOIN
  code_bearbeitungsstufen ba ON b.bearbeitungsstufe = ba.stufe JOIN
  kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN
  kampagnen kk ON kg.kampagne_id = kk.id JOIN
  kartierebenen ke ON b.kartierebene_id = ke.id LEFT JOIN
  bewertungen_stillgewaesser bs ON bs.kartierung_id = b.id LEFT JOIN
  bewertungen_fliessgewaesser bf ON bf.kartierung_id = b.id LEFT JOIN
  bewertungen_kueste bk ON bk.kartierung_id = b.id LEFT JOIN
  bewertungen_moore bm ON bm.kartierung_id = b.id LEFT JOIN
  bewertungen_offenland bo ON bo.kartierung_id = b.id
WHERE
  ($kampagne_id = 0 OR $kampagne_id = kg.kampagne_id) AND
  ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id) AND
  b.bearbeitungsstufe < 3',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.bogenart_id, b.hc, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, CASE WHEN b.nicht_begehbar IS NOT NULL AND b.nicht_begehbar then 'nicht_begehbar' ELSE 'kartiert' END AS nicht_begehbar, b.stelle_id, b.user_id, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.bearbeitungsstufe < 3) as foo using unique oid using srid=5650'
WHERE Layer_ID = 139;	
UPDATE layer
SET
  drawingorder = 3118,
  pfad = 'SELECT
  kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene,  b.kartierebene_id, b.bogenart, COALESCE(giscode, label) AS label, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehme, '' AS uebernehme_als_verlustbogen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, CASE WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_10 THEN 'historische Nutzungsform'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_11 THEN 'aktuelle Nutzung'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_12 THEN 'Flächengröße/Länge'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'||chr(10) ELSE '' END as wert_krit, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23,'' nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' as pflanzen_dominant, '' as pflanzen_zahlreich, '' as pflanzen_vereinzelt, fauna, literatur, e_datum, l_datum, bearbeiter, foto, ''::text as fotos, '/var/www/data/mvbio/boegen/' || kk.abk || '/' || b.giscode || CASE WHEN kk.abk LIKE 'BK%' THEN '' ELSE '_GB' END || '.pdf' as pdf_bogen,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, import_table, old_id,  userid, unb, altbestand, b.id AS bogen_id, b.kartierobjekt_id as kartierung_id, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter,
  b.kartierer,
  b.created_at,
  b.geom
FROM
  grundboegen b JOIN
  archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN
  archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN
  archiv.kartierebenen ke ON b.kartierebene_id = ke.id
WHERE
  b.kampagne_id = 1',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.hc as beschriftung, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 1) as foo using unique oid using srid=5650'
WHERE Layer_ID = 140;	
UPDATE layer
SET
  drawingorder = 2120,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, giscode, label, userid, lage, biotopname, standort, unb, flaeche, la_sper, la_sp_txt, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, fb_id, bntk_code, hc, hcp, uc1, uc2, vegeinheit, verl_ursa, verl_nat, verl_ant, bearbeiter, e_datum, l_datum, foto, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, legende, version, verl_wf, los_nr, import_table, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter,  b.geom  FROM verlustboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE 1=1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, verl_ursa, verl_nat, verl_ant, verl_wf, b.bearbeitungsstufe, b.oid, b.geprueft, k.kartierungsart || k.abk AS filter FROM archiv.verlustboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 141;	
UPDATE layer
SET
  drawingorder = 3040,
  pfad = 'SELECT giscode, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, bearbeitungsstufe, flaeche, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id,  schutz_bio, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, hc,  uc1, uc2, hcp, '' as dummy1, '' as dummy2, vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna, e_datum, l_datum, bearbeiter,  loeschen, druck, aend_datum, korrektur,        geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.kampagne_id, b.id as kartierung_id, b.kartierer,  userid, unb, altbestand, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter, b.geom  FROM bewertungsboegen b JOIN kampagnen k ON b.kampagne_id = k.id JOIN kartiergebiete r ON b.kartiergebiet_id = r.id WHERE 1 = 1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter FROM archiv.bewertungsboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 142;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT
  kartierung_id,
  pflanzenart_id,
  pflanzenart_name || CASE WHEN cf = 'C' THEN ' (cf)' ELSE '' END AS abbreviat,
  fsk, rl, bav,
  gsl_version
FROM
  pflanzenvorkommen
WHERE
  dzv = 'D'
ORDER BY
  abbreviat',
  data = ''
WHERE Layer_ID = 165;	
UPDATE layer
SET
  drawingorder = 0,
  pfad = '',
  data = ''
WHERE Layer_ID = 143;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT '' as zum_kartierobjekt, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, lrt_gr, lrt_code, label, b.bearbeitungsstufe AS bearbeitungsstufe_bogen, bs.bearbeitungsstufe AS bearbeitungsstufe_kartierer, bs.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  bs.bearbeitungsstufe AS bearbeitungsstufe_pruefer, bs.bearbeitungsstufe > 2 OR b.user_id != $user_id AS editiersperre_kartierer, bs.bearbeitungsstufe != 3 AS editiersperre_koordinator, bs.bearbeitungsstufe != 4 AS editiersperre_pruefer, bs.pruefhinweis_koordinator, bs.pruefhinweis_pruefer, bs.rueckweisung, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierung_id, b.stelle_id, b.user_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom, '' as t1, t111_1, t111_2, t112_1,  t112_2, t113_1_1, t113_1_2, t113_1_3,        t113_1_4, t113_1_5, t113_1_6, t121_1_1, t121_1_2, t121_1_3,        t121_1_4, t121_1_5, t121_1_6, t121_1_7, t121_1_8, t121_1_9, t121_1_10,        t121_1_11, t121_1_12, t121_1_13, t121_2, t121_3, '' as t2, t211_1_1, t211_1_2,        t211a, t212_1, t212_2, t22_1, t22_2, t22a, t311a_1, t311a_2,        t311b_1, t311b_2, t312_1, t313_1, t313_2, t313_3, t313_4, t315_1, t315_2, t315_3, t317_1_1, t317_2_1, t321_1, t321_2, t322_1, t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungen_stillgewaesser bs JOIN kartierobjekte b ON b.id = bs.kartierung_id JOIN kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN kartierebenen ke ON b.kartierebene_id = ke.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.stelle_id, b.user_id, b.kartierer, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 144;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT '' as zum_kartierobjekt, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, lrt_gr, lrt_code, label, b.bearbeitungsstufe AS bearbeitungsstufe_bogen, bs.bearbeitungsstufe AS bearbeitungsstufe_kartierer, bs.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  bs.bearbeitungsstufe AS bearbeitungsstufe_pruefer, bs.bearbeitungsstufe > 2 OR b.user_id != $user_id AS editiersperre_kartierer, bs.bearbeitungsstufe != 3 AS editiersperre_koordinator, bs.bearbeitungsstufe != 4 AS editiersperre_pruefer, bs.pruefhinweis_koordinator, bs.pruefhinweis_pruefer, bs.rueckweisung, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierung_id, b.stelle_id, b.user_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom, '' as t1, t111_1, t121_1, t121_2, '' as t2, t211_1_1, t211_1_2, t22_1_1,        t22_2, t22a, t22b, t22c, '' as t3, t311_1_1, t311_2, t312_1, t313_1, t313_2,        t321_1, t321_2, t322_1, t331_1, t331_2, t331_3, t331_4, t331_5,        t332_1, t332_2, t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungen_fliessgewaesser bs JOIN kartierobjekte b ON b.id = bs.kartierung_id JOIN kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN kartierebenen ke ON b.kartierebene_id = ke.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.stelle_id, b.user_id, b.kartierer, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 145;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT kampagne_id, kampagne_abk, kampagne, abgeschlossen, kartiergebiet_id, kartiergebiet FROM liste_kartiergebiete WHERE 1=1',
  data = ''
WHERE Layer_ID = 151;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM liste_kampagnen WHERE 1=1',
  data = ''
WHERE Layer_ID = 152;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT id AS kampagne_id, abk, bezeichnung, datenschichten, erfassungszeitraum, umfang, '' as kartiergebiete, '' as kartierebenen, erstellt_am, erstellt_von, geom FROM kampagnen WHERE ($kampagne_id = 0 OR $kampagne_id = id)',
  data = 'geom from (SELECT id, abk, geom FROM mvbio.kampagnen WHERE ($kampagne_id = 0 OR $kampagne_id = id)) as foo using unique id using srid=5650'
WHERE Layer_ID = 153;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT k2k.kampagne_id AS kampagne_id, k2k.kartierebene_id,  ke.bezeichnung || ' (' || ke.abk || ')' AS beschriftung  FROM kartierebenen2kampagne k2k JOIN kartierebenen ke ON k2k.kartierebene_id = ke.id WHERE true',
  data = ''
WHERE Layer_ID = 154;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT '' as zum_kartierobjekt, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, lrt_gr, lrt_code, label, b.bearbeitungsstufe AS bearbeitungsstufe_bogen, bs.bearbeitungsstufe AS bearbeitungsstufe_kartierer, bs.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  bs.bearbeitungsstufe AS bearbeitungsstufe_pruefer, bs.bearbeitungsstufe > 2 OR b.user_id != $user_id AS editiersperre_kartierer, bs.bearbeitungsstufe != 3 AS editiersperre_koordinator, bs.bearbeitungsstufe != 4 AS editiersperre_pruefer, bs.pruefhinweis_koordinator, bs.pruefhinweis_pruefer, bs.rueckweisung, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' as nc, (select count(kartierung_id) from biotoptypen_nebencodes n where n.kartierung_id = bs.kartierung_id) as anzahl_nc, uc1, uc2, vegeinheit, '' habitate, gefcode,  beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierung_id, b.stelle_id, b,user_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, b.ost_west_lage  as t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t311_1_7 as t311_1_7_1, t321_1, t321_2, t321_3, t321_4,  t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungen_kueste bs JOIN kartierobjekte b ON b.id = bs.kartierung_id JOIN kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN kartierebenen ke ON b.kartierebene_id = ke.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.stelle_id, b.user_id, b.kartierer, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 156;	
UPDATE layer
SET
  drawingorder = 3100,
  pfad = 'SELECT '' as zum_kartierobjekt, kk.id as kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, lrt_gr, lrt_code, label, b.bearbeitungsstufe AS bearbeitungsstufe_bogen, bs.bearbeitungsstufe AS bearbeitungsstufe_kartierer, bs.bearbeitungsstufe AS bearbeitungsstufe_koordinator,  bs.bearbeitungsstufe AS bearbeitungsstufe_pruefer, bs.bearbeitungsstufe > 2 OR b.user_id != $user_id AS editiersperre_kartierer, bs.bearbeitungsstufe != 3 AS editiersperre_koordinator, bs.bearbeitungsstufe != 4 AS editiersperre_pruefer, bs.pruefhinweis_koordinator, bs.pruefhinweis_pruefer, bs.rueckweisung, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate,  gefcode, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierung_id, b.stelle_id, b,user_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom, '' as t1, t111_1, t111_2, t112_1, t113_1, t113_2, t121_1,        t122, t131_1, t132_1, '' as t2, t211_1_1, t211_1_2, t211_1_3_1, t211_3_1,        t211_3_2, t211_4, t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,   t311_1_6 as t311_1_6_1,     t311_2, t321_1, t321_2, t322_1, t322_2, t322_3,t322_4, t323_1, t324_1,        t325_1, t328_1_2, t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungen_moore bs JOIN kartierobjekte b ON b.id = bs.kartierung_id JOIN kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN kartierebenen ke ON b.kartierebene_id = ke.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.stelle_id, b.user_id, b.kartierer, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.kartierobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id WHERE ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650'
WHERE Layer_ID = 157;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM fotos WHERE true',
  data = ''
WHERE Layer_ID = 158;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT k2k.kampagne_id AS kampagne_id, k2k.kartierebene_id,  ke.bezeichnung || ' (' || ke.abk || ')' AS beschriftung  FROM kartierebenen2kampagne k2k JOIN kartierebenen ke ON k2k.kartierebene_id = ke.id WHERE true',
  data = ''
WHERE Layer_ID = 161;	
UPDATE layer
SET
  drawingorder = 2120,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche,  schutz_bio, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, hc, uc1, uc2, hcp, '' as dummy1, '' as dummy2, vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna, e_datum, l_datum, bearbeiter, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.id, b.kartierobjekt_id,
  userid,
  unb,
  altbestand,
  kartierer,
  created_at,
  b.geom
FROM kurzboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE status = 1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter FROM archiv.kurzboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id WHERE status = 1) as foo using unique oid using srid=5650 '
WHERE Layer_ID = 162;	
UPDATE layer
SET
  drawingorder = 2110,
  pfad = ' SELECT    kk.abk         AS kampagne,          kk.id          AS kampagne_id,          kg.bezeichnung AS kartiergebiet,          b.kartiergebiet_id,          ke.bezeichnung AS kartierebene,          b.kartierebene_id,          b.bogenart_id,          label,          giscode,          biotopname,          nummer,          standort,          la_sper,          la_sp_txt,          fb_id,          flaeche,          '' AS uebernehme, '' AS uebernehme_als_verlustbogen,         schutz_bio,          schutz_ffh,          alt_giscod,          alt_lfd_nr,          see_nr,          alt_bearb,          alt_datp20,          alt_datffh,          lrt,          eu_nr,          erhaltung,          hc,          hc AS hc_abk,          hcp,          ''                              nc,          (nc.code)[1]                    as nc1,          (nc.flaechendeckung_prozent)[1] AS ncp1,          (nc.code)[2]                    AS nc2,          (nc.flaechendeckung_prozent)[2] AS ncp2,          (nc.code)[3]                    AS nc3,          (nc.flaechendeckung_prozent)[3] AS ncp3,          (nc.code)[4]                    AS nc4,          (nc.flaechendeckung_prozent)[4] AS ncp4,          (nc.code)[5]                    AS nc5,          (nc.flaechendeckung_prozent)[5] AS ncp5,          (nc.code)[6]                    AS nc6,          (nc.flaechendeckung_prozent)[6] AS ncp6,          (nc.code)[7]                    AS nc7,          (nc.flaechendeckung_prozent)[7] AS ncp7,          (nc.code)[8]                    AS nc8,          (nc.flaechendeckung_prozent)[8] AS ncp8,          uc1,          uc1 AS uc1_abk,          uc2,          uc2 AS uc2_abk,          vegeinheit,          ''              habitate,          (hv.code)[1]  AS hv1,          (hv.code)[2]  AS hv2,          (hv.code)[3]  AS hv3,          (hv.code)[4]  AS hv4,          (hv.code)[5]  AS hv5,          (hv.code)[6]  AS hv6,          (hv.code)[7]  AS hv7,          (hv.code)[8]  AS hv8,          (hv.code)[9]  AS hv9,          (hv.code)[10] AS hv10,          (hv.code)[11] AS hv11,          (hv.code)[12] AS hv12,          (hv.code)[13] AS hv13,          (hv.code)[14] AS hv14,          (hv.code)[15] AS hv15,          (hv.code)[16] AS hv16,          (hv.code)[17] AS hv17,          (hv.code)[18] AS hv18,          (hv.code)[19] AS hv19,          (hv.code)[20] AS hv20,          (hv.code)[21] AS hv21,          beschreibg, replace(substring(beschreibg, 1, 1300), chr(10), ' ') ||          CASE                    WHEN length(beschreibg) > 1300 THEN ' ...Fortsetzung'                    ELSE ''          END AS beschreibg_1,          CASE                    WHEN length(beschreibg) > 1300 THEN 'Fortsetzung... '                                        || substring(beschreibg, 1300)                    ELSE NULL          END AS beschreibg_2,          wert_krit_1,          wert_krit_9,          wert_krit_2,          wert_krit_10,          wert_krit_3,          wert_krit_11,          wert_krit_4,          wert_krit_12,          wert_krit_5,          wert_krit_13,          wert_krit_6,          wert_krit_14,          wert_krit_7,          wert_krit_15,          wert_krit_8,          wert_krit_16,          CASE                    WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_10 THEN 'historische Nutzungsform'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_11 THEN 'aktuelle Nutzung'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_12 THEN 'Flächengröße/Länge'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'                                        ||chr(10)                    ELSE ''          END AS wert_krit,          gefaehrdg,          gefcode,          (gf.code)[1] AS gf1,          (gf.code)[2] AS gf2,          (gf.code)[3] AS gf3,          (gf.code)[4] AS gf4,          (gf.code)[5] AS gf5,          (gf.code)[6] AS gf6,          (gf.code)[7] AS gf7,          ohnegefahr,          empfehlung,          ''           AS empfcode,          (em.code)[1] AS em1,          (em.code)[2] AS em2,          (em.code)[3] AS em3,          (em.code)[4] AS em4,          ''           AS dummy24,          ''           AS dummy25,          ''           AS dummy26,          ''           AS dummy35,          ''           AS dummy36,          ''           AS dummy37,          ''           AS dummy38,          ''           AS dummy39,          ''           AS dummy40,          substrat_1,          trophie_1,          wasser_1,          relief_1,          exposition_1,          substrat_2,          trophie_2,          wasser_2,          relief_2,          exposition_2,          substrat_3,          trophie_3,          wasser_3,          relief_3,          exposition_3,          substrat_4,          trophie_4,          wasser_4,          relief_4,          exposition_4,          substrat_5,          trophie_5,          CASE                    WHEN trophie_1 IS NULL THEN '        '                    WHEN trophie_1 THEN '      g'                    ELSE 'k      '          END                    || '    dystroph' AS trophie_1_p,          CASE                    WHEN trophie_2 IS NULL THEN '        '                    WHEN trophie_2 THEN '      g'                    ELSE 'k      '          END                    || '    oligotroph' AS trophie_2_p,          CASE                    WHEN trophie_3 IS NULL THEN '        '                    WHEN trophie_3 THEN '      g'                    ELSE 'k      '          END                    || '    mesotroph' AS trophie_3_p,          CASE                    WHEN trophie_4 IS NULL THEN '        '                    WHEN trophie_4 THEN '      g'                    ELSE 'k      '          END                    || '    eutroph' AS trophie_4_p,          CASE                    WHEN trophie_5 IS NULL THEN '        '                    WHEN trophie_5 THEN '      g'                    ELSE 'k      '          END                    || '    poly-/hypertroph' AS trophie_5_p,          wasser_5,          relief_5,          exposition_5,          substrat_6,          '' AS dummy3,          wasser_6,          relief_6,          exposition_6,          substrat_7,          '' AS dummy4,          wasser_7,          relief_7,          exposition_7,          CASE                    WHEN exposition_1 IS NULL THEN '        '                    WHEN exposition_1 THEN '      g'                    ELSE 'k      '          END                    || '    N' AS exposition_1_p,          CASE                    WHEN exposition_2 IS NULL THEN '        '                    WHEN exposition_2 THEN '      g'                    ELSE 'k      '          END                    || '    NO' AS exposition_2_p,          CASE                    WHEN exposition_3 IS NULL THEN '        '                    WHEN exposition_3 THEN '      g'                    ELSE 'k      '          END                    || '    O' AS exposition_3_p,          CASE                    WHEN exposition_4 IS NULL THEN '        '                    WHEN exposition_4 THEN '      g'                    ELSE 'k      '          END                    || '    SO' AS exposition_4_p,          CASE                    WHEN exposition_5 IS NULL THEN '        '                    WHEN exposition_5 THEN '      g'                    ELSE 'k      '          END                    || '    S' AS exposition_5_p,          CASE                    WHEN exposition_6 IS NULL THEN '        '                    WHEN exposition_6 THEN '      g'                    ELSE 'k      '          END                    || '    SW' AS exposition_6_p,          CASE                    WHEN exposition_7 IS NULL THEN '        '                    WHEN exposition_7 THEN '      g'                    ELSE 'k      '          END                    || '    W' AS exposition_7_p,          CASE                    WHEN exposition_8 IS NULL THEN '        '                    WHEN exposition_8 THEN '      g'                    ELSE 'k      '          END                    || '    NW' AS exposition_8_p,          substrat_8,          '' AS dummy5,          wasser_8,          relief_8,          CASE                    WHEN relief_1 IS NULL THEN '        '                    WHEN relief_1 THEN '      g'                    ELSE 'k      '          END                    || '    eben' AS relief_1_p,          CASE                    WHEN relief_2 IS NULL THEN '        '                    WHEN relief_2 THEN '      g'                    ELSE 'k      '          END                    || '    wellig' AS relief_2_p,          CASE                    WHEN relief_3 IS NULL THEN '        '                    WHEN relief_3 THEN '      g'                    ELSE 'k      '          END                    || '    kuppig' AS relief_3_p,          CASE                    WHEN relief_4 IS NULL THEN '        '                    WHEN relief_4 THEN '      g'                    ELSE 'k      '          END                    || '    dünig' AS relief_4_p,          CASE                    WHEN relief_5 IS NULL THEN '        '                    WHEN relief_5 THEN '      g'                    ELSE 'k      '          END                    || '    Berg/Rücken' AS relief_5_p,          CASE                    WHEN relief_6 IS NULL THEN '        '                    WHEN relief_6 THEN '      g'                    ELSE 'k      '          END                    || '    Riedel' AS relief_6_p,          CASE                    WHEN relief_7 IS NULL THEN '        '                    WHEN relief_7 THEN '      g'                    ELSE 'k      '          END                    || '    Flachhang <= 9°' AS relief_7_p,          CASE                    WHEN relief_8 IS NULL THEN '        '                    WHEN relief_8 THEN '      g'                    ELSE 'k      '          END                    || '    Steilhang > 9°' AS relief_8_p,          CASE                    WHEN relief_9 IS NULL THEN '        '                    WHEN relief_9 THEN '      g'                    ELSE 'k      '          END                    || '    Nische' AS relief_9_p,          CASE                    WHEN relief_10 IS NULL THEN '        '                    WHEN relief_10 THEN '      g'                    ELSE 'k      '          END                    || '    Senke/Strecksenke' AS relief_10_p,          CASE                    WHEN relief_11 IS NULL THEN '        '                    WHEN relief_11 THEN '      g'                    ELSE 'k      '          END                    || '    Kerbtal' AS relief_11_p,          CASE                    WHEN relief_12 IS NULL THEN '        '                    WHEN relief_12 THEN '      g'                    ELSE 'k      '          END                    || '    Sohlental' AS relief_12_p,          exposition_8,          substrat_9,          '' AS dummy6,          wasser_9,          CASE                    WHEN wasser_1 IS NULL THEN '        '                    WHEN wasser_1 THEN '      x'                    ELSE 'x      '          END                    || '    trocken' AS wasser_1_p,          CASE                    WHEN wasser_2 IS NULL THEN '        '                    WHEN wasser_2 THEN '      g'                    ELSE 'k      '          END                    || '    mäßig trocken' AS wasser_2_p,          CASE                    WHEN wasser_3 IS NULL THEN '        '                    WHEN wasser_3 THEN '      g'                    ELSE 'k      '          END                    || '    wechselfeucht' AS wasser_3_p,          CASE                    WHEN wasser_4 IS NULL THEN '        '                    WHEN wasser_4 THEN '      g'                    ELSE 'k      '          END                    || '    frisch' AS wasser_4_p,          CASE                    WHEN wasser_5 IS NULL THEN '        '                    WHEN wasser_5 THEN '      g'                    ELSE 'k      '          END                    || '    feucht' AS wasser_5_p,          CASE                    WHEN wasser_6 IS NULL THEN '        '                    WHEN wasser_6 THEN '      g'                    ELSE 'k      '          END                    || '    sehr feucht' AS wasser_6_p,          CASE                    WHEN wasser_7 IS NULL THEN '        '                    WHEN wasser_7 THEN '      g'                    ELSE 'k      '          END                    || '    nass' AS wasser_7_p,          CASE                    WHEN wasser_8 IS NULL THEN '        '                    WHEN wasser_8 THEN '      g'                    ELSE 'k      '          END                    || '    offenes Wasser' AS wasser_8_p,          CASE                    WHEN wasser_9 IS NULL THEN '        '                    WHEN wasser_9 THEN '      g'                    ELSE 'k      '          END                    || '    quellig' AS wasser_9_p,          relief_9,          '' AS dummy7,          substrat_10,          CASE                    WHEN substrat_1 IS NULL THEN '        '                    WHEN substrat_1 THEN '     g'                    ELSE 'k     '          END                    || '    Torf, wenig gestört' AS substrat_1_p,          CASE                    WHEN substrat_2 IS NULL THEN '        '                    WHEN substrat_2 THEN '     g'                    ELSE 'k     '          END                    || '    Torf, degradiert' AS substrat_2_p,          CASE                    WHEN substrat_3 IS NULL THEN '        '                    WHEN substrat_3 THEN '     g'                    ELSE 'k     '          END                    || '    Antorf' AS substrat_3_p,          CASE                    WHEN substrat_4 IS NULL THEN '        '                    WHEN substrat_4 THEN '     g'                    ELSE 'k     '          END                    || '    Sand' AS substrat_4_p,          CASE                    WHEN substrat_5 IS NULL THEN '        '                    WHEN substrat_5 THEN '     g'                    ELSE 'k     '          END                    || '    Kies/Steine' AS substrat_5_p,          CASE                    WHEN substrat_6 IS NULL THEN '        '                    WHEN substrat_6 THEN '     g'                    ELSE 'k     '          END                    || '    Lehm' AS substrat_6_p,          CASE                    WHEN substrat_7 IS NULL THEN '        '                    WHEN substrat_7 THEN '     g'                    ELSE 'k     '          END                    || '    Ton' AS substrat_7_p,          CASE                    WHEN substrat_8 IS NULL THEN '        '                    WHEN substrat_8 THEN '     g'                    ELSE 'k     '          END                    || '    Halbkalk/Kalk' AS substrat_8_p,          CASE                    WHEN substrat_9 IS NULL THEN '        '                    WHEN substrat_9 THEN '     g'                    ELSE 'k     '          END                    || '    Schlamm/Faulschlamm' AS substrat_9_p,          CASE                    WHEN substrat_10 IS NULL THEN '        '                    WHEN substrat_10 THEN '     g'                    ELSE 'k     '          END                    || '    gestörter Boden' AS substrat_10_p,          ''                                  AS dummy8,          ''                                  AS dummy9,          relief_10,          '' AS dummy10,          '' AS dummy11,          '' AS dummy12,          '' AS dummy13,          relief_11,          '' AS dummy14,          '' AS dummy15,          '' AS dummy16,          '' AS dummy17,          relief_12,          '' AS dummy18,          '' AS dummy19,          '' AS dummy20,          '' AS dummy21,          '' AS dummy22,          '' AS dummy23,          ''    nutzintens_1,          nutzungsart_5,          umgebung_1,          umgebung_14,          nutzintens_2,          nutzungsart_6,          umgebung_2,          umgebung_15,          nutzintens_3,          nutzungsart_7,          umgebung_3,          umgebung_16,          nutzintens_4,          CASE                    WHEN nutzintens_1 IS NULL THEN '        '                    WHEN nutzintens_1 THEN '      g'                    ELSE 'k      '          END                    || '    intensiv' AS nutzintens_1_p,          CASE                    WHEN nutzintens_2 IS NULL THEN '        '                    WHEN nutzintens_2 THEN '      g'                    ELSE 'k      '          END                    || '    extensiv' AS nutzintens_2_p,          CASE                    WHEN nutzintens_3 IS NULL THEN '        '                    WHEN nutzintens_3 THEN '      g'                    ELSE 'k      '          END                    || '    aufgelassen' AS nutzintens_3_p,          CASE                    WHEN nutzintens_4 IS NULL THEN '        '                    WHEN nutzintens_4 THEN '      g'                    ELSE 'k      '          END                    || '    keine Nutzung' AS nutzintens_4_p,          nutzungsart_8,          umgebung_4,          umgebung_17,          '' AS dummy27,          nutzungsart_9,          umgebung_5,          umgebung_18,          '' AS dummy28,          nutzungsart_10,          umgebung_6,          umgebung_19,          nutzungsart_1,          nutzungsart_11,          umgebung_7,          umgebung_20,          nutzungsart_2,          nutzungsart_12,          umgebung_8,          umgebung_21,          nutzungsart_3,          nutzungsart_13,          umgebung_9,          umgebung_22,          nutzungsart_4,          nutzungsart_14,          CASE                    WHEN nutzungsart_1 IS NULL THEN '        '                    WHEN nutzungsart_1 THEN '      g'                    ELSE 'k      '          END                    || '    Acker' AS nutzungsart_1_p,          CASE                    WHEN nutzungsart_2 IS NULL THEN '        '                    WHEN nutzungsart_2 THEN '      g'                    ELSE 'k      '          END                    || '    Wiese' AS nutzungsart_2_p,          CASE                    WHEN nutzungsart_3 IS NULL THEN '        '                    WHEN nutzungsart_3 THEN '      g'                    ELSE 'k      '          END                    || '    Weide' AS nutzungsart_3_p,          CASE                    WHEN nutzungsart_4 IS NULL THEN '        '                    WHEN nutzungsart_4 THEN '      g'                    ELSE 'k      '          END                    || '    forstliche Nutzung' AS nutzungsart_4_p,          CASE                    WHEN nutzungsart_5 IS NULL THEN '        '                    WHEN nutzungsart_5 THEN '      g'                    ELSE 'k      '          END                    || '    Fischerei' AS nutzungsart_5_p,          CASE                    WHEN nutzungsart_6 IS NULL THEN '        '                    WHEN nutzungsart_6 THEN '      g'                    ELSE 'k      '          END                    || '    Angeln' AS nutzungsart_6_p,          CASE                    WHEN nutzungsart_7 IS NULL THEN '        '                    WHEN nutzungsart_7 THEN '      g'                    ELSE 'k      '          END                    || '    Erholung' AS nutzungsart_7_p,          CASE                    WHEN nutzungsart_8 IS NULL THEN '        '                    WHEN nutzungsart_8 THEN '      g'                    ELSE 'k      '          END                    || '    Kleingartenbau' AS nutzungsart_8_p,          CASE                    WHEN nutzungsart_9 IS NULL THEN '        '                    WHEN nutzungsart_9 THEN '      g'                    ELSE 'k      '          END                    || '    Erwerbsgartenbau' AS nutzungsart_9_p,          CASE                    WHEN nutzungsart_10 IS NULL THEN '        '                    WHEN nutzungsart_10 THEN '      g'                    ELSE 'k      '          END                    || '    Ferienhäuser' AS nutzungsart_10_p,          CASE                    WHEN nutzungsart_11 IS NULL THEN '        '                    WHEN nutzungsart_11 THEN '      g'                    ELSE 'k      '          END                    || '    Bodenentnahme' AS nutzungsart_11_p,          CASE                    WHEN nutzungsart_12 IS NULL THEN '        '                    WHEN nutzungsart_12 THEN '      g'                    ELSE 'k      '          END                    || '    Verkehr' AS nutzungsart_12_p,          CASE                    WHEN nutzungsart_13 IS NULL THEN '        '                    WHEN nutzungsart_13 THEN '      g'                    ELSE 'k      '          END                    || '    Ver-/Entsorgungsanlage' AS nutzungsart_13_p,          CASE                    WHEN nutzungsart_14 IS NULL THEN '        '                    WHEN nutzungsart_14 THEN '      g'                    ELSE 'k      '          END                    || '    sonstige Nutzung:' AS nutzungsart_14_p,          umgebung_10,          umgebung_23,          '' AS dummy29,          nutzartzus,          '' AS dummy30,          umgebung_11,          umgebung_24,          '' AS dummy31,          '' AS dummy32,          umgebung_12,          umgebung_25,          CASE                    WHEN umgebung_1 IS NULL THEN '        '                    WHEN umgebung_1 THEN '      g'                    ELSE 'k      '          END                    || '    Acker/Gartenbau' AS umgebung_1_p,          CASE                    WHEN umgebung_2 IS NULL THEN '        '                    WHEN umgebung_2 THEN '      g'                    ELSE 'k      '          END                    || '    Ackerbrache' AS umgebung_2_p,          CASE                    WHEN umgebung_3 IS NULL THEN '        '                    WHEN umgebung_3 THEN '      g'                    ELSE 'k      '          END                    || '    Grünland, intensiv' AS umgebung_3_p,          CASE                    WHEN umgebung_4 IS NULL THEN '        '                    WHEN umgebung_4 THEN '      g'                    ELSE 'k      '          END                    || '    Grünland, extensiv' AS umgebung_4_p,          CASE                    WHEN umgebung_5 IS NULL THEN '        '                    WHEN umgebung_5 THEN '      g'                    ELSE 'k      '          END                    || '    Laub-/Mischwald' AS umgebung_5_p,          CASE                    WHEN umgebung_6 IS NULL THEN '        '                    WHEN umgebung_6 THEN '      g'                    ELSE 'k      '          END                    || '    Nadelwald' AS umgebung_6_p,          CASE                    WHEN umgebung_7 IS NULL THEN '        '                    WHEN umgebung_7 THEN '      g'                    ELSE 'k      '          END                    || '    Feuchtwald/-gebüsch' AS umgebung_7_p,          CASE                    WHEN umgebung_8 IS NULL THEN '        '                    WHEN umgebung_8 THEN '      g'                    ELSE 'k      '          END                    || '    Gehölz' AS umgebung_8_p,          CASE                    WHEN umgebung_9 IS NULL THEN '        '                    WHEN umgebung_9 THEN '      g'                    ELSE 'k      '          END                    || '    Röhricht/Feuchtbrache' AS umgebung_9_p,          CASE                    WHEN umgebung_10 IS NULL THEN '        '                    WHEN umgebung_10 THEN '      g'                    ELSE 'k      '          END                    || '    Hochstauden-/Ruderalflur' AS umgebung_10_p,          CASE                    WHEN umgebung_11 IS NULL THEN '        '                    WHEN umgebung_11 THEN '      g'                    ELSE 'k      '          END                    || '    Graben' AS umgebung_11_p,          CASE                    WHEN umgebung_12 IS NULL THEN '        '                    WHEN umgebung_12 THEN '      g'                    ELSE 'k      '          END                    || '    Fließgewässer' AS umgebung_12_p,          CASE                    WHEN umgebung_13 IS NULL THEN '        '                    WHEN umgebung_13 THEN '      g'                    ELSE 'k      '          END                    || '    Stillgewässer' AS umgebung_13_p,          CASE                    WHEN umgebung_14 IS NULL THEN '        '                    WHEN umgebung_14 THEN '      g'                    ELSE 'k      '          END                    || '    Trockenbiotop' AS umgebung_14_p,          CASE                    WHEN umgebung_15 IS NULL THEN '        '                    WHEN umgebung_15 THEN '      g'                    ELSE 'k      '          END                    || '    Grünanlage/Kleingarten' AS umgebung_15_p,          CASE                    WHEN umgebung_16 IS NULL THEN '        '                    WHEN umgebung_16 THEN '      g'                    ELSE 'k      '          END                    || '    Weg' AS umgebung_16_p,          CASE                    WHEN umgebung_17 IS NULL THEN '        '                    WHEN umgebung_17 THEN '      g'                    ELSE 'k      '          END                    || '    Straße/Parkplatz' AS umgebung_17_p,          CASE                    WHEN umgebung_18 IS NULL THEN '        '                    WHEN umgebung_18 THEN '      g'                    ELSE 'k      '          END                    || '    Bahnanlage' AS umgebung_18_p,          CASE                    WHEN umgebung_19 IS NULL THEN '        '                    WHEN umgebung_19 THEN '      g'                    ELSE 'k      '          END                    || '    Gewerbe/Industrie' AS umgebung_19_p,          CASE                    WHEN umgebung_20 IS NULL THEN '        '                    WHEN umgebung_20 THEN '      g'                    ELSE 'k      '          END                    || '    Silo/Stallanlage' AS umgebung_20_p,          CASE                    WHEN umgebung_21 IS NULL THEN '        '                    WHEN umgebung_21 THEN '      g'                    ELSE 'k      '          END                    || '    Gebäude/Siedlung' AS umgebung_21_p,          CASE                    WHEN umgebung_22 IS NULL THEN '        '                    WHEN umgebung_22 THEN '      g'                    ELSE 'k      '          END                    || '    Spülfeld/Halde' AS umgebung_22_p,          CASE                    WHEN umgebung_23 IS NULL THEN '        '                    WHEN umgebung_23 THEN '      g'                    ELSE 'k      '          END                    || '    Bodenentnahme' AS umgebung_23_p,          CASE                    WHEN umgebung_24 IS NULL THEN '        '                    WHEN umgebung_24 THEN '      g'                    ELSE 'k      '          END                    || '    Deich / Damm' AS umgebung_24_p,          CASE                    WHEN umgebung_25 IS NULL THEN '        '                    WHEN umgebung_25 THEN '      g'                    ELSE 'k      '          END                    || '    sonstige:' AS umgebung_25_p,          ''                           AS dummy33,          ''                           AS dummy34,          umgebung_13,          umgebzus,          '' AS pflanzen_dominant,          '' AS pflanzen_zahlreich,          '' AS pflanzen_vereinzelt,          fauna,          literatur,          e_datum,          l_datum,          bearbeiter,          foto,          '' :: text AS fotos,          '/var/www/data/mvbio/boegen/'                    || kk.abk                    || '/'                    || b.giscode                    ||          CASE                    WHEN kk.abk LIKE 'BK%' THEN ''                    ELSE '_GB'          END                    || '.pdf' AS pdf_bogen,          loeschen,          druck,          aend_datum,          korrektur,          geprueft,          pruefer,          pruefdatum,          lock,          status,          version,          import_table,          old_id,          userid,          unb,          altbestand,          b.id,          b.kartierobjekt_id AS kartierung_id,
  kartierer,
  created_at,
  kk.kartierungsart                    || kk.abk                    || COALESCE('-'                    || kg.losnummer, '') AS filter,          b.geom FROM      grundboegen b JOIN      archiv.kartiergebiete kg ON        b.kartiergebiet_id = kg.id JOIN      archiv.kampagnen kk ON        kg.kampagne_id = kk.id JOIN      archiv.kartierebenen ke ON        b.kartierebene_id = ke.id LEFT JOIN archiv.biotoptypen_nebencodes_agg nc ON        b.id = nc.kartierung_id LEFT JOIN archiv.habitatvorkommen_agg hv ON        b.id = hv.kartierung_id LEFT JOIN archiv.beeintraechtigungen_gefaehrdungen_agg gf ON        b.id = gf.kartierung_id LEFT JOIN archiv.empfehlungen_massnahmen_agg em ON        b.id = em.kartierung_id WHERE     status = 1 ',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE status=1) as foo using unique oid using srid=5650'
WHERE Layer_ID = 163;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT
  kartierung_id,
  pflanzenart_id,
  pflanzenart_name || CASE WHEN cf = 'C' THEN ' (cf)' ELSE '' END AS abbreviat,
  fsk, rl, bav,
  gsl_version
FROM
  pflanzenvorkommen
WHERE
  dzv = 'V'
ORDER BY
  abbreviat',
  data = ''
WHERE Layer_ID = 166;	
UPDATE layer
SET
  drawingorder = 3227,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t321_1, t321_2, t321_3, t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_kueste bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 8',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_kueste b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 8) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 167;	
UPDATE layer
SET
  drawingorder = 2210,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,t121_1,t121_2,t211_1_1,t211_1_2,t22_1_1,t22_2,t22a,t22b,t22c,t311_1_1,t311_2,t312_1,t313_1,t313_2,t321_1,t321_2,t322_1,t331_1,t331_2,t331_3,t331_4,t331_5,t332_1,t332_2,t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_fliessgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_fliessgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 172;	
UPDATE layer
SET
  drawingorder = 3226,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1,t113_1,t121_1,t121_2,t131_1_1,t131_1_2,t131_2_1,t131_2_2,t131_3,t132_1,t132_2,t211_1_1,t211_1_2,t211_1_3,t211_3_1,t212_1,t22_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t321_1,t321_2,t322_1,t322_2,t322_3,t323_1,t324_1,t325_1,t326_1,t327_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_offenland bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 8',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_offenland b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 8 ) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 168;	
UPDATE layer
SET
  drawingorder = 3225,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t112_2,	t113_1_1, t113_1_2, t113_1_3,t113_1_4,t113_1_5,t113_1_6,t121_1_1,t121_1_2,t121_1_3,t121_1_4,t121_1_5,t121_1_6,t121_1_7,t121_1_8,t121_1_9,t121_1_10,t121_1_11,t121_1_12,t121_1_13,	t121_2,t121_3,t211_1_1,t211_1_2,t211a,t212_1,t212_2,t22_1,t22_2,t22a,t311a_1,t311a_2,t311b_1,t311b_2,t312_1,t313_1,	t313_2,t313_3,t313_4,t315_1,t315_2,t315_3,t317_1_1,t317_2_1,t321_1,t321_2,t322_1,t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_stillgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 8',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_stillgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 8) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 169;	
UPDATE layer
SET
  drawingorder = 3223,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,t121_1,t121_2,t211_1_1,t211_1_2,t22_1_1,t22_2,t22a,t22b,t22c,t311_1_1,t311_2,t312_1,t313_1,t313_2,t321_1,t321_2,t322_1,t331_1,t331_2,t331_3,t331_4,t331_5,t332_1,t332_2,t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_fliessgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 8 ',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_fliessgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 8) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 170;	
UPDATE layer
SET
  drawingorder = 3224,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t113_1,	t113_2,	t121_1,	t122,t131_1,t132_1,	t211_1_1,t211_1_2,t211_1_3_1,t211_3_1,	t211_3_2,t211_4,t22_1_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t311_2,t321_1,t321_2,t322_1,t322_2,	t322_3,t323_1,t324_1,t325_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_moore bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id=8',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_moore b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 8) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 171;	
UPDATE layer
SET
  drawingorder = 2250,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t321_1, t321_2, t321_3, t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_kueste bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_kueste b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id AND b.kampagne_id = 7) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 173;	
UPDATE layer
SET
  drawingorder = 2220,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t113_1,	t113_2,	t121_1,	t122,t131_1,t132_1,	t211_1_1,t211_1_2,t211_1_3_1,t211_3_1,	t211_3_2,t211_4,t22_1_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t311_2,t321_1,t321_2,t322_1,t322_2,	t322_3,t323_1,t324_1,t325_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_moore bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_moore b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 174;	
UPDATE layer
SET
  drawingorder = 2240,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1,t113_1,t121_1,t121_2,t131_1_1,t131_1_2,t131_2_1,t131_2_2,t131_3,t132_1,t132_2,t211_1_1,t211_1_2,t211_1_3,t211_3_1,t212_1,t22_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t321_1,t321_2,t322_1,t322_2,t322_3,t323_1,t324_1,t325_1,t326_1,t327_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_offenland bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_offenland b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 175;	
UPDATE layer
SET
  drawingorder = 2230,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t112_2,	t113_1_1, t113_1_2, t113_1_3,t113_1_4,t113_1_5,t113_1_6,t121_1_1,t121_1_2,t121_1_3,t121_1_4,t121_1_5,t121_1_6,t121_1_7,t121_1_8,t121_1_9,t121_1_10,t121_1_11,t121_1_12,t121_1_13,	t121_2,t121_3,t211_1_1,t211_1_2,t211a,t212_1,t212_2,t22_1,t22_2,t22a,t311a_1,t311a_2,t311b_1,t311b_2,t312_1,t313_1,	t313_2,t313_3,t313_4,t315_1,t315_2,t315_3,t317_1_1,t317_2_1,t321_1,t321_2,t322_1,t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_stillgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_stillgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7 AND ($kartiergebietfilter = 0 OR $kartiergebietfilter = kg.id)) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 176;	
UPDATE layer
SET
  drawingorder = 3128,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene,  b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehme, '' AS uebernehme_als_verlustbogen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, CASE WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_10 THEN 'historische Nutzungsform'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_11 THEN 'aktuelle Nutzung'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_12 THEN 'Flächengröße/Länge'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'||chr(10) ELSE '' END as wert_krit, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23,'' nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' as pflanzen_dominant, '' as pflanzen_zahlreich, '' as pflanzen_vereinzelt, fauna, literatur, e_datum, l_datum, bearbeiter, foto, '' as fotos, '/var/www/data/mvbio/boegen/' || kk.abk || '/' || b.giscode || CASE WHEN kk.abk LIKE 'BK%' THEN '' ELSE '_GB' END || '.pdf' as pdf_bogen,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, import_table, old_id,  userid, unb, altbestand, b.id, b.kartierobjekt_id as kartierung_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom FROM grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE b.kampagne_id = 3',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.hc as beschriftung, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 3) as foo using unique oid using srid=5650'
WHERE Layer_ID = 177;	
UPDATE layer
SET
  drawingorder = 3136,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene,  b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehme, '' AS uebernehme_als_verlustbogen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, CASE WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_10 THEN 'historische Nutzungsform'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_11 THEN 'aktuelle Nutzung'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_12 THEN 'Flächengröße/Länge'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'||chr(10) ELSE '' END as wert_krit, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23,'' nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' as pflanzen_dominant, '' as pflanzen_zahlreich, '' as pflanzen_vereinzelt, fauna, literatur, e_datum, l_datum, bearbeiter, foto, '' as fotos, '/var/www/data/mvbio/boegen/' || kk.abk || '/' || b.giscode || CASE WHEN kk.abk LIKE 'BK%' THEN '' ELSE '_GB' END || '.pdf' as pdf_bogen,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, import_table, old_id,  userid, unb, altbestand, b.id, b.kartierobjekt_id as kartierung_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom FROM grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE b.kampagne_id = 2',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.hc beschriftung, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 2) as foo using unique oid using srid=5650'
WHERE Layer_ID = 178;	
UPDATE layer
SET
  drawingorder = 3313,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene,  b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehme, '' AS uebernehme_als_verlustbogen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, CASE WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_10 THEN 'historische Nutzungsform'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_11 THEN 'aktuelle Nutzung'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_12 THEN 'Flächengröße/Länge'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'||chr(10) ELSE '' END ||	CASE WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'||chr(10) ELSE '' END as wert_krit, gefaehrdg, gefcode, ohnegefahr, empfehlung, '' AS empfcode, '' as dummy24, '' as dummy25, '' as dummy26, '' as dummy35, '' as dummy36, '' as dummy37, '' as dummy38, '' as dummy39, '' as dummy40,substrat_1, trophie_1, wasser_1, relief_1, exposition_1,substrat_2, trophie_2, wasser_2, relief_2, exposition_2,substrat_3, trophie_3, wasser_3, relief_3, exposition_3,substrat_4, trophie_4, wasser_4, relief_4, exposition_4,substrat_5, trophie_5, wasser_5, relief_5, exposition_5,substrat_6, '' as dummy3, wasser_6, relief_6, exposition_6,substrat_7, '' as dummy4, wasser_7, relief_7, exposition_7,substrat_8, '' as dummy5, wasser_8, relief_8, exposition_8,substrat_9, '' as dummy6, wasser_9, relief_9, '' as dummy7,substrat_10, '' as dummy8, '' as dummy9, relief_10, '' as dummy10,'' as dummy11, '' as dummy12, '' as dummy13, relief_11, '' as dummy14,'' as dummy15, '' as dummy16, '' as dummy17, relief_12, '' as dummy18, '' as dummy19,'' as dummy20,'' as dummy21, '' as dummy22, '' as dummy23,'' nutzintens_1, nutzungsart_5, umgebung_1, umgebung_14,  nutzintens_2, nutzungsart_6, umgebung_2, umgebung_15,  nutzintens_3, nutzungsart_7, umgebung_3, umgebung_16,  nutzintens_4, nutzungsart_8, umgebung_4, umgebung_17,   '' as dummy27, nutzungsart_9, umgebung_5, umgebung_18,  '' as dummy28, nutzungsart_10, umgebung_6, umgebung_19,  nutzungsart_1, nutzungsart_11, umgebung_7, umgebung_20,  nutzungsart_2, nutzungsart_12, umgebung_8, umgebung_21,  nutzungsart_3, nutzungsart_13, umgebung_9, umgebung_22,  nutzungsart_4, nutzungsart_14, umgebung_10, umgebung_23,  '' as dummy29, nutzartzus, '' as dummy30, umgebung_11, umgebung_24,  '' as dummy31, '' as dummy32, umgebung_12, umgebung_25,  '' as dummy33, '' as dummy34, umgebung_13, umgebzus, '' as pflanzen_dominant, '' as pflanzen_zahlreich, '' as pflanzen_vereinzelt, fauna, literatur, e_datum, l_datum, bearbeiter, foto, '' as fotos, '/var/www/data/mvbio/boegen/' || kk.abk || '/' || b.giscode || CASE WHEN kk.abk LIKE 'BK%' THEN '' ELSE '_GB' END || '.pdf' as pdf_bogen,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, import_table, old_id,  userid, unb, altbestand, b.id, b.kartierobjekt_id as kartierung_id, b.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, b.geom FROM grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE b.kampagne_id = 4',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 4) as foo using unique oid using srid=5650'
WHERE Layer_ID = 179;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT bn.kartierung_id, bn.code, bn.flaechendeckung_prozent, bn.vegeinheit FROM biotoptypen_nebencodes AS bn WHERE 1=1',
  data = ''
WHERE Layer_ID = 180;	
UPDATE layer
SET
  drawingorder = 3233,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,t121_1,t121_2,t211_1_1,t211_1_2,t22_1_1,t22_2,t22a,t22b,t22c,t311_1_1,t311_2,t312_1,t313_1,t313_2,t321_1,t321_2,t322_1,t331_1,t331_2,t331_3,t331_4,t331_5,t332_1,t332_2,t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_fliessgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 6',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_fliessgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 6) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 181;	
UPDATE layer
SET
  drawingorder = 3243,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,t121_1,t121_2,t211_1_1,t211_1_2,t22_1_1,t22_2,t22a,t22b,t22c,t311_1_1,t311_2,t312_1,t313_1,t313_2,t321_1,t321_2,t322_1,t331_1,t331_2,t331_3,t331_4,t331_5,t332_1,t332_2,t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_fliessgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 5',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_fliessgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 5) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 182;	
UPDATE layer
SET
  drawingorder = 3217,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,t121_1,t121_2,t211_1_1,t211_1_2,t22_1_1,t22_2,t22a,t22b,t22c,t311_1_1,t311_2,t312_1,t313_1,t313_2,t321_1,t321_2,t322_1,t331_1,t331_2,t331_3,t331_4,t331_5,t332_1,t332_2,t341, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_fliessgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_fliessgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 183;	
UPDATE layer
SET
  drawingorder = 3216,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t113_1,	t113_2,	t121_1,	t122,t131_1,t132_1,	t211_1_1,t211_1_2,t211_1_3_1,t211_3_1,	t211_3_2,t211_4,t22_1_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t311_2,t321_1,t321_2,t322_1,t322_2,	t322_3,t323_1,t324_1,t325_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_moore bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_moore b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 184;	
UPDATE layer
SET
  drawingorder = 3234,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t113_1,	t113_2,	t121_1,	t122,t131_1,t132_1,	t211_1_1,t211_1_2,t211_1_3_1,t211_3_1,	t211_3_2,t211_4,t22_1_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t311_2,t321_1,t321_2,t322_1,t322_2,	t322_3,t323_1,t324_1,t325_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_moore bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 6',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_moore b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 6) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 185;	
UPDATE layer
SET
  drawingorder = 3244,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t113_1,	t113_2,	t121_1,	t122,t131_1,t132_1,	t211_1_1,t211_1_2,t211_1_3_1,t211_3_1,	t211_3_2,t211_4,t22_1_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t311_2,t321_1,t321_2,t322_1,t322_2,	t322_3,t323_1,t324_1,t325_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_moore bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 5',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_moore b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 5) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 186;	
UPDATE layer
SET
  drawingorder = 3215,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t112_2,	t113_1_1, t113_1_2, t113_1_3,t113_1_4,t113_1_5,t113_1_6,t121_1_1,t121_1_2,t121_1_3,t121_1_4,t121_1_5,t121_1_6,t121_1_7,t121_1_8,t121_1_9,t121_1_10,t121_1_11,t121_1_12,t121_1_13,	t121_2,t121_3,t211_1_1,t211_1_2,t211a,t212_1,t212_2,t22_1,t22_2,t22a,t311a_1,t311a_2,t311b_1,t311b_2,t312_1,t313_1,	t313_2,t313_3,t313_4,t315_1,t315_2,t315_3,t317_1_1,t317_2_1,t321_1,t321_2,t322_1,t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_stillgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.lrt_code, b.geom, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_stillgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 187;	
UPDATE layer
SET
  drawingorder = 3245,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t112_2,	t113_1_1, t113_1_2, t113_1_3,t113_1_4,t113_1_5,t113_1_6,t121_1_1,t121_1_2,t121_1_3,t121_1_4,t121_1_5,t121_1_6,t121_1_7,t121_1_8,t121_1_9,t121_1_10,t121_1_11,t121_1_12,t121_1_13,	t121_2,t121_3,t211_1_1,t211_1_2,t211a,t212_1,t212_2,t22_1,t22_2,t22a,t311a_1,t311a_2,t311b_1,t311b_2,t312_1,t313_1,	t313_2,t313_3,t313_4,t315_1,t315_2,t315_3,t317_1_1,t317_2_1,t321_1,t321_2,t322_1,t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_stillgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 5',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_stillgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 5) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 188;	
UPDATE layer
SET
  drawingorder = 3235,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1,	t111_2,	t112_1,	t112_2,	t113_1_1, t113_1_2, t113_1_3,t113_1_4,t113_1_5,t113_1_6,t121_1_1,t121_1_2,t121_1_3,t121_1_4,t121_1_5,t121_1_6,t121_1_7,t121_1_8,t121_1_9,t121_1_10,t121_1_11,t121_1_12,t121_1_13,	t121_2,t121_3,t211_1_1,t211_1_2,t211a,t212_1,t212_2,t22_1,t22_2,t22a,t311a_1,t311a_2,t311b_1,t311b_2,t312_1,t313_1,	t313_2,t313_3,t313_4,t315_1,t315_2,t315_3,t317_1_1,t317_2_1,t321_1,t321_2,t322_1,t322_2, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_stillgewaesser bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 6',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_stillgewaesser b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 6) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 189;	
UPDATE layer
SET
  drawingorder = 3214,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1,t113_1,t121_1,t121_2,t131_1_1,t131_1_2,t131_2_1,t131_2_2,t131_3,t132_1,t132_2,t211_1_1,t211_1_2,t211_1_3,t211_3_1,t212_1,t22_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t321_1,t321_2,t322_1,t322_2,t322_3,t323_1,t324_1,t325_1,t326_1,t327_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_offenland bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_offenland b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7 ) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 190;	
UPDATE layer
SET
  drawingorder = 3236,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1,t113_1,t121_1,t121_2,t131_1_1,t131_1_2,t131_2_1,t131_2_2,t131_3,t132_1,t132_2,t211_1_1,t211_1_2,t211_1_3,t211_3_1,t212_1,t22_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t321_1,t321_2,t322_1,t322_2,t322_3,t323_1,t324_1,t325_1,t326_1,t327_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_offenland bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 6',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_offenland b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 6 ) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 191;	
UPDATE layer
SET
  drawingorder = 3246,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1,t113_1,t121_1,t121_2,t131_1_1,t131_1_2,t131_2_1,t131_2_2,t131_3,t132_1,t132_2,t211_1_1,t211_1_2,t211_1_3,t211_3_1,t212_1,t22_1,t22_2,t311_1_1,t311_1_2,t311_1_3,t311_1_4,t311_1_5,t321_1,t321_2,t322_1,t322_2,t322_3,t323_1,t324_1,t325_1,t326_1,t327_1,t328_1_1, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_offenland bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 5',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_offenland b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 5 ) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 192;	
UPDATE layer
SET
  drawingorder = 3213,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t321_1, t321_2, t321_3, t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_kueste bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 7',
  data = 'geom from (SELECT b.oid, b.geom, lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_kueste b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 7) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 193;	
UPDATE layer
SET
  drawingorder = 3237,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t321_1, t321_2, t321_3, t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_kueste bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 6',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_kueste b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 6) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 194;	
UPDATE layer
SET
  drawingorder = 3247,
  pfad = 'SELECT bs.id AS bogen_id, kk.id as kampagne_id, bs.kartiergebiet_id, bs.kartierebene_id, bs.bogenart_id, lrt_gr, lrt_code, label, biotopname, standort, la_sper, la_sp_txt, fb_id, flaeche, '' AS uebernehmen, schutz_bio, schutz_ffh, alt_giscod, alt_lfd_nr, see_nr, alt_bearb, alt_datp20, alt_datffh, lrt, eu_nr, erhaltung, hc, hcp, '' nc, uc1, uc2, vegeinheit, '' habitate, beschreibg, e_datum, l_datum, bearbeiter, foto, '' as fotos,  loeschen, druck, aend_datum, korrektur,  geprueft, pruefer, pruefdatum, lock, status, version, bew_datum, old_id, unb, altbestand, bs.kartierobjekt_id, bs.kartierer, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter, bs.geom, '' as t1, t111_1, t112_1, t112a, t113_1, t121_1, t121_2,        t131_1_1, t131_1_2, t131_1_3, t131_1_4, t131_1_5, t141_1, t141_2_1,        t141_2_2, t141_2_3, t141_2_4, t141_2_5, t141_2_6, t142_1, t142_2,  t143,        t144, t145_1, t145_2, t145_3, t145_4, t145_5, t151_1, t151_2,        t152, t153, t154_1_1, t154_1_2, t154_1_3, t154_1_4, t154_1_5,        t154_1_6, t154_2, t155_1, t156, '' as t2, t211_1_1, t211_1_2, t211_1_3,        t22_1_1, t22_2, '' as t3, t311_1_1, t311_1_2, t311_1_3, t311_1_4, t311_1_5,        t311_1_6, t321_1, t321_2, t321_3, t322_1, t323_1, t324_1, t324_2, t325_1, t326_1, t328, t3211_1, t3211_2_1, t3211_3, t3211_2_2, t3211_4, 'Gesamtbewertung Habitatstrukturen' as dummy1, sys_habit, bea_habit, 'Gesamtbewertung Lebensraumtypische Arten' as dummy2, sys_leben, bea_leben, 'Gesamtbewertung Beeinträchtigungen' as dummy3, sys_beein, bea_beein, 'Erhaltungszustand' as dummy4, sys_erhalt, bea_erhalt, bemerkung, bew_datum, bearbeiter FROM bewertungsboegen_kueste bs JOIN kartiergebiete kg ON bs.kartiergebiet_id = kg.id JOIN kampagnen kk ON kg.kampagne_id = kk.id JOIN mvbio.kartierebenen ke ON bs.kartierebene_id = ke.id WHERE bs.kampagne_id = 5',
  data = 'geom from (SELECT b.oid, b.geom, b.lrt_code, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.bewertungsboegen_kueste b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE b.kampagne_id = 5) as foo using unique oid using srid=5650

'
WHERE Layer_ID = 195;	
UPDATE layer
SET
  drawingorder = 3116,
  pfad = 'SELECT
  kk.abk AS kampagne,
  kk.id as kampagne_id,
  kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id,
  b.bogenart, coalesce(giscode, label) AS label,
  biotopname,
  nummer, standort, la_sper, la_sp_txt, fb_id, flaeche,
  '' AS uebernehme,
  '' AS uebernehme_als_verlustbogen,
   schutz_bio,
  alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20,
  hc,
  hcp,
  uc1, uc2,
  vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna,
  e_datum,
  l_datum,
  created_at,
  bearbeiter, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.id, b.kartierobjekt_id, b.kartierer, userid, unb, altbestand,
  b.created_at,
  b.geom
FROM
  kurzboegen b JOIN
  archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN
  archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN
  archiv.kartierebenen ke ON b.kartierebene_id = ke.id
WHERE
  b.kampagne_id = 1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.hc as beschriftung, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter FROM archiv.kurzboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id WHERE b.kampagne_id = 1) as foo using unique oid using srid=5650 '
WHERE Layer_ID = 196;	
UPDATE layer
SET
  drawingorder = 4120,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche,  schutz_bio, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, hc, uc1, uc2, hcp, '' as dummy1, '' as dummy2, vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna, e_datum, l_datum, bearbeiter, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.id, b.kartierobjekt_id, b.kartierer, userid, unb, altbestand, b.geom FROM kurzboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE kk.id = 1',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter FROM archiv.kurzboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id WHERE b.kampagne_id = 1) as foo using unique oid using srid=5650 '
WHERE Layer_ID = 197;	
UPDATE layer
SET
  drawingorder = 4110,
  pfad = ' SELECT    kk.abk         AS kampagne,          kk.id          AS kampagne_id,          kg.bezeichnung AS kartiergebiet,          b.kartiergebiet_id,          ke.bezeichnung AS kartierebene,          b.kartierebene_id,          b.bogenart_id,          label,          giscode,          biotopname,          nummer,          standort,          la_sper,          la_sp_txt,          fb_id,          flaeche,          '' AS uebernehme,          schutz_bio,          schutz_ffh,          alt_giscod,          alt_lfd_nr,          see_nr,          alt_bearb,          alt_datp20,          alt_datffh,          lrt,          eu_nr,          erhaltung,          hc,          hc AS hc_abk,          hcp,          ''                              nc,          (nc.code)[1]                    as nc1,          (nc.flaechendeckung_prozent)[1] AS ncp1,          (nc.code)[2]                    AS nc2,          (nc.flaechendeckung_prozent)[2] AS ncp2,          (nc.code)[3]                    AS nc3,          (nc.flaechendeckung_prozent)[3] AS ncp3,          (nc.code)[4]                    AS nc4,          (nc.flaechendeckung_prozent)[4] AS ncp4,          (nc.code)[5]                    AS nc5,          (nc.flaechendeckung_prozent)[5] AS ncp5,          (nc.code)[6]                    AS nc6,          (nc.flaechendeckung_prozent)[6] AS ncp6,          (nc.code)[7]                    AS nc7,          (nc.flaechendeckung_prozent)[7] AS ncp7,          (nc.code)[8]                    AS nc8,          (nc.flaechendeckung_prozent)[8] AS ncp8,          uc1,          uc1 AS uc1_abk,          uc2,          uc2 AS uc2_abk,          vegeinheit,          ''              habitate,          (hv.code)[1]  AS hv1,          (hv.code)[2]  AS hv2,          (hv.code)[3]  AS hv3,          (hv.code)[4]  AS hv4,          (hv.code)[5]  AS hv5,          (hv.code)[6]  AS hv6,          (hv.code)[7]  AS hv7,          (hv.code)[8]  AS hv8,          (hv.code)[9]  AS hv9,          (hv.code)[10] AS hv10,          (hv.code)[11] AS hv11,          (hv.code)[12] AS hv12,          (hv.code)[13] AS hv13,          (hv.code)[14] AS hv14,          (hv.code)[15] AS hv15,          (hv.code)[16] AS hv16,          (hv.code)[17] AS hv17,          (hv.code)[18] AS hv18,          (hv.code)[19] AS hv19,          (hv.code)[20] AS hv20,          (hv.code)[21] AS hv21,          beschreibg, replace(substring(beschreibg, 1, 1300), chr(10), ' ') ||          CASE                    WHEN length(beschreibg) > 1300 THEN ' ...Fortsetzung'                    ELSE ''          END AS beschreibg_1,          CASE                    WHEN length(beschreibg) > 1300 THEN 'Fortsetzung... '                                        || substring(beschreibg, 1300)                    ELSE NULL          END AS beschreibg_2,          wert_krit_1,          wert_krit_9,          wert_krit_2,          wert_krit_10,          wert_krit_3,          wert_krit_11,          wert_krit_4,          wert_krit_12,          wert_krit_5,          wert_krit_13,          wert_krit_6,          wert_krit_14,          wert_krit_7,          wert_krit_15,          wert_krit_8,          wert_krit_16,          CASE                    WHEN wert_krit_1 THEN 'Artenreichtum (Flora)'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_2 THEN 'Vorkommen seltener/typischer Tierarten'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_3 THEN 'seltener/gefährdeter Pflanzenbestand'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_4 THEN 'seltene/gefährdete Pflanzengesellschaft'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_5 THEN 'natürliche/naturnahe Ausprägung des Biotops'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_6 THEN 'gute Ausbildung eines halbnatürlichen Biotops'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_7 THEN 'typische Zonierung von Biotoptypen'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_8 THEN 'Struktur- und Habitatreichtum'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_9 THEN 'vielfältige Standortverhältnisse'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_10 THEN 'historische Nutzungsform'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_11 THEN 'aktuelle Nutzung'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_12 THEN 'Flächengröße/Länge'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_13 THEN 'Umgebung relativ störungsarm'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_14 THEN 'landschaftsprägender Charakter'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_15 THEN 'Trittsteinbiotop/Verbundfunktion'                                        ||chr(10)                    ELSE ''          END                    ||          CASE                    WHEN wert_krit_16 THEN 'Pflanzenbestand nach Florenschutzkonzept'                                        ||chr(10)                    ELSE ''          END AS wert_krit,          gefaehrdg,          gefcode,          (gf.code)[1] AS gf1,          (gf.code)[2] AS gf2,          (gf.code)[3] AS gf3,          (gf.code)[4] AS gf4,          (gf.code)[5] AS gf5,          (gf.code)[6] AS gf6,          (gf.code)[7] AS gf7,          ohnegefahr,          empfehlung,          ''           AS empfcode,          (em.code)[1] AS em1,          (em.code)[2] AS em2,          (em.code)[3] AS em3,          (em.code)[4] AS em4,          ''           AS dummy24,          ''           AS dummy25,          ''           AS dummy26,          ''           AS dummy35,          ''           AS dummy36,          ''           AS dummy37,          ''           AS dummy38,          ''           AS dummy39,          ''           AS dummy40,          substrat_1,          trophie_1,          wasser_1,          relief_1,          exposition_1,          substrat_2,          trophie_2,          wasser_2,          relief_2,          exposition_2,          substrat_3,          trophie_3,          wasser_3,          relief_3,          exposition_3,          substrat_4,          trophie_4,          wasser_4,          relief_4,          exposition_4,          substrat_5,          trophie_5,          CASE                    WHEN trophie_1 IS NULL THEN '        '                    WHEN trophie_1 THEN '      g'                    ELSE 'k      '          END                    || '    dystroph' AS trophie_1_p,          CASE                    WHEN trophie_2 IS NULL THEN '        '                    WHEN trophie_2 THEN '      g'                    ELSE 'k      '          END                    || '    oligotroph' AS trophie_2_p,          CASE                    WHEN trophie_3 IS NULL THEN '        '                    WHEN trophie_3 THEN '      g'                    ELSE 'k      '          END                    || '    mesotroph' AS trophie_3_p,          CASE                    WHEN trophie_4 IS NULL THEN '        '                    WHEN trophie_4 THEN '      g'                    ELSE 'k      '          END                    || '    eutroph' AS trophie_4_p,          CASE                    WHEN trophie_5 IS NULL THEN '        '                    WHEN trophie_5 THEN '      g'                    ELSE 'k      '          END                    || '    poly-/hypertroph' AS trophie_5_p,          wasser_5,          relief_5,          exposition_5,          substrat_6,          '' AS dummy3,          wasser_6,          relief_6,          exposition_6,          substrat_7,          '' AS dummy4,          wasser_7,          relief_7,          exposition_7,          CASE                    WHEN exposition_1 IS NULL THEN '        '                    WHEN exposition_1 THEN '      g'                    ELSE 'k      '          END                    || '    N' AS exposition_1_p,          CASE                    WHEN exposition_2 IS NULL THEN '        '                    WHEN exposition_2 THEN '      g'                    ELSE 'k      '          END                    || '    NO' AS exposition_2_p,          CASE                    WHEN exposition_3 IS NULL THEN '        '                    WHEN exposition_3 THEN '      g'                    ELSE 'k      '          END                    || '    O' AS exposition_3_p,          CASE                    WHEN exposition_4 IS NULL THEN '        '                    WHEN exposition_4 THEN '      g'                    ELSE 'k      '          END                    || '    SO' AS exposition_4_p,          CASE                    WHEN exposition_5 IS NULL THEN '        '                    WHEN exposition_5 THEN '      g'                    ELSE 'k      '          END                    || '    S' AS exposition_5_p,          CASE                    WHEN exposition_6 IS NULL THEN '        '                    WHEN exposition_6 THEN '      g'                    ELSE 'k      '          END                    || '    SW' AS exposition_6_p,          CASE                    WHEN exposition_7 IS NULL THEN '        '                    WHEN exposition_7 THEN '      g'                    ELSE 'k      '          END                    || '    W' AS exposition_7_p,          CASE                    WHEN exposition_8 IS NULL THEN '        '                    WHEN exposition_8 THEN '      g'                    ELSE 'k      '          END                    || '    NW' AS exposition_8_p,          substrat_8,          '' AS dummy5,          wasser_8,          relief_8,          CASE                    WHEN relief_1 IS NULL THEN '        '                    WHEN relief_1 THEN '      g'                    ELSE 'k      '          END                    || '    eben' AS relief_1_p,          CASE                    WHEN relief_2 IS NULL THEN '        '                    WHEN relief_2 THEN '      g'                    ELSE 'k      '          END                    || '    wellig' AS relief_2_p,          CASE                    WHEN relief_3 IS NULL THEN '        '                    WHEN relief_3 THEN '      g'                    ELSE 'k      '          END                    || '    kuppig' AS relief_3_p,          CASE                    WHEN relief_4 IS NULL THEN '        '                    WHEN relief_4 THEN '      g'                    ELSE 'k      '          END                    || '    dünig' AS relief_4_p,          CASE                    WHEN relief_5 IS NULL THEN '        '                    WHEN relief_5 THEN '      g'                    ELSE 'k      '          END                    || '    Berg/Rücken' AS relief_5_p,          CASE                    WHEN relief_6 IS NULL THEN '        '                    WHEN relief_6 THEN '      g'                    ELSE 'k      '          END                    || '    Riedel' AS relief_6_p,          CASE                    WHEN relief_7 IS NULL THEN '        '                    WHEN relief_7 THEN '      g'                    ELSE 'k      '          END                    || '    Flachhang <= 9°' AS relief_7_p,          CASE                    WHEN relief_8 IS NULL THEN '        '                    WHEN relief_8 THEN '      g'                    ELSE 'k      '          END                    || '    Steilhang > 9°' AS relief_8_p,          CASE                    WHEN relief_9 IS NULL THEN '        '                    WHEN relief_9 THEN '      g'                    ELSE 'k      '          END                    || '    Nische' AS relief_9_p,          CASE                    WHEN relief_10 IS NULL THEN '        '                    WHEN relief_10 THEN '      g'                    ELSE 'k      '          END                    || '    Senke/Strecksenke' AS relief_10_p,          CASE                    WHEN relief_11 IS NULL THEN '        '                    WHEN relief_11 THEN '      g'                    ELSE 'k      '          END                    || '    Kerbtal' AS relief_11_p,          CASE                    WHEN relief_12 IS NULL THEN '        '                    WHEN relief_12 THEN '      g'                    ELSE 'k      '          END                    || '    Sohlental' AS relief_12_p,          exposition_8,          substrat_9,          '' AS dummy6,          wasser_9,          CASE                    WHEN wasser_1 IS NULL THEN '        '                    WHEN wasser_1 THEN '      x'                    ELSE 'x      '          END                    || '    trocken' AS wasser_1_p,          CASE                    WHEN wasser_2 IS NULL THEN '        '                    WHEN wasser_2 THEN '      g'                    ELSE 'k      '          END                    || '    mäßig trocken' AS wasser_2_p,          CASE                    WHEN wasser_3 IS NULL THEN '        '                    WHEN wasser_3 THEN '      g'                    ELSE 'k      '          END                    || '    wechselfeucht' AS wasser_3_p,          CASE                    WHEN wasser_4 IS NULL THEN '        '                    WHEN wasser_4 THEN '      g'                    ELSE 'k      '          END                    || '    frisch' AS wasser_4_p,          CASE                    WHEN wasser_5 IS NULL THEN '        '                    WHEN wasser_5 THEN '      g'                    ELSE 'k      '          END                    || '    feucht' AS wasser_5_p,          CASE                    WHEN wasser_6 IS NULL THEN '        '                    WHEN wasser_6 THEN '      g'                    ELSE 'k      '          END                    || '    sehr feucht' AS wasser_6_p,          CASE                    WHEN wasser_7 IS NULL THEN '        '                    WHEN wasser_7 THEN '      g'                    ELSE 'k      '          END                    || '    nass' AS wasser_7_p,          CASE                    WHEN wasser_8 IS NULL THEN '        '                    WHEN wasser_8 THEN '      g'                    ELSE 'k      '          END                    || '    offenes Wasser' AS wasser_8_p,          CASE                    WHEN wasser_9 IS NULL THEN '        '                    WHEN wasser_9 THEN '      g'                    ELSE 'k      '          END                    || '    quellig' AS wasser_9_p,          relief_9,          '' AS dummy7,          substrat_10,          CASE                    WHEN substrat_1 IS NULL THEN '        '                    WHEN substrat_1 THEN '     g'                    ELSE 'k     '          END                    || '    Torf, wenig gestört' AS substrat_1_p,          CASE                    WHEN substrat_2 IS NULL THEN '        '                    WHEN substrat_2 THEN '     g'                    ELSE 'k     '          END                    || '    Torf, degradiert' AS substrat_2_p,          CASE                    WHEN substrat_3 IS NULL THEN '        '                    WHEN substrat_3 THEN '     g'                    ELSE 'k     '          END                    || '    Antorf' AS substrat_3_p,          CASE                    WHEN substrat_4 IS NULL THEN '        '                    WHEN substrat_4 THEN '     g'                    ELSE 'k     '          END                    || '    Sand' AS substrat_4_p,          CASE                    WHEN substrat_5 IS NULL THEN '        '                    WHEN substrat_5 THEN '     g'                    ELSE 'k     '          END                    || '    Kies/Steine' AS substrat_5_p,          CASE                    WHEN substrat_6 IS NULL THEN '        '                    WHEN substrat_6 THEN '     g'                    ELSE 'k     '          END                    || '    Lehm' AS substrat_6_p,          CASE                    WHEN substrat_7 IS NULL THEN '        '                    WHEN substrat_7 THEN '     g'                    ELSE 'k     '          END                    || '    Ton' AS substrat_7_p,          CASE                    WHEN substrat_8 IS NULL THEN '        '                    WHEN substrat_8 THEN '     g'                    ELSE 'k     '          END                    || '    Halbkalk/Kalk' AS substrat_8_p,          CASE                    WHEN substrat_9 IS NULL THEN '        '                    WHEN substrat_9 THEN '     g'                    ELSE 'k     '          END                    || '    Schlamm/Faulschlamm' AS substrat_9_p,          CASE                    WHEN substrat_10 IS NULL THEN '        '                    WHEN substrat_10 THEN '     g'                    ELSE 'k     '          END                    || '    gestörter Boden' AS substrat_10_p,          ''                                  AS dummy8,          ''                                  AS dummy9,          relief_10,          '' AS dummy10,          '' AS dummy11,          '' AS dummy12,          '' AS dummy13,          relief_11,          '' AS dummy14,          '' AS dummy15,          '' AS dummy16,          '' AS dummy17,          relief_12,          '' AS dummy18,          '' AS dummy19,          '' AS dummy20,          '' AS dummy21,          '' AS dummy22,          '' AS dummy23,          ''    nutzintens_1,          nutzungsart_5,          umgebung_1,          umgebung_14,          nutzintens_2,          nutzungsart_6,          umgebung_2,          umgebung_15,          nutzintens_3,          nutzungsart_7,          umgebung_3,          umgebung_16,          nutzintens_4,          CASE                    WHEN nutzintens_1 IS NULL THEN '        '                    WHEN nutzintens_1 THEN '      g'                    ELSE 'k      '          END                    || '    intensiv' AS nutzintens_1_p,          CASE                    WHEN nutzintens_2 IS NULL THEN '        '                    WHEN nutzintens_2 THEN '      g'                    ELSE 'k      '          END                    || '    extensiv' AS nutzintens_2_p,          CASE                    WHEN nutzintens_3 IS NULL THEN '        '                    WHEN nutzintens_3 THEN '      g'                    ELSE 'k      '          END                    || '    aufgelassen' AS nutzintens_3_p,          CASE                    WHEN nutzintens_4 IS NULL THEN '        '                    WHEN nutzintens_4 THEN '      g'                    ELSE 'k      '          END                    || '    keine Nutzung' AS nutzintens_4_p,          nutzungsart_8,          umgebung_4,          umgebung_17,          '' AS dummy27,          nutzungsart_9,          umgebung_5,          umgebung_18,          '' AS dummy28,          nutzungsart_10,          umgebung_6,          umgebung_19,          nutzungsart_1,          nutzungsart_11,          umgebung_7,          umgebung_20,          nutzungsart_2,          nutzungsart_12,          umgebung_8,          umgebung_21,          nutzungsart_3,          nutzungsart_13,          umgebung_9,          umgebung_22,          nutzungsart_4,          nutzungsart_14,          CASE                    WHEN nutzungsart_1 IS NULL THEN '        '                    WHEN nutzungsart_1 THEN '      g'                    ELSE 'k      '          END                    || '    Acker' AS nutzungsart_1_p,          CASE                    WHEN nutzungsart_2 IS NULL THEN '        '                    WHEN nutzungsart_2 THEN '      g'                    ELSE 'k      '          END                    || '    Wiese' AS nutzungsart_2_p,          CASE                    WHEN nutzungsart_3 IS NULL THEN '        '                    WHEN nutzungsart_3 THEN '      g'                    ELSE 'k      '          END                    || '    Weide' AS nutzungsart_3_p,          CASE                    WHEN nutzungsart_4 IS NULL THEN '        '                    WHEN nutzungsart_4 THEN '      g'                    ELSE 'k      '          END                    || '    forstliche Nutzung' AS nutzungsart_4_p,          CASE                    WHEN nutzungsart_5 IS NULL THEN '        '                    WHEN nutzungsart_5 THEN '      g'                    ELSE 'k      '          END                    || '    Fischerei' AS nutzungsart_5_p,          CASE                    WHEN nutzungsart_6 IS NULL THEN '        '                    WHEN nutzungsart_6 THEN '      g'                    ELSE 'k      '          END                    || '    Angeln' AS nutzungsart_6_p,          CASE                    WHEN nutzungsart_7 IS NULL THEN '        '                    WHEN nutzungsart_7 THEN '      g'                    ELSE 'k      '          END                    || '    Erholung' AS nutzungsart_7_p,          CASE                    WHEN nutzungsart_8 IS NULL THEN '        '                    WHEN nutzungsart_8 THEN '      g'                    ELSE 'k      '          END                    || '    Kleingartenbau' AS nutzungsart_8_p,          CASE                    WHEN nutzungsart_9 IS NULL THEN '        '                    WHEN nutzungsart_9 THEN '      g'                    ELSE 'k      '          END                    || '    Erwerbsgartenbau' AS nutzungsart_9_p,          CASE                    WHEN nutzungsart_10 IS NULL THEN '        '                    WHEN nutzungsart_10 THEN '      g'                    ELSE 'k      '          END                    || '    Ferienhäuser' AS nutzungsart_10_p,          CASE                    WHEN nutzungsart_11 IS NULL THEN '        '                    WHEN nutzungsart_11 THEN '      g'                    ELSE 'k      '          END                    || '    Bodenentnahme' AS nutzungsart_11_p,          CASE                    WHEN nutzungsart_12 IS NULL THEN '        '                    WHEN nutzungsart_12 THEN '      g'                    ELSE 'k      '          END                    || '    Verkehr' AS nutzungsart_12_p,          CASE                    WHEN nutzungsart_13 IS NULL THEN '        '                    WHEN nutzungsart_13 THEN '      g'                    ELSE 'k      '          END                    || '    Ver-/Entsorgungsanlage' AS nutzungsart_13_p,          CASE                    WHEN nutzungsart_14 IS NULL THEN '        '                    WHEN nutzungsart_14 THEN '      g'                    ELSE 'k      '          END                    || '    sonstige Nutzung:' AS nutzungsart_14_p,          umgebung_10,          umgebung_23,          '' AS dummy29,          nutzartzus,          '' AS dummy30,          umgebung_11,          umgebung_24,          '' AS dummy31,          '' AS dummy32,          umgebung_12,          umgebung_25,          CASE                    WHEN umgebung_1 IS NULL THEN '        '                    WHEN umgebung_1 THEN '      g'                    ELSE 'k      '          END                    || '    Acker/Gartenbau' AS umgebung_1_p,          CASE                    WHEN umgebung_2 IS NULL THEN '        '                    WHEN umgebung_2 THEN '      g'                    ELSE 'k      '          END                    || '    Ackerbrache' AS umgebung_2_p,          CASE                    WHEN umgebung_3 IS NULL THEN '        '                    WHEN umgebung_3 THEN '      g'                    ELSE 'k      '          END                    || '    Grünland, intensiv' AS umgebung_3_p,          CASE                    WHEN umgebung_4 IS NULL THEN '        '                    WHEN umgebung_4 THEN '      g'                    ELSE 'k      '          END                    || '    Grünland, extensiv' AS umgebung_4_p,          CASE                    WHEN umgebung_5 IS NULL THEN '        '                    WHEN umgebung_5 THEN '      g'                    ELSE 'k      '          END                    || '    Laub-/Mischwald' AS umgebung_5_p,          CASE                    WHEN umgebung_6 IS NULL THEN '        '                    WHEN umgebung_6 THEN '      g'                    ELSE 'k      '          END                    || '    Nadelwald' AS umgebung_6_p,          CASE                    WHEN umgebung_7 IS NULL THEN '        '                    WHEN umgebung_7 THEN '      g'                    ELSE 'k      '          END                    || '    Feuchtwald/-gebüsch' AS umgebung_7_p,          CASE                    WHEN umgebung_8 IS NULL THEN '        '                    WHEN umgebung_8 THEN '      g'                    ELSE 'k      '          END                    || '    Gehölz' AS umgebung_8_p,          CASE                    WHEN umgebung_9 IS NULL THEN '        '                    WHEN umgebung_9 THEN '      g'                    ELSE 'k      '          END                    || '    Röhricht/Feuchtbrache' AS umgebung_9_p,          CASE                    WHEN umgebung_10 IS NULL THEN '        '                    WHEN umgebung_10 THEN '      g'                    ELSE 'k      '          END                    || '    Hochstauden-/Ruderalflur' AS umgebung_10_p,          CASE                    WHEN umgebung_11 IS NULL THEN '        '                    WHEN umgebung_11 THEN '      g'                    ELSE 'k      '          END                    || '    Graben' AS umgebung_11_p,          CASE                    WHEN umgebung_12 IS NULL THEN '        '                    WHEN umgebung_12 THEN '      g'                    ELSE 'k      '          END                    || '    Fließgewässer' AS umgebung_12_p,          CASE                    WHEN umgebung_13 IS NULL THEN '        '                    WHEN umgebung_13 THEN '      g'                    ELSE 'k      '          END                    || '    Stillgewässer' AS umgebung_13_p,          CASE                    WHEN umgebung_14 IS NULL THEN '        '                    WHEN umgebung_14 THEN '      g'                    ELSE 'k      '          END                    || '    Trockenbiotop' AS umgebung_14_p,          CASE                    WHEN umgebung_15 IS NULL THEN '        '                    WHEN umgebung_15 THEN '      g'                    ELSE 'k      '          END                    || '    Grünanlage/Kleingarten' AS umgebung_15_p,          CASE                    WHEN umgebung_16 IS NULL THEN '        '                    WHEN umgebung_16 THEN '      g'                    ELSE 'k      '          END                    || '    Weg' AS umgebung_16_p,          CASE                    WHEN umgebung_17 IS NULL THEN '        '                    WHEN umgebung_17 THEN '      g'                    ELSE 'k      '          END                    || '    Straße/Parkplatz' AS umgebung_17_p,          CASE                    WHEN umgebung_18 IS NULL THEN '        '                    WHEN umgebung_18 THEN '      g'                    ELSE 'k      '          END                    || '    Bahnanlage' AS umgebung_18_p,          CASE                    WHEN umgebung_19 IS NULL THEN '        '                    WHEN umgebung_19 THEN '      g'                    ELSE 'k      '          END                    || '    Gewerbe/Industrie' AS umgebung_19_p,          CASE                    WHEN umgebung_20 IS NULL THEN '        '                    WHEN umgebung_20 THEN '      g'                    ELSE 'k      '          END                    || '    Silo/Stallanlage' AS umgebung_20_p,          CASE                    WHEN umgebung_21 IS NULL THEN '        '                    WHEN umgebung_21 THEN '      g'                    ELSE 'k      '          END                    || '    Gebäude/Siedlung' AS umgebung_21_p,          CASE                    WHEN umgebung_22 IS NULL THEN '        '                    WHEN umgebung_22 THEN '      g'                    ELSE 'k      '          END                    || '    Spülfeld/Halde' AS umgebung_22_p,          CASE                    WHEN umgebung_23 IS NULL THEN '        '                    WHEN umgebung_23 THEN '      g'                    ELSE 'k      '          END                    || '    Bodenentnahme' AS umgebung_23_p,          CASE                    WHEN umgebung_24 IS NULL THEN '        '                    WHEN umgebung_24 THEN '      g'                    ELSE 'k      '          END                    || '    Deich / Damm' AS umgebung_24_p,          CASE                    WHEN umgebung_25 IS NULL THEN '        '                    WHEN umgebung_25 THEN '      g'                    ELSE 'k      '          END                    || '    sonstige:' AS umgebung_25_p,          ''                           AS dummy33,          ''                           AS dummy34,          umgebung_13,          umgebzus,          '' AS pflanzen_dominant,          '' AS pflanzen_zahlreich,          '' AS pflanzen_vereinzelt,          fauna,          literatur,          e_datum,          l_datum,          bearbeiter,          foto,          '' :: text AS fotos,          '/var/www/data/mvbio/boegen/'                    || kk.abk                    || '/'                    || b.giscode                    ||          CASE                    WHEN kk.abk LIKE 'BK%' THEN ''                    ELSE '_GB'          END                    || '.pdf' AS pdf_bogen,          loeschen,          druck,          aend_datum,          korrektur,          geprueft,          pruefer,          pruefdatum,          lock,          status,          version,          import_table,          old_id,          userid,          unb,          altbestand,          b.id,          b.kartierobjekt_id AS kartierung_id,
  b.kartierer,
  kk.kartierungsart                    || kk.abk                    || COALESCE('-'                    || kg.losnummer, '') AS filter,          b.geom FROM      grundboegen b JOIN      archiv.kartiergebiete kg ON        b.kartiergebiet_id = kg.id JOIN      archiv.kampagnen kk ON        kg.kampagne_id = kk.id JOIN      archiv.kartierebenen ke ON        b.kartierebene_id = ke.id LEFT JOIN archiv.biotoptypen_nebencodes_agg nc ON        b.id = nc.kartierung_id LEFT JOIN archiv.habitatvorkommen_agg hv ON        b.id = hv.kartierung_id LEFT JOIN archiv.beeintraechtigungen_gefaehrdungen_agg gf ON        b.id = gf.kartierung_id LEFT JOIN archiv.empfehlungen_massnahmen_agg em ON        b.id = em.kartierung_id WHERE kk.id = 1 ',
  data = 'geom from (SELECT b.geom, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM archiv.grundboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kk.id = 1) as foo using unique oid using srid=5650'
WHERE Layer_ID = 198;	
UPDATE layer
SET
  drawingorder = 3126,
  pfad = 'SELECT kk.abk AS kampagne, kk.id as kampagne_id, kg.bezeichnung AS kartiergebiet, b.kartiergebiet_id, ke.bezeichnung AS kartierebene, b.kartierebene_id, b.bogenart_id, label, giscode, biotopname, nummer, standort, la_sper, la_sp_txt, fb_id, flaeche,  schutz_bio, alt_giscod, alt_lfd_nr, alt_bearb, alt_datp20, hc, uc1, uc2, hcp, '' as dummy1, '' as dummy2, vegeinheit, wert_krit_1, wert_krit_9, wert_krit_2, wert_krit_10, wert_krit_3, wert_krit_11, wert_krit_4, wert_krit_12, wert_krit_5, wert_krit_13, wert_krit_6, wert_krit_14, wert_krit_7, wert_krit_15, wert_krit_8, wert_krit_16, gefaehrdg, '' AS gefcode, ohnegefahr, empfehlung, '' AS empfcode, fauna, e_datum, l_datum, bearbeiter, loeschen, druck, aend_datum, korrektur, geprueft, pruefer, pruefdatum, lock, status, version, import_table, b.id, b.kartierobjekt_id, b.kartierer, userid, unb, altbestand, b.geom FROM kurzboegen b JOIN archiv.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id JOIN archiv.kartierebenen ke ON b.kartierebene_id = ke.id WHERE b.kampagne_id = 3',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.hc as beschriftung, b.kartiergebiet_id, b.kartierebene_id, b.bogenart_id, b.bearbeitungsstufe, b.oid, b.kartierer, b.geprueft, k.kartierungsart || k.abk || coalesce('-' || r.losnummer, '') AS filter, b.hc FROM archiv.kurzboegen b JOIN archiv.kampagnen k ON b.kampagne_id = k.id JOIN archiv.kartiergebiete r ON b.kartiergebiet_id = r.id WHERE b.kampagne_id = 3) as foo using unique oid using srid=5650 '
WHERE Layer_ID = 199;	
UPDATE layer
SET
  drawingorder = 1000,
  pfad = '',
  data = ''
WHERE Layer_ID = 200;	
UPDATE layer
SET
  drawingorder = 1000,
  pfad = '',
  data = ''
WHERE Layer_ID = 201;	
UPDATE layer
SET
  drawingorder = 3112,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 1',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 1) as foo using unique id using srid=5650'
WHERE Layer_ID = 204;	
UPDATE layer
SET
  drawingorder = 3114,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 1',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 1) as foo using unique id using srid=5650'
WHERE Layer_ID = 205;	
UPDATE layer
SET
  drawingorder = 3124,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 3',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 3) as foo using unique id using srid=5650'
WHERE Layer_ID = 206;	
UPDATE layer
SET
  drawingorder = 3126,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 3',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 3) as foo using unique id using srid=5650'
WHERE Layer_ID = 207;	
UPDATE layer
SET
  drawingorder = 3134,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 2',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 2) as foo using unique id using srid=5650'
WHERE Layer_ID = 208;	
UPDATE layer
SET
  drawingorder = 3132,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 2',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 2) as foo using unique id using srid=5650'
WHERE Layer_ID = 209;	
UPDATE layer
SET
  drawingorder = 3211,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 7',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 7) as foo using unique id using srid=5650'
WHERE Layer_ID = 210;	
UPDATE layer
SET
  drawingorder = 3212,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 7',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 7) as foo using unique id using srid=5650'
WHERE Layer_ID = 211;	
UPDATE layer
SET
  drawingorder = 3221,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 8',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 8) as foo using unique id using srid=5650'
WHERE Layer_ID = 212;	
UPDATE layer
SET
  drawingorder = 3222,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 8',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 8) as foo using unique id using srid=5650'
WHERE Layer_ID = 213;	
UPDATE layer
SET
  drawingorder = 3231,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 6',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 6) as foo using unique id using srid=5650'
WHERE Layer_ID = 214;	
UPDATE layer
SET
  drawingorder = 3232,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 6',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 6) as foo using unique id using srid=5650'
WHERE Layer_ID = 215;	
UPDATE layer
SET
  drawingorder = 3241,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 5',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 5) as foo using unique id using srid=5650'
WHERE Layer_ID = 216;	
UPDATE layer
SET
  drawingorder = 3242,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 5',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 5) as foo using unique id using srid=5650'
WHERE Layer_ID = 217;	
UPDATE layer
SET
  drawingorder = 3311,
  pfad = 'SELECT
  id AS kampagne_id,
  abk,
  bezeichnung,
  datenschichten,
  erfassungszeitraum,
  umfang,
  '' as kartiergebiete,
  '' as kartierebenen,
  erstellt_am,
  erstellt_von,
  geaendert_am,
  geaendert_von,
  geom
FROM
  kampagnen
WHERE
  id = 4',
  data = 'geom from (SELECT oid, id, bezeichnung, geom FROM archiv.kampagnen WHERE id = 4) as foo using unique id using srid=5650'
WHERE Layer_ID = 218;	
UPDATE layer
SET
  drawingorder = 3312,
  pfad = 'SELECT
  kg.id AS kartiergebiet_id,
  kg.kampagne_id,
  kk.abk, '' AS zur_kampagne,
  kg.losnummer, kg.bezeichnung,
  kg.bemerkungen,
  kk.abk || kg.bezeichnung AS filter,
  kg.geom
FROM
  kartiergebiete kg JOIN
  kampagnen kk ON kg.kampagne_id = kk.id
WHERE
  kg.kampagne_id = 4',
  data = 'geom from (SELECT kg.oid, kg.id, kg.kampagne_id, kg.losnummer, kg.bezeichnung, kg.stelle_id, kg.id AS kartiergebietfilter, kk.abk, kg.geom FROM archiv.kartiergebiete kg JOIN archiv.kampagnen kk ON kg.kampagne_id = kk.id WHERE kg.kampagne_id = 4) as foo using unique id using srid=5650'
WHERE Layer_ID = 219;	
UPDATE layer
SET
  drawingorder = 5030,
  pfad = '',
  data = ''
WHERE Layer_ID = 220;	
UPDATE layer
SET
  drawingorder = 5040,
  pfad = '',
  data = ''
WHERE Layer_ID = 221;	
UPDATE layer
SET
  drawingorder = 5050,
  pfad = '',
  data = ''
WHERE Layer_ID = 222;	
UPDATE layer
SET
  drawingorder = 5060,
  pfad = '',
  data = ''
WHERE Layer_ID = 223;	
UPDATE layer
SET
  drawingorder = 6130,
  pfad = '',
  data = ''
WHERE Layer_ID = 224;	
UPDATE layer
SET
  drawingorder = 6020,
  pfad = 'SELECT * FROM gebietseinheiten.dvg WHERE kennung IN ('große kreisangehörige Stadt', 'kreisfreie Stadt', 'amtsfreie Gemeinde', 'Amt')',
  data = 'the_geom from (SELECT gid, gen, the_geom FROM gebietseinheiten.dvg WHERE kennung IN ('große kreisangehörige Stadt', 'kreisfreie Stadt', 'amtsfreie Gemeinde', 'Amt')) as foo using unique gid using srid=25833

'
WHERE Layer_ID = 225;	

UPDATE layer
SET
  drawingorder = 6030,
  pfad = 'SELECT * FROM gebietseinheiten.gemeinden WHERE true',
  data = 'geom from (SELECT schluessel, name, geom FROM gebietseinheiten.gemeinden) as foo using unique schluessel using srid=5650

'
WHERE Layer_ID = 238;	
UPDATE layer
SET
  drawingorder = 6112,
  pfad = 'SELECT * FROM mvl WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.mvl) as foo using unique gid using srid=5650'
WHERE Layer_ID = 226;	
UPDATE layer
SET
  drawingorder = 6110,
  pfad = 'SELECT * FROM mvkb WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.mvkb) as foo using unique gid using srid=5650'
WHERE Layer_ID = 227;	
UPDATE layer
SET
  drawingorder = 6114,
  pfad = 'SELECT * FROM awz WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.awz) as foo using unique gid using srid=5650'
WHERE Layer_ID = 228;	
UPDATE layer
SET
  drawingorder = 6100,
  pfad = 'SELECT gid, nr, nr_tf, label, gis_code, name, kreis1, kreis2, altkreis1, altkreis2, altkreis3, es, area_ha, geb_id_mv, name_mv, ausweis_mv, flaeche_mv, erfass_mv, url_mv, anz_aevo, meta_mv, ecosystem, per_mar, per_ter, the_geom FROM lsg WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.lsg) as foo using unique gid using srid=5650'
WHERE Layer_ID = 229;	
UPDATE layer
SET
  drawingorder = 6090,
  pfad = 'SELECT * FROM np WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.np) as foo using unique gid using srid=5650'
WHERE Layer_ID = 230;	
UPDATE layer
SET
  drawingorder = 6070,
  pfad = 'SELECT * FROM nlp WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.nlp) as foo using unique gid using srid=5650'
WHERE Layer_ID = 231;	
UPDATE layer
SET
  drawingorder = 6071,
  pfad = 'SELECT * FROM nlpz WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.nlpz) as foo using unique gid using srid=5650'
WHERE Layer_ID = 232;	
UPDATE layer
SET
  drawingorder = 6080,
  pfad = 'SELECT * FROM br WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.br) as foo using unique gid using srid=5650'
WHERE Layer_ID = 233;	
UPDATE layer
SET
  drawingorder = 6081,
  pfad = 'SELECT * FROM brz WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.brz) as foo using unique gid using srid=5650'
WHERE Layer_ID = 234;	
UPDATE layer
SET
  drawingorder = 6060,
  pfad = 'SELECT * FROM nsg WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.nsg) as foo using unique gid using srid=5650'
WHERE Layer_ID = 235;	
UPDATE layer
SET
  drawingorder = 6050,
  pfad = 'SELECT * FROM spa WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.spa) as foo using unique gid using srid=5650'
WHERE Layer_ID = 236;	
UPDATE layer
SET
  drawingorder = 6040,
  pfad = 'SELECT * FROM ffh WHERE true',
  data = 'the_geom from (SELECT gid, the_geom FROM gebietseinheiten.ffh) as foo using unique gid using srid=5650'
WHERE Layer_ID = 237;	
UPDATE layer
SET
  drawingorder = 6010,
  pfad = 'SELECT * FROM gebietseinheiten.dvg WHERE kennung IN ('Landkreis', 'kreisfreie Stadt')',
  data = 'the_geom from (SELECT gid, gen, the_geom FROM gebietseinheiten.dvg WHERE kennung IN ('Landkreis', 'kreisfreie Stadt')) as foo using unique gid using srid=25833

'
WHERE Layer_ID = 239;	
UPDATE layer
SET
  drawingorder = 3200,
  pfad = 'SELECT
  v.id AS verlustobjekt_id,
  v.kampagne_id,
  v.kartiergebiet_id,
  v.kartierebene_id,
  v.bogenart_id,
  v.label,
  giscode,
  lfd_nr_kr,
  label,
  biotopname,
  standort,
  unb,
  flaeche,
  bogen_id,
  schutz_bio,
  schutz_ffh,
  bntk_code,
  hc,
  nc,
  uc1,
  uc2,
  vegeinheit,
  verl_ursache,
  verl_art,
  e_datum,
  l_datum,
  aend_datum,
  korrektur,
  geprueft,
  pruefer,
  pruefdatum,
  lock,
  status,
  v.bearbeitungsstufe,
  v.bearbeitungsstufe AS bearbeitungsstufe_koordinator, 
  v.bearbeitungsstufe AS bearbeitungsstufe_pruefer,
  ba.aenderungsberechtigte_stelle_id != $stelle_id OR CASE WHEN $stelle_id = 3 THEN v.user_id != $user_id ELSE false END AS editiersperre,
  koordinator_korrekturhinweis,
  pruefer_pruefhinweis,
  koordinator_rueckweisung,
  kommentar_zum_korrekturhinweis,
  kommentar_zum_pruefhinweis,
  pruefer_rueckweisung,
  v.stelle_id,
  v.user_id,
  v.kartierer,
  kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter,
  v.created_at,
  v.created_from,
  v.updated_at,
  v.updated_from,
  v.geom
FROM
  verlustobjekte v
  JOIN code_bearbeitungsstufen ba ON v.bearbeitungsstufe = ba.stufe
  JOIN kampagnen kk ON v.kampagne_id = kk.id
  JOIN kartiergebiete kg ON v.kartiergebiet_id = kg.id
  JOIN kartierebenen ke ON v.kartierebene_id = ke.id 
WHERE
  true',
  data = 'geom from (SELECT b.geom, b.kampagne_id, b.bogenart_id, b.bearbeitungsstufe, b.bearbeitungsstufe AS bearbeitungsstufe_koordinator, b.bearbeitungsstufe AS bearbeitungsstufe_pruefer, b.oid, b.kartierer, b.user_id, b.label, b.geprueft, b.zur_pruefung_freigegeben, kk.kartierungsart || kk.abk || coalesce('-' || kg.losnummer, '') AS filter FROM mvbio.verlustobjekte b JOIN mvbio.kartiergebiete kg ON b.kartiergebiet_id = kg.id JOIN mvbio.kampagnen kk ON kg.kampagne_id = kk.id) as foo using unique oid using srid=5650'
WHERE Layer_ID = 240;	
UPDATE layer
SET
  drawingorder = 3000,
  pfad = 'SELECT * FROM bogenarten WHERE true ORDER BY bezeichnung',
  data = ''
WHERE Layer_ID = 241;	

/*
SELECT
  concat('UPDATE used_layer
SET
  drawingorder = ', drawingorder, '
WHERE Layer_ID = ', Layer_ID, ';')
FROM
  layer
WHERE
  drawingorder IS NOT NULL;
*/

UPDATE used_layer SET drawingorder = 0 WHERE Layer_ID = 1;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 105;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 106;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 107;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 109;
UPDATE used_layer SET drawingorder = 3010 WHERE Layer_ID = 110;
UPDATE used_layer SET drawingorder = 0 WHERE Layer_ID = 119;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 120;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 122;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 124;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 160;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 125;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 126;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 127;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 128;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 129;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 130;
UPDATE used_layer SET drawingorder = 3010 WHERE Layer_ID = 131;
UPDATE used_layer SET drawingorder = 3010 WHERE Layer_ID = 147;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 132;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 133;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 134;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 135;
UPDATE used_layer SET drawingorder = 10 WHERE Layer_ID = 136;
UPDATE used_layer SET drawingorder = 3080 WHERE Layer_ID = 139;
UPDATE used_layer SET drawingorder = 3118 WHERE Layer_ID = 140;
UPDATE used_layer SET drawingorder = 2120 WHERE Layer_ID = 141;
UPDATE used_layer SET drawingorder = 3040 WHERE Layer_ID = 142;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 165;
UPDATE used_layer SET drawingorder = 0 WHERE Layer_ID = 143;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 144;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 145;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 151;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 152;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 153;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 154;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 156;
UPDATE used_layer SET drawingorder = 3100 WHERE Layer_ID = 157;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 158;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 161;
UPDATE used_layer SET drawingorder = 2120 WHERE Layer_ID = 162;
UPDATE used_layer SET drawingorder = 2110 WHERE Layer_ID = 163;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 166;
UPDATE used_layer SET drawingorder = 3227 WHERE Layer_ID = 167;
UPDATE used_layer SET drawingorder = 2210 WHERE Layer_ID = 172;
UPDATE used_layer SET drawingorder = 3226 WHERE Layer_ID = 168;
UPDATE used_layer SET drawingorder = 3225 WHERE Layer_ID = 169;
UPDATE used_layer SET drawingorder = 3223 WHERE Layer_ID = 170;
UPDATE used_layer SET drawingorder = 3224 WHERE Layer_ID = 171;
UPDATE used_layer SET drawingorder = 2250 WHERE Layer_ID = 173;
UPDATE used_layer SET drawingorder = 2220 WHERE Layer_ID = 174;
UPDATE used_layer SET drawingorder = 2240 WHERE Layer_ID = 175;
UPDATE used_layer SET drawingorder = 2230 WHERE Layer_ID = 176;
UPDATE used_layer SET drawingorder = 3128 WHERE Layer_ID = 177;
UPDATE used_layer SET drawingorder = 3136 WHERE Layer_ID = 178;
UPDATE used_layer SET drawingorder = 3313 WHERE Layer_ID = 179;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 180;
UPDATE used_layer SET drawingorder = 3233 WHERE Layer_ID = 181;
UPDATE used_layer SET drawingorder = 3243 WHERE Layer_ID = 182;
UPDATE used_layer SET drawingorder = 3217 WHERE Layer_ID = 183;
UPDATE used_layer SET drawingorder = 3216 WHERE Layer_ID = 184;
UPDATE used_layer SET drawingorder = 3234 WHERE Layer_ID = 185;
UPDATE used_layer SET drawingorder = 3244 WHERE Layer_ID = 186;
UPDATE used_layer SET drawingorder = 3215 WHERE Layer_ID = 187;
UPDATE used_layer SET drawingorder = 3245 WHERE Layer_ID = 188;
UPDATE used_layer SET drawingorder = 3235 WHERE Layer_ID = 189;
UPDATE used_layer SET drawingorder = 3214 WHERE Layer_ID = 190;
UPDATE used_layer SET drawingorder = 3236 WHERE Layer_ID = 191;
UPDATE used_layer SET drawingorder = 3246 WHERE Layer_ID = 192;
UPDATE used_layer SET drawingorder = 3213 WHERE Layer_ID = 193;
UPDATE used_layer SET drawingorder = 3237 WHERE Layer_ID = 194;
UPDATE used_layer SET drawingorder = 3247 WHERE Layer_ID = 195;
UPDATE used_layer SET drawingorder = 3116 WHERE Layer_ID = 196;
UPDATE used_layer SET drawingorder = 4120 WHERE Layer_ID = 197;
UPDATE used_layer SET drawingorder = 4110 WHERE Layer_ID = 198;
UPDATE used_layer SET drawingorder = 3126 WHERE Layer_ID = 199;
UPDATE used_layer SET drawingorder = 1000 WHERE Layer_ID = 200;
UPDATE used_layer SET drawingorder = 1000 WHERE Layer_ID = 201;
UPDATE used_layer SET drawingorder = 3112 WHERE Layer_ID = 204;
UPDATE used_layer SET drawingorder = 3114 WHERE Layer_ID = 205;
UPDATE used_layer SET drawingorder = 3124 WHERE Layer_ID = 206;
UPDATE used_layer SET drawingorder = 3126 WHERE Layer_ID = 207;
UPDATE used_layer SET drawingorder = 3134 WHERE Layer_ID = 208;
UPDATE used_layer SET drawingorder = 3132 WHERE Layer_ID = 209;
UPDATE used_layer SET drawingorder = 3211 WHERE Layer_ID = 210;
UPDATE used_layer SET drawingorder = 3212 WHERE Layer_ID = 211;
UPDATE used_layer SET drawingorder = 3221 WHERE Layer_ID = 212;
UPDATE used_layer SET drawingorder = 3222 WHERE Layer_ID = 213;
UPDATE used_layer SET drawingorder = 3231 WHERE Layer_ID = 214;
UPDATE used_layer SET drawingorder = 3232 WHERE Layer_ID = 215;
UPDATE used_layer SET drawingorder = 3241 WHERE Layer_ID = 216;
UPDATE used_layer SET drawingorder = 3242 WHERE Layer_ID = 217;
UPDATE used_layer SET drawingorder = 3311 WHERE Layer_ID = 218;
UPDATE used_layer SET drawingorder = 3312 WHERE Layer_ID = 219;
UPDATE used_layer SET drawingorder = 5030 WHERE Layer_ID = 220;
UPDATE used_layer SET drawingorder = 5040 WHERE Layer_ID = 221;
UPDATE used_layer SET drawingorder = 5050 WHERE Layer_ID = 222;
UPDATE used_layer SET drawingorder = 5060 WHERE Layer_ID = 223;
UPDATE used_layer SET drawingorder = 6130 WHERE Layer_ID = 224;
UPDATE used_layer SET drawingorder = 6020 WHERE Layer_ID = 225;
UPDATE used_layer SET drawingorder = 6030 WHERE Layer_ID = 238;
UPDATE used_layer SET drawingorder = 6112 WHERE Layer_ID = 226;
UPDATE used_layer SET drawingorder = 6110 WHERE Layer_ID = 227;
UPDATE used_layer SET drawingorder = 6114 WHERE Layer_ID = 228;
UPDATE used_layer SET drawingorder = 6100 WHERE Layer_ID = 229;
UPDATE used_layer SET drawingorder = 6090 WHERE Layer_ID = 230;
UPDATE used_layer SET drawingorder = 6070 WHERE Layer_ID = 231;
UPDATE used_layer SET drawingorder = 6071 WHERE Layer_ID = 232;
UPDATE used_layer SET drawingorder = 6080 WHERE Layer_ID = 233;
UPDATE used_layer SET drawingorder = 6081 WHERE Layer_ID = 234;
UPDATE used_layer SET drawingorder = 6060 WHERE Layer_ID = 235;
UPDATE used_layer SET drawingorder = 6050 WHERE Layer_ID = 236;
UPDATE used_layer SET drawingorder = 6040 WHERE Layer_ID = 237;
UPDATE used_layer SET drawingorder = 6010 WHERE Layer_ID = 239;
UPDATE used_layer SET drawingorder = 3200 WHERE Layer_ID = 240;
UPDATE used_layer SET drawingorder = 3000 WHERE Layer_ID = 241;
