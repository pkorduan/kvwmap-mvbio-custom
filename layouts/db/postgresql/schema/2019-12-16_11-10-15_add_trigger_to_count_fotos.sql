BEGIN;

	CREATE TRIGGER tr_50_update_num_fotos
    BEFORE INSERT OR UPDATE
    ON mvbio.kartierobjekte
    FOR EACH ROW
    EXECUTE PROCEDURE mvbio.update_num_fotos_kartierobjekt();

COMMIT;