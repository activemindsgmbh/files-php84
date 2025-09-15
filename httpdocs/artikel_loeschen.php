<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('artikel.inc.php');

$id = trim (form_input(@$_GET['id']));
$artikel = NULL;
if ($id !== '' && (!isuint($id) || !($artikel = read_artikel($id))))
{
  fatal_error ('INVALID_ID');
}

$step = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false) && trim(form_input(@$_POST['confirm_delete'])) === '1')
{
  safe_mysql_query ('DELETE FROM artikel WHERE id=' . (int)$artikel->id);
  safe_mysql_query ('DELETE FROM artikelupdate WHERE artikel=' . (int)$artikel->id);
  safe_mysql_query ('DELETE FROM kundenpreis WHERE artikel=' . (int)$artikel->id);
  $step = 2;
}

require_once ("templates/html/artikel_loeschen_{$step}.html");

?>