<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$id = trim (form_input(@$_GET['kunde']));
$kunde = NULL;
if ($id !== '' && (!isuint($id) || !($kunde = read_kunde($id))))
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
$query .= ',leistung.kunde';
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
$query .= ' WHERE leistung.domain!=0';
if ($kunde)
{
	$query .= ' AND leistung.kunde=' . (int)$kunde->kundennummer;
}
$query .= ' GROUP BY leistung.id';
$query .= ' ORDER BY domain.domain';
$rdomains = safe_mysql_query ($query);

if (mysql_num_rows($rdomains))
{
	header ('Content-Type: text/csv');
	header ('Content-Disposition: attachment; filename="domains.csv"');
	echo "Domainname;Kundennummer;Registrierungsdatum;Abrechnungsbeginn;Preis;Setup;zuletzt abgerechnet;Abrechnungsende;Hosting zugeordnet;NS-Records;MX-Records;Domain-Record;WWW-Record\n";
	while ($domain = mysql_fetch_object($rdomains))
	{
		echo '"' . $domain->domain_name . '"';
		echo ';"' . $domain->kunde . '"';
		echo ';"' . $domain->regdate . '"';
		echo ';"' . $domain->referenzdatum . '"';
		echo ';"' . number_format($domain->preis,2,',','.') . '"';
		echo ';"' . number_format($domain->setup,2,',','.') . '"';
		echo ';"' . $domain->abgerechnet . '"';
		echo ';"' . $domain->endedatum . '"';
		echo ';"' . ($domain->hosting ? 'ja' : '') . '"';
		echo ';"' . $domain->nshosts . '"';
		echo ';"' . $domain->mxhosts . '"';
		echo ';"' . $domain->a_dom . '"';
		echo ';"' . $domain->a_www . '"';
		echo "\n";
	}
	exit;
}

require_once ("templates/html/domainexport.html");

?>