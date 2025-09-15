<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$ids = '';
$id = form_input (@$_GET['id']);
if ($id !== '')
{
  if (!isuint($id) || !mysql_count('rechnung', 'id=' . (int)$id))
  {
    fatal_error ('INVALID_ID');
  }
  $ids = $id;
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  if (form_input(@$_POST['confirm']) === '1')
  {
    $ids = trim (form_input(@$_POST['ids']));
    if (!preg_match('#^[0-9]+(,[0-9]+)*$#', $ids))
    {
      $ids = '';
    }
  }
  else
  {
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
  }
}
if ($ids !== '')
{
  $query  = 'SELECT';
  $query .= ' DATE_FORMAT(rechnung.datum,\'%d.%m.%Y\') AS datum';
  $query .= ',DATE_FORMAT(rechnung.faellig,\'%d.%m.%Y\') AS faellig';
  $query .= ',rechnung.forderung';
  $query .= ',rechnung.id';
  $query .= ',rechnung.status';
  $query .= ',rechnung.zahlung';
  $query .= ',IF(ISNULL(rechnung.verschickt),\'\',DATE_FORMAT(rechnung.verschickt,\'%d.%m.%Y\')) AS verschickt';
  $query .= ',rechnung.waehrung';
  $query .= ',kunde.kundennummer';
  $query .= ',kunde.name';
  $query .= ',kunde.rechnungsemail';
  $query .= ',kunde.vorname';
  $query .= ' FROM rechnung';
  $query .= ' LEFT JOIN kunde ON kunde.kundennummer=rechnung.kunde';
  $query .= ' WHERE rechnung.id IN(' . mysql_real_escape_string($ids) . ')';
  $query .= ' AND kunde.rechnungsemail!=\'\'';
  $query .= ' ORDER BY rechnung.datum,rechnung.id';
  $rrechnungen = safe_mysql_query ($query);
  $ids = '';
  while ($obj = mysql_fetch_object($rrechnungen))
  {
    $ids .= ($ids ? ',' : '') . (int)$obj->id;
  }
  mysql_data_seek ($rrechnungen, 0);
}
if ($ids === '')
{
  fatal_error ('MISSING_PARAMETER');
}

$query  = 'SELECT DISTINCT kunde';
$query .= ' FROM rechnung';
$query .= ' WHERE rechnung.id IN(' . mysql_real_escape_string($ids) . ')';
$query .= ' LIMIT 2';
$res = safe_mysql_query ($query);
$owner = NULL;
if (mysql_num_rows($res) == 1)
{
  require_once ('kunde.inc.php');
  $row = mysql_fetch_row ($res);
  $owner = read_kunde ((int)$row[0]);
}

$step = 1;

if (form_input(@$_POST['confirm']) === '1')
{
  while ($rechnung = mysql_fetch_object($rrechnungen))
  {
    require ('templates/eml/rechnung.eml');
    safe_mysql_query ('UPDATE rechnung SET verschickt=CURDATE() WHERE id=' . (int)$rechnung->id);
  }
  $step = 2;
}

require_once ("templates/html/rechnungen_versenden_{$step}.html");

?>