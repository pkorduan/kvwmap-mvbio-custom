#!/bin/bash
max_parallel_jobs=10
if [ -z "$1" ] ; then
  echo "Missing argument 1 for file that contain the convert commands."
  exit
fi
datei_name=$1

if [ ! -f "${datei_name}" ] ; then
  echo "Missing file ${datei_name}"
  exit
fi

echo "Starte Convertierung der Bilder."
while IFS= read -r cmd; do
  if [[ -z "$cmd" ]]; then
    continue
  fi
  echo "$cmd";
done < "${datei_name}" | xargs -I {} -P $max_parallel_jobs bash -c '{}'
wait
echo "Convertierung der Bilder beendet."