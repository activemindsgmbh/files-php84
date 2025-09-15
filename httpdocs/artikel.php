<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$query  = 'SELECT';
$query .= ' artikel.artikelnummer';
$query .= ',artikel.domainreg';
$query .= ',artikel.domains';
$query .= ',artikel.fibukonto';
$query .= ',artikel.id';
$query .= ',artikel.intervall';
$query .= ',artikel.kurztext';
$query .= ',artikel.preis';
$query .= ',artikel.setup';
$query .= ',artikel.umsatzsteuer';
$query .= ' FROM artikel';
$query .= ' ORDER BY artikel.artikelnummer';
$rartikel = safe_mysql_query ($query);

require_once ("templates/html/artikel.html");

?>