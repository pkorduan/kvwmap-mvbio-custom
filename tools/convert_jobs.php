<?php
/*
  Die Konvertjobs werden aktuell direkt bei der archivierung der bögen über das Snippet mvbio_archivierung.php gestartet.
  $timestamp = date("Y-m-d H:i:s", time());
  // echo "\n\n" . $timestamp . ": Start convert_jobs.php";

  // Running this script with a cron job like this:
  // cd /var/www/apps/kvwmap/custom/tools; php -f convert_jobs.php stelle_id=1 login_name=admin >> /var/www/logs/cron/convert_jobs.log 2>&1
  error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING));

  function include_($filename) {
    include_once $filename;
  }

  try {
    include('../../credentials.php');
    include('../../config.php');
    include(WWWROOT . APPLVERSION . 'funktionen/allg_funktionen.php');
    include(CLASSPATH . 'kvwmap.php');
    include(CLASSPATH . 'log.php');
    include(CLASSPATH . 'rolle.php');
    include(CLASSPATH . 'stelle.php');
    include(CLASSPATH . 'users.php');
    include(CLASSPATH . 'mysql.php');
    include(CLASSPATH . 'postgresql.php');
    include(CLASSPATH . 'PgObject.php');
    include(CLASSPATH . 'Nutzer.php');
    include(CLASSPATH . 'ConvertJob.php');

    define('DBWRITE', DEFAULTDBWRITE);
    $language = 'german';

    $debug = new Debugger(DEBUGFILE);	# öffnen der Debug-log-datei
    $log_convert = new LogFile(LOGPATH . 'cron/convert_jobs.log', 'text', 'a');
    $cmds_convert = new LogFile(LOGPATH . 'cron/convert_cmds.log', 'text', 'a');

    if (LOG_LEVEL > 0) {
      $log_mysql = new LogFile(LOGFILE_MYSQL,'text','Log-Datei MySQL', '#------v: ' . date("Y-m-d H:i:s", time()));
      $log_postgres = new LogFile(LOGFILE_POSTGRES, 'text', 'Log-Datei Postgres', '------v: ' . date("Y-m-d H:i:s", time()));
    }

    $GUI = new GUI('', '', ''); // übernimmt $debug aus globaler Variable

    // if (!$GUI->is_tool_allowed('only_cli')) exit;
    $userDb = new database(); // übernimmt auch $debug aus globale Variable
    $userDb->host = MYSQL_HOST;
    $userDb->user = MYSQL_USER;
    $userDb->passwd = MYSQL_PASSWORD;
    $userDb->dbName = MYSQL_DBNAME;
    $GUI->database = $userDb;
    $GUI->database->open();
    $GUI->pgdatabase = new pgdatabase();
    $GUI->debug_level = 5;
    $GUI->pgdatabase->open(2);

    if (isset($argv)) {
      array_shift($argv);
      $_REQUEST = array();
      foreach ($argv AS $arg) {
        list($key, $val) = explode('=', $arg);
        $_REQUEST[$key] = $val;
      }
    }
    $GUI->formvars = $_REQUEST;
    $log_convert->write(date('Y-m-d H:i:s', time()) . ' Starte convert_jobs.php mit Parametern: ' . print_r($GUI->formvars, true) . "\n");
    if (!array_key_exists('stelle_id', $GUI->formvars)) {
      $log_convert->write("Parameter stelle_id wurde nicht übergeben.\n");
      exit;
    }
    if ($GUI->formvars['stelle_id'] == '') {
      $log_convert->write("Parameter stelle_id ist leer.\n");
      exit;
    }
    if (!array_key_exists('login_name', $GUI->formvars)) {
      $log_convert->write("Parameter login_name wurde nicht übergeben.\n");
      exit;
    }
    if ($GUI->formvars['login_name'] == '') {
      $log_convert->write("Parameter login_name ist leer.\n");
      exit;
    }

    $err_msgs = array();
    $GUI->Stelle = new stelle($GUI->formvars['stelle_id'], $GUI->database);
    $GUI->user = new user($GUI->formvars['login_name'], 0, $GUI->database);
    $GUI->user->setRolle($GUI->formvars['stelle_id']);

    if ($GUI->formvars['dryrun'] == 1) {
      // Frage alle convert_jobs ab und schreibe Befehl in Log-Datei cmds_convert
      foreach (ConvertJob::find_beauftragte($GUI) AS $convert_job) {
        $cmds_convert->write($convert_job->get_convert_cmd());
      }
    }
    else {
      // Frage convert_jobs ab und arbeite sie ab bis keine offenen mehr da sind
      while (true) {
        $convert_job = ConvertJob::find_next($GUI);
        if ($convert_job) {
          $log_convert->write("\n" . date('Y-m-d H:i:s', time()) . " Starte Konvertierung des Convertjobs mit ID " . $convert_job->get_id() . ".");
          $result = $convert_job->convert();
          if ($result['success']) {
            $log_convert->write($result['msg'] . "\n" . $convert_job->get_convert_cmd());
            $result = $convert_job->update_dst_file();
            if ($result['success']) {
              $convert_job->update_attr(array("msg = 'size: " . filesize($convert_job->get('src_file')) . "/" . filesize($convert_job->get('dst_file')) . "'", "finished_at = 'now()'", "status = 'fertig'"));
            }
            else {
              $log_convert->write("\nFehler beim Setzen des Datei-Attributs in der Zieltabelle: " . $result['msg']);
              $convert_job->update_attr(array("msg = '" . $result['msg'] . "'", "status = 'fehlerhaft'"));
            }
          }
          else {
            $log_convert->write($result['msg'] . "\n" . $convert_job->get_convert_cmd());
            $convert_job->update_attr(array("msg = '" . $result['msg'] . "'", "status = 'fehlerhaft'"));
          }
        }
        else {
          $log_convert->write("Keine beauftragte Konvertierung gefunden. Nichts zu tun.\n");
          break;
        }
      }
    }
    $GUI->debug->close();
    $log_convert->close();
    $cmds_convert->close();
  }
  catch (Exception $e) {
    echo "\nFehler: " . $e->getMessage();
  }
*/?>