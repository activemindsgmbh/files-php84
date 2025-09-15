<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$query  = 'SELECT';
$query .= ' DATE_FORMAT(rechnung.datum,\'%d.%m.%Y\') AS datum';
$query .= ',DATE_FORMAT(rechnung.faellig,\'%d.%m.%Y\') AS faellig';
$query .= ',rechnung.forderung';
$query .= ',rechnung.id';
$query .= ',rechnung.status';
$query .= ',rechnung.zahlung';
$query .= ',IF(ISNULL(rechnung.verschickt),\'\',DATE_FORMAT(rechnung.verschickt,\'%d.%m.%Y\')) AS verschickt';
$query .= ',rechnung.waehrung';
$query .= ',kunde.kundennummer';
$query .= ',kunde.name';
$query .= ',kunde.vorname';
$query .= ' FROM rechnung';
$query .= ' LEFT JOIN kunde ON kunde.kundennummer=rechnung.kunde';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  $query .= ' WHERE rechnung.id LIKE \'' . mysql_escape_pattern(trim(form_input(@$_POST['pattern']))) . '\'';
  $query .= ' ORDER BY rechnung.id DESC';
}
else
{
  $query .= ' WHERE rechnung.status!=\'ERLEDIGT\'';
  $query .= ' ORDER BY rechnung.id DESC';
  $query .= ' LIMIT 25';
}

$rrechnungen = safe_mysql_query ($query);

require_once ("templates/html/rechnungen_suchen.html");

?>