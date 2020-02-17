BEGIN;

  CREATE OR REPLACE FUNCTION mvbio.update_num_fotos_kartierobjekt()
      RETURNS trigger
      LANGUAGE 'plpgsql'
      COST 100
      VOLATILE NOT LEAKPROOF
  AS $BODY$
    BEGIN
      EXECUTE FORMAT('
        SELECT
          count(*)
        FROM
          mvbio.fotos
        WHERE
          kartierung_id = %1$s
        ',
        NEW.id
      )
      INTO
        NEW.foto;
      RETURN NEW;
    END;
  $BODY$;

  DROP TRIGGER IF EXISTS tr_50_update_num_fotos ON mvbio.kartierobjekte;
  CREATE TRIGGER tr_50_update_num_fotos
    BEFORE UPDATE
    ON mvbio.kartierobjekte
    FOR EACH ROW
    EXECUTE PROCEDURE mvbio.update_num_fotos_kartierobjekt();

COMMIT;