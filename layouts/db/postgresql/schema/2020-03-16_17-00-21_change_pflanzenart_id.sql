BEGIN;
  ALTER TABLE mvbio.pflanzenvorkommen ADD COLUMN valid_nr integer;
  ALTER TABLE mvbio.pflanzenvorkommen RENAME COLUMN pflanzenart_id TO species_nr;
  ALTER TABLE archiv.pflanzenvorkommen RENAME COLUMN pflanzenart_id TO species_nr;
  ALTER TABLE archiv.pflanzenvorkommen RENAME COLUMN pflanzenart_name TO valid_name;

  CREATE OR REPLACE FUNCTION mvbio.update_num_pflanzen(
  	kartierobjekt_id integer)
      RETURNS boolean
      LANGUAGE 'plpgsql'

      COST 100
      VOLATILE 
  AS $BODY$
    DECLARE
      sql text;
  	  bewertungstabelle character varying;
  	  /* lrt zur Auswahl der Pflanzen für das Kartierobjekt aus der Tabelle code_lrt_arten_gsl 
  	     normalerweise ist die gleich dem LRT-Code
  	    bei Fließgewässern und dem LRT-Code 3260 vom HC und UC (Überlagerungscode)
  	  */
  	  lrt_pflanzen character varying;
  	  rec_kartier_objekt record;
  	  rec_pflanzen_anzahl record;
    BEGIN
      -- SELECT id, hc, uc, uc1, uc2, nc1, nc2, nc3, nc4, nc5 INTO rec_kartier_objekt FROM mvbio.kartierobjekte where id = kartierobjekt_id;
  	  sql = FORMAT('
        SELECT
          ko.id, lrt_gr, g.bezeichnung, lrt_code, hc, uc1, uc2, nc1, nc2, nc3, nc4, nc5
  	    FROM
          mvbio.kartierobjekte ko LEFT JOIN
          mvbio.lrt_gruppen g ON g.id = ko.lrt_gr::integer
        WHERE
          ko.id = %1$s
       ', kartierobjekt_id
      );
    
      EXECUTE sql INTO rec_kartier_objekt;
  		-- RAISE NOTICE 'rec_kartier_objekt: %', rec_kartier_objekt;

  		IF rec_kartier_objekt.lrt_gr IS NOT NULL THEN
  			/*
  			1	Küste
  			2	Offenland
  			3	Fließgewässer
  			4	Stillgewässer
  			5	Moore
  			6	Wälder
  			*/
  			IF rec_kartier_objekt.lrt_gr = '1' THEN
  				bewertungstabelle = 'bewertungen_kueste';
  				lrt_pflanzen = rec_kartier_objekt.lrt_code;
  			ELSIF rec_kartier_objekt.lrt_gr = '2' THEN	
  				bewertungstabelle = 'bewertungen_offenland';
  				lrt_pflanzen = rec_kartier_objekt.lrt_code;
  			ELSIF rec_kartier_objekt.lrt_gr like '3' THEN
  				bewertungstabelle = 'bewertungen_fliessgewaesser';
  				lrt_pflanzen = rec_kartier_objekt.lrt_code;
  				-- bei LRT_CODE 3260 wird in Abhängigkeit von den Pberlagerungscodes ein Buchstabe und
  				-- dann in Abhängigkeit vom Hauptcode die Ziffern 1 oder 2 angehangen
  				IF rec_kartier_objekt.lrt_code = '3260' THEN
  					IF rec_kartier_objekt.uc1 = 'UFN' OR rec_kartier_objekt.uc2 = 'UFN' THEN
  						lrt_pflanzen = 'A';
  					ELSIF rec_kartier_objekt.uc1 = 'UFG' OR rec_kartier_objekt.uc2 = 'UFG' THEN
  						lrt_pflanzen = 'B';
  					ELSIF rec_kartier_objekt.uc1 = 'UFK' OR rec_kartier_objekt.uc2 = 'UFK' THEN
  						lrt_pflanzen = 'C';
  					ELSIF rec_kartier_objekt.uc1 = 'UFA' OR rec_kartier_objekt.uc2 = 'UFA' THEN
  						lrt_pflanzen = 'D';
  					ELSIF rec_kartier_objekt.uc1 = 'UFQ' OR rec_kartier_objekt.uc2 = 'UFQ' THEN
  						lrt_pflanzen = 'E';	
  					ELSIF rec_kartier_objekt.uc1 = 'UFS' OR rec_kartier_objekt.uc2 = 'UFS' THEN
  						lrt_pflanzen = 'F';				
  					ELSIF rec_kartier_objekt.uc1 = 'UFR' OR rec_kartier_objekt.uc2 = 'UFR' THEN
  						lrt_pflanzen = 'G';				
  					ELSE		
  						RAISE EXCEPTION 'kein gültiger Überlagerungscode';
  					END IF;
  					-- Ziffer
  					IF lrt_pflanzen in ('E', 'G') then
  						lrt_pflanzen = '3260' || lrt_pflanzen;
  					ELSE
  						IF rec_kartier_objekt.hc in ('FBN','FBB','FBA') THEN
  							lrt_pflanzen = '3260' || lrt_pflanzen  || '1';
  						ELSIF rec_kartier_objekt.hc in ('FFN','FFB','FFA') THEN
  							lrt_pflanzen = '3260' || lrt_pflanzen  || '2';
  						ELSE		
  							RAISE EXCEPTION 'kein gültiger Hauptcode';
  						END IF;
  					END IF;
  				END IF;
  			ELSIF rec_kartier_objekt.lrt_gr = '4' THEN
  				bewertungstabelle = 'bewertungen_stillgewaesser';
  				lrt_pflanzen = rec_kartier_objekt.lrt_code;
  			ELSIF rec_kartier_objekt.lrt_gr = '5' THEN
  				bewertungstabelle = 'bewertungen_moore';
  				lrt_pflanzen = rec_kartier_objekt.lrt_code;
  			ELSE
  				bewertungstabelle = 'unknown';	
  				RAISE EXCEPTION 'keine gültige LRT-Gruppe';
  			END IF;	

  			-- RAISE NOTICE '  rec_kartier_objekt: %', rec_kartier_objekt;
  			-- RAISE NOTICE '  bewertungstabelle: %', bewertungstabelle;
  			-- RAISE NOTICE '  lrt_pflanzen: %', lrt_pflanzen;

  			-- Moore
  			IF rec_kartier_objekt.lrt_gr = '5' THEN
  				SELECT
  					coalesce(sum(CASE WHEN (
  						a.art_gr in  ('G', 'K')
  					)
  						THEN 1 ELSE 0 END), 0) AS t211_1_1,
  					coalesce(sum(CASE WHEN (
  						a.art_gr in  ('G', 'K') AND
  						a.bedeutung LIKE '%+%'
  					)
  						THEN 1 ELSE 0 END), 0) AS t211_1_2,
  					coalesce(sum(CASE WHEN ( 
  						a.art_gr in  ('M') 
  					)
  						THEN 1 ELSE 0 END), 0) AS t211_3_1,
  					coalesce(sum(CASE WHEN (
  						a.art_gr in  ('M') AND
  						a.bedeutung LIKE '%+%'
  					)
  						THEN 1 ELSE 0 END), 0) AS t211_3_2,
  					coalesce(sum(CASE WHEN (
  						a.art_gr in  ('A')
  					)
  						THEN 1 ELSE 0 END), 0) AS t211_4,
  					coalesce(sum(1), 0) AS pflanzen_total
  				INTO
  					rec_pflanzen_anzahl
  				FROM
  					mvbio.code_lrt_arten_gsl a JOIN
  					mvbio.pflanzenarten_gsl pa ON a.species_nr = pa.species_nr JOIN
  					mvbio.pflanzenvorkommen pv ON pa.species_nr = pv.species_nr JOIN
  					mvbio.kartierobjekte ko ON pv.kartierung_id = ko.id
  				WHERE
  					ko.id = kartierobjekt_id
  				AND  
  					a.lrt = lrt_pflanzen
  				AND	((
  					-- 7120
  					ko.lrt_code = '7120'
  				) OR (
  					-- 7140 Schnabelsegge an Ufern mit Sand
  					ko.lrt_code = '7140' AND 
  					ko.hc = 'VRX' AND
  					(a.bedeutung ISNULL OR a.bedeutung LIKE '%SUS%' OR a.bedeutung not LIKE '%K%' )
  				) OR (
  					-- 7140 Kesselmoor
  					ko.lrt_code = '7140' AND
  					(ko.uc1 = 'UML' OR ko.uc2 = 'UML') AND
  					(a.bedeutung ISNULL OR a.bedeutung LIKE '%K%' OR a.bedeutung not LIKE '%SUS%')
  				) OR (
  					-- 7140 Rest
  					ko.lrt_code = '7140' 
  					AND (ko.uc1 ISNULL OR ko.uc1 NOT LIKE 'UML')
  					AND (ko.uc2 ISNULL OR ko.uc2 NOT LIKE 'UML')
  					AND ko.hc NOT LIKE 'VRX' 
  					AND (a.bedeutung ISNULL OR (a.bedeutung not LIKE '%K%' AND a.bedeutung not LIKE '%SUS%'))						
  				) OR (
  					-- 7150 SumpfbärlappFeuchtheide
  					ko.lrt_code = '7150' AND
  					(ko.hc = 'TFB' OR ko.nc1 = 'TFB' OR ko.nc2 ='TFB') 
  				) OR (
  					-- 7150 Rest
  					ko.lrt_code = '7150' AND 
  					ko.hc <> 'TFB' AND 
  					(ko.nc1 ISNULL OR ko.nc1 <> 'TFB') AND 
  					(ko.nc2 ISNULL OR ko.nc2 <> 'TFB') AND 
  					(a.bedeutung is null or a.bedeutung NOT LIKE '%SBFH%')
  				) OR (
  					-- 7120|7210|7220|7230
  					ko.lrt_code in ('7120', '7210', '7220', '7230')
  				));

  				sql = FORMAT(
  						'UPDATE
  								mvbio.' || bewertungstabelle || ' b
  						set
  						t211_1_1 = $1,
  						t211_1_2 = $2,
  						t211_3_1 = $3,
  						t211_3_2 = $4,
  						t211_4 = $5
  					WHERE
  								b.kartierung_id = $6
  						'
  					);
  				-- RAISE NOTICE '  update sql: %', sql;	 

  				EXECUTE sql using 
  					rec_pflanzen_anzahl.t211_1_1, 
  					rec_pflanzen_anzahl.t211_1_2,
  					rec_pflanzen_anzahl.t211_3_1,
  					rec_pflanzen_anzahl.t211_3_2,
  					rec_pflanzen_anzahl.t211_4,
  					kartierobjekt_id;

  			ELSE 

  				-- Rest (keine Moore) 
  				SELECT		
  					coalesce(sum(1), 0) AS t212_1,
  					coalesce(sum(CASE WHEN
  					(
  									ko.lrt_code = '3140' AND 
  									(a.bedeutung LIKE '%+%' OR a.art_gr LIKE '%A%')
  							) OR (
  						ko.lrt_code <> '3140' AND
  									a.bedeutung LIKE '%+%'
  							)
  					THEN 1 ELSE 0 END), 0) AS t212_2,
  					coalesce(sum(CASE WHEN
  					(
  						-- Halophyt
  									ko.lrt_code = '1340' AND 
  									a.bedeutung LIKE '%H%'
  							)
  					THEN 1 ELSE 0 END), 0) AS t212_3		  
  				INTO
  					rec_pflanzen_anzahl
  				FROM
  					mvbio.code_lrt_arten_gsl a JOIN
  					mvbio.pflanzenarten_gsl pa ON a.species_nr = pa.species_nr JOIN
  					mvbio.pflanzenvorkommen pv ON pa.species_nr = pv.species_nr JOIN
  					mvbio.kartierobjekte ko ON pv.kartierung_id = ko.id
  				WHERE
  					ko.id = kartierobjekt_id
  				AND  
  					a.lrt = lrt_pflanzen
  				AND	((
  					(ko.lrt_code = '1330' OR ko.lrt_code = '2130') AND
  					ko.ost_west_lage = '1' AND
  					(a.bedeutung ISNULL OR a.bedeutung not LIKE '%o%')
  				) OR (
  					(ko.lrt_code = '1330' OR ko.lrt_code = '2130') AND
  					ko.ost_west_lage = '2' AND
  					(a.bedeutung ISNULL OR a.bedeutung not LIKE '%w%')
  				) OR (
  					(ko.lrt_code <> '1330' AND ko.lrt_code <> '2130')
  				));

  				-- raise notice '  rec_pflanzen_anzahl: %', rec_pflanzen_anzahl;

  				-- Schreiben der Pflanzenzahlen in die jeweilige Tabelle
  				IF rec_kartier_objekt.lrt_gr = '1' THEN	
  					-- Küste
  					UPDATE mvbio.bewertungen_kueste b
  							set 
  							t211_1_1 = rec_pflanzen_anzahl.t212_1,
  							t211_1_2 = rec_pflanzen_anzahl.t212_2
  						WHERE b.kartierung_id = kartierobjekt_id;

  				ELSIF rec_kartier_objekt.lrt_gr = '2' THEN	
  					-- Offenland
  					UPDATE mvbio.bewertungen_offenland b
  							set 
  							t211_1_1 = rec_pflanzen_anzahl.t212_1,
  							t211_1_2 = rec_pflanzen_anzahl.t212_2,
  							t211_1_3 = rec_pflanzen_anzahl.t212_3
  						WHERE b.kartierung_id = kartierobjekt_id;

  				ELSIF rec_kartier_objekt.lrt_gr like '3' THEN
  					-- Fliessgewaesser
  					UPDATE mvbio.bewertungen_fliessgewaesser b
  							set 
  							t211_1_1 = rec_pflanzen_anzahl.t212_1,
  							t211_1_2 = rec_pflanzen_anzahl.t212_2
  						WHERE b.kartierung_id = kartierobjekt_id;

  				ELSIF rec_kartier_objekt.lrt_gr = '4' THEN
  					-- Stillgewaesser
  					-- Im alten MVBio wird 3140 (besonders charakteristischer Pflanzenarten und Armleuchteralgen) als Sonderfall betrachtet 
  					-- und die Artenanzahlen unter t212_1 und t212_2 gespeichert
  					-- Deshalb hier doppelt
  					UPDATE mvbio.bewertungen_stillgewaesser b
  							set 
  							t212_1 = rec_pflanzen_anzahl.t212_1,
  							t212_2 = rec_pflanzen_anzahl.t212_2,
  							t211_1_1 = rec_pflanzen_anzahl.t212_1,
  							t211_1_2 = rec_pflanzen_anzahl.t212_2
  						WHERE b.kartierung_id = kartierobjekt_id;

  				END IF;	
  				/*
  				sql = FORMAT('UPDATE mvbio.bewertungen_kueste b
  						set t212_1 = $1, t212_2 = $2
  					WHERE b.kartierung_id = $3');
  				RAISE NOTICE '  update sql: %', sql;	 

  				EXECUTE sql using , rec_pflanzen_anzahl.t212_2, kartierobjekt_id;
  				*/
  			END IF;

  			-- RAISE NOTICE '  rec_pflanzen_anzahl: %', rec_pflanzen_anzahl;
  	  END IF;
      RETURN true;
    END;
  $BODY$;

  COMMENT ON FUNCTION mvbio.update_num_pflanzen(integer)
      IS 'Berechnet unterschiedliche Anzahlen von Pflanzenarten, die für die Bewertungen benötigt werden und speichert diese in der für die die LRT-Gruppe zugehörigen Bewertungs-Tabelle.
  Parameter: ID des Kartierobjektes
  Tabellen: code_lrt_arten_gsl, pflanzenarten_gsl, pflanzenvorkommen, kartierobjekte
  verwendet in Triggerfunction: update_bewertung
  Bei
  bewertungen_* insert 
  pflanzenvorkommen insert, delete pflanzenvorkommen
  kartierobjekte update

  update kartierobjekte
  insert bewertungen_*
  ';

  CREATE FUNCTION mvbio.set_valid_nr()
      RETURNS trigger
      LANGUAGE 'plpgsql'
      COST 100
      VOLATILE NOT LEAKPROOF
  AS $BODY$
    DECLARE
      kampagne RECORD;
      kartiergebiet RECORD;
    BEGIN
      -- Frage die valid_nr der species_nr ab.
      EXECUTE FORMAT('
        SELECT
          valid_nr
        FROM
          mvbio.pflanzenarten_gsl
        WHERE
          species_nr = %1$s
        ',
        NEW.species_nr
      )
      INTO NEW.valid_nr;
      RETURN NEW;
    END;
  $BODY$;
  COMMENT ON FUNCTION mvbio.set_valid_nr()
      IS 'Fragt die aktuelle valid_nr des species_nr aus der Tabelle pflanzenarten_gsl ab und ordnet sie dem Attribut valid_nr zu. Wird aufgerufen before Insert and Update von Pflanzenvorkommen zur Vervollständigung des Attributes valid_nr.'

  CREATE TRIGGER tr_01_set_valid_nr BEFORE INSERT OR UPDATE
  ON mvbio.pflanzenvorkommen FOR EACH ROW
  EXECUTE PROCEDURE mvbio.set_valid_nr();

COMMIT;