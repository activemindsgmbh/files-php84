<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$kunde = NULL;
$id = trim (form_input(@$_GET['kunde']));
if ($id !== '' && (!isuint($id) || !($kunde = read_kunde($id))))
{
  fatal_error ('INVALID_ID');
}

$query  = 'SELECT';
$query .= ' DATE_FORMAT(rechnung.datum,\'%d.%m.%Y\') As datum';
$query .= ',DATE_FORMAT(rechnung.faellig,\'%d.%m.%Y\') As faellig';
$query .= ',rechnung.forderung';
$query .= ',rechnung.id';
$query .= ',rechnung.kunde';
$query .= ',rechnung.status';
$query .= ',rechnung.zahlung';
$query .= ',rechnung.waehrung';
$query .= ' FROM rechnung';
$query .= ' LEFT JOIN kunde ON kunde.kundennummer=rechnung.kunde';
$query .= ' WHERE rechnung.status IN (\'OFFEN\',\'RUECKLASTSCHRIFT\')';
if ($kunde)
{
  $query .= ' AND rechnung.kunde=' . (int)$kunde->kundennummer;
}
$query .= ' ORDER BY kunde.name,kunde.vorname,rechnung.kunde,rechnung.id DESC';
$rrechnungen = safe_mysql_query ($query);

if ($kunde && mysql_num_rows($rrechnungen))
{
  $query  = 'SELECT COUNT(*) FROM rechnung';
  $query .= ' WHERE kunde=' . (int)$kunde->kundennummer;
  $query .= ' AND status IN (\'OFFEN\',\'RUECKLASTSCHRIFT\')';
  $query .= ' AND faellig<=CURDATE()';
  $res = safe_mysql_query ($query);
  $row = mysql_fetch_row ($res);
  $faellig = (int)$row[0];
  if ($faellig)
  {
    if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
    {
      $now = getdate();
      $_POST['datum'] = date ('d.m.Y', mktime(0, 0, 0, $now['mon'], $now['mday'] + 7, $now['year']));
    }
    else
    {
      $datum = trim (form_input(@$_POST['datum']));
      if ($datum === '')
      {
        set_error ('datum', 'EMPTY');
      }
      else if (($datum = parse_date($datum)) < 0)
      {
        set_error ('datum', 'INVALID');
      }
      if (!error())
      {
        require_once ('pdf.inc.php');
        $pdf = new PDF ($kunde);
        $pdf->column_align  = array ( 'R', 'C', 'C', 'R' );
        $pdf->column_header = array ( 'Rechnungsnummer', 'Rechnungsdatum', 'Fälligkeit', 'Betrag' );
        $pdf->column_width  = array ( 40, 40, 40, 60 );
        $pdf->falthilfe ();
        $pdf->anschrift ();

        $pdf->SetFont ('Arial', 'B', 14);
        $pdf->Cell (0, $pdf->FontSize, 'Offene Posten', 0, 1);
        $pdf->Ln ();
        $pdf->SetFont ('Arial', 'B', 10);
        $w = max ($pdf->GetStringWidth('Kundennummer:  '), $pdf->GetStringWidth('Datum:  '));
        $pdf->Cell ($w, $pdf->FontSize, 'Kundennummer:', 0, 0);
        $pdf->Cell (0, $pdf->FontSize, $kunde->kundennummer, 0, 1);
        $pdf->Cell ($w, $pdf->FontSize, 'Datum:', 0, 0);
        $pdf->Cell (0, $pdf->FontSize, date('d.m.Y'), 0, 1);

        $pdf->SetY ($pdf->GetY() + 10);
        $pdf->SetFont ('Arial', '', 10);
        $pdf->MultiCell (0, $pdf->FontSize, "Sehr geehrte Damen und Herren,\n\nbei Prüfung unserer Unterlagen haben wir folgende offene Posten festgestellt. Sofern sich diese Liste nicht mit Ihren Aufzeichnungen decken sollte, bitten wir um Mitteilung. Andernfalls gleichen Sie bitte die offenen Posten durch Banküberweisung bis zum " . (sprintf('%02d.%02d.%02d', $datum % 100, floor($datum / 100) % 100, floor($datum / 10000))) . ' aus.');

        $pdf->SetY ($pdf->GetY() + 10);
        $pdf->tableHeader ();
        $summe = 0.0;
        while ($rechnung = mysql_fetch_object($rrechnungen))
        {
          preg_match ('#^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$#', $rechnung->faellig, $regs);
          $faellig = $regs[3] * 10000 + $regs[2] * 100 + $regs[1];
          if ($faellig <= date('Ymd'))
          {
            $data = array ();
            $data[] = $rechnung->id;
            $data[] = $rechnung->datum;
            $data[] = $rechnung->faellig;
            $data[] = number_format ($rechnung->forderung - $rechnung->zahlung, 2, ',', '.') . ' ' . $kunde->waehrung;
            $pdf->Row ($data, 0);
            $summe += $rechnung->forderung - $rechnung->zahlung;
          }
        }

        $pdf->Line ($pdf->lMargin, $pdf->GetY(), $pdf->w - $pdf->rMargin, $pdf->GetY());
        $pdf->SetY ($pdf->GetY() + 4);

        $pdf->SetFont ('Arial', 'B', 9);
        $pdf->Cell (145, $pdf->FontSize + 1, 'Gesamt:', 0, 0, 'R');
        $pdf->Cell (35, $pdf->FontSize + 1, number_format ($summe, 2, ',', '.') . ' ' . $kunde->waehrung, 0, 1, 'R');

        $pdf->SetY ($pdf->GetY() + 10);
        $pdf->SetFont ('Arial', '', 10);
        $pdf->MultiCell (0, $pdf->FontSize, "Bei Rückfragen stehen wir Ihnen gerne zur Verfügung.\n\nMit freundlichen Grüßen\n\nactive minds GmbH\nBuchhaltung");

        $query  = 'INSERT INTO mahnung SET';
        $query .= ' betrag=\'' . mysql_real_escape_string($summe) . '\'';
        $query .= ',datum=CURDATE()';
        $query .= ',kunde=' . (int)$kunde->kundennummer;
        $query .= ',waehrung=\'' . mysql_real_escape_string($kunde->waehrung) . '\'';
        safe_mysql_query ($query);
        $id = mysql_insert_id ();

        $pdf->Output ($GLOBALS['conf_dir_mahnung'] . '/' . (int)$id . '.pdf');
        $pdf->Output ((int)$id . '.pdf', 'D');
        exit;
      }
    }
  }
}

require_once ("templates/html/offene_posten.html");

?>
