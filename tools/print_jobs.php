<?php
  $timestamp = date("Y-m-d H:i:s", time());
  // echo "\n\n" . $timestamp . ": Start print_jobs.php";

  // Running this script with a cron job like this:
  // cd /var/www/apps/kvwmap/custom/tools; php -f print_jobs.php stelle_id=1 login_name=admin >> /var/www/logs/cron/print_jobs.log 2>&1
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
    include(CLASSPATH . 'PrintJob.php');

    define('DBWRITE', DEFAULTDBWRITE);
    $language = 'german';

    $debug = new Debugger('cron/print_jobs.log', 'text/plain', 'w');
    $debug->user_funktion = 'admin';

    if (LOG_LEVEL > 0) {
      $log_mysql = new LogFile(LOGFILE_MYSQL,'text','Log-Datei MySQL', '#------v: ' . date("Y:m:d H:i:s", time()));
      $log_postgres = new LogFile(LOGFILE_POSTGRES, 'text', 'Log-Datei Postgres', '------v: ' . date("Y:m:d H:i:s", time()));
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

    if (!array_key_exists('stelle_id', $GUI->formvars)) {
      $GUI->debug->show("Parameter stelle_id wurde nicht übergeben.\n", true);
      exit;
    }
    if ($GUI->formvars['stelle_id'] == '') {
      $GUI->debug->show("Parameter stelle_id ist leer.\n", true);
      exit;
    }
    if (!array_key_exists('login_name', $GUI->formvars)) {
      $GUI->debug->show("Parameter login_name wurde nicht übergeben.\n", true);
      exit;
    }
    if ($GUI->formvars['login_name'] == '') {
      $GUI->debug->show("Parameter login_name ist leer.\n", true);
      exit;
    }

    $err_msgs = array();
    $GUI->Stelle = new stelle($GUI->formvars['stelle_id'], $GUI->database);
    $GUI->user = new user($GUI->formvars['login_name'], 0, $GUI->database);
    $GUI->user->setRolle($GUI->formvars['stelle_id']);
    $pj = PrintJob::find_next($GUI);
    if ($pj) {
      // Abfrage der Stelle und des Users für den der Druck erfolgen soll
      $GUI->Stelle = new stelle($pj->get('stelle_id'), $GUI->database);
      $user = Nutzer::find_by_id($GUI, $pj->get('user_id'));
      $GUI->user = new user($user->get('login_name'), 0, $GUI->database);
      $GUI->user->setRolle($pj->get('stelle_id'));
      $GUI->user->debug->user_funktion = 'admin';
      $result = $pj->print();
      if ($result['success']) {
        $pj->update_attr(array("printed_at = 'now()'", "status = 'gedruckt'"));
      }
      else {
        $pj->update_attr(array("msg = '" . $result['msg'] . "'", "status = 'fehlerhaft'"));
      }
      $GUI->debug->show($result['msg'] . "\n", true);
    }
    else {
      // $GUI->debug->show("No print job found.\n", true);
    }
    $GUI->debug->close();
  }
  catch (Exception $e) {
    $GUI->debug->show("Fehler: " . $e, true);
  }
?>