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
$datum = trim (form_input(@$_GET['datum']));
if ($datum === '' || ($datum = parse_date($datum)) < 0)
{
  fatal_error ('INVALID_PARAMETER');
}

$query  = 'SELECT';
$query .= ' leistung.abgerechnet+0 AS abgerechnet';
$query .= ',leistung.anzahl';
$query .= ',leistung.endedatum+0 AS endedatum';
$query .= ',leistung.id';
$query .= ',IF(leistung.domain,CONCAT(artikel.kurztext,\'\\n\',domain.domain),leistung.kommentar) AS kommentar';
$query .= ',leistung.preis';
$query .= ',leistung.referenzdatum+0 AS referenzdatum';
$query .= ',leistung.setup';
$query .= ',artikel.artikelnummer';
$query .= ',artikel.fibukonto';
$query .= ',artikel.intervall';
$query .= ',domain.domain';
$query .= ',' . ($kunde->umsatzsteuerbefreit ? '0' : 'IF(ISNULL(artikel.umsatzsteuer),' . mysql_real_escape_string($GLOBALS['conf_umsatzsteuer']) . ',artikel.umsatzsteuer)') . ' AS umsatzsteuer';
$query .= ' FROM leistung';
$query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
$query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
$query .= ' WHERE leistung.kunde=' . (int)$kunde->kundennummer;
$query .= ' AND leistung.referenzdatum<=' . (int)$datum;
$query .= ' AND (ISNULL(leistung.abgerechnet) OR leistung.abgerechnet<' . (int)$datum . ')';
$query .= ' ORDER BY leistung.id';
$rleistungen = safe_mysql_query ($query);

require_once ("templates/html/rechnungsvorschau.html");

?>