<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$kunde = NULL;
$leistung = NULL;
$id = trim (form_input(@$_GET['id']));
if ($id !== '')
{
  if (!isuint($id))
  {
    fatal_error ('INVALID_ID');
  }
  $query  = 'SELECT';
  $query .= ' IF(ISNULL(leistung.abgerechnet),\'\',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\')) AS abgerechnet';
  $query .= ',leistung.anzahl';
  $query .= ',leistung.artikel';
  $query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
  $query .= ',leistung.id';
  $query .= ',leistung.kommentar';
  $query .= ',leistung.kunde';
  $query .= ',leistung.preis';
  $query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
  $query .= ',leistung.setup';
  $query .= ',artikel.artikelnummer';
  $query .= ' FROM leistung';
  $query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
  $query .= ' WHERE leistung.id=' . (int)$id;
  $query .= ' AND leistung.domain=0';
  $leistung = mysql_query_object ($query);
  if (!$leistung)
  {
    fatal_error ('INVALID_ID');
  }
  $kunde = read_kunde ($leistung->kunde);
  if (!$kunde)
  {
    fatal_error ('INVALID_CUSTOMER');
  }
}
else
{
  $id = trim (form_input(@$_GET['kunde']));
  if (!isuint($id) || !($kunde = read_kunde($id)))
  {
    fatal_error ('INVALID_CUSTOMER');
  }
}

$step = 1;

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  if ($leistung)
  {
    $_POST['abgerechnet']   = db2form ($leistung->abgerechnet);
    $_POST['anzahl']        = db2form ($leistung->anzahl);
    $_POST['artikel']       = db2form ($leistung->artikelnummer);
    $_POST['endedatum']     = db2form ($leistung->endedatum);
    $_POST['kommentar']     = db2form ($leistung->kommentar);
    $_POST['preis']         = db2form ($leistung->preis);
    $_POST['referenzdatum'] = db2form ($leistung->referenzdatum);
    $_POST['setup']         = db2form ($leistung->setup);
  }
  else
  {
    $_POST['anzahl']        = 1;
    $_POST['referenzdatum'] = date ('d.m.Y');
  }
}
else
{
  $abgerechnet   = trim (form_input(@$_POST['abgerechnet']));
  $anzahl        = trim (form_input(@$_POST['anzahl']));
  $artikel       = trim (form_input(@$_POST['artikel']));
  $endedatum     = trim (form_input(@$_POST['endedatum']));
  $kommentar     = trim (form_input(@$_POST['kommentar']));
  $preis         = trim (form_input(@$_POST['preis']));
  $referenzdatum = trim (form_input(@$_POST['referenzdatum']));
  $setup         = trim (form_input(@$_POST['setup']));
  $article = NULL;
  if ($abgerechnet !== '' && ($abgerechnet = parse_date($abgerechnet)) < 0)
  {
    set_error ('leistung.abgerechnet', 'INVALID');
  }
  if ($anzahl === '')
  {
    set_error ('leistung.anzahl', 'EMPTY');
  }
  else if (!preg_match('#^([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $anzahl) || !((float)str_replace(',','.',$anzahl) > 0))
  {
    set_error ('leistung.anzahl', 'INVALID');
  }
  if ($artikel === '')
  {
    set_error ('leistung.artikel', 'EMPTY');
  }
  else
  {
    $query = 'SELECT';
    $query .= ' artikel.id';
    $query .= ',artikel.kurztext';
    $query .= ',artikel.langtext';
    $query .= ',IF(NOT ISNULL(kundenpreis.preis),kundenpreis.preis,artikel.preis) AS preis';
    $query .= ',IF(NOT ISNULL(kundenpreis.setup),kundenpreis.setup,artikel.setup) AS setup';
    $query .= ',artikel.textanzeige';
    $query .= ' FROM artikel';
    $query .= ' LEFT JOIN kundenpreis ON kundenpreis.kunde=' . (int)$kunde->kundennummer;
    $query .= ' AND kundenpreis.artikel=artikel.id';
    $query .= ' AND (ISNULL(kundenpreis.von) OR kundenpreis.von<=CURDATE())';
    $query .= ' AND (ISNULL(kundenpreis.bis) OR kundenpreis.bis>=CURDATE())';
    $query .= ' WHERE artikel.artikelnummer=\'' . mysql_real_escape_string($artikel) . '\'';
    $query .= ' AND artikel.domainreg=0';
    if (!($article = mysql_query_object($query)))
    {
      set_error ('leistung.artikel', 'INVALID');
    }
    else
    {
      $artikel = (int)$article->id;
    }
  }
  if ($endedatum !== '' && ($endedatum = parse_date($endedatum)) < 0)
  {
    set_error ('leistung.endedatum', 'INVALID');
  }
  if ($kommentar === '' && $article)
  {
    $kommentar = ($article->textanzeige && $article->langtext !== '' ? $article->langtext : $article->kurztext);
    $_POST['kommentar'] = $kommentar;
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
  if (!error())
  {
    $query  = ($leistung ? 'UPDATE' : 'INSERT INTO') . ' leistung SET';
    $query .= ' abgerechnet=' . ($abgerechnet ? (int)$abgerechnet : 'NULL');
    $query .= ',anzahl=\'' . mysql_real_escape_string(str_replace(',','.',$anzahl)) . '\'';
    $query .= ',artikel=' . (int)$artikel;
    $query .= ',domain=0';
    $query .= ',endedatum=' . ($endedatum !== '' ? (int)$endedatum : 'NULL');
    $query .= ',hosting=0';
    $query .= ',kommentar=\'' . mysql_real_escape_string($kommentar) . '\'';
    $query .= ',kunde=' . (int)$kunde->kundennummer;
    $query .= ',preis=\'' . mysql_real_escape_string(str_replace(',','.',$preis)) . '\'';
    $query .= ',referenzdatum=' . (int)$referenzdatum;
    $query .= ',setup=\'' . mysql_real_escape_string(str_replace(',','.',$setup)) . '\'';
    if ($leistung)
    {
      $query .= ' WHERE id=' . (int)$leistung->id;
    }
    safe_mysql_query ($query);
    $step = 2;
  }
}

require_once ("templates/html/leistung_{$step}.html");

?>