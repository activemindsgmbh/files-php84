<?php
 error_reporting ( E_ALL ); 
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

$query  = 'SELECT';
$query .= ' artikel.artikelnummer';
$query .= ',artikel.id';
$query .= ',artikel.kurztext';
$query .= ',artikel.preis';
$query .= ',artikel.setup';
$query .= ',DATE_FORMAT(kundenpreis.bis,\'%d.%m.%Y\') AS bis';
$query .= ',kundenpreis.preis AS sonderpreis';
$query .= ',kundenpreis.setup AS sondersetup';
$query .= ',IF(ISNULL(kundenpreis.preis) AND ISNULL(kundenpreis.setup),1,0) AS changed';
$query .= ',DATE_FORMAT(kundenpreis.von,\'%d.%m.%Y\') AS von';
$query .= ' FROM artikel';
$query .= ' LEFT JOIN kundenpreis ON kundenpreis.kunde=' . (int)$kunde->kundennummer . ' AND kundenpreis.artikel=artikel.id';
$query .= ' ORDER BY changed,artikel.artikelnummer';
$rartikel = safe_mysql_query ($query);

$step = 1;

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  while ($obj = mysql_fetch_object($rartikel))
  {
    $_POST['bis@' . (int)$obj->id]   = db2form ($obj->bis);
    $_POST['preis@' . (int)$obj->id] = db2form ($obj->sonderpreis);
    $_POST['setup@' . (int)$obj->id] = db2form ($obj->sondersetup);
    $_POST['von@' . (int)$obj->id]   = db2form ($obj->von);
  }
  mysql_data_seek ($rartikel, 0);
}
else
{
  $ids = '';
  $queries = array ();
  while ($obj = mysql_fetch_object($rartikel))
  {
    $bis   = trim (form_input(@$_POST['bis@' . (int)$obj->id]));
    $preis = trim (form_input(@$_POST['preis@' . (int)$obj->id]));
    $setup = trim (form_input(@$_POST['setup@' . (int)$obj->id]));
    $von   = trim (form_input(@$_POST['von@' . (int)$obj->id]));

    if (($von !== '' || $bis !== '') && $preis === '' && $setup === '')
    {
      set_error ('kundenpreis.preis[' . (int)$obj->id . ']', 'EMPTY');
    }
    else if ($preis !== '' && !preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $preis))
    {
      set_error ('kundenpreis.preis[' . (int)$obj->id . ']', 'INVALID');
    }

    if (($von !== '' || $bis !== '') && $setup === '' && $preis === '')
    {
      set_error ('kundenpreis.setup[' . (int)$obj->id . ']', 'EMPTY');
    }
    else if ($setup !== '' && !preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $setup))
    {
      set_error ('kundenpreis.setup[' . (int)$obj->id . ']', 'INVALID');
    }

    if ($von !== '' && ($von = parse_date($von)) < 0)
    {
      set_error ('kundenpreis.von[' . (int)$obj->id . ']', 'INVALID');
    }

    if ($bis !== '')
    {
      if (($bis = parse_date($bis)) < 0)
      {
        set_error ('kundenpreis.bis[' . (int)$obj->id . ']', 'INVALID');
      }
      else if ($von !== '' && $von >= 0 && $bis < $von)
      {
        set_error ('kundenpreis.bis[' . (int)$obj->id . ']', 'IMPLAUSIBLE');
      }
    }

    if (!error())
    {
      if ($preis !== '' || $setup !== '')
      {
        $query  = 'INSERT INTO kundenpreis SET';
        $query .= ' artikel=' . (int)$obj->id;
        $query .= ',bis=' . ($bis !== '' ? (int)$bis : 'NULL');
        $query .= ',kunde=' . (int)$kunde->kundennummer;
        $query .= ',preis=' . ($preis !== '' ? '\'' . mysql_real_escape_string(str_replace(',','.',$preis)) . '\'' : 'NULL');
        $query .= ',setup=' . ($setup !== '' ? '\'' . mysql_real_escape_string(str_replace(',','.',$setup)) . '\'' : 'NULL');
        $query .= ',von=' . ($von !== '' ? (int)$von : 'NULL');
        $query .= ' ON DUPLICATE KEY UPDATE bis=VALUES(bis),preis=VALUES(preis),von=VALUES(von)';
        $queries[] = $query;
      }
      else
      {
        $ids .= ($ids ? ',' : '') . $obj->id;
      }
    }
  }
  mysql_data_seek ($rartikel, 0);
  if (!error())
  {
    if ($ids !== '')
    {
      $query  = 'DELETE FROM kundenpreis';
      $query .= ' WHERE kunde=' . (int)$kunde->kundennummer;
      $query .= ' AND artikel IN(' . mysql_real_escape_string($ids) . ')';
      safe_mysql_query ($query);
    }
    foreach ($queries as $query)
    {
      safe_mysql_query ($query);
    }
    safe_mysql_query ('OPTIMIZE TABLE kundenpreis');
    $step = 2;
  }
}

require_once ("templates/html/kundenpreise_{$step}.html");



?>

