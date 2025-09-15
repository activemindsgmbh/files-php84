<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$ids = '';
$id = form_input (@$_GET['id']);
if ($id !== '')
{
  if (!isuint($id) || !mysql_count('mahnung', 'id=' . (int)$id))
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
    if (!ereg('^[0-9]+(,[0-9]+)*$', $ids))
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
  $query .= ' mahnung.betrag';
  $query .= ',DATE_FORMAT(mahnung.datum,\'%d.%m.%Y\') AS datum';
  $query .= ',mahnung.id';
  $query .= ',IF(ISNULL(mahnung.verschickt),\'\',DATE_FORMAT(mahnung.verschickt,\'%d.%m.%Y\')) AS verschickt';
  $query .= ',mahnung.waehrung';
  $query .= ',kunde.kundennummer';
  $query .= ',kunde.name';
  $query .= ',kunde.rechnungsemail';
  $query .= ',kunde.vorname';
  $query .= ' FROM mahnung';
  $query .= ' LEFT JOIN kunde ON kunde.kundennummer=mahnung.kunde';
  $query .= ' WHERE mahnung.id IN(' . mysql_real_escape_string($ids) . ')';
  $query .= ' AND kunde.rechnungsemail!=\'\'';
  $query .= ' ORDER BY mahnung.datum,mahnung.id';
  $rmahnungen = safe_mysql_query ($query);
  $ids = '';
  while ($obj = mysql_fetch_object($rmahnungen))
  {
    $ids .= ($ids ? ',' : '') . (int)$obj->id;
  }
  mysql_data_seek ($rmahnungen, 0);
}
if ($ids === '')
{
  fatal_error ('MISSING_PARAMETER');
}

$query  = 'SELECT DISTINCT kunde';
$query .= ' FROM mahnung';
$query .= ' WHERE mahnung.id IN(' . mysql_real_escape_string($ids) . ')';
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
  while ($mahnung = mysql_fetch_object($rmahnungen))
  {
    require ('templates/eml/mahnung.eml');
    safe_mysql_query ('UPDATE mahnung SET verschickt=CURDATE() WHERE id=' . (int)$mahnung->id);
  }
  $step = 2;
}

require_once ("templates/html/mahnungen_versenden_{$step}.html");

?>