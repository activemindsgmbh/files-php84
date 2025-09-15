<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$konten = safe_mysql_query ('SELECT id,name FROM konto WHERE hidden=0 ORDER BY name,id');

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
	while ($konto = mysql_fetch_object($konten))
	{
		$_POST['name_' . (int)$konto->id] = db2form ($konto->name);
	}
	@mysql_data_seek ($konten, 0);
	$_POST['new'] = '';
}
else
{
	$ids = '';
	$queries = array ();
	$tmp = array ();
	while ($konto = mysql_fetch_object($konten))
	{
		$name = trim (form_input(@$_POST['name_' . (int)$konto->id]));
		if ($name === '')
		{
			$ids .= ($ids ? ',' : '') . (int)$konto->id;
		}
		else
		{
			$clause  = 'id!=' . (int)$konto->id;
			$clause .= ' AND hidden=0';
			$clause .= ' AND name=\'' . mysql_real_escape_string($name) . '\'';
			if (mysql_count('konto', $clause))
			{
				set_error ('konto.name[' . (int)$konto->id . ']', 'NOT UNIQUE');
			}
			else if (strcasecmp($name, $konto->name))
			{
				foreach ($tmp as $s)
				{
					if (!strcasecmp($name, $s))
					{
						set_error ('konto.name[' . (int)$konto->id . ']', 'NOT UNIQUE');
						break;
					}
				}
			}
			if (strcasecmp($name, $konto->name))
			{
				$ids .= ($ids ? ',' : '') . (int)$konto->id;
				$queries[] = 'INSERT INTO konto SET hidden=0,name=\'' . mysql_real_escape_string($name) . '\'';
			}
			$tmp[] = $name;
		}
	}
	@mysql_data_seek ($konten, 0);
	$name = trim (form_input(@$_POST['new']));
	if ($name !== '')
	{
		$clause  = ' hidden=0';
		$clause .= ' AND name=\'' . mysql_real_escape_string($name) . '\'';
		if (mysql_count('konto', $clause))
		{
			set_error ('konto.name[\'new\']', 'NOT UNIQUE');
		}
		else
		{
			foreach ($tmp as $s)
			{
				if (!strcasecmp($name, $s))
				{
					set_error ('konto.name[\'new\']', 'NOT UNIQUE');
					break;
				}
			}
		}
		$queries[] = 'INSERT INTO konto SET hidden=0,name=\'' . mysql_real_escape_string($name) . '\'';
		$tmp[] = $name;
	}
  if (!error())
  {
		foreach ($queries as $query)
		{
			safe_mysql_query ($query);
		}
		if ($ids !== '')
		{
			safe_mysql_query ('UPDATE konto SET hidden=1 WHERE id IN(' . mysql_real_escape_string($ids) . ')');
		}
		$konten = safe_mysql_query ('SELECT id,name FROM konto WHERE hidden=0 ORDER BY name,id');
		while ($konto = mysql_fetch_object($konten))
		{
			$_POST['name_' . (int)$konto->id] = db2form ($konto->name);
		}
		@mysql_data_seek ($konten, 0);
		$_POST['new'] = '';
  }
}

require_once ("templates/html/konten.html");

?>