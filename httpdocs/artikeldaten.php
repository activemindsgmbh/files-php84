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

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  if ($artikel)
  {
    $_POST['artikelnummer'] = db2form ($artikel->artikelnummer);
    $_POST['domainreg']     = db2form ($artikel->domainreg);
    $_POST['domains']       = db2form ($artikel->domains);
    $_POST['fibukonto']     = db2form ($artikel->fibukonto);
    $_POST['intervall']     = db2form ($artikel->intervall);
    $_POST['kurztext']      = db2form ($artikel->kurztext);
    $_POST['langtext']      = db2form ($artikel->langtext);
    $_POST['preis']         = db2form ($artikel->preis);
    $_POST['setup']         = db2form ($artikel->setup);
    $_POST['textanzeige']   = db2form ($artikel->textanzeige);
    $_POST['umsatzsteuer']  = db2form ($artikel->umsatzsteuer);
  }
}
else
{
  $artikelnummer = trim (form_input(@$_POST['artikelnummer']));
  $domains       = trim (form_input(@$_POST['domains']));
  $fibukonto     = trim (form_input(@$_POST['fibukonto']));
  $intervall     = trim (form_input(@$_POST['intervall']));
  $kurztext      = trim (form_input(@$_POST['kurztext']));
//  $langtext      = trim (form_input(@$_POST['langtext']));
  $preis         = trim (form_input(@$_POST['preis']));
  $setup         = trim (form_input(@$_POST['setup']));
//  $textanzeige   = trim (form_input(@$_POST['textanzeige']));
  $umsatzsteuer  = trim (form_input(@$_POST['umsatzsteuer']));

  if (!$artikel)
  {
    if ($artikelnummer === '')
    {
      set_error ('artikel.artikelnummer', 'EMPTY');
    }
    else if (!preg_match('#^[a-zA-Z0-9הצüִײÜ][-a-zA-Z0-9הצüִײÜ]{0,8}[a-zA-Z0-9הצüִײÜ]$#', $artikelnummer))
    {
      set_error ('artikel.artikelnummer', 'INVALID');
    }
    else if (mysql_count('artikel', 'artikelnummer=\'' . mysql_real_escape_string($artikelnummer) . '\''))
    {
      set_error ('artikel.artikelnummer', 'NOT UNIQUE');
    }
  }

  set_error_if ($domains !== '' && !isuint($domains), 'artikel.domains', 'INVALID');

  if ($fibukonto === '')
  {
    set_error ('artikel.fibukonto', 'EMPTY');
  }
  else if (!isuint($fibukonto))
  {
    set_error ('artikel.fibukonto', 'INVALID');
  }

  set_error_if ($intervall !== '' && !isuint($intervall), 'artikel.intervall', 'INVALID');
  set_error_if ($kurztext === '', 'artikel.kurztext', 'EMPTY');

  if ($preis === '')
  {
    set_error ('artikel.preis', 'EMPTY');
  }
  else if (!preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $preis))
  {
    set_error ('artikel.preis', 'INVALID');
  }

  if ($setup === '')
  {
    $setup = 0;
  }
  else if (!preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $setup))
  {
    set_error ('artikel.setup', 'INVALID');
  }

  if ($umsatzsteuer !== '' && !preg_match('^(100|100[,.]00?|[0-9][0-9]?|[0-9]{0,2}[,.][0-9][0-9]?)$', $umsatzsteuer))
  {
    set_error ('artikel.umsatzsteuer', 'INVALID');
  }

  if (!error())
  {
    $query  = ($artikel ? 'UPDATE artikel SET' : 'INSERT INTO artikel SET');
    $query .= ' domainreg=' . (int)form_input(@$_POST['domainreg']);
    $query .= ',domains=' . (int)$domains;
    $query .= ',fibukonto=' . (int)$fibukonto;
    $query .= ',intervall=' . (int)$intervall;
    $query .= ',kurztext=\'' . mysql_real_escape_string($kurztext) . '\'';
    $query .= ',langtext=\'' . mysql_real_escape_string(trim(form_input(@$_POST['langtext']))) . '\'';
    $query .= ',preis=\'' . mysql_real_escape_string(str_replace(',','.',$preis)) . '\'';
    $query .= ',setup=\'' . mysql_real_escape_string(str_replace(',','.',$setup)) . '\'';
    $query .= ',textanzeige=' . (int)form_input(@$_POST['textanzeige']);
    $query .= ',umsatzsteuer=' . ($umsatzsteuer !== '' ? '\'' . str_replace(',','.',$umsatzsteuer) . '\'' : 'NULL');
    $query .= ($artikel ? ' WHERE id=' . (int)$artikel->id : ',artikelnummer=\'' . mysql_real_escape_string($artikelnummer) . '\'');
    safe_mysql_query ($query);
    if (!$artikel)
    {
      $artikel = read_artikel(mysql_insert_id());
    }
    $step = 2;
  }
}

require_once ("templates/html/artikeldaten_{$step}.html");

?>