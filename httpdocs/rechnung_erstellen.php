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
$datum = trim (form_input(@$_GET['datum']));
if ($datum === '' || ($datum = parse_date($datum)) < 0)
{
  fatal_error ('INVALID_PARAMETER');
}
$rdatum = trim (form_input(@$_POST['rdatum']));
if ($rdatum === '')
{
	$rdatum = (int)date ('Ymd');
}
else if (($rdatum = parse_date($rdatum)) < 0)
{
  fatal_error ('INVALID_PARAMETER');
}

$ids = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false) && isset($_POST['id']) && is_array($_POST['id']))
{
  foreach ($_POST['id'] as $id)
  {
    if (isuint($id))
    {
      $ids .= ($ids ? ',' : '') . (int)$id;
    }
  }
}
if ($ids === '')
{
//  fatal_error ('MISSING_PARAMETER');
  redirect ('/rechnungsvorschau.php?kunde=' . urlencode($kunde->kundennummer) . '&datum=' . urlencode($_GET['datum']));
}

$query  = 'SELECT';
$query .= ' leistung.abgerechnet+0 AS abgerechnet';
$query .= ',leistung.anzahl';
$query .= ',leistung.endedatum+0 AS endedatum';
$query .= ',leistung.id';
$query .= ',IF(leistung.domain,CONCAT(artikel.kurztext,\'\\n\',domain.domain),leistung.kommentar) AS kommentar';
$query .= ',leistung.preis';
$query .= ',leistung.referenzdatum+0 AS referenzdatum';
$query .= ',leistung.setup';
$query .= ',artikel.artikelnummer';
if ($kunde->domainzusammenfassung)
{
  $query .= ',IF(artikel.textanzeige AND artikel.langtext,artikel.langtext,artikel.kurztext) AS artikeltext';
}
$query .= ',artikel.fibukonto';
$query .= ',artikel.id AS artikelid';
$query .= ',artikel.intervall';
$query .= ',domain.domain';
$query .= ',' . ($kunde->umsatzsteuerbefreit ? '0' : 'IF(ISNULL(artikel.umsatzsteuer),' . mysql_real_escape_string($GLOBALS['conf_umsatzsteuer']) . ',artikel.umsatzsteuer)') . ' AS umsatzsteuer';
$query .= ' FROM leistung';
$query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
$query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
$query .= ' WHERE leistung.id IN(' . mysql_real_escape_string($ids) . ')';
$query .= ' AND leistung.kunde=' . (int)$kunde->kundennummer;
$query .= ' AND leistung.referenzdatum<=' . (int)$datum;
$query .= ' AND (ISNULL(leistung.abgerechnet) OR leistung.abgerechnet<' . (int)$datum . ')';
$query .= ' ORDER BY leistung.id';
$rleistungen = safe_mysql_query ($query);

if (mysql_num_rows($rleistungen) < 1)
{
  fatal_error ('INVALID_PARAMETER');
}

$delids = '';
$netto = 0.0;
$index = 0;
$data = array ();
$domains = array ();
$fibu = array ();
$queries = array ();
$subtotal = array ();

while ($leistung = mysql_fetch_object($rleistungen))
{
  $refdate = $leistung->referenzdatum;
  if ($leistung->intervall)
  {
    $endedatum = ($leistung->endedatum ? min($leistung->endedatum, $datum) : $datum);
    $refdate = advance_date ($leistung->referenzdatum, $leistung->intervall, (int)$leistung->abgerechnet);
  }
  else
  {
    $endedatum = $refdate;
  }
  if (!array_key_exists($leistung->fibukonto, $fibu))
  {
    $fibu[$leistung->fibukonto] = array ();
  }
  if (!array_key_exists($leistung->umsatzsteuer, $fibu[$leistung->fibukonto]))
  {
    $fibu[$leistung->fibukonto][$leistung->umsatzsteuer] = 0.0;
  }
  if (!array_key_exists($leistung->umsatzsteuer, $subtotal))
  {
    $subtotal[$leistung->umsatzsteuer] = 0.0;
  }
  $abgerechnet = (int)$leistung->abgerechnet;



  if ($refdate <= $endedatum && $leistung->setup && $leistung->setup !== '0.00' && !$leistung->abgerechnet)
  {
    $sum = $leistung->anzahl * $leistung->setup;
    if ($leistung->domain && $kunde->domainzusammenfassung)
    {
      if (!array_key_exists($leistung->artikelid, $domains))
      {
        $domains[$leistung->artikelid] = array
        (
          'artikelnummer' => $leistung->artikelnummer,
          'artikeltext'   => $leistung->artikeltext,
          'monat'         => array (),
          'umsatzsteuer'  => $leistung->umsatzsteuer,
        );
      }
      $mon = (int)floor ($refdate / 100);
      if (!array_key_exists($mon, $domains[$leistung->artikelid]['monat']))
      {
        $domains[$leistung->artikelid]['monat'][$mon] = array
        (
          'preis' => array (),
          'setup' => array (),
        );
      }
      if (!array_key_exists($leistung->setup, $domains[$leistung->artikelid]['monat'][$mon]['setup']))
      {
        $domains[$leistung->artikelid]['monat'][$mon]['setup'][$leistung->setup] = 0.0;
      }
      $domains[$leistung->artikelid]['monat'][$mon]['setup'][$leistung->setup] += $leistung->anzahl;
    }
    else
    {
      $data[$index] = array ();
      $data[$index][] = utf8_decode($leistung->artikelnummer);
      $str = utf8_decode ($leistung->kommentar);
      $str .= "\nSetup am " . sprintf('%02d.%02d.%04d', $refdate % 100, floor($refdate / 100) % 100, floor($refdate / 10000));
      $data[$index][] = $str;
      $data[$index][] = number_format ($leistung->anzahl, 2, ',', '.');
      $data[$index][] = number_format ($leistung->setup, 2, ',', '.');
      $data[$index][] = number_format ($sum, 2, ',', '.');
      $data[$index][] = str_replace ('.', ',', $leistung->umsatzsteuer) . ' %';
      ++$index;
    }
    $netto += $sum;
    $fibu[$leistung->fibukonto][$leistung->umsatzsteuer] += $sum;
    $subtotal[$leistung->umsatzsteuer] += $sum;
  }



  while ($refdate <= $endedatum)
  {
    $sum = $leistung->anzahl * $leistung->preis;
    if ($leistung->domain && $kunde->domainzusammenfassung)
    {
      if (!array_key_exists($leistung->artikelid, $domains))
      {
        $domains[$leistung->artikelid] = array
        (
          'artikelnummer' => $leistung->artikelnummer,
          'artikeltext'   => $leistung->artikeltext,
          'monat'         => array (),
          'umsatzsteuer'  => $leistung->umsatzsteuer,
        );
      }
      $mon = (int)floor ($refdate / 100);
      if (!array_key_exists($mon, $domains[$leistung->artikelid]['monat']))
      {
        $domains[$leistung->artikelid]['monat'][$mon] = array
        (
          'preis' => array (),
          'setup' => array (),
        );
      }
      if (!array_key_exists($leistung->preis, $domains[$leistung->artikelid]['monat'][$mon]['preis']))
      {
        $domains[$leistung->artikelid]['monat'][$mon]['preis'][$leistung->preis] = 0.0;
      }
      $domains[$leistung->artikelid]['monat'][$mon]['preis'][$leistung->preis] += $leistung->anzahl;
    }
    else
    {
      $data[$index] = array ();
      $data[$index][] = utf8_decode($leistung->artikelnummer);
      $str = utf8_decode ($leistung->kommentar);
      if ($leistung->domain)
      {
        $str .= "\nRegistrierung/Verlängerung am " . sprintf('%02d.%02d.%04d', $refdate % 100, floor($refdate / 100) % 100, floor($refdate / 10000));
      }
      else if ($leistung->intervall)
      {
        $str .= "\n" . sprintf('%02d/%04d', floor($refdate / 100) % 100, floor($refdate / 10000));
      }
      $data[$index][] = $str;
      $data[$index][] = number_format ($leistung->anzahl, 2, ',', '.');
      $data[$index][] = number_format ($leistung->preis, 2, ',', '.');
      $data[$index][] = number_format ($sum, 2, ',', '.');
      $data[$index][] = str_replace ('.', ',', $leistung->umsatzsteuer) . ' %';
      ++$index;
    }
    $netto += $sum;
    $fibu[$leistung->fibukonto][$leistung->umsatzsteuer] += $sum;
    $subtotal[$leistung->umsatzsteuer] += $sum;
    $abgerechnet = $refdate;
    $refdate = $leistung->intervall ? advance_date ($leistung->referenzdatum, $leistung->intervall, $refdate) : $endedatum + 1;
  }
  if ($leistung->intervall)
  {
    $queries[] = 'UPDATE leistung SET abgerechnet=' . (int)$abgerechnet . ' WHERE id=' . (int)$leistung->id;
  }
  else
  {
    $delids .= ($delids ? ',' : '') . (int)$leistung->id;
  }
}

if ($kunde->domainzusammenfassung && count($domains) > 0)
{
  foreach ($domains as $domain)
  {
    ksort ($domain['monat']);
    foreach ($domain['monat'] as $monat => $arr)
    {
      foreach ($arr['setup'] as $setup => $anzahl)
      {
        $data[$index] = array ();
        $data[$index][] = utf8_decode($domain['artikelnummer']);
        $data[$index][] = utf8_decode($domain['artikeltext']) . "\nSetup: " . sprintf('%02d/%04d', floor($monat % 100), floor($monat / 100));
        $data[$index][] = number_format ($anzahl, 2, ',', '.');
        $data[$index][] = number_format ($setup, 2, ',', '.');
        $data[$index][] = number_format ($anzahl * $setup, 2, ',', '.');
        $data[$index][] = str_replace ('.', ',', $domain['umsatzsteuer']) . ' %';
        ++$index;
      }
      foreach ($arr['preis'] as $preis => $anzahl)
      {
        $data[$index] = array ();
        $data[$index][] = utf8_decode($domain['artikelnummer']);
        $data[$index][] = utf8_decode($domain['artikeltext']) . "\nRegistrierung/Verlängerung: " . sprintf('%02d/%04d', floor($monat % 100), floor($monat / 100));
        $data[$index][] = number_format ($anzahl, 2, ',', '.');
        $data[$index][] = number_format ($preis, 2, ',', '.');
        $data[$index][] = number_format ($anzahl * $preis, 2, ',', '.');
        $data[$index][] = str_replace ('.', ',', $domain['umsatzsteuer']) . ' %';
        ++$index;
      }
    }
  }
}

if ($index < 1)
{
//  fatal_error ('MISSING_PARAMETER');
  redirect ('/rechnungsvorschau.php?kunde=' . urlencode($kunde->kundennummer) . '&datum=' . urlencode($_GET['datum']));
}

$brutto = 0.0;
foreach ($subtotal as $key => $val)
{
  $brutto += $val + round (($val * $key) / 100, 2);
}

$res = safe_mysql_query ('SELECT MAX(id) FROM rechnung');
$row = mysql_fetch_row ($res);
$nummer = 1 + (int)$row[0];
if (isuint($GLOBALS['conf_min_rechnung_id']) && $GLOBALS['conf_min_rechnung_id'] > $nummer)
{
  $nummer = $GLOBALS['conf_min_rechnung_id'];
}

require_once ('pdf.inc.php');

foreach (array('SELF', 'OTHER') as $mode)
{
  $pdf = new PDF ($kunde);

  $pdf->column_align  = array ( 'L', 'L', 'R', 'R', 'R', 'R' );
  $pdf->column_header = array ( 'Artikel-Nr.', 'Artikelbezeichnung/Leistung', 'Menge', 'Grundpreis', $kunde->waehrung, 'USt.' );
  $pdf->column_width  = array ( 30, 80, 15, 20, 20, 15 );

  $pdf->falthilfe ();
//  $pdf->SetLineWidth (0.04);
//  $pdf->Line (0, 100, 5, 100);
//  $pdf->SetLineWidth (0.2);

  $pdf->anschrift ();
//  $str = 'active minds GmbH · Zum Lokschuppen · 66424 Homburg';
//  $pdf->SetY (50);
//  $pdf->SetFont ('Arial', '', 7);
//  $pdf->Cell ($pdf->GetStringWidth($str), $pdf->FontSize, $str, 'B', 1);
//  $pdf->SetY ($pdf->GetY() + 2);
//  if ($kunde->anschrift !== '')
//  {
//    $str = $kunde->anschrift;
//  }
//  else
//  {
//    $str = $kunde->anrede . "\n";
//    $str .= ($kunde->vorname !== '' ? $kunde->vorname . ' ' : '') . $kunde->name . "\n";
//    $str .= $kunde->strasse . "\n\n";
//    $str .= $kunde->postleitzahl . ' ' . $kunde->ort . "\n";
//  }
//  $pdf->SetFont ('Arial', '', 10);
//  $pdf->MultiCell (90, $pdf->FontSize, $str);
//  $pdf->SetY ($pdf->GetY() + 20);

  $pdf->SetFont ('Arial', 'B', 14);
  $pdf->Cell (0, $pdf->FontSize, 'Rechnung', 0, 1);
  $pdf->Ln ();
  $pdf->SetFont ('Arial', 'B', 10);
  $w = max ($pdf->GetStringWidth('Kundennummer:  '), $pdf->GetStringWidth('Rechnungsnummer:  '), $pdf->GetStringWidth('Rechnungsdatum:  '));
  $pdf->Cell ($w, $pdf->FontSize, 'Kundennummer:', 0, 0);
  $pdf->Cell (0, $pdf->FontSize, $kunde->kundennummer, 0, 1);
  $pdf->Cell ($w, $pdf->FontSize, 'Rechnungsnummer:', 0, 0);
  $pdf->Cell (0, $pdf->FontSize, $nummer, 0, 1);
  $pdf->Cell ($w, $pdf->FontSize, 'Rechnungsdatum:', 0, 0);
//  $pdf->Cell (0, $pdf->FontSize, date('d.m.Y'), 0, 1);
  $pdf->Cell (0, $pdf->FontSize, sprintf('%02d.%02d.%04d', floor($rdatum % 100), floor($rdatum / 100) % 100, floor($rdatum / 10000)), 0, 1);
  $pdf->SetFont ('Arial', '', 7);
  $pdf->SetY ($pdf->GetY() + 2);
  $pdf->MultiCell (0, $pdf->FontSize, 'Bitte bei Zahlung und Schriftverkehr angeben. Das Rechnungsdatum entspricht dem Tag der Lieferung/Leistung, sofern nicht anders angegeben.');
  $pdf->SetY ($pdf->GetY() + 10);

  if ($mode == 'SELF')
  {
    $x = $pdf->GetX ();
    $y = $pdf->GetY ();
    $pdf->SetXY (120, 40);
    $pdf->SetDrawColor (96, 96, 96);
    $pdf->SetTextColor (96, 96, 96);
    $pdf->SetFont ('Arial', 'B', 9);
    $pdf->Cell (25, $pdf->FontSize + 1, 'Konto', 1);
    $pdf->Cell (15, $pdf->FontSize + 1, 'USt.', 1, 0, 'R');
    $pdf->Cell (30, $pdf->FontSize + 1, 'Betrag', 1, 0, 'R');
    $pdf->Ln ();
    $pdf->SetFont ('Arial', '', 9);
    foreach ($fibu as $konto => $arr)
    {
      foreach ($arr as $ust => $sum)
      {
        $pdf->SetX (120);
        $tax = round (($sum * $ust) / 100, 2);
        $pdf->Cell (25, $pdf->FontSize + 1, $konto, 1);
        $pdf->Cell (15, $pdf->FontSize + 1, str_replace('.', ',', $ust) . ' %', 1, 0, 'R');
        $pdf->Cell (30, $pdf->FontSize + 1, number_format($sum + $tax, 2, ',', '.') . ' ' . $kunde->waehrung, 1, 0, 'R');
        $pdf->Ln ();
      }
    }
    $pdf->SetXY ($x, $y);
    $pdf->SetDrawColor (0, 0, 0);
    $pdf->SetTextColor (0, 0, 0);
  }

  $pdf->tableHeader ();

  foreach ($data as $position)
  {
    $pdf->Row ($position, 0);
  }

  $pdf->Line ($pdf->lMargin, $pdf->GetY(), $pdf->w - $pdf->rMargin, $pdf->GetY());
  $pdf->SetY ($pdf->GetY() + 4);

  $pdf->SetFont ('Arial', '', 9);
  $h = ($pdf->FontSize + 1) * (2 + count($subtotal));
  if ($pdf->GetY() + $h > $pdf->PageBreakTrigger)
  {
    $pdf->print_header = false;
    $pdf->AddPage ();
  }
  if ($kunde->zahlweise == 'LASTSCHRIFT')
  {
    $pdf->Cell (110, $pdf->FontSize + 1, 'Rechnungsbetrag wird abgebucht');
  }
  else
  {
//    $now = getdate ();
//    $pdf->Cell (110, $pdf->FontSize + 1, 'Rechnung zahlbar bis ' . date('d.m.Y', mktime(0, 0, 0, $now['mon'], $now['mday'] + $kunde->zahlungsziel, $now['year'])));
    $pdf->Cell (110, $pdf->FontSize + 1, 'Rechnung zahlbar bis ' . date('d.m.Y', mktime(0, 0, 0, floor($rdatum / 100) % 100, floor($rdatum % 100) + $kunde->zahlungsziel, floor($rdatum / 10000))));
  }
  $pdf->Cell (35, $pdf->FontSize + 1, 'Nettobetrag:', 0, 0, 'R');
  $pdf->Cell (20, $pdf->FontSize + 1, number_format ($netto, 2, ',', '.'), 0, 0, 'R');
  $pdf->Cell (15, $pdf->FontSize + 1, $kunde->waehrung, 0, 1);
  $first = true;
  foreach ($subtotal as $key => $val)
  {
    if ($first)
    {
      $pdf->Cell (110, $pdf->FontSize + 1, $kunde->umsatzsteuerid ? 'Ihre Umsatzsteuer-ID: ' . $kunde->umsatzsteuerid : '');
    }
    $tax = round (($val * $key) / 100, 2);
    $pdf->Cell ($first ? 35 : 145, $pdf->FontSize + 1, str_replace('.', ',', $key) . ' % USt.:', 0, 0, 'R');
    $pdf->Cell (20, $pdf->FontSize + 1, number_format ($tax, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell (15, $pdf->FontSize + 1, $kunde->waehrung, 0, 1);
//    $brutto += $val + $tax;
    $first = false;
  }
  $pdf->SetFont ('Arial', 'B', 9);
  $pdf->Cell (145, $pdf->FontSize + 1, 'Gesamtbetrag:', 0, 0, 'R');
  $pdf->Cell (20, $pdf->FontSize + 1, number_format ($brutto, 2, ',', '.'), 0, 0, 'R');
  $pdf->Cell (15, $pdf->FontSize + 1, $kunde->waehrung, 0, 1);

  $pdf->Output (($mode == 'SELF' ? $GLOBALS['conf_dir_rechnung_intern'] : $GLOBALS['conf_dir_rechnung_kunde']) . '/' . (int)$nummer . '.pdf');
}

$query  = 'INSERT INTO rechnung SET';
//$query .= ' datum=CURDATE()';
//$query .= ',faellig=DATE_ADD(CURDATE(),INTERVAL ' . (int)$kunde->zahlungsziel . ' DAY)';
$query .= ' datum=' . (int)$rdatum;
$query .= ',faellig=DATE_ADD(' . (int)$rdatum . ',INTERVAL ' . (int)$kunde->zahlungsziel . ' DAY)';
$query .= ',forderung=\'' . mysql_real_escape_string($brutto) . '\'';
$query .= ',id=' . (int)$nummer;
$query .= ',kunde=' . (int)$kunde->kundennummer;
$query .= ',status=\'' . ($brutto == 0 ? 'ERLEDIGT' : 'OFFEN') . '\'';
$query .= ',waehrung=\'' . mysql_real_escape_string($kunde->waehrung) . '\'';
$query .= ',zahlung=0';
safe_mysql_query ($query);

if ($kunde->zahlweise == 'LASTSCHRIFT' && $brutto > 0)
{
  $query  = 'INSERT INTO lastschrift SET';
  $query .= ' bankleitzahl=\'' . mysql_real_escape_string($kunde->bankleitzahl) . '\'';
  $query .= ',betrag=\'' . mysql_real_escape_string($brutto) . '\'';
  $query .= ',erstellt=CURDATE()';
  $query .= ',gebucht=NULL';
  $query .= ',kommentar=\'KN ' . mysql_real_escape_string($kunde->kundennummer) . ' RN ' . mysql_real_escape_string($nummer) . '\'';
  $query .= ',kontoinhaber=\'' . mysql_real_escape_string($kunde->kontoinhaber) . '\'';
  $query .= ',kontonummer=\'' . mysql_real_escape_string($kunde->kontonummer) . '\'';
  $query .= ',kunde=' . (int)$kunde->kundennummer;
  $query .= ',rechnung=' . (int)$nummer;
  safe_mysql_query ($query);
}

if ($delids !== '')
{
  safe_mysql_query ('DELETE FROM leistung WHERE id IN(' . mysql_real_escape_string($delids) . ')');
}
foreach ($queries as $query)
{
  safe_mysql_query ($query);
}

$pdf->Output ((int)$nummer . '.pdf', 'D');

//require_once ("templates/html/rechnungsvorschau.html");

?>