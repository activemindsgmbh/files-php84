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

$step = 1;

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  if ($kunde)
  {
    $_POST['anrede']                = db2form ($kunde->anrede);
    $_POST['anschrift']             = db2form ($kunde->anschrift);
    $_POST['ansprechpartner']       = db2form ($kunde->ansprechpartner);
    $_POST['bankleitzahl']          = db2form ($kunde->bankleitzahl);
    $_POST['domainzusammenfassung'] = db2form ($kunde->domainzusammenfassung);
    $_POST['email']                 = db2form ($kunde->email);
    $_POST['kommentar']             = db2form ($kunde->kommentar);
    $_POST['kontoinhaber']          = db2form ($kunde->kontoinhaber);
    $_POST['kontonummer']           = db2form ($kunde->kontonummer);
    $_POST['kundennummer']          = db2form ($kunde->kundennummer);
    $_POST['land']                  = db2form ($kunde->land_code);
    $_POST['mobil']                 = db2form ($kunde->mobil);
    $_POST['name']                  = db2form ($kunde->name);
    $_POST['ort']                   = db2form ($kunde->ort);
    $_POST['postleitzahl']          = db2form ($kunde->postleitzahl);
    $_POST['rechnungsemail']        = db2form ($kunde->rechnungsemail);
    $_POST['strasse']               = db2form ($kunde->strasse);
    $_POST['telefax']               = db2form ($kunde->telefax);
    $_POST['telefon']               = db2form ($kunde->telefon);
    $_POST['telefon2']              = db2form ($kunde->telefon2);
    $_POST['umsatzsteuerbefreit']   = db2form ($kunde->umsatzsteuerbefreit);
    $_POST['umsatzsteuerid']        = db2form ($kunde->umsatzsteuerid);
    $_POST['vorname']               = db2form ($kunde->vorname);
    $_POST['waehrung']              = db2form ($kunde->waehrung);
    $_POST['zahlungsziel']          = db2form ($kunde->zahlungsziel);
    $_POST['zahlweise']             = db2form ($kunde->zahlweise);
  }
  else
  {
    $res = safe_mysql_query ('SELECT MAX(kundennummer) FROM kunde');
    $row = mysql_fetch_row ($res);
    $_POST['kundennummer'] = ((int)$row[0] ? (int)$row[0] + 1 : 1001);
    $_POST['land']         = 'DE';
    $_POST['zahlungsziel'] = $GLOBALS['conf_default_zahlungsziel'];
    $_POST['waehrung']     = $GLOBALS['conf_default_waehrung'];
  }
}
else
{
  $anrede              = trim (form_input(@$_POST['anrede']));
//  $anschrift           = trim (form_input(@$_POST['anschrift']));
//  $ansprechpartner     = trim (form_input(@$_POST['ansprechpartner']));
  $bankleitzahl        = trim (form_input(@$_POST['bankleitzahl']));
  $email               = trim (form_input(@$_POST['email']));
//  $kommentar           = trim (form_input(@$_POST['kommentar']));
  $kontoinhaber        = trim (form_input(@$_POST['kontoinhaber']));
  $kontonummer         = trim (form_input(@$_POST['kontonummer']));
  $kundennummer        = trim (form_input(@$_POST['kundennummer']));
  $land                = trim (form_input(@$_POST['land']));
//  $mobil               = trim (form_input(@$_POST['mobil']));
  $name                = trim (form_input(@$_POST['name']));
  $ort                 = trim (form_input(@$_POST['ort']));
  $postleitzahl        = trim (form_input(@$_POST['postleitzahl']));
  $rechnungsemail      = trim (form_input(@$_POST['rechnungsemail']));
  $strasse             = trim (form_input(@$_POST['strasse']));
//  $telefax             = trim (form_input(@$_POST['telefax']));
//  $telefon             = trim (form_input(@$_POST['telefon']));
//  $telefon2            = trim (form_input(@$_POST['telefon']));
//  $umsatzsteuerbefreit = trim (form_input(@$_POST['umsatzsteuerbefreit']));
//  $umsatzsteuerid      = trim (form_input(@$_POST['umsatzsteuerid']));
//  $vorname             = trim (form_input(@$_POST['vorname']));
  $waehrung            = trim (form_input(@$_POST['waehrung']));
  $zahlungsziel        = trim (form_input(@$_POST['zahlungsziel']));
  $zahlweise           = trim (form_input(@$_POST['zahlweise']));

  set_error_if ($anrede === '', 'kunde.anrede', 'EMPTY');
  set_error_if ($email !== '' && !check_email($email), 'kunde.email', 'INVALID');

  if (!$kunde)
  {
    if ($kundennummer === '')
    {
      set_error ('kunde.kundennummer', 'EMPTY');
    }
    else if (!isuint($kundennummer) || $kundennummer <= 1000)
    {
      set_error ('kunde.kundennummer', 'INVALID');
    }
    else if (mysql_count('kunde', 'kundennummer=' . (int)$kundennummer))
    {
      set_error ('kunde.kundennummer', 'NOT UNIQUE');
    }
  }

  if ($land === '')
  {
    set_error ('kunde.land', 'EMPTY');
  }
  else if (!mysql_count('land', 'code=\'' . mysql_real_escape_string($land) . '\''))
  {
    set_error ('kunde.land', 'INVALID');
  }

  set_error_if ($name === '', 'kunde.name', 'EMPTY');
  set_error_if ($ort === '', 'kunde.ort', 'EMPTY');
  set_error_if ($postleitzahl === '', 'kunde.postleitzahl', 'EMPTY');
  set_error_if ($rechnungsemail !== '' && !check_email($rechnungsemail), 'kunde.rechnungsemail', 'INVALID');
  set_error_if ($strasse === '', 'kunde.strasse', 'EMPTY');

  if ($waehrung === '')
  {
    set_error ('kunde.waehrung', 'EMPTY');
  }
      
  else if (!preg_match('/^[a-z]{3}$/i', $waehrung)) 
  {
    set_error ('kunde.waehrung', 'INVALID');
  }

  if ($zahlungsziel === '')
  {
    set_error ('kunde.zahlungsziel', 'EMPTY');
  }
  else if (!isuint($zahlungsziel))
  {
    set_error ('kunde.zahlungsziel', 'INVALID');
  }

  if ($zahlweise === '')
  {
    set_error ('kunde.zahlweise', 'EMPTY');
  }
  else if ($zahlweise != 'LASTSCHRIFT' && $zahlweise != 'UEBERWEISUNG')
  {
    set_error ('kunde.zahlweise', 'INVALID');
  }

  if ($zahlweise == 'LASTSCHRIFT')
  {
    if ($bankleitzahl === '')
    {
      set_error ('kunde.bankleitzahl', 'EMPTY');
    }
    if ($kontoinhaber === '')
    {
      set_error ('kunde.kontoinhaber', 'EMPTY');
    }
    if ($kontonummer === '')
    {
      set_error ('kunde.kontonummer', 'EMPTY');
    }
  }

  if (!error())
  {
    $query  = ($kunde ? 'UPDATE kunde SET' : 'INSERT INTO kunde SET');
    $query .= ' anrede=\'' . mysql_real_escape_string($anrede) . '\'';
    $query .= ',anschrift=\'' . mysql_real_escape_string(trim(form_input(@$_POST['anschrift']))) . '\'';
    $query .= ',ansprechpartner=\'' . mysql_real_escape_string(trim(form_input(@$_POST['ansprechpartner']))) . '\'';
    $query .= ',bankleitzahl=\'' . mysql_real_escape_string($bankleitzahl) . '\'';
    $query .= ',domainzusammenfassung=' . (trim(form_input(@$_POST['domainzusammenfassung'])) == '1' ? '1' : '0');
    $query .= ',email=\'' . mysql_real_escape_string($email) . '\'';
    $query .= ',kommentar=\'' . mysql_real_escape_string(trim(form_input(@$_POST['kommentar']))) . '\'';
    $query .= ',kontoinhaber=\'' . mysql_real_escape_string($kontoinhaber) . '\'';
    $query .= ',kontonummer=\'' . mysql_real_escape_string($kontonummer) . '\'';
    $query .= ',land=\'' . mysql_real_escape_string($land) . '\'';
    $query .= ',mobil=\'' . mysql_real_escape_string(trim(form_input(@$_POST['mobil']))) . '\'';
    $query .= ',name=\'' . mysql_real_escape_string($name) . '\'';
    $query .= ',ort=\'' . mysql_real_escape_string($ort) . '\'';
    $query .= ',postleitzahl=\'' . mysql_real_escape_string($postleitzahl) . '\'';
    $query .= ',rechnungsemail=\'' . mysql_real_escape_string($rechnungsemail) . '\'';
    $query .= ',strasse=\'' . mysql_real_escape_string($strasse) . '\'';
    $query .= ',telefax=\'' . mysql_real_escape_string(trim(form_input(@$_POST['telefax']))) . '\'';
    $query .= ',telefon=\'' . mysql_real_escape_string(trim(form_input(@$_POST['telefon']))) . '\'';
    $query .= ',telefon2=\'' . mysql_real_escape_string(trim(form_input(@$_POST['telefon2']))) . '\'';
    $query .= ',umsatzsteuerbefreit=' . (trim(form_input(@$_POST['umsatzsteuerbefreit'])) == '1' ? '1' : '0');
    $query .= ',umsatzsteuerid=\'' . mysql_real_escape_string(trim(form_input(@$_POST['umsatzsteuerid']))) . '\'';
    $query .= ',vorname=\'' . mysql_real_escape_string(trim(form_input(@$_POST['vorname']))) . '\'';
    $query .= ',waehrung=\'' . mysql_real_escape_string($waehrung) . '\'';
    $query .= ',zahlungsziel=' . (int)$zahlungsziel;
    $query .= ',zahlweise=\'' . mysql_real_escape_string($zahlweise) . '\'';
    $query .= ($kunde ? ' WHERE kundennummer=' . (int)$kunde->kundennummer : ',kundennummer=' . (int)$kundennummer);
    safe_mysql_query ($query);
    if (!$kunde)
    {
      $kunde = read_kunde (mysql_insert_id());
    }
    $step = 2;
  }
}

require_once ("templates/html/kundendaten_{$step}.html");

?>