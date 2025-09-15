<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$query  = 'SELECT';
$query .= ' kunde.anrede';
$query .= ',kunde.kundennummer';
$query .= ',kunde.name';
$query .= ',kunde.ort';
$query .= ',kunde.postleitzahl';
$query .= ',kunde.strasse';
$query .= ',kunde.email';
$query .= ',kunde.vorname';
$query .= ' FROM kunde';
$query .= ' ORDER BY name,vorname,kundennummer';
$rkunde = safe_mysql_query ($query);

require_once ("templates/html/kunden.html");

?>