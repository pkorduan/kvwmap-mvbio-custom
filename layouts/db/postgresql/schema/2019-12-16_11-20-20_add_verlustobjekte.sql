BEGIN;

	CREATE TABLE mvbio.verlustobjekte (
    id serial,
    kampagne_id integer,
    kartiergebiet_id integer,
    kartierebene_id integer,
    bogenart_id integer,
    kartiergebiet_name character varying COLLATE pg_catalog."default",
    stelle_id integer,
    user_id integer,
    giscode character varying(13) COLLATE pg_catalog."default",
		lfd_nr_kr integer,
    label character varying COLLATE pg_catalog."default",
    biotopname character varying(120) COLLATE pg_catalog."default",
    standort character varying(60) COLLATE pg_catalog."default",
    unb character varying(3) COLLATE pg_catalog."default",
    flaeche double precision,
    e_datum date,
    l_datum date,
    aend_datum date,
    korrektur boolean,
    geprueft boolean,
    pruefer character varying(30) COLLATE pg_catalog."default",
    pruefdatum date,
    zur_pruefung_freigegeben boolean,
    lock boolean DEFAULT false,
    status integer,
    bearbeitungsstufe integer NOT NULL DEFAULT 1,
    koordinator_korrekturhinweis text COLLATE pg_catalog."default",
    pruefer_pruefhinweis text COLLATE pg_catalog."default",
    koordinator_rueckweisung boolean DEFAULT false,
    geom geometry(MultiPolygon,5650),
    ost_west_lage integer,
    kommentar_zum_korrekturhinweis text COLLATE pg_catalog."default",
    kommentar_zum_pruefhinweis text COLLATE pg_catalog."default",
    pruefer_rueckweisung boolean NOT NULL DEFAULT false,
    nicht_begehbar boolean,
    verl_ursa text,
		verl_nat character(1),
		verl_ant character(1),
		verl_wf character(1),
    CONSTRAINT verlustobjekte_pkey PRIMARY KEY (id),
    CONSTRAINT verlustobjekte_bogenart_id_fkey FOREIGN KEY (bogenart_id)
        REFERENCES mvbio.bogenarten (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT verlustobjekte_kampagne_id_fkey FOREIGN KEY (kampagne_id)
        REFERENCES mvbio.kampagnen (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT verlustobjekte_kartierebene_id_fkey FOREIGN KEY (kartierebene_id)
        REFERENCES mvbio.kartierebenen (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT verlustobjekte_kartiergebiet_id_fkey FOREIGN KEY (kartiergebiet_id)
        REFERENCES mvbio.kartiergebiete (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
  )
  WITH (
      OIDS = TRUE
  );

  CREATE FUNCTION mvbio.validate_verlustobjekt()
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

  		raise notice 'new.lock: %', NEW.lock;
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
        IF NOT COALESCE(NEW.kartierebene_id, 0) BETWEEN 1 AND 4 THEN
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

  COMMENT ON FUNCTION mvbio.validate_verlustobjekt() IS 'Validiert die Inhalte des Verlustobjektes wenn lock = false.';

  CREATE TRIGGER tr_10_validate_verlustobjekt
    BEFORE INSERT OR UPDATE 
    ON mvbio.verlustobjekte
    FOR EACH ROW
    EXECUTE PROCEDURE mvbio.validate_verlustobjekt();

  CREATE TRIGGER tr_20_lfd_nr_label
    BEFORE INSERT
    ON mvbio.verlustobjekte
    FOR EACH ROW
    EXECUTE PROCEDURE mvbio.lfd_nr_kr_und_label();

  ALTER TABLE mvbio.kartierobjekte
    DROP COLUMN verlustursachen,
    DROP COLUMN beschreibung_der_verlustursachen;

COMMIT;