<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$kostenstellen = safe_mysql_query ('SELECT id,name,comment FROM kostenstelle ORDER BY name,id');

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
	while ($kostenstelle = mysql_fetch_object($kostenstellen))
	{
		$_POST['comment_' . (int)$kostenstelle->id] = db2form ($kostenstelle->comment);
	}
	@mysql_data_seek ($kostenstellen, 0);
	$_POST['comment'] = '';
	$_POST['name'] = '';
}
else if (@$_POST['edit'] === '1')
{
	$ids = '';
	$queries = array ();
	$tmp = array ();
	while ($kostenstelle = mysql_fetch_object($kostenstellen))
	{
		$comment = trim (form_input(@$_POST['comment_' . (int)$kostenstelle->id]));
		if ($comment === '')
		{
			$ids .= ($ids ? ',' : '') . (int)$kostenstelle->id;
		}
		else
		{
			$queries[] = 'UPDATE kostenstelle SET comment=\'' . mysql_real_escape_string($comment) . '\' WHERE id=' . (int)$kostenstelle->id;
			$tmp[] = $comment;
		}
	}
	if ($ids !== '')
	{
		safe_mysql_query ('DELETE FROM kostenstelle WHERE id IN(' . mysql_real_escape_string($ids) . ')');
	}
	foreach ($queries as $query)
	{
		safe_mysql_query ($query);
	}
	$kostenstellen = safe_mysql_query ('SELECT id,name,comment FROM kostenstelle ORDER BY name,id');
	while ($kostenstelle = mysql_fetch_object($kostenstellen))
	{
		$_POST['comment_' . (int)$kostenstelle->id] = db2form ($kostenstelle->comment);
	}
	@mysql_data_seek ($kostenstellen, 0);
}
else
{
	$comment = trim (form_input(@$_POST['comment']));
	$name = trim (form_input(@$_POST['name']));
	if ($comment === '' && $name !== '')
	{
		set_error ('kostenstelle.comment', 'EMPTY');
	}
	else if ($name === '' && $comment !== '')
	{
		set_error ('kostenstelle.name', 'EMPTY');
	}
	else if ($comment !== '' && $name !== '')
	{
		$query  = 'INSERT INTO kostenstelle SET';
		$query .= ' comment=\'' . mysql_real_escape_string($comment) . '\'';
		$query .= ',name=\'' . mysql_real_escape_string($name) . '\'';
		$query .= ' ON DUPLICATE KEY UPDATE';
		$query .= ' comment=VALUES(comment)';
		safe_mysql_query ($query);
		$kostenstellen = safe_mysql_query ('SELECT id,name,comment FROM kostenstelle ORDER BY name,id');
		while ($kostenstelle = mysql_fetch_object($kostenstellen))
		{
			$_POST['comment_' . (int)$kostenstelle->id] = db2form ($kostenstelle->comment);
		}
		@mysql_data_seek ($kostenstellen, 0);
	}
}

require_once ("templates/html/kostenstellen.html");

?>