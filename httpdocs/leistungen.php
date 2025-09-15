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
$query .= ' IF(ISNULL(leistung.abgerechnet),\'\',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\')) AS abgerechnet';
$query .= ',leistung.anzahl';
$query .= ',leistung.artikel';
$query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
$query .= ',leistung.id';
$query .= ',leistung.kommentar';
$query .= ',leistung.preis';
$query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
$query .= ',leistung.setup';
$query .= ',artikel.artikelnummer';
$query .= ' FROM leistung';
$query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
$query .= ' WHERE leistung.kunde=' . (int)$kunde->kundennummer;
$query .= ' AND leistung.domain=0';
$query .= ' ORDER BY leistung.id';
$rleistungen = safe_mysql_query ($query);

require_once ("templates/html/leistungen.html");

?>