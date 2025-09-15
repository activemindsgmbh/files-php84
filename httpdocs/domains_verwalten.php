<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$cmd = '';
$ids = '';
$id = form_input (@$_GET['id']);
if ($id !== '')
{
  if (!isuint($id) || !mysql_count('leistung', 'id=' . (int)$id . ' AND domain!=0'))
  {
    fatal_error ('INVALID_ID');
  }
  $cmd = form_input (@$_GET['cmd']);
  $ids = $id;
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  $cmd = form_input (@$_POST['cmd']);
  if (($cmd == 'edit' || $cmd == 'remove') && form_input(@$_POST['confirm']) === '1')
  {
    $ids = trim (form_input(@$_POST['ids']));
    if (!preg_match('#^[0-9]+(,[0-9]+)*$#', $ids))
    {
      $ids = '';
    }
  }
  else if (@$_POST['edit'] || @$_POST['remove'])
  {
    $cmd = @$_POST['remove'] ? 'remove' : 'edit';
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
if ($cmd != 'edit' && $cmd != 'remove')
{
  fatal_error ('INVALID_COMMAND');
}
if ($ids !== '')
{
  $query  = 'SELECT';
  $query .= ' leistung.artikel';
  $query .= ',leistung.domain';
  $query .= ',leistung.id';
  $query .= ',leistung.kunde';
	$query .= ',domain.a_dom';
	$query .= ',domain.a_www';
  $query .= ',domain.domain AS domain_name';
  $query .= ',domain.unicode AS domain_utf8name';
	$query .= ',GROUP_CONCAT(DISTINCT CONCAT(mx.priority,\' \',mx.host) ORDER BY mx.priority,mx.host) AS mxhosts';
	$query .= ',GROUP_CONCAT(DISTINCT ns.host ORDER BY ns.host) AS nshosts';
  $query .= ' FROM leistung';
  $query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
	$query .= ' LEFT JOIN mx ON mx.domain=domain.id';
	$query .= ' LEFT JOIN ns ON ns.domain=domain.id';
  $query .= ' WHERE leistung.id IN(' . mysql_real_escape_string($ids) . ')';
  $query .= ' AND leistung.domain!=0';
  $query .= ' AND NOT ISNULL(domain.id)';
	$query .= ' GROUP BY leistung.id';
  $rdomains = safe_mysql_query ($query);
  $ids = '';
  while ($obj = mysql_fetch_object($rdomains))
  {
    $ids .= ($ids ? ',' : '') . (int)$obj->id;
  }
  mysql_data_seek ($rdomains, 0);
}
if ($ids === '')
{
  fatal_error ('MISSING_PARAMETER');
}

$query  = 'SELECT DISTINCT kunde';
$query .= ' FROM leistung';
$query .= ' WHERE leistung.id IN(' . mysql_real_escape_string($ids) . ')';
$query .= ' AND leistung.domain!=0';
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

$domain = NULL;
if (mysql_num_rows($rdomains) == 1)
{
  $domain = mysql_fetch_object ($rdomains);
  mysql_data_seek ($rdomains, 0);
}

if ($cmd == 'edit')
{
  if (form_input(@$_POST['confirm']) !== '1')
  {
    if (mysql_num_rows($rdomains) == 1 && $domain)
    {
      $query  = 'SELECT';
      $query .= ' IF(ISNULL(leistung.abgerechnet),\'\',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\')) AS abgerechnet';
      $query .= ',IF(ISNULL(leistung.endedatum),\'\',DATE_FORMAT(leistung.endedatum,\'%d.%m.%Y\')) AS endedatum';
      $query .= ',leistung.hosting';
      $query .= ',leistung.kunde';
      $query .= ',leistung.preis';
      $query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
      $query .= ',leistung.setup';
      $query .= ' FROM leistung';
      $query .= ' WHERE leistung.id=' . (int)$domain->id;
      $obj = mysql_query_object ($query);
      $_POST['abgerechnet']   = db2form ($obj->abgerechnet);
      $_POST['endedatum']     = db2form ($obj->endedatum);
      $_POST['hosting']       = (int)$obj->hosting;
      $_POST['kunde']         = (int)$obj->kunde;
      $_POST['preis']         = db2form ($obj->preis);
      $_POST['referenzdatum'] = db2form ($obj->referenzdatum);
      $_POST['setup']         = db2form ($obj->setup);
    }
  }
  else
  {
    $abgerechnet   = trim (form_input(@$_POST['abgerechnet']));
    $endedatum     = trim (form_input(@$_POST['endedatum']));
    $hosting       = trim (form_input(@$_POST['hosting']));
    $kunde         = trim (form_input(@$_POST['kunde']));
    $preis         = trim (form_input(@$_POST['preis']));
    $referenzdatum = trim (form_input(@$_POST['referenzdatum']));
    $setup         = trim (form_input(@$_POST['setup']));
    if ($abgerechnet !== '' && ($abgerechnet = parse_date($abgerechnet)) < 0)
    {
      set_error ('leistung.abgerechnet', 'INVALID');
    }
    if ($endedatum !== '' && ($endedatum = parse_date($endedatum)) < 0)
    {
      set_error ('leistung.endedatum', 'INVALID');
    }
    if ($kunde !== '' && !(isuint($kunde) && mysql_count('kunde', 'kundennummer=' . (int)$kunde) == 1))
    {
      set_error ('leistung.kunde', 'INVALID');
    }
    if ($hosting && !isuint($hosting))
    {
      set_error ('leistung.hosting', 'INVALID');
    }
    else if (($hosting = (int)$hosting) > 0 && mysql_num_rows($rdomains) == 1 && $domain && $kunde == $domain->kunde)
    {
      $query  = 'SELECT';
      $query .= ' artikel.domains';
      $query .= ',COUNT(leistung2.id) AS assigned';
      $query .= ' FROM leistung';
      $query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
      $query .= ' LEFT JOIN leistung AS leistung2 ON leistung2.hosting=leistung.id AND leistung2.domain!=' . (int)$domain->domain;
      $query .= ' WHERE leistung.id=' . (int)$hosting;
      $query .= ' AND leistung.kunde=' . (int)$domain->kunde;
      $query .= ' AND leistung.domain=0';
      $query .= ' AND artikel.domains>0';
      $query .= ' GROUP BY leistung.id';
      $obj = mysql_query_object ($query);
      if (!$obj || $obj->domains <= $obj->assigned)
      {
        set_error ('leistung.hosting', 'INVALID');
      }
    }
    if ($preis !== '' && !preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $preis))
    {
      set_error ('leistung.preis', 'INVALID');
    }
    if ($referenzdatum !== '' && ($referenzdatum = parse_date($referenzdatum)) < 0)
    {
      set_error ('leistung.referenzdatum', 'INVALID');
    }
    if ($setup !== '' && !preg_match('#^-?([0-9]+|[0-9]*[,.][0-9][0-9]?)$#', $setup))
    {
      set_error ('leistung.setup', 'INVALID');
    }
    if (!error())
    {
      $query = '';
      if ($abgerechnet !== '')
      {
        $query .= ($query ? ',' : '') . 'abgerechnet=' . (int)$abgerechnet;
      }
      else if (mysql_num_rows($rdomains) == 1)
      {
        $query .= ($query ? ',' : '') . 'abgerechnet=NULL';
      }
      if ($endedatum !== '')
      {
        $query .= ($query ? ',' : '') . 'endedatum=' . (int)$endedatum;
      }
      else if (mysql_num_rows($rdomains) == 1)
      {
        $query .= ($query ? ',' : '') . 'endedatum=NULL';
      }
      if ($preis !== '')
      {
        $query .= ($query ? ',' : '') . 'preis=\'' . mysql_real_escape_string(str_replace(',','.',$preis)) . '\'';
      }
      if ($referenzdatum !== '')
      {
        $query .= ($query ? ',' : '') . 'referenzdatum=' . (int)$referenzdatum;
      }
      if ($setup !== '')
      {
        $query .= ($query ? ',' : '') . 'setup=\'' . mysql_real_escape_string(str_replace(',','.',$setup)) . '\'';
      }
      if ($kunde === '' || mysql_num_rows($rdomains) == 1 && $domain && $kunde == $domain->kunde)
      {
        if (mysql_num_rows($rdomains) == 1 || $hosting === 0)
        {
          $query .= ($query ? ',' : '') . 'hosting=' . (int)$hosting;
        }
        if ($query)
        {
          safe_mysql_query ('UPDATE leistung SET ' . $query . ' WHERE id IN(' . mysql_real_escape_string($ids) . ')');
        }
      }
      else
      {
        while ($obj = mysql_fetch_object($rdomains))
        {
          if ($kunde != $obj->kunde)
          {
            $query .= ($query ? ',' : '') . 'kunde=' . (int)$kunde;
            $query .= ',hosting=0';
            if ($preis === '' || $setup === '')
            {
              $q  = 'SELECT';
              $q .= ' IF(NOT ISNULL(kundenpreis.preis),kundenpreis.preis,artikel.preis) AS preis';
              $q .= ',IF(NOT ISNULL(kundenpreis.setup),kundenpreis.setup,artikel.setup) AS setup';
              $q .= ' FROM artikel';
              $q .= ' LEFT JOIN kundenpreis ON kundenpreis.kunde=' . (int)$kunde;
              $q .= ' AND kundenpreis.artikel=artikel.id';
              $q .= ' AND (ISNULL(kundenpreis.von) OR kundenpreis.von<=CURDATE())';
              $q .= ' AND (ISNULL(kundenpreis.bis) OR kundenpreis.bis>=CURDATE())';
              $q .= ' WHERE artikel.id=' . (int)$obj->artikel;
              $res = safe_mysql_query ($q);
              if ($row = mysql_fetch_row($res))
              {
                if ($preis === '')
                {
                  $query .= ',preis=\'' . mysql_real_escape_string($row[0]) . '\'';
                }
                if ($setup === '')
                {
                  $query .= ',setup=\'' . mysql_real_escape_string($row[1]) . '\'';
                }
              }
            }
          }
          if ($query)
          {
            safe_mysql_query ('UPDATE leistung SET ' . $query . ' WHERE id=' . (int)$obj->id);
          }
        }
        mysql_data_seek ($rdomains, 0);
      }
      $step = 2;
    }
  }
  require_once ("templates/html/domains_bearbeiten_{$step}.html");
}
else if ($cmd == 'remove')
{
  if (form_input(@$_POST['confirm']) === '1')
  {
    safe_mysql_query ('DELETE FROM leistung WHERE id IN(' . mysql_real_escape_string($ids) . ')');
    $ids = '';
    while ($obj = mysql_fetch_object($rdomains))
    {
      $ids .= ($ids ? ',' : '') . (int)$obj->domain;
    }
    safe_mysql_query ('DELETE FROM domain WHERE id IN(' . mysql_real_escape_string($ids) . ')');
    mysql_data_seek ($rdomains, 0);
    $step = 2;
  }
  require_once ("templates/html/domains_loeschen_{$step}.html");
}

?>