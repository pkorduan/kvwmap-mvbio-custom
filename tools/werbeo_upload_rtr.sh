#!/bin/bash

pgsqlHost=localhost

LOG_PATH=/var/www/logs/werbeo_export
LOG_PATH="."
LOG_FILE=${LOG_PATH}/werbeo_export.log
ERROR_FILE=${LOG_PATH}/werbeo_export.err



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
    echo "exec: psql -h ${pgsqlHost} -U gisadmin -c \"${sql}\" kvwmapsp_${NEW_VERSION} >> ${LOG_PATH}/${LOG_FILE} 2>> ${LOG_PATH}/${ERROR_FILE}"
  else
    #echo "Execute sql"
    psql -h ${pgsqlHost} -U kvwmap -c "${sql}" kvwmapsp >> ${LOG_PATH}/${LOG_FILE} 2>> ${LOG_PATH}/${ERROR_FILE}
  fi
}

function logOn() {
  WERBEO_TOKEN=$(curl -X POST --header 'Content-Type: application/json' --header 'Accept: application/json' -d '{"email": "peter.korduan@gdi-service.de", "password": "'"${WERBEO_PASSWORD}"'"}' "${WERBEO_URL}/security/token" -s| jq -r .token.access_token)
  echo "WERBEO_TOKEN ${WERBEO_TOKEN} geholt";
}

function printUsage() {
  echo "usage:  "
  echo " werbeo_upload.sh kampagnen_id=number deleteOnWerbeo=[true|false] test=[true|false] dry_run=[true|false]"
  echo " werbeo_upload.sh pflanzenarten=true test=[true|false]"
  echo
  echo "    dry_run: true|false if true nothing will written to database"
  echo "    test: true|false if true test instance of werbeo will be used"
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

  sql="SELECT kartierobjekt_id, mvbio.as_werbeo_sample(kartierobjekt_id) FROM archiv.grundboegen WHERE kampagne_id = ${kampagne_id} AND werbeo_sample_id IS NULL LIMIT 1";
  log "Frage grundboegen ab mit sql: $sql"
  psql -X -c "$sql" --no-align -t --field-separator '|' --quiet -h ${pgsqlHost} -U kvwmap kvwmapsp |
  while read line ; do
    readarray -td '|' rs <<< "$line"
    kartierobjekt_id=${rs[0]}
    log "Lege Sample für Kartierobjekt ${kartierobjekt_id} an."
    grundbogen=$(tr -d '\n' <<< "${rs[1]}")
    sample=''
    # log "curl -s -S --location --request POST ${WERBEO_URL}/5/samples --header 'Content-Type: application/json' --header 'Authorization: Bearer ${WERBEO_TOKEN}' --data-raw '${grundbogen}'"; 

    sample=$(curl -s -S --location --request POST "${WERBEO_URL}/5/samples" --header 'Content-Type: application/json' --header 'Authorization: Bearer '${WERBEO_TOKEN} --data-raw "${grundbogen}")
    exitCode=$?
    if [ $exitCode -ne 0 ]; then 
	logErr "Error saving samples; curlExitcode=${exitCode}; response=${sample}"
    else 
      sample_id=$(echo ${sample} | jq -r .entityId)
      if ! [ -z "$sample_id" ]  ; then
        log "Sample mit id: $sample_id in WERBEO angelegt."; 
        sql="UPDATE archiv.grundboegen SET werbeo_export_am = now(), werbeo_sample_id = '${sample_id}' WHERE id = ${kartierobjekt_id}"
        exec_sql
        log "Grundboegen erfolgreich exportiert $sql." 
      else
        logErr "Fehler beim anlegen des Samples wegen: ${sample}"
      fi
    fi
  done
}

function getWerbeoIdOfKampagne() {
  sql="SELECT werbeo_survey_id from archiv.kampagnen where id=${1}"
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

function insertSurveyOnWerbeo() {
  log "insertSurveyOnWerbeo surveyId=${1} !!!! not implemented"
}


#################################
# check params                  #
#################################
eval $@
# entwederi ist die kampagnen_id oder pflanzenarten==true
if [ -z ${kampagnen_id} ] && [ "${pflanzenarten}" != "true" ] ; then
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
  echo "deleteOnWerbeo=${deleteOnWerbeo}"
  if [ "${deleteOnWerbeo}" != "true" ] && [ "${deleteOnWerbeo}" != "false" ]; then
	echo "Parameter deleteOnWerbeo not set"
	printUsage
        exit 1
  fi
fi


if [ "${test}" == "true" ]; then
  WERBEO_URL="https://api.test.infinitenature.org/api/v1"
  WERBEO_PASSWORD='_ndv&yJLL~dG]qe*'
elif [ "${test}" ==  "false" ]; then
  	# Produktion
	WERBEO_URL="https://service.infinitenature.org/api/v1"
	WERBEO_PASSWORD="Kr_-T%b3%)Jx{dy("
else
        echo "Parameter test not true or false"
        printUsage
        exit 1;
fi

echo "WERBEO_IRL=${WERBEO_URL}"
echo "WERBEO_PASSWORD=${WERBEO_PASSWORD}"

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
  elif [ "${deleteOnWerbeo}" == "true" ]; then
    deleteSamples ${werbeoId}	
  fi

  saveSamplesToWerbeo $kampagnen_id

fi

