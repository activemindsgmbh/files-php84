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
$query .= ',leistung.artikel';
$query .= ',leistung.domain';
$query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
$query .= ',leistung.hosting';
$query .= ',leistung.id';
$query .= ',leistung.preis';
$query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
$query .= ',leistung.setup';
$query .= ',domain.a_dom';
$query .= ',domain.a_www';
$query .= ',domain.domain AS domain_name';
$query .= ',DATE_FORMAT(domain.regdate,\'%d.%m.%Y\') AS regdate';
$query .= ',domain.unicode AS domain_utf8name';
$query .= ',GROUP_CONCAT(DISTINCT CONCAT(mx.priority,\' \',mx.host) ORDER BY mx.priority,mx.host) AS mxhosts';
$query .= ',GROUP_CONCAT(DISTINCT ns.host ORDER BY ns.host) AS nshosts';
$query .= ' FROM leistung';
$query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
$query .= ' LEFT JOIN mx ON mx.domain=domain.id';
$query .= ' LEFT JOIN ns ON ns.domain=domain.id';
$query .= ' WHERE leistung.kunde=' . (int)$kunde->kundennummer;
$query .= ' AND leistung.domain!=0';
$query .= ' GROUP BY leistung.id';
$query .= ' ORDER BY domain.unicode';
$rdomains = safe_mysql_query ($query);

require_once ("templates/html/domains.html");

?>