<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('artikel.inc.php');

$id = trim (form_input(@$_GET['id']));
$artikel = NULL;
if ($id !== '' && (!isuint($id) || !($artikel = read_artikel($id))))
{
  fatal_error ('INVALID_ID');
}

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  $query  = 'SELECT';
  $query .= ' preis';
  $query .= ',setup';
  $query .= ',DATE_FORMAT(termin,\'%d.%m.%Y\') AS termin';
  $query .= ' FROM artikelupdate';
  $query .= ' WHERE artikel=' . (int)$id;
  $res = safe_mysql_query ($query);
  $_POST['preis'] = array ();
  $_POST['setup'] = array ();
  $_POST['termin'] = array ();
  $count = 0;
  while ($obj = mysql_fetch_object($res))
  {
    $_POST['preis'][$count]  = db2form ($obj->preis);
    $_POST['setup'][$count]  = db2form ($obj->setup);
    $_POST['termin'][$count] = db2form ($obj->termin);
    ++$count;
  }
}
else
{
  $dates = array ();
  $queries = array ();
  reset ($_POST['preis']);
  reset ($_POST['setup']);
  reset ($_POST['termin']);
  $index = 0;
  while (1)
  {
    if (!(($preis = each($_POST['preis'])) && ($setup = each($_POST['setup'])) && ($termin = each ($_POST['termin']))))
    {
      break;
    }
    if ($preis[1] !== '' || $setup[1] !== '' || $termin[1] !== '')
    {
      if ($preis[1] === '' && $setup[1] === '')
      {
        set_error ('artikelupdate.preis[' . $index . ']', 'EMPTY');
      }
      else if ($preis[1] !== '' && !ereg('^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$', $preis[1]))
      {
        set_error ('artikelupdate.preis[' . $index . ']', 'INVALID');
      }
      if ($setup[1] === '' && $preis[1] === '')
      {
        set_error ('artikelupdate.setup[' . $index . ']', 'EMPTY');
      }
      else if ($setup[1] !== '' && !ereg('^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$', $setup[1]))
      {
        set_error ('artikelupdate.setup[' . $index . ']', 'INVALID');
      }
      if ($termin[1] === '')
      {
        set_error ('artikelupdate.termin[' . $index . ']', 'EMPTY');
      }
      else if (($date = parse_date($termin[1])) < 0 || $date <= (int)date('Ymd'))
      {
        set_error ('artikelupdate.termin[' . $index . ']', 'INVALID');
      }
      else if (in_array($date, $dates))
      {
        set_error ('artikelupdate.termin[' . $index . ']', 'NOT UNIQUE');
      }
      else
      {
        $dates[] = $date;
      }
      if (!error())
      {
        $query  = 'INSERT INTO artikelupdate SET';
        $query .= ' artikel=' . (int)$artikel->id;
        $query .= ',preis=' . ($preis[1] !== '' ? '\'' . mysql_real_escape_string(str_replace(',','.',$preis[1])) . '\'' : 'NULL');
        $query .= ',setup=' . ($setup[1] !== '' ? '\'' . mysql_real_escape_string(str_replace(',','.',$setup[1])) . '\'' : 'NULL');
        $query .= ',termin=' . (int)$date;
        $queries[] = $query;
      }
    }
    ++$index;
  }
  if (!error())
  {
    safe_mysql_query ('DELETE FROM artikelupdate WHERE artikel=' . (int)$artikel->id);
    foreach ($queries as $query)
    {
      safe_mysql_query ($query);
    }
    safe_mysql_query ('OPTIMIZE TABLE artikelupdate');
  }
}

require_once ("templates/html/artikelupdate.html");

?>