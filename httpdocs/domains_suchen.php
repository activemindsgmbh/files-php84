<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$rdomains = NULL;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  require_once ('punycode/idna_convert.class.php');
 	$IDN = new idna_convert ();
//  $domains = split ("\n", form_input(@$_POST['domains']));
  $domains = preg_split ("#[,;: \n\r\t]#", form_input(@$_POST['domains']));
	$list = array ();
	foreach ($domains as $domain)
	{
		$domain = trim ($domain);
		if ($domain !== '')
		{
			$list[] = $IDN->encode ($domain);
		}
	}
	if (count($list) > 0)
	{
		$query  = 'SELECT';
		$query .= ' artikel.artikelnummer';
		$query .= ',IF(ISNULL(leistung.abgerechnet),\'\',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\')) AS abgerechnet';
		$query .= ',leistung.artikel';
		$query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
		$query .= ',leistung.id';
		$query .= ',leistung.preis';
		$query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
		$query .= ',leistung.setup';
		$query .= ',domain.domain AS domain_name';
		$query .= ',DATE_FORMAT(domain.regdate,\'%d.%m.%Y\') AS regdate';
		$query .= ',domain.a_dom';
		$query .= ',domain.a_www';
		$query .= ',domain.unicode AS domain_utf8name';
		$query .= ',kunde.kundennummer';
		$query .= ',kunde.name';
		$query .= ',kunde.vorname';
		$query .= ',IF(leistung.preis>0 AND ISNULL(leistung.endedatum),IF(ISNULL(leistung.abgerechnet) OR leistung.abgerechnet<DATE_SUB(CURDATE(),INTERVAL artikel.intervall MONTH),\'ASAP\',DATE_FORMAT(DATE_ADD(leistung.referenzdatum,INTERVAL artikel.intervall*FLOOR((YEAR(leistung.abgerechnet)*12+MONTH(leistung.abgerechnet)-(YEAR(leistung.referenzdatum)*12+MONTH(leistung.referenzdatum))+artikel.intervall)/artikel.intervall) MONTH),\'%d.%m.%Y\')),\'\') AS rechnung';
		$query .= ',GROUP_CONCAT(DISTINCT CONCAT(mx.priority,\' \',mx.host) ORDER BY mx.priority,mx.host) AS mxhosts';
		$query .= ',GROUP_CONCAT(DISTINCT ns.host ORDER BY ns.host) AS nshosts';
		$query .= ' FROM leistung';
		$query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
		$query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
		$query .= ' LEFT JOIN kunde ON kunde.kundennummer=leistung.kunde';
		$query .= ' LEFT JOIN mx ON mx.domain=domain.id';
		$query .= ' LEFT JOIN ns ON ns.domain=domain.id';
		$query .= ' WHERE leistung.domain!=0';
		$k = trim (form_input(@$_POST['kunde']));
		if ($k !== '' && isuint($k) && $k !== '0')
		{
			$query .= ' AND leistung.kunde=' . (int)$k;
		}
		if (count($list) == 1)
		{
			$query .= ' AND domain.domain LIKE \'' . mysql_escape_pattern($list[0]) . '\'';
		}
		else
		{
			$domains = '';
			foreach ($list as $domain)
			{
				$domains .= ($domains !== '' ? ',' : '') . '\'' . mysql_real_escape_string($domain) . '\'';
			}
			$query .= " AND domain.domain IN({$domains})";
		}
		$query .= ' GROUP BY leistung.id';
		$query .= ' ORDER BY domain.unicode';
		$rdomains = safe_mysql_query ($query);
	}
}

$query  = 'SELECT';
$query .= ' kunde.kundennummer';
$query .= ',kunde.name';
$query .= ',kunde.vorname';
$query .= ' FROM kunde';
$query .= ' ORDER BY name,vorname,kundennummer';
$rkunde = safe_mysql_query ($query);

require_once ("templates/html/domains_suchen.html");

?>