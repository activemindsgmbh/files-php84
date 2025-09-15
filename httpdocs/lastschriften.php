<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$rlastschriften = NULL;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  $cmd = form_input(@$_POST['download']) ? 'download' : (form_input(@$_POST['process']) ? 'process' : '');
  if ($cmd !== '')
  {
    $ids = '';
    if (isset($_POST['id']) && is_array($_POST['id']))
    {
      foreach ($_POST['id'] as $id)
      {
        if (isuint($id))
        {
          $ids .= ($ids ? ',' : '') . (int)$id;
        }
      }
    }
    if ($ids !== '')
    {
      if ($cmd == 'download')
      {
        $query  = 'SELECT';
        $query .= ' bankleitzahl';
        $query .= ',FLOOR(betrag*100) AS betrag';
        $query .= ',kommentar';
        $query .= ',kontoinhaber';
        $query .= ',kontonummer';
        $query .= ' FROM lastschrift';
        $query .= ' WHERE id IN(' . mysql_real_escape_string($ids) . ')';
        $query .= ' AND ISNULL(gebucht)';
				$query .= ' AND lastschrift.betrag>0';
        $query .= ' ORDER BY id';
        $res = safe_mysql_query ($query);
        $dtaus = '0128ALK';
        $dtaus .= '59020090'; // Bankleitzahl des Kreditinstitutes (Diskettenempfaenger)
        $dtaus .= '00000000';
        $dtaus .= strfix ('active minds GmbH', 27); // Kundenname Diskettenabsender
        $dtaus .= date('dmy');
        $dtaus .= str_repeat (' ', 4);
        $dtaus .= strfix ('353752383', 10, 'R', '0'); // Kontonummer
        $dtaus .= str_repeat ('0', 10);
        $dtaus .= str_repeat (' ', 15);
        $dtaus .= str_repeat (' ', 8);
        $dtaus .= str_repeat (' ', 24);
        $dtaus .= '1';
        $bankleitzahl = '0';
        $kontonummer = '0';
        $summe = '0';
        $zeilen = 0;
        while ($obj = mysql_fetch_object($res))
        {
          $dtaus .= '0187C';
          $dtaus .= str_repeat ('0', 8);
          $dtaus .= strfix ($obj->bankleitzahl, 8, 'R', '0');
          $dtaus .= strfix ($obj->kontonummer, 10, 'R', '0');
          $dtaus .= str_repeat ('0', 13);
          $dtaus .= '05000';
          $dtaus .= ' ';
          $dtaus .= str_repeat ('0', 11);
          $dtaus .= '59020090'; // Bankleitzahl
          $dtaus .= strfix ('353752383', 10, 'R', '0'); // Kontonummer
          $dtaus .= strfix ($obj->betrag, 11, 'R', '0');
          $dtaus .= str_repeat (' ', 3);
          $dtaus .= strfix (str_replace('ß', 'ss', $obj->kontoinhaber), 27);
    	    $dtaus .= str_repeat (' ', 8);
          $dtaus .= strfix ('active minds GmbH', 27); // Kundenname
          $dtaus .= strfix ($obj->kommentar, 27);
          $dtaus .= '1';
          $dtaus .= str_repeat (' ', 2);
          $dtaus .= '00';
          $dtaus .= str_repeat (' ', 69);
          $bankleitzahl = bcadd ($bankleitzahl, $obj->bankleitzahl);
          $kontonummer = bcadd ($kontonummer, $obj->kontonummer);
          $summe = bcadd ($summe, $obj->betrag);
          ++$zeilen;
        }
        $dtaus .= '0128E';
        $dtaus .= str_repeat (' ', 5);
        $dtaus .= strfix ($zeilen, 7, 'R', '0');
        $dtaus .= str_repeat ('0', 13);
        $dtaus .= strfix ($kontonummer, 17, 'R', '0');
        $dtaus .= strfix ($bankleitzahl, 17, 'R', '0');
        $dtaus .= strfix ($summe, 13, 'R', '0');
        $dtaus .= str_repeat (' ', 51);
        header ('Content-Type: application/octet-stream');
        header ('Content-Disposition: attachment; filename="dtaus_' . date('Ymd') . '"');
        echo $dtaus;
        exit;
      }
      else if ($cmd == 'process')
      {
        $query  = 'UPDATE lastschrift LEFT JOIN rechnung ON rechnung.id=lastschrift.rechnung SET';
        $query .= ' lastschrift.gebucht=CURDATE()';
        $query .= ',rechnung.status=\'ERLEDIGT\'';
        $query .= ',rechnung.zahlung=rechnung.forderung';
        $query .= ' WHERE lastschrift.id IN(' . mysql_real_escape_string($ids) . ')';
        $query .= ' AND ISNULL(lastschrift.gebucht)';
        safe_mysql_query ($query);
      }
    }
  }
}

$query  = 'SELECT';
$query .= ' lastschrift.bankleitzahl';
$query .= ',lastschrift.betrag';
$query .= ',DATE_FORMAT(lastschrift.erstellt,\'%d.%m.%Y\') AS erstellt';
$query .= ',lastschrift.id';
$query .= ',lastschrift.kommentar';
$query .= ',lastschrift.kontoinhaber';
$query .= ',lastschrift.kontonummer';
$query .= ',lastschrift.kunde';
$query .= ',lastschrift.rechnung';
$query .= ' FROM lastschrift';
$query .= ' WHERE ISNULL(gebucht)';
$query .= ' ORDER BY id';
$rlastschriften = safe_mysql_query ($query);

require_once ("templates/html/lastschriften.html");

?>