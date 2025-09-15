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

$step = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false) && trim(form_input(@$_POST['confirm_delete'])) === '1')
{
  safe_mysql_query ('DELETE FROM kunde WHERE kundennummer=' . (int)$kunde->kundennummer);
  safe_mysql_query ('DELETE FROM kundenpreis WHERE kunde=' . (int)$kunde->kundennummer);
  $step = 2;
}

require_once ("templates/html/kunde_loeschen_{$step}.html");

?>