<?php

require_once('../include/Database.php');

function safe_mysql_query($query)
{
    $db = Database::getInstance();
    try {
        return $db->query($query);
    } catch (Exception $e) {
        echo "error: mysql_query\n";
        echo $e->getMessage() . "\n";
        exit;
    }
}

function usdate($s)
{
    if (preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $s, $regs))
    {
        return ($regs[3] * 10000 + $regs[2] * 100 + $regs[1]);
    }
    return 0;
}

try {
    $db = Database::getInstance('localhost', 'amfakt', 'Gd7muApMeaWP', 'amfakt');
} catch (Exception $e) {
    echo "error: database connection\n";
    echo $e->getMessage() . "\n";
    exit;
}

safe_mysql_query ('SET NAMES utf8 COLLATE utf8_unicode_ci');

safe_mysql_query ('TRUNCATE TABLE artikel');
safe_mysql_query ('TRUNCATE TABLE artikelupdate');
safe_mysql_query ('TRUNCATE TABLE domain');
safe_mysql_query ('TRUNCATE TABLE kunde');
safe_mysql_query ('TRUNCATE TABLE kundenpreis');
safe_mysql_query ('TRUNCATE TABLE lastschrift');
safe_mysql_query ('TRUNCATE TABLE leistung');
safe_mysql_query ('TRUNCATE TABLE mahnung');
safe_mysql_query ('TRUNCATE TABLE rechnung');


echo "importing 'artikel.csv'\n";
$fp = fopen ('artikel.csv', 'r');
if (!$fp)
{
  echo "error: can't open artikel.csv\n";
  exit;
}
if (($line = fgetcsv($fp, 0, ';', '"')) !== false)
{
  $KEY = array ();
  foreach ($line as $key => $val)
  {
    $KEY[$val] = $key;
  }
  while (($line = fgetcsv($fp, 0, ';', '"')) !== false)
  {
    $query  = 'INSERT INTO artikel SET';
    $query .= ' artikelnummer=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['ARTNR']]))) . '\'';
    $query .= ',domainreg=' . (strncasecmp($line[$KEY['ARTNR']], 'reg-', 4) ? 0 : 1);
    $query .= ',domains=0';
    $query .= ',fibukonto=' . (int)$line[$KEY['FIBUKTO']];
    $query .= ',intervall=';
    switch ($line[$KEY['LIEFERANT']])
    {
      case 'dreij�hrlich':
        $query .= '36';
        break;
      case 'einmalig':
      case 'keine Berechnung':
        $query .= '0';
        break;
      case 'halbj�hrlich':
        $query .= '6';
        break;
      case 'j�hrlich':
        $query .= '12';
        break;
      case 'monatlich':
        $query .= '1';
        break;
      case 'zweij�hrlich':
        $query .= '24';
        break;
      default:
        echo "error: unknown LIEFERANT '{$line[$KEY['LIEFERANT']]}'\n";
        exit;
    }
    $query .= ',kurztext=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['ARTIKEL1']]))) . '\'';
    $query .= ',langtext=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['LANGTEXT']]))) . '\'';
    $query .= ',preis=\'' . mysql_real_escape_string(utf8_encode(str_replace(',', '.', trim($line[$KEY['VK']])))) . '\'';
    $query .= ',textanzeige=0';
    if ($line[$KEY['MWST']] != 19)
    {
      $query .= ',umsatzsteuer=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['MWST']]))) . '\'';
    }
    safe_mysql_query ($query);
  }
}
fclose ($fp);


safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0001\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0002\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0003\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0004\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0005\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0006\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0007\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0008\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0009\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0010\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0011\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0012\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'0013\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0020\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0021\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0022\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0023\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0024\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0025\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0026\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0027\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0028\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'0030\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'0031\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'0032\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0033\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'0034\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'0035\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'1000\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'1001\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'9000\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'9010\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'9011\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'9020\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'9021\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'9022\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4459,domainreg=0 WHERE artikelnummer=\'BOR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'CLICK-B\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'CLICK-E\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'CLICK-T\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'CNOBI-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'CNO-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'CN-REG\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'CONS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DE-KK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DE-REG-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DNS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DNS-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DNS-LI\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'DNS-Z\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4401,domainreg=0 WHERE artikelnummer=\'DOM-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'EU-ANZ\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'FAHR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'FEST\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HO-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-A\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-B\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-EM\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-F1\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-M-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-P16\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-P\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-PM\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-RPO\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-S\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-SCMS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HO-SU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'HOS-X\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'HW\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'IDN-DE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'KON\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'LAS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'MX-10\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'MX-2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'MYSQL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PC\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2001\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2003\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2004\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2006\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2007\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2010\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2016\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2016-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2021\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2021-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2022\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2025\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-2026\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'PG-VN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'POP3\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4454,domainreg=0 WHERE artikelnummer=\'PP-16\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4454,domainreg=0 WHERE artikelnummer=\'PP-E\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4454,domainreg=0 WHERE artikelnummer=\'PP-S\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'PRE-EU2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'PRE-EU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'PRE-EU-NL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'PRE-EU-PB\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'PROC-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'PROF\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'RECH-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-AE3\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-AG\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-AT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-AT-IDN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BIZ-1\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BIZ\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BIZ-IDN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BY3\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-BZ\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CC\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CD\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CHK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COAT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COEE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COIL2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COM\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COM-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COMMX\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COMPT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CONZ\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COUK2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COUK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-COZA\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-CZ\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-DE3\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-DE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-DE-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-DE-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-DK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-ES\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-EU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-FR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-GR2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-GR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-GS1\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-GS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-HK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-HU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-IDN-CN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-IN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-INFO-1\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-INFO\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-INFO-P\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-INFO-PRO\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-INF-P2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-IT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-LI\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-LT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-LU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-LV\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-MOBI\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-NET\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-NET-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-NL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-ORAT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-ORG\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-PH2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-PL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-RO\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-RU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-RU-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-SC\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-SE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-SK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-TR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-TV\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-TW\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-US\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'REG-WS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'RES1\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'RES-TV\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'SC-T\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'SC-W\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4457,domainreg=0 WHERE artikelnummer=\'SCWERB\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'SER\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'SERV-D\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'SERV-H\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4403,domainreg=0 WHERE artikelnummer=\'SERV-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SERV-V\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'SET-16\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-AT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-AT-IDN\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-BY\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-CH\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-DE\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-DK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-GR\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-INFO\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-LI\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-LT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-NL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'SET-RU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SET-SCMS\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'SET-SD\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SHOP-S\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SH-S2\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'SOFT\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SOF-VBB-U\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'SSL\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'STD-T\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4410,domainreg=0 WHERE artikelnummer=\'SV-16\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'TRACK\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'TRAF-M\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4402,domainreg=0 WHERE artikelnummer=\'TRAF-W\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'US-REG\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4400,domainreg=1 WHERE artikelnummer=\'VOR-EU\'');
safe_mysql_query ('UPDATE artikel SET fibukonto=4404,domainreg=0 WHERE artikelnummer=\'WEB\'');


echo "importing 'kunden.csv'\n";
$fp = fopen ('kunden.csv', 'r');
if (!$fp)
{
  echo "error: can't open kunden.csv\n";
  exit;
}
if (($line = fgetcsv($fp, 0, ';', '"')) !== false)
{
  $KEY = array ();
  foreach ($line as $key => $val)
  {
    $KEY[$val] = $key;
  }
  while (($line = fgetcsv($fp, 0, ';', '"')) !== false)
  {
    if ($line[$KEY['GESPERRT']] == 'J')
    {
      continue;
    }
    $query  = 'INSERT INTO kunde SET';
    $query .= ' anrede=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['ANREDE']]))) . '\'';
    $query .= ',anschrift=\'\'';
    $query .= ',ansprechpartner=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['PART']]))) . '\'';
    $query .= ',bankleitzahl=\'\'';
    $query .= ',domainzusammenfassung=0';
    $query .= ',email=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['EMAIL2']]))) . '\'';
    $query .= ',kommentar=\'\'';
    $query .= ',kontoinhaber=\'\'';
    $query .= ',kontonummer=\'\'';
    $query .= ',kundennummer=' . (int)$line[$KEY['KUNDNR']];
    $query .= ',land=';
    switch ($line[$KEY['NA']])
    {
      case '':
        $query .= '\'\'';
        break;
      case 'A':
        $query .= '\'AT\'';
        break;
      case 'B':
        $query .= '\'BE\'';
        break;
      case 'CH':
        $query .= '\'CH\'';
        break;
      case 'CR':
        $query .= '\'CR\'';
        break;
      case 'D':
        $query .= '\'DE\'';
        break;
      case 'E':
        $query .= '\'ES\'';
        break;
      case 'F':
        $query .= '\'FR\'';
        break;
      case 'GB':
        $query .= '\'UK\'';
        break;
      case 'I':
        $query .= '\'IT\'';
        break;
      case 'L':
        $query .= '\'LU\'';
        break;
      case 'NL':
        $query .= '\'NL\'';
        break;
      case 'USA':
        $query .= '\'US\'';
        break;
      default:
        echo "error: unknown NA '{$line[$KEY['NA']]}'\n";
        exit;
    }
    $query .= ',mobil=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['FUNK']]))) . '\'';
    $query .= ',name=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['NAME']]))) . '\'';
    $query .= ',ort=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['ORT']]))) . '\'';
    $query .= ',postleitzahl=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['PLZ']]))) . '\'';
    $query .= ',rechnungsemail=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['EMAIL']]))) . '\'';
    $query .= ',strasse=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['STR']]))) . '\'';
    $query .= ',telefax=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['FAX']]))) . '\'';
    $query .= ',telefon=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['TELD']]))) . '\'';
    $query .= ',telefon2=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['TELP']]))) . '\'';
    $query .= ',umsatzsteuerbefreit=' . ($line[$KEY['MWST']] == 'N' ? '1' : '0');
    $query .= ',umsatzsteuerid=\'\'';
    $query .= ',vorname=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['VORNAME']]))) . '\'';
    $query .= ',waehrung=\'EUR\'';
    $query .= ',zahlungsziel=';
    switch ($line[$KEY['ZAHLBED']])
    {
      case '':
      case '7 Tage rein netto':
      case 'innerhalb von 7 Tagen ohne Abzug':
      case 'Lastschrifteinzug':
        $query .= '7';
        break;
      case '14 Tage rein netto':
      case 'innerhalb von 14 Tagen ohne Abzug':
        $query .= '14';
        break;
      case 'Abwicklung �ber Sedo - Keine Zahlung mehr erforderlich':
      case 'gem. gesonderter vertragl. Vereinbarung':
      case 'per Vorauskasse':
      case 'Ratenzahlung':
      case 'sofort f�llig':
      case 'Zahlungseing. bis sp�testens 1 Tag vor Domainverl�ngerung':
        $query .= '0';
        break;
      default:
        echo "error: unknown ZAHLBED '{$line[$KEY['ZAHLBED']]}'\n";
        exit;
    }
    $query .= ',zahlweise=';
    switch ($line[$KEY['ZAHLBED2']])
    {
      case '':
      case 'per Nachnahme':
      case 'per PayPal':
      case 'per Rechnung/�berweisung':
      case 'per �berweisung':
        $query .= '\'UEBERWEISUNG\'';
        break;
      case 'per Bankeinzug':
        $query .= '\'LASTSCHRIFT\'';
        break;
      default:
        echo "error: unknown ZAHLBED2 '{$line[$KEY['ZAHLBED2']]}'\n";
        exit;
    }
    safe_mysql_query ($query);
  }
}
fclose ($fp);


echo "importing 'domains.csv'\n";
$fp = fopen ('domains.csv', 'r');
if (!$fp)
{
  echo "error: can't open domains.csv\n";
  exit;
}
if (($line = fgetcsv($fp, 0, ';', '"')) !== false)
{
  $KEY = array ();
  foreach ($line as $key => $val)
  {
    $KEY[$val] = $key;
  }
  require_once ('../include/punycode/idna_convert.class.php');
  $IDN = new idna_convert ();
  $duplicate = array
  (
    'abwehrschw�che.com' => 0,
    'versand-apotheke-medivitan.de' => 0,
    'versand-apotheke-strobby.de' => 0,
  );
  $today = (int)date ('Ymd');
  while (($line = fgetcsv($fp, 0, ';', '"')) !== false)
  {
    if (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 && $ymd <= $today)
    {
      continue;
    }
    $res = safe_mysql_query ('SELECT NULL FROM kunde WHERE kundennummer=' . (int)$line[$KEY['KUNDNR']]);
    if (mysql_num_rows($res) < 1)
    {
      echo "error: invalid KUNDNR '{$line[$KEY['KUNDNR']]}'\n";
      continue;
    }
    $skip = false;
    foreach ($duplicate as $key => $val)
    {
      if ($key == trim($line[$KEY['ACCOUNT']]))
      {
        if ($val > 0)
        {
          $skip = true;
        }
        else
        {
          $duplicate[$key] = 1;
        }
      }
    }
    if ($skip)
    {
      continue;
    }
    $query  = 'INSERT INTO domain SET';
    $query .= ' domain=\'' . mysql_real_escape_string($IDN->encode(utf8_encode(trim($line[$KEY['ACCOUNT']])))) . '\'';
    if (($ymd = usdate(trim($line[$KEY['BESTELLT']]))) < 1)
    {
      echo "error: invalid BESTELLT '{$line[$KEY['BESTELLT']]}'\n";
      exit;
    }
    $query .= ',regdate=' . sprintf('%08d', (int)$ymd);
    $query .= ',unicode=\'' . mysql_real_escape_string($IDN->decode(utf8_encode(trim($line[$KEY['ACCOUNT']])))) . '\'';
    safe_mysql_query ($query);
    $domain = mysql_insert_id ();
    if ($line[$KEY['ARTIKELNR']] === '')
    {
      if (eregi('\.at$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-AT';
      }
      else if (eregi('\.biz$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-BIZ';
      }
      else if (eregi('\.ch$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-CH';
      }
      else if (eregi('\.co.uk$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-COUK';
      }
      else if (eregi('\.com$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-COM';
      }
      else if (eregi('\.de$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-DE';
      }
      else if (eregi('\.fr$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-FR';
      }
      else if (eregi('\.info$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-INFO';
      }
      else if (eregi('\.net$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-NET';
      }
      else if (eregi('\.org$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-ORG';
      }
      else if (eregi('\.ws$', trim($line[$KEY['ACCOUNT']])))
      {
        $line[$KEY['ARTIKELNR']] = 'REG-WS';
      }
    }
    else if ($line[$KEY['ARTIKELNR']] === 'REG-DE-3')
    {
      $line[$KEY['ARTIKELNR']] = 'REG-DE3';
    }
    else if ($line[$KEY['ARTIKELNR']] === 'REG-INFOq')
    {
      $line[$KEY['ARTIKELNR']] = 'REG-INFO';
    }
    else if ($line[$KEY['ARTIKELNR']] == 'REG-COMHR' || $line[$KEY['ARTIKELNR']] == 'REG-NAME')
    {
      $query  = 'INSERT INTO artikel SET';
      $query .= ' artikelnummer=\'' . mysql_real_escape_string(trim($line[$KEY['ARTIKELNR']])) . '\'';
      $query .= ',domainreg=1';
      $query .= ',domains=0';
      $query .= ',fibukonto=0';
      $query .= ',intervall=1';
      $query .= ',kurztext=\'\'';
      $query .= ',langtext=\'\'';
      $query .= ',preis=99999.99';
      $query .= ',textanzeige=0';
      $query .= ' ON DUPLICATE KEY UPDATE domains=0';
      safe_mysql_query ($query);
    }
    $res = safe_mysql_query ('SELECT id,intervall FROM artikel WHERE artikelnummer=\'' . mysql_real_escape_string(trim($line[$KEY['ARTIKELNR']])) . '\'');
    if (!($artikel = mysql_fetch_object($res)))
    {
      echo "error: unknown ARTIKELNR '{$line[$KEY['ARTIKELNR']]}' ( {$line[$KEY['ACCOUNT']]} )\n";
      continue;
    }
    $query  = 'INSERT INTO leistung SET';
    if (trim($line[$KEY['ABLAUFDOMAIN']]) === '' || $artikel->intervall < 1)
    {
      $query .= ' abgerechnet=NULL';
    }
    else if (($ymd = usdate(trim($line[$KEY['ABLAUFDOMAIN']]))) > 0)
    {
      $refdate = usdate (trim($line[$KEY['BESTELLT']]));
      if ($refdate >= $ymd)
      {
        $query .= ' abgerechnet=NULL';
      }
      else
      {
        require_once ('../include/phplib/stdlib.inc.php');
        require_once ('../include/phplib/time.inc.php');
        $date = $refdate;
        $ldate = NULL;
        $lldate = NULL;
        while ($date < $ymd)
        {
          $lldate = $ldate;
          $ldate = $date;
          $date = advance_date ($refdate, $artikel->intervall, $date);
        }
        if ($date == $ymd)
        {
          $query .= ' abgerechnet=' . ($ldate ? sprintf('%08d', (int)$ldate) : 'NULL');
        }
        else
        {
          $query .= ' abgerechnet=' . ($lldate ? sprintf('%08d', (int)$lldate) : 'NULL');
        }
      }
    }
    else
    {
      echo "error: invalid ABLAUFDOMAIN '{$line[$KEY['ABLAUFDOMAIN']]}'\n";
      exit;
    }
    $query .= ',anzahl=1';
    $query .= ',artikel=' . (int)$artikel->id;
    $query .= ',domain=' . (int)$domain;
    $query .= ',endedatum=' . (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 ? (int)$ymd : 'NULL');
    $query .= ',hosting=0';
    $query .= ',kommentar=\'\'';
    $query .= ',kunde=' . (int)$line[$KEY['KUNDNR']];
    $query .= ',preis=\'' . mysql_real_escape_string(trim(str_replace(',', '.', $line[$KEY['DOMAINKOST']]))) . '\'';
    if (($ymd = usdate(trim($line[$KEY['BESTELLT']]))) < 1)
    {
      echo "error: invalid BESTELLT '{$line[$KEY['BESTELLT']]}'\n";
      exit;
    }
    $query .= ',referenzdatum=' . sprintf('%08d', (int)$ymd);
    safe_mysql_query ($query);
  }
}
fclose ($fp);


echo "importing 'hostings.csv'\n";
$fp = fopen ('hostings.csv', 'r');
if (!$fp)
{
  echo "error: can't open hostings.csv\n";
  exit;
}
if (($line = fgetcsv($fp, 0, ';', '"')) !== false)
{
  $KEY = array ();
  foreach ($line as $key => $val)
  {
    $KEY[$val] = $key;
  }
  $today = (int)date ('Ymd');
  while (($line = fgetcsv($fp, 0, ';', '"')) !== false)
  {
    if (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 && $ymd <= $today)
    {
      continue;
    }
    $res = safe_mysql_query ('SELECT NULL FROM kunde WHERE kundennummer=' . (int)$line[$KEY['KUNDNR']]);
    if (mysql_num_rows($res) < 1)
    {
      echo "error: invalid KUNDNR '{$line[$KEY['KUNDNR']]}'\n";
      continue;
    }
    $res = safe_mysql_query ('SELECT id,intervall FROM artikel WHERE artikelnummer=\'' . mysql_real_escape_string(trim($line[$KEY['ARTNRSERVER']])) . '\'');
    if (!($artikel = mysql_fetch_object($res)))
    {
      echo "error: unknown ARTNRSERVER '{$line[$KEY['ARTNRSERVER']]}' ({$line[$KEY['KUNDNR']]})\n";
      continue;
    }
    $query  = 'INSERT INTO leistung SET';
    if (trim($line[$KEY['NRECHNUNG']]) === '' || $artikel->intervall < 1)
    {
      $query .= ' abgerechnet=NULL';
    }
    else if (($ymd = usdate(trim($line[$KEY['NRECHNUNG']]))) > 0)
    {
      $refdate = usdate (trim($line[$KEY['DATUM']]));
      if ($refdate >= $ymd)
      {
        $query .= ' abgerechnet=NULL';
      }
      else
      {
        require_once ('../include/phplib/stdlib.inc.php');
        require_once ('../include/phplib/time.inc.php');
        $date = $refdate;
        $ldate = NULL;
        $lldate = NULL;
        while ($date < $ymd)
        {
          $lldate = $ldate;
          $ldate = $date;
          $date = advance_date ($refdate, $artikel->intervall, $date);
        }
        if ($date == $ymd)
        {
          $query .= ' abgerechnet=' . ($ldate ? sprintf('%08d', (int)$ldate) : 'NULL');
        }
        else
        {
          $query .= ' abgerechnet=' . ($lldate ? sprintf('%08d', (int)$lldate) : 'NULL');
        }
      }
    }
    else
    {
      echo "error: invalid NRECHNUNG '{$line[$KEY['NRECHNUNG']]}'\n";
      exit;
    }
    $query .= ',anzahl=1';
    $query .= ',artikel=' . (int)$artikel->id;
    $query .= ',domain=0';
    $query .= ',endedatum=' . (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 ? (int)$ymd : 'NULL');
    $query .= ',hosting=0';
    $query .= ',kommentar=\'' . mysql_real_escape_string(utf8_encode(trim($line[$KEY['LEISTUNG']]))) . '\'';
    $query .= ',kunde=' . (int)$line[$KEY['KUNDNR']];
    $query .= ',preis=\'' . mysql_real_escape_string(trim(str_replace(',', '.', $line[$KEY['PREISSERVER']]))) . '\'';
    if (($ymd = usdate(trim($line[$KEY['DATUM']]))) < 1)
    {
      echo "error: invalid DATUM '{$line[$KEY['DATUM']]}'\n";
      exit;
    }
    $query .= ',referenzdatum=' . sprintf('%08d', (int)$ymd);
    safe_mysql_query ($query);
  }
}
fclose ($fp);


echo "importing 'wdienste.csv'\n";
$fp = fopen ('wdienste.csv', 'r');
if (!$fp)
{
  echo "error: can't open wdienste.csv\n";
  exit;
}
if (($line = fgetcsv($fp, 0, ';', '"')) !== false)
{
  $KEY = array ();
  foreach ($line as $key => $val)
  {
    $KEY[$val] = $key;
  }
  $today = (int)date ('Ymd');
  while (($line = fgetcsv($fp, 0, ';', '"')) !== false)
  {
    if ($line[$KEY['ABRECH']] == 'E')
    {
      continue;
    }
    if (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 && $ymd <= $today)
    {
      continue;
    }
    $res = safe_mysql_query ('SELECT NULL FROM kunde WHERE kundennummer=' . (int)$line[$KEY['KUNDNR']]);
    if (mysql_num_rows($res) < 1)
    {
      echo "error: invalid KUNDNR '{$line[$KEY['KUNDNR']]}'\n";
      continue;
    }
    $res = safe_mysql_query ('SELECT id,intervall FROM artikel WHERE artikelnummer=\'' . mysql_real_escape_string(trim($line[$KEY['ARTNR']])) . '\'');
    if (!($artikel = mysql_fetch_object($res)))
    {
      echo "error: unknown ARTNR '{$line[$KEY['ARTNR']]}' ({$line[$KEY['KUNDNR']]})\n";
      continue;
    }
    if ($artikel->intervall < 1)
    {
      continue;
    }
    $query  = 'INSERT INTO leistung SET';
    if (trim($line[$KEY['NRECHNUNG']]) === '')
    {
      $query .= ' abgerechnet=NULL';
    }
    else if (($ymd = usdate(trim($line[$KEY['NRECHNUNG']]))) > 0)
    {
      $refdate = usdate (trim($line[$KEY['DATUM']]));
      if ($refdate >= $ymd)
      {
        $query .= ' abgerechnet=NULL';
      }
      else
      {
        require_once ('../include/phplib/stdlib.inc.php');
        require_once ('../include/phplib/time.inc.php');
        $date = $refdate;
        $ldate = NULL;
        $lldate = NULL;
        while ($date < $ymd)
        {
          $lldate = $ldate;
          $ldate = $date;
          $date = advance_date ($refdate, $artikel->intervall, $date);
        }
        if ($date == $ymd)
        {
          $query .= ' abgerechnet=' . ($ldate ? sprintf('%08d', (int)$ldate) : 'NULL');
        }
        else
        {
          $query .= ' abgerechnet=' . ($lldate ? sprintf('%08d', (int)$lldate) : 'NULL');
        }
      }
    }
    else
    {
      echo "error: invalid NRECHNUNG '{$line[$KEY['NRECHNUNG']]}'\n";
      exit;
    }
    $query .= ',anzahl=\'' . mysql_real_escape_string(trim(str_replace(',', '.', $line[$KEY['ANZAHL']]))) . '\'';
    $query .= ',artikel=' . (int)$artikel->id;
    $query .= ',domain=0';
    $query .= ',endedatum=' . (($ymd = usdate(trim($line[$KEY['GELOESCHT']]))) > 0 ? (int)$ymd : 'NULL');
    $query .= ',hosting=0';
    $tmp = trim ($line[$KEY['LEISTUNG']]);
    if (trim($line[$KEY['TEXT']]))
    {
      $tmp .= ($tmp !== '' ? "\n" : '') . trim ($line[$KEY['TEXT']]);
    }
    $query .= ',kommentar=\'' . mysql_real_escape_string(utf8_encode($tmp)) . '\'';
    $query .= ',kunde=' . (int)$line[$KEY['KUNDNR']];
    $query .= ',preis=\'' . mysql_real_escape_string(trim(str_replace(',', '.', $line[$KEY['PREIS']]))) . '\'';
    if (($ymd = usdate(trim($line[$KEY['DATUM']]))) < 1)
    {
      echo "error: invalid DATUM '{$line[$KEY['DATUM']]}'\n";
      exit;
    }
    $query .= ',referenzdatum=' . sprintf('%08d', (int)$ymd);
    safe_mysql_query ($query);
  }
}
fclose ($fp);

?>