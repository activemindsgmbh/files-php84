<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

require_once ('kunde.inc.php');

$kunde = NULL;
$rechnung = NULL;
$id = trim (form_input(@$_GET['id']));
if ($id === '' || !isuint($id))
{
  fatal_error ('INVALID_ID');
}
$query  = 'SELECT';
$query .= ' DATE_FORMAT(datum,\'%d.%m.%Y\') As datum';
$query .= ',DATE_FORMAT(faellig,\'%d.%m.%Y\') As faellig';
$query .= ',forderung';
$query .= ',id';
$query .= ',kunde';
$query .= ',status';
$query .= ',waehrung';
$query .= ',zahlung';
$query .= ' FROM rechnung';
$query .= ' WHERE id=' . (int)$id;
$rechnung = mysql_query_object ($query);
if (!$rechnung)
{
  fatal_error ('INVALID_ID');
}
$kunde = read_kunde ($rechnung->kunde);
if (!$kunde)
{
  fatal_error ('INVALID_CUSTOMER');
}

$step = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  if ($rechnung->status != 'STORNO')
  {
    if (trim(form_input(@$_POST['cmd'])) == 'BEZAHLT')
    {
			$datum = false;
			if ((int)form_input(@$_POST['konto']))
			{
				$datum = trim (form_input(@$_POST['datum']));
				if ($datum === '')
				{
					set_error ('datum', 'EMPTY');
				}
				else
				{
					$datum = parse_date ($datum);
					if ($datum < 0 || $datum > (int)date('Ymd'))
					{
						set_error ('datum', 'INVALID');
					}
				}
			}
			if (!error())
			{
      	$query  = 'UPDATE rechnung SET';
      	$query .= ' status=\'ERLEDIGT\'';
      	$query .= ',zahlung=forderung';
      	$query .= ' WHERE id=' . (int)$rechnung->id;
      	safe_mysql_query ($query);
      	$step = 2;
				$query  = 'INSERT INTO rechnungslog SET';
				$query .= ' aktion=\'ZAHLUNG\'';
				$query .= ',betrag=' . mysql_real_escape_string($rechnung->forderung - $rechnung->zahlung);
				$query .= ',ctime=NOW()';
				$query .= ',datum=' . ($datum ? '\'' . mysql_real_escape_string($datum) . '\'' : 'NULL');
				$query .= ',konto=' . (int)form_input(@$_POST['konto']);
				$query .= ',rechnung=' . (int)$rechnung->id;
      	safe_mysql_query ($query);
			}
    }
    else if (trim(form_input(@$_POST['cmd'])) == 'RUECKLASTSCHRIFT')
    {
      $query  = 'UPDATE rechnung SET';
      $query .= ' status=\'RUECKLASTSCHRIFT\'';
      $query .= ',zahlung=0';
      $query .= ' WHERE id=' . (int)$rechnung->id;
      safe_mysql_query ($query);
      $step = 2;
			$query  = 'INSERT INTO rechnungslog SET';
			$query .= ' aktion=\'RUECKLASTSCHRIFT\'';
			$query .= ',betrag=0';
			$query .= ',ctime=NOW()';
			$query .= ',rechnung=' . (int)$rechnung->id;
      safe_mysql_query ($query);
    }
    else if (trim(form_input(@$_POST['cmd'])) == 'STORNO' && !(float)$rechnung->zahlung)
    {
      $query  = 'UPDATE rechnung SET';
      $query .= ' status=\'STORNO\'';
      $query .= ' WHERE id=' . (int)$rechnung->id;
      safe_mysql_query ($query);
      $step = 2;
			$query  = 'INSERT INTO rechnungslog SET';
			$query .= ' aktion=\'STORNO\'';
			$query .= ',betrag=0';
			$query .= ',ctime=NOW()';
			$query .= ',rechnung=' . (int)$rechnung->id;
      safe_mysql_query ($query);
    }
    else if (trim(form_input(@$_POST['cmd'])) == 'TEILZAHLUNG')
    {
      $betrag = trim (form_input(@$_POST['betrag']));
      if ($betrag === '')
      {
        set_error ('betrag', 'EMPTY');
      }
      else if (!ereg('^([0-9]+|[0-9]*[,.][0-9][0-9]?)$', $betrag))
      {
        set_error ('betrag', 'INVALID');
      }
      else if ($rechnung->forderung - $rechnung->zahlung < (float)str_replace(',', '.', $betrag))
      {
        set_error ('betrag', 'EXCESS');
      }
			$datum = false;
			if ((int)form_input(@$_POST['konto']))
			{
				$datum = trim (form_input(@$_POST['datum']));
				if ($datum === '')
				{
					set_error ('datum', 'EMPTY');
				}
				else
				{
					$datum = parse_date ($datum);
					if ($datum < 0 || $datum > (int)date('Ymd'))
					{
						set_error ('datum', 'INVALID');
					}
				}
			}
      if (!error())
      {
        $betrag = mysql_real_escape_string (str_replace(',', '.', $betrag));
        $query  = 'UPDATE rechnung SET';
        $query .= " status=IF(zahlung+{$betrag}<forderung,'OFFEN','ERLEDIGT')";
        $query .= ",zahlung=LEAST(forderung,zahlung+{$betrag})";
        $query .= ' WHERE id=' . (int)$rechnung->id;
        safe_mysql_query ($query);
        $step = 2;
				$query  = 'INSERT INTO rechnungslog SET';
				$query .= ' aktion=\'ZAHLUNG\'';
				$query .= ',betrag=' . mysql_real_escape_string($betrag);
				$query .= ',ctime=NOW()';
				$query .= ',datum=' . ($datum ? '\'' . mysql_real_escape_string($datum) . '\'' : 'NULL');
				$query .= ',konto=' . (int)form_input(@$_POST['konto']);
				$query .= ',rechnung=' . (int)$rechnung->id;
      	safe_mysql_query ($query);
      }
    }
  }
  else if (trim(form_input(@$_POST['cmd'])) == 'UNSTORNO')
  {
    $query  = 'UPDATE rechnung SET';
    $query .= ' status=\'OFFEN\'';
    $query .= ' WHERE id=' . (int)$rechnung->id;
    safe_mysql_query ($query);
    $step = 2;
		$query  = 'INSERT INTO rechnungslog SET';
		$query .= ' aktion=\'UNSTORNO\'';
		$query .= ',betrag=0';
		$query .= ',ctime=NOW()';
		$query .= ',rechnung=' . (int)$rechnung->id;
    safe_mysql_query ($query);
  }
}

require_once ("templates/html/rechnung_{$step}.html");

?>