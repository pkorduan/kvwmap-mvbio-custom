BEGIN;
  DROP TRIGGER IF EXISTS tr_50_update_num_fotos ON mvbio.kartierobjekte;

  CREATE OR REPLACE FUNCTION mvbio.update_num_fotos_kartierobjekt()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
  AS $BODY$
    DECLARE
      kartierobjekt_id integer;
      num_fotos integer;
    BEGIN

    IF TG_OP = 'DELETE' THEN
      kartierobjekt_id = OLD.kartierung_id;
    ELSE
      kartierobjekt_id = NEW.kartierung_id;
    END IF;

      EXECUTE FORMAT('
        SELECT
          count(*)
        FROM
          mvbio.fotos
        WHERE
          kartierung_id = %1$s
        ',
        kartierobjekt_id
      )
      INTO num_fotos;

      RAISE NOTICE 'Anzahl Fotos von Kartierobjekt % ist nach %: %', kartierobjekt_id, TG_OP, num_fotos;

      EXECUTE FORMAT('
        UPDATE
          mvbio.kartierobjekte
        SET
          foto = %1$s
        WHERE
          id = %2$s
  	',
        num_fotos, kartierobjekt_id
      );
      RETURN NEW;
    END;
  $BODY$;

  COMMENT ON FUNCTION mvbio.update_num_fotos_kartierobjekt()
    IS 'Aktualisiert die Anzahl der Fotos im Kartierobjekt';

  DROP TRIGGER IF EXISTS tr_update_num_fotos_kartierobjekt ON mvbio.fotos;
  CREATE TRIGGER tr_update_num_fotos_kartierobjekt
    AFTER INSERT OR DELETE
    ON mvbio.fotos
    FOR EACH ROW
    EXECUTE PROCEDURE mvbio.update_num_fotos_kartierobjekt();

COMMIT;