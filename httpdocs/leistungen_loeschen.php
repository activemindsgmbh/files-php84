<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$ids = '';
$id = form_input (@$_GET['id']);
if ($id !== '')
{
  if (!isuint($id) || !mysql_count('leistung', 'id=' . (int)$id . ' AND domain=0'))
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
    if (!preg_match('/^[0-9]+(,[0-9]+)*$/i', $ids))
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
  $query .= ' IF(ISNULL(leistung.abgerechnet),\'\',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\')) AS abgerechnet';
  $query .= ',leistung.anzahl';
  $query .= ',leistung.artikel';
  $query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
  $query .= ',leistung.id';
  $query .= ',leistung.kommentar';
  $query .= ',leistung.preis';
  $query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
  $query .= ',leistung.setup';
  $query .= ',artikel.artikelnummer';
  $query .= ' FROM leistung';
  $query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
  $query .= ' WHERE leistung.id IN(' . mysql_real_escape_string($ids) . ')';
  $query .= ' AND leistung.domain=0';
  $query .= ' ORDER BY leistung.id';
  $rleistungen = safe_mysql_query ($query);
  $ids = '';
  while ($obj = mysql_fetch_object($rleistungen))
  {
    $ids .= ($ids ? ',' : '') . (int)$obj->id;
  }
  mysql_data_seek ($rleistungen, 0);
}
if ($ids === '')
{
  fatal_error ('MISSING_PARAMETER');
}

$query  = 'SELECT DISTINCT kunde';
$query .= ' FROM leistung';
$query .= ' WHERE leistung.id IN(' . mysql_real_escape_string($ids) . ')';
$query .= ' AND leistung.domain=0';
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
  safe_mysql_query ('DELETE FROM leistung WHERE id IN(' . mysql_real_escape_string($ids) . ')');
  safe_mysql_query ('UPDATE leistung SET hosting=0 WHERE hosting IN(' . mysql_real_escape_string($ids) . ')');
  $step = 2;
}

require_once ("templates/html/leistungen_loeschen_{$step}.html");

?>