BEGIN;
  CREATE OR REPLACE FUNCTION mvbio.validate_kartierobjekt()
      RETURNS trigger
      LANGUAGE 'plpgsql'
      COST 100
      VOLATILE NOT LEAKPROOF
  AS $BODY$
    DECLARE
      kampagne RECORD;
      kartiergebiet RECORD;
      sql text;
      sql_where text = '';
      msg text[];
      liegt_ausserhalb_kartiergebiet boolean;
      geschnittenes_kartierobjekt record;
    BEGIN

      IF NEW.lock = false THEN
        -- Bricht ab wenn Kartiergebiet nicht übergeben wurde
        IF COALESCE(NEW.kartiergebiet_id, 0) = 0 THEN
          RAISE EXCEPTION 'Es muss die ID des Kartiergebietes angegeben werden.';
        END IF;
        -- Fragt das Kartiergebiet ab
        EXECUTE FORMAT('
          SElECT
            id, kampagne_id, losnummer, bezeichnung, geom
          FROM
            mvbio.kartiergebiete
          WHERE
            id = %1$s
          ', NEW.kartiergebiet_id
        )
        INTO kartiergebiet;

        -- Bricht ab wenn Kartiergebiet nicht existiert
        IF kartiergebiet IS NULL THEN
          RAISE EXCEPTION 'Das Kartiergebiet mit der ID: % wurde nicht gefunden.', NEW.kartiergebiet_id;
        END IF;
        -- Bricht ab wenn bezeichnung des Kartiergebietes leer ist
        IF kartiergebiet.bezeichnung IS NULL OR kartiergebiet.bezeichnung = '' THEN
          RAISE EXCEPTION 'Die Bezeichnung des Kartiergebietes ist nicht angegeben oder leer.';
        END IF;

        -- Fragt die Kampagne ab
        EXECUTE FORMAT('
          SElECT
            abk
          FROM
            mvbio.kampagnen
          WHERE
            id = %1$s
          ',
          kartiergebiet.kampagne_id
        )
        INTO kampagne;

        -- Bricht ab wenn Kampagne nicht existiert
        IF kampagne IS NULL THEN
          RAISE EXCEPTION 'Die Kampagne mit der ID: % wurde nicht gefunden.', NEW.kampagne_id;
        END IF;

        -- Bricht ab wenn abk der Kampagne leer ist
        IF kampagne.abk IS NULL or kampagne.abk = '' THEN
          RAISE EXCEPTION 'Die Abkürzung der Kampagne ist nicht angegeben oder leer.';
        END IF;

        IF NEW.geom IS NOT NULL THEN
          sql = FORMAT('
            SELECT ST_Intersects(%1$L, %2$L)
            ', kartiergebiet.geom, NEW.geom
          );
          EXECUTE sql
          INTO liegt_ausserhalb_kartiergebiet;
          if (liegt_ausserhalb_kartiergebiet <> true) then
            RAISE EXCEPTION 'Das Kartierobjekt liegt nicht im Bereich des Kartiergebietes.';
          END IF;

          IF TG_OP = 'UPDATE' THEN
            sql_where = FORMAT('
              AND id != %1$L
              ', NEW.id
            );
          END IF;
          -- Fehlermeldung wenn der Überlappungsbereich, der durch Verschneidung der zu speichernden Fläche mit allen seinen Nachbarn entsteht,
          -- nicht vollständig innerhalb des 5m Puffers des Umrings der zu speichernden Fläche liegt
          sql = FORMAT('
            SELECT
              label
            FROM
              mvbio.kartierobjekte
            WHERE
              kartiergebiet_id = %2$s AND
              ST_Intersects(geom, %1$L) AND
              NOT GDI_WithinBufferOfExteriorRing(ST_Intersection(geom, %1$L), geom, 5)
              %3$s
          ', NEW.geom, NEW.kartiergebiet_id, sql_where);
          FOR geschnittenes_kartierobjekt IN EXECUTE sql LOOP
            msg = array_append(msg, geschnittenes_kartierobjekt.label::text);
          END LOOP;

          IF array_length(msg, 1) > 0 THEN
            RAISE EXCEPTION 'Die Geometrie des Kartierobjektes überschneidet Kartierobjekt: %', replace(array_to_string(msg, ', '), '"', '\"');
          END IF;
        END IF;

        -- Setzt die Kampagne ID
        NEW.kampagne_id = kartiergebiet.kampagne_id;

        -- Prüft ob eine Kartierebene ausgewählt wurde
        IF COALESCE(NEW.kartierebene_id, 0) BETWEEN 1 AND 4 THEN

  	IF NEW.kartierebene_id = 2 AND (new.lrt_gr IS NULL OR new.lrt_code IS NULL) THEN
  		RAISE EXCEPTION 'Es muss die LRT-Gruppe und der LRT-Code angegeben werden.';
          END IF;

          -- Prüft ob eine Bearbeitungsstufe angegeben ist
          IF COALESCE(NEW.bearbeitungsstufe, 0) BETWEEN 1 AND 7 THEN
            -- Die folgenden Bedingungen werden nur geprüft wenn die Bearbeitungsstufe angegeben wurde

            IF NEW.bearbeitungsstufe > 1 THEN
              -- Prüfungen bei Bearbeitungsstufe > 1

              -- Prüfungen, die bei jedem Bogen gemacht werden

              -- Prüft ob mindestens ein Wertkriterium angegeben ist
              IF
                (NEW.wert_krit_1 IS NULL OR NOT NEW.wert_krit_1) AND
                (NEW.wert_krit_2 IS NULL OR NOT NEW.wert_krit_2) AND
                (NEW.wert_krit_3 IS NULL OR NOT NEW.wert_krit_3) AND
                (NEW.wert_krit_4 IS NULL OR NOT NEW.wert_krit_4) AND
                (NEW.wert_krit_5 IS NULL OR NOT NEW.wert_krit_5) AND
                (NEW.wert_krit_6 IS NULL OR NOT NEW.wert_krit_6) AND
                (NEW.wert_krit_7 IS NULL OR NOT NEW.wert_krit_7) AND
                (NEW.wert_krit_8 IS NULL OR NOT NEW.wert_krit_8) AND
                (NEW.wert_krit_9 IS NULL OR NOT NEW.wert_krit_9) AND
                (NEW.wert_krit_10 IS NULL OR NOT NEW.wert_krit_10) AND
                (NEW.wert_krit_11 IS NULL OR NOT NEW.wert_krit_11) AND
                (NEW.wert_krit_12 IS NULL OR NOT NEW.wert_krit_12) AND
                (NEW.wert_krit_13 IS NULL OR NOT NEW.wert_krit_13) AND
                (NEW.wert_krit_14 IS NULL OR NOT NEW.wert_krit_14) AND
                (NEW.wert_krit_15 IS NULL OR NOT NEW.wert_krit_15) AND
                (NEW.wert_krit_16 IS NULL OR NOT NEW.wert_krit_16)
              THEN
                msg = array_append(msg, 'Es muss mindestens ein Wertkriterium angegeben werden.');
              END IF;

              -- Prüft ob ein Biotopname angegeben wurde
              IF NEW.biotopname IS NULL OR NEW.biotopname = '' THEN
                msg = array_append(msg, 'Es muss ein Biotopname angegeben werden.');
              END IF;

              -- Prüft ob ein Standort / Geologie angegeben wurde
              IF NEW.standort IS NULL OR NEW.standort = '' THEN
                msg = array_append(msg, 'Es muss der Standort/Geologie angegeben werden.');
              END IF;

              -- Prüft ob eine vegeinheit angegeben wurde
              IF NEW.vegeinheit IS NULL THEN
                msg = array_append(msg, 'Es muss mindestens eine Vegetationseinheit angegeben werden.');
              END IF;

              -- Prüft ob ein Hauptcode gesetzt ist
              IF NEW.hc IS NULL THEN
                msg = array_append(msg, 'Es muss ein Hauptcode ausgewählt werden!');
              END IF;

              -- Prüft ob prozentuale Überdeckung vom Hauptcode gesetzt wurde und  zwischen 1 und 100 ist
              IF NEW.hcp IS NULL OR NEW.hcp < 1 OR NEW.hcp > 100 THEN
                msg = array_append(msg, 'Es muss eine Überdeckung zum Hauptcode zwischen 1 und 100 angegeben werden.');
              END IF;

              -- Prüft ob eine Beschreibung angegeben wurde.
              IF COALESCE(NEW.beschreibg, '') = '' THEN
                msg = array_append(msg, 'Es muss eine Beschreibung angegeben werden.');
              END IF;

              -- Prüft ob eine Bogenart angegeben wurde
              IF COALESCE(NEW.bogenart_id, 0) BETWEEN 1 AND 4 THEN
                -- Die folgenden Bedingungen werden nur geprüft wenn eine Bogenart angegeben wurde

                -- Prüfungen für Kurzbögen
                IF NEW.bogenart_id = 1 THEN
                  -- Prüft ob Schutz_Bio angegeben wurde.
                  IF COALESCE(NEW.schutz_bio, false) = false THEN
                    msg = array_append(msg, 'Das Schutzmerkmal "geschützt nach § 20 NatSchAG" muss angegeben werden.');
                  END IF;
                END IF;

                IF NEW.bogenart_id = 2 THEN
                  -- Prüft ob mindestens Schutz_Bio oder Schutz_FFH angegeben wurde.
                  IF Coalesce(NEW.schutz_bio, false) = FALSE AND COALESCE(NEW.schutz_ffh, false) = FALSE THEN
                    msg = array_append(msg, 'Im Grundbogen muss entweder das Schutzmerkmal "geschützt nach § 20 NatSchAG" oder "geschützt nach FFH-Richtlinie" angegeben werden.');
                  END IF;
                END IF;
              ELSE
                msg = array_append(msg, 'Es muss eine Bogenart angegeben werden');
              END IF;
            END IF; -- Ende Bearbeitungsstufe > 1
          ELSE
            msg = array_append(msg, 'Die ID des Bearbeitungsstandes muss ein Wert von 1 bis 7 haben.');
          END IF;
        ELSE
          msg = array_append(msg, 'Die ID der Kartierebene muss einen Wert von 1 bis 4 haben.');
        END IF;

        IF array_length(msg, 1) > 0 THEN
          RAISE EXCEPTION '{"success": false, "msg_type": "error", "msg" : "%"}', 'Fehlende Werte für Kartierobjekt Id: ' || NEW.id || '<p>' || replace(array_to_string(msg, '<p>'), '"', '\"');
          RETURN NULL;
        END IF;
      END IF;
      RETURN NEW;
    END;
  $BODY$;

  COMMENT ON FUNCTION mvbio.validate_kartierobjekt()
      IS 'Validiert die Inhalte des Kartierobjektes wenn lock = false und einige erst wenn bearbeitungsstufe > 1 ist';
COMMIT;