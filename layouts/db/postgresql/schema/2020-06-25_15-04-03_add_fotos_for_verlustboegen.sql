BEGIN;

  ALTER TABLE mvbio.verlustobjekte ADD COLUMN foto integer;

  CREATE TABLE mvbio.fotos_verlustobjekte
  (
      id serial PRIMARY KEY,
      verlustobjekt_id integer NOT NULL,
      datei character varying COLLATE pg_catalog."default",
      exif_latlng character varying COLLATE pg_catalog."default",
      exif_richtung double precision,
      exif_erstellungszeit timestamp without time zone,
      himmelsrichtung mvbio.himmelsrichtung,
      CONSTRAINT verlustobjekt_id_fkey FOREIGN KEY (verlustobjekt_id)
          REFERENCES mvbio.verlustobjekte (id) MATCH SIMPLE
          ON UPDATE CASCADE
          ON DELETE CASCADE
  )
  WITH (
      OIDS = TRUE
  );

  CREATE FUNCTION mvbio.update_num_fotos_verlustobjekt()
      RETURNS trigger
      LANGUAGE 'plpgsql'
      COST 100
      VOLATILE NOT LEAKPROOF
  AS $BODY$
    DECLARE
      verlustobjekt_id integer;
      num_fotos integer;
    BEGIN

    IF TG_OP = 'DELETE' THEN
      verlustobjekt_id = OLD.verlustobjekt_id;
    ELSE
      verlustobjekt_id = NEW.verlustobjekt_id;
    END IF;

      EXECUTE FORMAT('
        SELECT
          count(*)
        FROM
          mvbio.fotos_verlustobjekte
        WHERE
          verlustobjekt_id = %1$s
        ',
        verlustobjekt_id
      )
      INTO num_fotos;

      RAISE NOTICE 'Anzahl Fotos von Verlustobjekt % ist nach %: %', verlustobjekt_id, TG_OP, num_fotos;

      EXECUTE FORMAT('
        UPDATE
          mvbio.verlustobjekte
        SET
          foto = %1$s
        WHERE
          id = %2$s
  	    ',
        num_fotos, verlustobjekt_id
      );
      RETURN NEW;
    END;
  $BODY$;

  COMMENT ON FUNCTION mvbio.update_num_fotos_verlustobjekt()
      IS 'Aktualisiert die Anzahl der Fotos im Verlustobjekt';

  CREATE TRIGGER tr_calc_himmelsrichtung
      BEFORE INSERT OR UPDATE 
      ON mvbio.fotos_verlustobjekte
      FOR EACH ROW
      EXECUTE PROCEDURE mvbio.calc_himmelsrichtung();

  CREATE TRIGGER tr_update_num_fotos_verlustobjekt
      AFTER INSERT OR DELETE
      ON mvbio.fotos_verlustobjekte
      FOR EACH ROW
      EXECUTE PROCEDURE mvbio.update_num_fotos_verlustobjekt();

COMMIT;