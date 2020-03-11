BEGIN;
  ALTER TABLE archiv.kartierebenen2kampagne ADD CONSTRAINT kampagne_id_fkey FOREIGN KEY (kampagne_id)
  REFERENCES archiv.kampagnen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
  ALTER TABLE archiv.kartierebenen2kampagne ADD CONSTRAINT kartierebene_id_fkey FOREIGN KEY (kartierebene_id)
  REFERENCES archiv.kartierebenen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE archiv.kartiergebiete ADD CONSTRAINT kampagne_id_fkey FOREIGN KEY (kampagne_id)
  REFERENCES archiv.kampagnen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE archiv.pflanzenvorkommen DROP CONSTRAINT IF EXISTS pflanzenvorkommen_pflanzenart_id_fkey;
  ALTER TABLE archiv.pflanzenvorkommen ADD CONSTRAINT pflanzenvorkommen_pflanzenart_id_fkey FOREIGN KEY (pflanzenart_id)
  REFERENCES mvbio.pflanzenarten_gsl (species_nr) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE archiv.verlustboegen DROP CONSTRAINT IF EXISTS verlustboegen_bogenart_id_fkey;
  ALTER TABLE archiv.verlustboegen ADD CONSTRAINT verlustboegen_bogenart_id_fkey FOREIGN KEY (bogenart_id)
  REFERENCES mvbio.bogenarten (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
  ALTER TABLE archiv.verlustboegen DROP CONSTRAINT IF EXISTS verlustboegen_kartierebene_id_fkey;
  ALTER TABLE archiv.verlustboegen ADD CONSTRAINT verlustboegen_kartierebene_id_fkey FOREIGN KEY (kartierebene_id)
  REFERENCES archiv.kartierebenen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
  ALTER TABLE archiv.verlustboegen DROP CONSTRAINT IF EXISTS verlustboegen_kartiergebiet_id_fkey;
  ALTER TABLE archiv.verlustboegen ADD CONSTRAINT verlustboegen_kartiergebiet_id_fkey FOREIGN KEY (kartiergebiet_id)
  REFERENCES archiv.kartiergebiete (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;


  CREATE TABLE archiv.grundboegen_fotos (
    id serial,
    grundbogen_id integer NOT NULL,
    datei character varying NOT NULL,
    CONSTRAINT grundboegen_fotos_pkey PRIMARY KEY (id),
    CONSTRAINT grundboegen_fotos_grundbogen_id_fkey FOREIGN KEY (grundbogen_id)
    REFERENCES archiv.grundboegen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE
  )
  WITH ( OIDS = TRUE );

  INSERT INTO
    archiv.grundboegen_fotos
  SELECT
    f.id,
    b.id,
    f.datei
  FROM
    archiv.grundboegen b JOIN
    archiv.fotos f ON b.kartierobjekt_id = f.kartierung_id;

  CREATE TABLE archiv.kurzboegen_fotos (
    id serial,
    kurzbogen_id integer NOT NULL,
    datei character varying NOT NULL,
    CONSTRAINT kurzboegen_fotos_pkey PRIMARY KEY (id),
    CONSTRAINT kurzboegen_fotos_kurzbogen_id_fkey FOREIGN KEY (kurzbogen_id)
    REFERENCES archiv.kurzboegen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE
  )
  WITH ( OIDS = TRUE );

  INSERT INTO
    archiv.kurzboegen_fotos
  SELECT
    f.id,
    b.id,
    f.datei
  FROM
    archiv.kurzboegen b JOIN
    archiv.fotos f ON b.kartierobjekt_id = f.kartierung_id;


COMMIT;