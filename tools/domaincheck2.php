<?php

error_reporting(E_ALL);
ini_set('include_path', ini_get('include_path') . ':/var/www/vhosts/amfakt.activeminds.net/include');
require_once('system.inc.php');
require_once('Database.php');
system_init();

function my_gethostbyname($hostname)
{
    $ip = gethostbyname($hostname);
    return ($ip !== $hostname ? $ip : '');
}

$db = Database::getInstance();

$query  = 'SELECT domain,id FROM domain';
/*
$query .= ' WHERE lastcheck<DATE_SUB(NOW(),INTERVAL 7 DAY)';
$query .= ' ORDER BY lastcheck DESC';
$query .= ' LIMIT 25';
*/
$res = $db->query($query);

while ($obj = $db->fetchObject($res)) {
    $db->query('DELETE FROM mx WHERE domain=' . (int)$obj->id);
    $db->query('DELETE FROM ns WHERE domain=' . (int)$obj->id);
    $adom = my_gethostbyname($obj->domain);
    $awww = my_gethostbyname("www.{$obj->domain}");
    $query  = 'UPDATE domain SET';
    $query .= ' a_dom=\'' . $db->escapeString($adom) . '\'';
    $query .= ',a_www=\'' . $db->escapeString($awww) . '\'';
	$query .= ',lastcheck=NOW()';
	$query .= ' WHERE id=' . (int)$obj->id;
	safe_mysql_query ($query);
	$mxhosts = dns_get_record ($obj->domain, DNS_MX);
	if (is_array($mxhosts))
	{
		foreach ($mxhosts as $host)
		{
			$query  = 'INSERT INTO mx SET';
			$query .= ' domain=' . (int)$obj->id;
			$query .= ',host=\'' . mysql_real_escape_string($host['target']) . '\'';
			$query .= ',priority=' . (int)$host['pri'];
			safe_mysql_query ($query);
		}
	}
	$nshosts = dns_get_record ($obj->domain, DNS_NS);
	if (is_array($nshosts))
	{
		foreach ($nshosts as $host)
		{
			$query  = 'INSERT INTO ns SET';
			$query .= ' domain=' . (int)$obj->id;
			$query .= ',host=\'' . mysql_real_escape_string($host['target']) . '\'';
			safe_mysql_query ($query);
		}
	}

	echo "Domain: {$obj->domain}\n";
	echo "{$obj->domain} -> {$adom}\n";
	echo "www.{$obj->domain} -> {$awww}\n";
	foreach ($mxhosts as $host)
	{
		echo "MX: {$host['pri']} {$host['target']}\n";
	}
	foreach ($nshosts as $host)
	{
		echo "NS: {$host['target']}\n";
	}
	echo "\n";

}

?>
