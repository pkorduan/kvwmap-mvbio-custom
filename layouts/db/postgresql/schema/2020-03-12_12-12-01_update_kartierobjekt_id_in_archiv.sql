BEGIN;
  -- Setze die kartierobjekt_id für Bewertungsbögen auf die id des dazugehörigen Grundbogens
  UPDATE
    archiv.bewertungsboegen b
  SET
    kartierobjekt_id = g.kartierobjekt_id
  FROM
    archiv.grundboegen g
  WHERE
    b.label = g.label;

  -- Erzeugen Tabelle für kartierobjekte.
  CREATE TABLE archiv.kartierobjekte WITH OIDS AS
  SELECT DISTINCT kartierobjekt_id AS id FROM archiv.erfassungsboegen;
  ALTER TABLE archiv.kartierobjekte ADD CONSTRAINT kartierobjekte_id_pkey PRIMARY KEY (id);

  -- setze fkey von fotos auf kartierobjekte
  ALTER TABLE archiv.fotos RENAME kartierung_id TO kartierobjekt_id;
  ALTER TABLE archiv.fotos ADD CONSTRAINT fotos_kartierobjekt_id_fkey FOREIGN KEY (kartierobjekt_id)
  REFERENCES archiv.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  -- setze fkey von erfassungs- und verlustboegen auf kartierobjekte
  ALTER TABLE archiv.erfassungsboegen ADD CONSTRAINT erfassungsboegen_kartierobjekt_id_fkey FOREIGN KEY (kartierobjekt_id)
  REFERENCES archiv.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
  ALTER TABLE archiv.verlustboegen ADD CONSTRAINT verlustboegen_kartierobjekt_id_fkey FOREIGN KEY (kartierobjekt_id)
  REFERENCES archiv.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  -- Lösche grundbögen- und kurzbögenfotos tabellen
  DROP TABLE archiv.grundboegen_fotos, archiv.kurzboegen_fotos

COMMIT;