#!/bin/bash
dry_run=false
# muss ausgeführt werden im web Container

LOG_PATH=/var/www/logs/werbeo_export
LOG_FILE=werbeo_export.log
ERROR_FILE=werbeo_export.err
# Produktion
#WERBEO_URL="https://service.infinitenature.org/api/v1"
#WERBEO_PASSWORD="Kr_-T%b3%)Jx{dy("
# Test
WERBEO_URL="https://api.test.infinitenature.org/api/v1"
WERBEO_PASSWORD="_ndv&yJLL~dG]qe*"


if [ ! -d "$LOG_PATH" ]; then
  echo "Log Verzeichnis $LOG_PATH existiert nicht. Dieses Script im Web-Container ausführen und sicherstellen, dass es das Verzeichnis dort gibt."
  exit
fi

function log() {
  echo "########################################################" >> ${LOG_PATH}/${LOG_FILE}
  echo `date` >> ${LOG_PATH}/${LOG_FILE}
  echo "${msg}" >> ${LOG_PATH}/${LOG_FILE}
  echo `date` ${msg}
}

function err() {
  echo "########################################################" >> ${LOG_PATH}/${ERROR_FILE}
  echo `date` >> ${LOG_PATH}/${ERROR_FILE}
  echo "${msg}" >> ${LOG_PATH}/${ERROR_FILE}
  echo `date` ${msg}
  echo $sql
}

function exec_sql() {
  if [ "$dry_run" = "true" ] ; then
    echo "exec: psql -h pgsql -U gisadmin -c \"${sql}\" kvwmapsp_${NEW_VERSION} >> ${LOG_PATH}/${LOG_FILE} 2>> ${LOG_PATH}/${ERROR_FILE}"
  else
    #echo "Execute sql"
    psql -h pgsql -U kvwmap -c "${sql}" kvwmapsp >> ${LOG_PATH}/${LOG_FILE} 2>> ${LOG_PATH}/${ERROR_FILE}
  fi
}

WERBEO_TOKEN=$(curl -X POST --header 'Content-Type: application/json' --header 'Accept: application/json' -d '{"email": "peter.korduan@gdi-service.de", "password": "'"${WERBEO_PASSWORD}"'"}' "${WERBEO_URL}/security/token" -s| jq -r .token.access_token)
#WERBEO_TOKEN='eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJYbHU1ejRlMzY1eEU0SnVpZTJ4VG9IT2ZMVGFpR2ZRSFJGZXpuZ1JTaE1zIn0.eyJqdGkiOiIzMzgxMDUxYS05YjM1LTQ3ZWItODc1Yi1jNGRkMDYxZDg3NGMiLCJleHAiOjE2MzEyMTM4NDAsIm5iZiI6MCwiaWF0IjoxNjMxMTc3ODQwLCJpc3MiOiJodHRwczovL3Nzby10ZXN0LmxvZS5hdWYudW5pLXJvc3RvY2suZGUvYXV0aC9yZWFsbXMvdGVzdC5pbmZpbml0ZW5hdHVyZS5vcmciLCJhdWQiOiJmbG9yYS1jbXMiLCJzdWIiOiIzY2RjMGVlNS0xM2ZmLTQ5YjEtODk0ZS03YjVmYmYyNjM5NTQiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJmbG9yYS1jbXMiLCJhdXRoX3RpbWUiOjAsInNlc3Npb25fc3RhdGUiOiI1YmVhOWZhYS1lZDJiLTQzZTAtYTVmMC01MzI3NzM1NDA5YTUiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbImh0dHBzOi8vZmxvcmEtbXYudGVzdC5pbmZpbml0ZW5hdHVyZS5vcmciXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIldFUkJFT181X0FDQ0VQVEVEIiwiV0VSQkVPXzVfQVBQUk9WRUQiLCJXRVJCRU9fNV9BRE1JTiJdfSwicmVzb3VyY2VfYWNjZXNzIjp7ImFjY291bnQiOnsicm9sZXMiOlsibWFuYWdlLWFjY291bnQiLCJtYW5hZ2UtYWNjb3VudC1saW5rcyIsInZpZXctcHJvZmlsZSJdfX0sInNjb3BlIjoicHJvZmlsZSBlbWFpbCBXZXJiZW8iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6IlBldGVyIEtvcmR1YW4iLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJwZXRlci5rb3JkdWFuQGdkaS1zZXJ2aWNlLmRlIiwiZ2l2ZW5fbmFtZSI6IlBldGVyIiwiZmFtaWx5X25hbWUiOiJLb3JkdWFuIiwiZW1haWwiOiJwZXRlci5rb3JkdWFuQGdkaS1zZXJ2aWNlLmRlIn0.nmyDZY49O8Nz6J0_RkENAc173c30rDVrwJWPXrdGhcmGONvr5ZmY6vBb_5mTZfIUH38dW7sU6p0SHVnOWApGWmoyYb-XCkO954-x2w2nejdPkJlRFtghtUuZ_JWCM2Ddkqs7JxnI-KZhU8wFlgtDc_RaWF8oPWnTva96SM9OVdlec6c8bXzkl-oTQAIUH_Ys0Zp-W9o67fc4qYazH4HKZ09dJceESNgbbgKVvVQZERnLaLAfm976QubOraLkok8x7sVpM3G4T1CevRB7xuCAI5F9ryMvHnTPu5G-ddlB1vqhrdwMwpa_zrx1TV2zyK8oa2ROCF6KOvUvbSF-5oUC_w'
msg="WERBEO_TOKEN ${WERBEO_TOKEN} geholt"; log

# Surveys von Kampagnen in WerBeo anlegen und id's nach MVBio übernehmen
sql="SELECT id, mvbio.as_werbeo_survey(id) FROM archiv.kampagnen WHERE werbeo_survey_id IS NULL";
msg="Frage kampagnen ab mit sql: $sql"; log
psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h pgsql -U kvwmap kvwmapsp |
while read line ; do
  readarray -td '|' rs <<< "$line"
  kampagne_id=${rs[0]}
  kampagne=$(tr -d '\n' <<< "${rs[1]}")
  survey=''
  msg="Lege Kampagne an mit: $kampagne"; log
  survey=$(curl -s -S --location --request POST "${WERBEO_URL}/5/surveys" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw "${kampagne}")
  #echo "Antwort von Werbeo: $survey"
  survey_id=$(echo $survey | jq -r .entityId)
  if [ -n "$survey_id" ] && [ "$survey_id" -eq "$survey_id" ] ; then
    #msg="survey mit id: $survey_id in WERBEO angelegt."; log
    sql="UPDATE archiv.kampagnen SET werbeo_survey_id=$survey_id, werbeo_export_am = now() WHERE id = $kampagne_id"
    exec_sql
    msg="Kampagne exportiert $sql."; log
  else
    msg="Fehler beim anlegen der Kampagne: ${kampagne} Meldung: ${survey}"; log
  fi
done

# Taxon ids von Werbeo abfragen und in die MVBio Datenbank übernehmen
curl -s -S --location --request GET "${WERBEO_URL}/5/taxa/?limit=5000000" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} | jq -r '.taxon[] | "\(.id);\(.name);\(.externalKey)"' | psql -h pgsql -U kvwmap kvwmapsp -c "BEGIN;DELETE FROM import.werbeo_taxa;COPY import.werbeo_taxa FROM STDIN DELIMITER ';';COMMIT"

sql="UPDATE mvbio.pflanzenarten_gsl gsl SET werbeo_taxon_id = i.werbeo_taxon_id FROM import.werbeo_taxa i WHERE gsl.species_nr = i.externalid"
exec_sql

# Personen in Werbeo anlegen, id's abfragen und in die MVBio Datenbank übernehmen
sql="SELECT name FROM archiv.personen WHERE werbeo_person_id IS NULL"
msg="Frage personen ab mit sql: $sql"; log
psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h pgsql -U kvwmap kvwmapsp |
while read line ; do
  readarray -td '|' rs <<< "$line"
  name=$(tr -d '\n' <<< "${rs[0]}")
  #echo "cmd: curl -s -S --location --request POST "${WERBEO_URL}/5/people" --header 'Content-Type: application/json' --header 'Authorization: Bearer ${WERBEO_TOKEN}' --data-raw '{\"firstName\" : \"MVBio\", \"lastName\" : \"${name}\"}'"
  person=$(curl -s -S --location --request POST "${WERBEO_URL}/5/people" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw '{"firstName" : "MVBio", "lastName" : "'"${name}"'"}')
  #echo "Antwort von Werbeo: $person"
  person_id=$(echo $person | jq -r .entityId)
  if [ -n "$person_id" ] && [ "$person_id" -eq "$person_id" ] ; then
    #msg="person mit id: $person_id in WERBEO angelegt."; log
    sql="UPDATE archiv.personen SET werbeo_person_id=$person_id, werbeo_export_am = now() WHERE name like '${name}'"
    exec_sql
    msg="Person exportiert$sql."; log
  else
    msg="Fehler beim Anlegen der Person: {\"firstName\" : \"MVBio\", \"lastName\" : \"${name}\"} Meldung: ${person}"; log
  fi
done

# Samples mit Occurrences von Grundbögen und Pflanzenvorkommen in WerBeo anlegen und Id's nach MVBio übernehmen
sql="SELECT kartierobjekt_id, mvbio.as_werbeo_sample(kartierobjekt_id) FROM archiv.grundboegen WHERE kartierobjekt_id < 187900 AND werbeo_sample_id IS NULL LIMIT 1";
msg="Frage grundboegen ab mit sql: $sql"; log
psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h pgsql -U kvwmap kvwmapsp |
while read line ; do
  readarray -td '|' rs <<< "$line"
  kartierobjekt_id=${rs[0]}
  msg="Lege Sample für Kartierobjekt ${kartierobjekt_id} an."; log
  grundbogen=$(tr -d '\n' <<< "${rs[1]}")
  sample=''
  msg="curl -s -S --location --request POST ${WERBEO_URL}/5/samples --header 'Content-Type: application/json' --header 'Authorization: Bearer ${WERBEO_TOKEN}' --data-raw '${grundbogen}'"; log
  sample=$(curl -s -S --location --request POST "${WERBEO_URL}/5/samples" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw "${grundbogen}")
  msg="Antwort von WerBeo: ${sample}"; log
  sample_id=$(echo ${sample} | jq -r .entityId)
  if [ -n "$sample_id" ] && [ "$sample_id" -eq "$sample_id" ] ; then
    msg="Sample mit id: $sample_id in WERBEO angelegt."; log
    sql="UPDATE archiv.grundboegen SET werbeo_export_am = now(), werbeo_sample_id = '${sample_id}' WHERE id = ${kartierobjekt_id}"
    exec_sql
    msg="Grundboegen erfolgreich exportiert $sql."; log
  else
    msg="Fehler beim anlegen des Samples wegen: ${sample}"; log
  fi
done