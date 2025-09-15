<?php

set_time_limit (300);

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();


/*
$res = mysql_query ('SELECT code,name FROM land');
while ($obj = mysql_fetch_object($res))
{
  mysql_query ('UPDATE land SET name=\'' . mysql_real_escape_string(utf8_encode($obj->name)) . '\' WHERE code=\'' . $obj->code . '\'');
}
*/


require_once ('kunde.inc.php');

$id = trim (form_input(@$_GET['kunde']));
$kunde = NULL;
if (!isuint($id) || !($kunde = read_kunde($id)))
{
  fatal_error ('INVALID_ID');
}

$step = 1;

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  $_POST['referenzdatum'] = date ('d.m.Y');
  $_POST['regdate']       = date ('d.m.Y');
}
else
{
  $artikel       = trim (form_input(@$_POST['artikel']));
  $endedatum     = trim (form_input(@$_POST['endedatum']));
  $preis         = trim (form_input(@$_POST['preis']));
  $setup         = trim (form_input(@$_POST['setup']));
  $referenzdatum = trim (form_input(@$_POST['referenzdatum']));
  $regdate       = trim (form_input(@$_POST['regdate']));
  $article = NULL;
  if ($artikel === '')
  {
    set_error ('leistung.artikel', 'EMPTY');
  }
  else
  {
    $query = 'SELECT';
    $query .= ' artikel.id';
    $query .= ',IF(NOT ISNULL(kundenpreis.preis),kundenpreis.preis,artikel.preis) AS preis';
    $query .= ',IF(NOT ISNULL(kundenpreis.setup),kundenpreis.setup,artikel.setup) AS setup';
    $query .= ' FROM artikel';
    $query .= ' LEFT JOIN kundenpreis ON kundenpreis.kunde=' . (int)$kunde->kundennummer;
    $query .= ' AND kundenpreis.artikel=artikel.id';
    $query .= ' AND (ISNULL(kundenpreis.von) OR kundenpreis.von<=CURDATE())';
    $query .= ' AND (ISNULL(kundenpreis.bis) OR kundenpreis.bis>=CURDATE())';
    $query .= ' WHERE artikel.artikelnummer=\'' . mysql_real_escape_string($artikel) . '\'';
    $query .= ' AND artikel.domainreg=1';
    if (!($article = mysql_query_object($query)))
    {
      set_error ('leistung.artikel', 'INVALID');
    }
    else
    {
      $artikel = (int)$article->id;
    }
  }
  $domains = array ();
  require_once ('punycode/idna_convert.class.php');
  $IDN = new idna_convert ();
  foreach (preg_split("#[,;: \n\r\t]#", form_input(@$_POST['domains'])) as $domain)
  {
    $domain = $IDN->encode (trim($domain));
    if ($domain !== '' && !in_array($domain, $domains))
    {
      $domains[] = $domain;
    }
  }
  $domainerr = array ();
  if (count($domains) == 0)
  {
    set_error ('domains', 'EMPTY');
  }
  else
  {
    foreach ($domains as $domain)
    {
      if (mysql_count('domain', 'domain=\'' . mysql_real_escape_string($domain) . '\''))
      {
        $domainerr[$domain] = 'NOT UNIQUE';
      }
    }
    if (count($domainerr))
    {
      set_error ('domains', 'INVALID');
    }
  }
  if ($endedatum !== '' && ($endedatum = parse_date($endedatum)) < 0)
  {
    set_error ('leistung.endedatum', 'INVALID');
  }
  if ($preis === '')
  {
    $preis = @$article->preis;
    $_POST['preis'] = $preis;
  }
  else if (!preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $preis))
  {
    set_error ('leistung.preis', 'INVALID');
  }
  if ($referenzdatum === '')
  {
    set_error ('leistung.referenzdatum', 'EMPTY');
  }
  else if (($referenzdatum = parse_date($referenzdatum)) < 0)
  {
    set_error ('leistung.referenzdatum', 'INVALID');
  }
  if ($setup === '')
  {
    $setup = @$article->setup;
    $_POST['setup'] = $setup;
  }
  else if (!preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $setup))
  {
    set_error ('leistung.setup', 'INVALID');
  }
  if ($regdate === '')
  {
    set_error ('domain.regdate', 'EMPTY');
  }
  else if (($regdate = parse_date($regdate)) < 0)
  {
    set_error ('domain.regdate', 'INVALID');
  }
  if (!error())
  {
    reset ($domains);
    foreach ($domains as $domain)
    {
      $query  = 'INSERT INTO domain SET';
      $query .= ' domain=\'' . mysql_real_escape_string($domain) . '\'';
      $query .= ',regdate=' . (int)$regdate;
      $query .= ',unicode=\'' . mysql_real_escape_string($IDN->decode($domain)) . '\'';
      safe_mysql_query ($query);
      $domid = mysql_insert_id ();
      $query  = 'INSERT INTO leistung SET';
      $query .= ' abgerechnet=NULL';
      $query .= ',anzahl=1';
      $query .= ',artikel=' . (int)$artikel;
      $query .= ',domain=' . (int)$domid;
      $query .= ',endedatum=' . ($endedatum !== '' ? (int)$endedatum : 'NULL');
      $query .= ',hosting=0';
      $query .= ',kommentar=\'\'';
      $query .= ',kunde=' . (int)$kunde->kundennummer;
      $query .= ',preis=\'' . mysql_real_escape_string(str_replace(',','.',$preis)) . '\'';
      $query .= ',referenzdatum=' . (int)$referenzdatum;
      $query .= ',setup=\'' . mysql_real_escape_string(str_replace(',','.',$setup)) . '\'';
      safe_mysql_query ($query);
    }
    $step = 2;
  }
}

require_once ("templates/html/domains_erfassen_{$step}.html");

?>