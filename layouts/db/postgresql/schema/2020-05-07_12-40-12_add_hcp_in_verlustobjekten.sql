BEGIN;
  ALTER TABLE mvbio.verlustobjekte  ADD COLUMN hcp double precision;

  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustboegen RENAME TO biotoptypen_nebencodes_verlustobjekte;
  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustobjekte RENAME kartierung_id TO verlustobjekt_id;

  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustobjekte DROP CONSTRAINT kartierung_id_fkey;
  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustobjekte
      ADD CONSTRAINT verlustobjekt_id_fkey FOREIGN KEY (verlustobjekt_id)
      REFERENCES mvbio.verlustobjekte (id) MATCH SIMPLE
      ON UPDATE CASCADE
      ON DELETE CASCADE;
    
  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustobjekte DROP CONSTRAINT biotoptypen_nebencodes_verlustboegen_code_fkey;
  ALTER TABLE mvbio.biotoptypen_nebencodes_verlustobjekte
      ADD CONSTRAINT biotoptypen_nebencodes_verlustobjekte_code_fkey FOREIGN KEY (code)
      REFERENCES mvbio.code_biotoptypen (code) MATCH SIMPLE
      ON UPDATE CASCADE
      ON DELETE CASCADE;

COMMIT;