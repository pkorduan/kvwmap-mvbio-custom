#!/bin/bash 
# requires jq installed apt install jq
set -o pipefail

pgsqlHost=pgsql

LOG_PATH=/var/www/logs/werbeo_export
LOG_FILE=${LOG_PATH}/werbeo_export_`date +"%Y%m%d-%H%M"`.log
ERROR_FILE=${LOG_PATH}/werbeo_export_`date +"%Y%m%d-%H%M"`.err
echo "writing logs to ${LOG_FILE} and ${ERROR_FILE}"


function log() {
  echo "`date +"%Y-%m-%d %H:%M:%S%z"` ${@}"  >> "${LOG_FILE}"
}

function logErr() {
  echo "########################################################" >> "${ERROR_FILE}"
  1>&2 echo "`date +"%Y-%m-%d %H:%M:%S%z"` ${@}"
  log ${@}
}

function exec_sql() {
  if [ "$dry_run" = "true" ] ; then
    echo "exec: psql -h ${pgsqlHost} -U gisadmin -c \"${sql}\" kvwmapsp_${NEW_VERSION} >> ${LOG_FILE} 2>> ${ERROR_FILE}"
  else
    #echo "Execute sql"
    psql -h ${pgsqlHost} -U kvwmap -c "${sql}" kvwmapsp >> ${LOG_FILE} 2>> ${ERROR_FILE}
  fi
}

function updatePersons() {
  # Personen in Werbeo anlegen, id's abfragen und in die MVBio Datenbank übernehmen
  log "Start updating persons"
  sql="SELECT name FROM archiv.personen WHERE ${WERBEO_PERSON_ID} IS NULL"
  log "Frage personen ab mit sql: $sql"
  psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp |
  while read line ; do
    readarray -td '|' rs <<< "$line"
    name=$(tr -d '\n' <<< "${rs[0]}")
    person=$(curl -k -s -S --location --request POST "${WERBEO_URL}/5/people" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw '{"firstName" : "MVBio", "lastName" : "'"${name}"'"}')
    person_id=$( jq -r .entityId <<< "$person" )
    log "person $name $person_id"
    if [ -n "$person_id" ] && [ "$person_id" -eq "$person_id" ] ; then
      sql="UPDATE archiv.personen SET  ${WERBEO_PERSON_ID}=$person_id, ${WERBEO_EXPORT_AM} = now() WHERE name like '${name}'"
      exec_sql
      log "Person exportiert$sql."
    else
      log "Fehler beim Anlegen der Person: {\"firstName\" : \"MVBio\", \"lastName\" : \"${name}\"} Meldung: ${person}"
    fi
  done
  log "Personen have been updated"
}

function insertSurveyOnWerbeo() {
  # Survey von Kampagnen in WerBeo anlegen und id's nach MVBio übernehmen
  surveyId=${1}
  log "insertSurveyOnWerbeo surveyId=${surveyId}" 
  sql="SELECT id, mvbio.as_werbeo_survey(id) FROM archiv.kampagnen WHERE id = ${surveyId}";
  log "Frage kampagnen ab mit sql: $sql"

  psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp |
  while read line ; do
    readarray -td '|' rs <<< "$line"
    kampagne_id=${rs[0]}
    kampagne=$(tr -d '\n' <<< "${rs[1]}")
    survey=''
    log "Lege Kampagne an mit: $kampagne"
    survey=$(curl -s -S --location --request POST "${WERBEO_URL}/5/surveys" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw "${kampagne}")

    survey_id=$(echo $survey | jq -r .entityId)
    if [ -n "$survey_id" ] && [ "$survey_id" -eq "$survey_id" ] ; then
      sql="UPDATE archiv.kampagnen SET ${WERBEO_SURVEY_ID}=$survey_id, ${WERBEO_EXPORT_AM} = now() WHERE id = $kampagne_id"
      exec_sql
      log "Kampagne exportiert $sql."
      echo $survey_id
    else
      log "Fehler beim Anlegen der Kampagne: ${kampagne} Meldung: ${survey}"
    fi
  done
}

function logOn() {
  response=`curl -k -s -S -X POST --header 'Content-Type: application/json' --header 'Accept: application/json' -d '{"email": "peter.korduan@gdi-service.de", "password": "'"${WERBEO_PASSWORD}"'"}' "${WERBEO_URL}/security/token"`
  exitCode=$?
  if [ $exitCode -ne 0 ]; then
        logErr "Error fetching security token; Exitcode=${exitCode}; response=${response}"
	exit 1
  fi

  WERBEO_TOKEN=`jq  -r .token.access_token <<< ${response}`
  echo "${WERBEO_TOKEN}"
  exitCode=$?
  if [ $exitCode -ne 0 ]; then
        logErr "Error extracting security token; jq installed?; Exitcode=${exitCode}; response=${response}"
	exit 1
  fi
}

function printUsage() {
  echo "usage:  "
  echo " werbeo_upload.sh kampagnen_id=number deleteOnWerbeo=[true|false] test=[true|false] dry_run=[true|false]"
  echo " werbeo_upload.sh pflanzenarten=true test=[true|false]"
  echo " werbeo_upload.sh personen=true test=[true|false]"
  echo " werbeo_upload.sh listSurveys=true test=[true|false]"
  echo
  echo "    dry_run: true|false if true nothing will written to database"
  echo "    test: true|false if true test instance of werbeo will be used"
  log "printed Usage and exit"
}


function printSurveys() {
  url="${WERBEO_URL}/5/surveys?limit=1000"
  echo "printService"
  response=$(curl -s -S --location --request GET "${url}" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} )
  echo $response
}


function deleteSample() {
  url="${WERBEO_URL}/5/samples/${1}"
  log "delete sample ${1} ${url}"
  response=$(curl -s -S --location --request DELETE "${url}" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} )
  log "delete sample response ${response}"
}

function deleteSamples() {
  url="${WERBEO_URL}/5/samples/?surveyId=${1}"
  log "deleteSamples"
  response=$(curl -s -S --location --request GET "${url}" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} )
  ids=`jq  -r .samples[].id <<< ${response}`
  for row in $ids; do
    # echo "row=${row}"
    deleteSample $row
  done
}

function saveSamplesToWerbeo() {
# Samples mit Occurrences von Grundbögen und Pflanzenvorkommen in WerBeo anlegen und Id's nach MVBio übernehmen

  kampagne_id=${1}
  log "saveSamplesToWerbeo kampagne_id=${kampagne_id}"

  sql="SELECT count(*) FROM archiv.grundboegen gb LEFT JOIN archiv.kartierobjekte ko ON ko.id = gb.kartierobjekt_id WHERE gb.kartierebene_id = 1 AND gb.kampagne_id = ${kampagne_id} AND ko.${WERBEO_SAMPLE_ID} IS NULL AND mvbio.as_werbeo_test_sample(gb.kartierobjekt_id) is not null";
  recordCount=`psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp`
  log "Anzahl grundboegen: ${recordCount}"

  sql="SELECT ko.id AS kartierobjekt_id, mvbio.as_werbeo_test_sample(gb.id) FROM archiv.grundboegen gb JOIN archiv.kartierobjekte ko ON ko.id = gb.kartierobjekt_id WHERE gb.kartierebene_id = 1 AND gb.kampagne_id = ${kampagne_id} AND ko.${WERBEO_SAMPLE_ID} IS NULL AND mvbio.as_werbeo_test_sample(gb.kartierobjekt_id) is not null limit 300";
  log "Frage Kartierobjekt als Webeo Sample ab mit sql: $sql"
  i=1
  psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp |
  while read line ; do
    readarray -td '|' rs <<< "$line"
    kartierobjekt_id=${rs[0]}
    grundbogen=$(tr -d '\n' <<< "${rs[1]}")
    log "Lege Sample für Kartierobjekt id: ${kartierobjekt_id} an."

    sample=''
    # log "curl -s -S --location --request POST ${WERBEO_URL}/5/samples --header 'Content-Type: application/json' --header 'Authorization: Bearer ${WERBEO_TOKEN}' --data-raw '${grundbogen}'"; 

    sample=$(curl -s -S --location --request POST "${WERBEO_URL}/5/samples" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw "${grundbogen}")
    exitCode=$?
    if [ $exitCode -ne 0 ]; then 
	logErr "Error saving samples ${kartierobjekt_id}; curlExitcode=${exitCode}; response=${sample}"
    else 
      sample_id=$(echo ${sample} | jq -r .entityId)
      if ! [ -z "$sample_id" ]  ; then
	      if [ "$sample_id" = "null" ]; then
                logErr "sample_id war \"null\" kartierobjekt_id=${kartierobjekt_id}; response:\"${sample}\" sent json:\"${grundbogen}\""
              else
        	log "Sample mit id: $sample_id in WERBEO angelegt."; 
        	sql="UPDATE archiv.kartierobjekte SET ${WERBEO_EXPORT_AM} = now(), ${WERBEO_SAMPLE_ID} = '${sample_id}' WHERE id = ${kartierobjekt_id}"
        	exec_sql
        	log "Grundboegen erfolgreich exportiert $sql." 
	      fi
      else
        logErr "Fehler beim Anlegen des Samples mit ${kartierobjekt_id} wegen: ${sample} ${sample_id} exitCodeCurl=${exitCode}"
      fi
    fi
    log "bearbeitet $i von ${recordCount}"
    let i++
  done
  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
	  logErr "psql cmd to read from archiv.grundboegen not succedded; script ended prematurely; saveSamplesToWerbeo psql=>excitcode=${exitCode}"
	  exit 1;
  fi

  log "saveSamplesToWerbeo kampagne_id=${kampagne_id} done"
}

function getWerbeoIdOfKampagne() {
  sql="SELECT ${WERBEO_SURVEY_ID} from archiv.kampagnen where id=${1}"
  werbeoId=`psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp`
  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
	  logErr "psql cmd not succedded; script ended prematurely; getWerbeoIdOfKampagne psql=>excitcode=${exitCode}"
	  exit 1;
  fi
  echo $werbeoId
}

function existsKampagne() {
  sql="SELECT count(id) from archiv.kampagnen where id=${1}"
  count=`psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp`
  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
	  logErr "psql cmd not succedded; script ended prematurely; existsKampagne psql=>excitcode=${exitCode}"
	  exit 1;
  fi
  if [ $count == 1 ];then
	echo true
  else 
	echo false
  fi
}


function updatePlants() {
  # Taxon ids von Werbeo abfragen und in die MVBio Datenbank übernehmen
  url="${WERBEO_URL}/5/taxa/?limit=5000000"
  log "Start importing Plants ${url}"

  # Option -k curl sagt Certificate stimmt nicht
  # curl -k  -s -S --location --request GET "${url}" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} | jq -r '.taxon[] | "\(.id);\(.name);\(.externalKey)"' | psql -h ${pgsqlHost} -U kvwmap kvwmapsp -c "BEGIN;DELETE FROM import.werbeo_taxa;COPY import.werbeo_taxa FROM STDIN DELIMITER ';';COMMIT"
  # curl -k -s -S --location --request GET "https://service.infinitenature.org/api/v1/5/taxa/?limit=5000000" --header 'Content-Type: application/json' | jq -r '.taxon[] | "\(.id);\(.name);\(.externalKey)"' | psql -h pgsql -U kvwmap kvwmapsp -c "BEGIN;DELETE FROM import.werbeo_taxa;COPY import.werbeo_taxa FROM STDIN DELIMITER ';';COMMIT"
  # curl -k -s -S --location --request GET "https://service.infinitenature.org/api/v1/5/taxa/?limit=10" --header 'Content-Type: application/json'

  response=`curl -s -S --location --request GET "${url}" --header 'Content-Type: application/json'`
  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
	logErr "fetching plants not succedded; script ended prematurely; excitcode=${exitCode}"
	exit 1;
  fi

  jq -r '.taxon[] | "\(.id);\(.name);\(.externalKey)"' <<< ${response} | psql -h ${pgsqlHost} -U kvwmap kvwmapsp -c "BEGIN;DELETE FROM import.werbeo_taxa;COPY import.werbeo_taxa FROM STDIN DELIMITER ';';COMMIT"
  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
	logErr "importing plants to table import.werbeo_taxa not succedded; script ended prematurely; excitcode=${exitCode}"
	exit 1;
  fi

  sql="UPDATE mvbio.pflanzenarten_gsl gsl SET werbeo_taxon_id = i.werbeo_taxon_id FROM import.werbeo_taxa i WHERE gsl.species_nr = i.externalid"
  psql -h ${pgsqlHost} -U kvwmap -c "${sql}" kvwmapsp

  exitCode=$?
  if [ ${exitCode} -ne 0 ]; then
        logErr "updatePlants - psql cmd not succedded; script ended prematurely; updatePlants psql=>excitcode=${exitCode}"
        exit 1;
  fi

  log "Plants imported"
}

echo "########################################################" >> "${LOG_FILE}"
log "Starte werbeo script mit Params: \"$@\""
#################################
# check params                  #
#################################
eval $@
# entweder ist die kampagnen_id oder pflanzenarten==true
if [ -z ${kampagnen_id} ] && [ "${pflanzenarten}" != "true" ] && [ "${personen}" != "true" ] && [ "${listSurveys}" != "true" ] ; then
	printUsage 
 	exit 1
else 
  if [ -z ${test} ]; then
	echo "Parameter test not set"
	printUsage
        exit 1
  fi
fi

if [  ${kampagnen_id} > 1 ]; then
  if [ "${deleteOnWerbeo}" != "true" ] && [ "${deleteOnWerbeo}" != "false" ]; then
	echo "Parameter deleteOnWerbeo not set"
	printUsage
        exit 1
  fi
fi


if [ "${test}" == "true" ]; then
  WERBEO_URL="https://api.test.infinitenature.org/api/v1"
  WERBEO_PASSWORD='_ndv&yJLL~dG]qe*'
  WERBEO_PERSON_ID="werbeo_test_person_id"
  WERBEO_SURVEY_ID="werbeo_test_survey_id"
  WERBEO_SAMPLE_ID="werbeo_test_sample_id"
  WERBEO_EXPORT_AM="werbeo_test_export_am"
elif [ "${test}" ==  "false" ]; then
  	# Produktion
	WERBEO_URL="https://service.infinitenature.org/api/v1"
	WERBEO_PASSWORD="Kr_-T%b3%)Jx{dy("
	WERBEO_PERSON_ID="werbeo_person_id"
	WERBEO_SURVEY_ID="werbeo_survey_id"
	WERBEO_SAMPLE_ID="werbeo_sample_id"
	WERBEO_EXPORT_AM="werbeo_export_am"
else
        echo "Parameter test not true or false"
        printUsage
        exit 1;
fi

# echo "WERBEO_IRL=${WERBEO_URL}"
# echo "WERBEO_PASSWORD=${WERBEO_PASSWORD}"

exec >>${LOG_FILE} 2>>${ERROR_FILE}



#################################
# jetzt gehts los               #
#################################

if ! [ -z ${kampagnen_id} ]; then
  logOn

  # Schritt 1 - existiert die Kampagne ansonsten ende
  existsKampagne=$(existsKampagne ${kampagnen_id})
  if [ $? -ne 0 ]; then
	exit 1;
  fi
  log "existsKampagne=${existsKampagne}"
  if [ "${existsKampagne}" == "false" ]; then
  	logErr "Kampagne mit id=${kampagnen_id} existiert nicht"
	exit 1
  fi

  # Schritt 2 - existiert für die Kampagne eine werbeoId in der Datenbank
  werbeoId=$(getWerbeoIdOfKampagne ${kampagnen_id})
  log "kampagnen_id=${kampagnen_id} werbeoId=${werbeoId}"

  if [ -z ${werbeoId} ]; then 
    log "insert"
    werbeoId=$(insertSurveyOnWerbeo $kampagnen_id)
    if [ -z ${werbeoId} ]; then
	  logErr "insertSurveyOnWerbeo doesnt return werbeoId; script ended prematurely;"
	  exit 1;
    fi    
  elif [ "${deleteOnWerbeo}" == "true" ]; then
    deleteSamples ${werbeoId}	
  fi

  saveSamplesToWerbeo $kampagnen_id

fi

if [ "${pflanzenarten}" == "true" ]; then 
  logOn
  updatePlants
fi

if [ "${personen}" == "true" ]; then 
  logOn
  updatePersons
fi

if [ "${listSurveys}" == "true" ]; then 
  logOn
  printSurveys
fi
log "work done"
