<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$id = trim (form_input(@$_GET['kunde']));
$kunde = NULL;
if ($id === '')
{
	$id = 1083;
}
if (!isuint($id) || !($kunde = read_kunde($id)))
{
  fatal_error ('INVALID_ID');
}
$rdatum = (int)date ('Ymd');

require_once ('pdf.inc.php');
$pdf = new PDF ($kunde);
$pdf->column_align  = array ( 'L', 'L', 'R', 'R', 'R', 'R' );
$pdf->column_header = array ( 'Artikel-Nr.', 'Artikelbezeichnung/Leistung', 'Menge', 'Grundpreis', $kunde->waehrung, 'USt.' );
$pdf->column_width  = array ( 30, 80, 15, 20, 20, 15 );
$pdf->falthilfe ();
$pdf->anschrift ();
$pdf->SetFont ('Arial', 'B', 14);
$pdf->Cell (0, $pdf->FontSize, 'Rechnung', 0, 1);
$pdf->Ln ();
$pdf->SetFont ('Arial', 'B', 10);
$w = max ($pdf->GetStringWidth('Kundennummer:  '), $pdf->GetStringWidth('Rechnungsnummer:  '), $pdf->GetStringWidth('Rechnungsdatum:  '));
$pdf->Cell ($w, $pdf->FontSize, 'Kundennummer:', 0, 0);
$pdf->Cell (0, $pdf->FontSize, $kunde->kundennummer, 0, 1);
$pdf->Cell ($w, $pdf->FontSize, 'Rechnungsnummer:', 0, 0);
$pdf->Cell (0, $pdf->FontSize, '0', 0, 1);
$pdf->Cell ($w, $pdf->FontSize, 'Rechnungsdatum:', 0, 0);
$pdf->Cell (0, $pdf->FontSize, sprintf('%02d.%02d.%04d', floor($rdatum % 100), floor($rdatum / 100) % 100, floor($rdatum / 10000)), 0, 1);
$pdf->SetFont ('Arial', '', 7);
$pdf->SetY ($pdf->GetY() + 2);
$pdf->MultiCell (0, $pdf->FontSize, 'Bitte bei Zahlung und Schriftverkehr angeben. Das Rechnungsdatum entspricht dem Tag der Lieferung/Leistung, sofern nicht anders angegeben.');
$pdf->SetY ($pdf->GetY() + 10);
$pdf->tableHeader ();
$pdf->Output ('dummy.pdf', 'D');

?>