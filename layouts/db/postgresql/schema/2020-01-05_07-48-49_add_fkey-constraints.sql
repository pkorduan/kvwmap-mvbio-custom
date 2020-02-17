BEGIN;
  ALTER TABLE mvbio.kartierobjekte DROP CONSTRAINT IF EXISTS kartierobjekte_kampagne_id_fkey;

  ALTER TABLE mvbio.kartiergebiete DROP CONSTRAINT IF EXISTS kampagne_id_fkey;
  ALTER TABLE mvbio.kartiergebiete ADD CONSTRAINT kampagne_id_fkey FOREIGN KEY (kampagne_id)
  REFERENCES mvbio.kampagnen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.kartierobjekte DROP CONSTRAINT IF EXISTS kartierobjekte_kartiergebiet_id_fkey;
  ALTER TABLE mvbio.kartierobjekte DROP CONSTRAINT IF EXISTS kartiergebiet_id_fkey;
  ALTER TABLE mvbio.kartierobjekte ADD CONSTRAINT kartiergebiet_id_fkey FOREIGN KEY (kartiergebiet_id)
  REFERENCES mvbio.kartiergebiete (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.habitatvorkommen DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.habitatvorkommen ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.fotos DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.fotos ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.empfehlungen_massnahmen DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.empfehlungen_massnahmen ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.biotoptypen_nebencodes DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.biotoptypen_nebencodes ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.bewertungen_fliessgewaesser
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id  
    FROM
      mvbio.bewertungen_fliessgewaesser n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.bewertungen_fliessgewaesser DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.bewertungen_fliessgewaesser ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.bewertungen_kueste
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id  
    FROM
      mvbio.bewertungen_kueste n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.bewertungen_kueste DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.bewertungen_kueste ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.bewertungen_moore
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id
    FROM
      mvbio.bewertungen_moore n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.bewertungen_moore DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.bewertungen_moore ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.bewertungen_offenland
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id
    FROM
      mvbio.bewertungen_offenland n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.bewertungen_offenland DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.bewertungen_offenland ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.bewertungen_stillgewaesser
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id
    FROM
      mvbio.bewertungen_stillgewaesser n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.bewertungen_stillgewaesser DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.bewertungen_stillgewaesser ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.beeintraechtigungen_gefaehrdungen
  WHERE
    kartierung_id IN (
    SELECT
      n.kartierung_id
    FROM
      mvbio.beeintraechtigungen_gefaehrdungen n LEFT JOIN
      mvbio.kartierobjekte ko ON n.kartierung_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.beeintraechtigungen_gefaehrdungen DROP CONSTRAINT IF EXISTS kartierung_id_fkey;
  ALTER TABLE mvbio.beeintraechtigungen_gefaehrdungen ADD CONSTRAINT kartierung_id_fkey FOREIGN KEY (kartierung_id)
  REFERENCES mvbio.kartierobjekte (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.bogenarten2kartierebenen DROP CONSTRAINT IF EXISTS kartierebene_id_fkey;
  ALTER TABLE mvbio.bogenarten2kartierebenen ADD CONSTRAINT kartierebene_id_fkey FOREIGN KEY (kartierebene_id)
  REFERENCES mvbio.kartierebenen (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.bogenarten2kartierebenen DROP CONSTRAINT IF EXISTS bogenart_id_fkey;
  ALTER TABLE mvbio.bogenarten2kartierebenen ADD CONSTRAINT bogenart_id_fkey FOREIGN KEY (bogenart_id)
  REFERENCES mvbio.bogenarten (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  DELETE FROM
    mvbio.kartierer
  WHERE
    kartiergebiet_id IN (
    SELECT
      n.kartiergebiet_id
    FROM
      mvbio.kartierer n LEFT JOIN
      mvbio.kartiergebiete ko ON n.kartiergebiet_id = ko.id
    WHERE
      ko.id IS NULL
    );
  ALTER TABLE mvbio.kartierer DROP CONSTRAINT IF EXISTS kartiergebiet_id_fkey;
  ALTER TABLE mvbio.kartierer ADD CONSTRAINT kartiergebiet_id_fkey FOREIGN KEY (kartiergebiet_id)
  REFERENCES mvbio.kartiergebiete (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

  ALTER TABLE mvbio.verlustobjekte DROP CONSTRAINT IF EXISTS verlustobjekte_kampagne_id_fkey;
  ALTER TABLE mvbio.verlustobjekte DROP CONSTRAINT IF EXISTS verlustobjekte_kartiergebiet_id_fkey;

  ALTER TABLE mvbio.verlustobjekte DROP CONSTRAINT IF EXISTS kartiergebiet_id_fkey;
  ALTER TABLE mvbio.verlustobjekte ADD CONSTRAINT kartiergebiet_id_fkey FOREIGN KEY (kartiergebiet_id)
  REFERENCES mvbio.kartiergebiete (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;


COMMIT;