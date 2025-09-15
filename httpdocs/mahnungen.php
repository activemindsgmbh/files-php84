<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$id = trim (form_input(@$_GET['kunde']));
$kunde = NULL;
if (!isuint($id) || !($kunde = read_kunde($id)))
{
  fatal_error ('INVALID_ID');
}

$query  = 'SELECT';
$query .= ' betrag';
$query .= ',DATE_FORMAT(datum,\'%d.%m.%Y\') AS datum';
$query .= ',id';
$query .= ',IF(ISNULL(verschickt),\'\',DATE_FORMAT(verschickt,\'%d.%m.%Y\')) AS verschickt';
$query .= ',waehrung';
$query .= ' FROM mahnung';
$query .= ' WHERE kunde=' . (int)$kunde->kundennummer;
$query .= ' ORDER BY id DESC';
$rmahnungen = safe_mysql_query ($query);

require_once ("templates/html/mahnungen.html");

?>