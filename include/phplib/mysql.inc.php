<?php

require_once ('error.inc.php');
require_once ('Database.php');


function mysql_count($table, $clause = '')
{
    $db = Database::getInstance();
    $res = $db->query('SELECT COUNT(*) FROM ' . $db->escapeString($table) . ($clause ? ' WHERE ' . $clause : ''));
    $row = $db->fetchRow($res);
    return (int)@$row[0];
}


function mysql_escape_pattern(string $pattern): string
{
    $len = strlen($pattern);
    $s = '';
    $slash = false;
    for ($i = 0; $i < $len; ++$i) {
        if ($pattern[$i] === '*') {
            $s .= ($slash ? '*' : '%');
            $slash = false;
        }
        else if ($pattern[$i] === '?') {
            $s .= ($slash ? '?' : '_');
            $slash = false;
        }
        else if ($pattern[$i] === '%') {
            $s .= "\\%";
            $slash = false;
        }
        else if ($pattern[$i] === '_') {
            $s .= "\\_";
            $slash = false;
        }
        else {
            if ($slash) {
                $s .= "\\\\";
                $slash = false;
            }
            if ($pattern[$i] === "\r")
      {
        $s .= "\\r";
      }
      else if ($pattern[$i] == "\n")
      {
        $s .= "\\n";
      }
      else if ($pattern[$i] == "\\")
      {
        $s .= "\\\\";
      }
      else if ($pattern[$i] == "'")
      {
        $s .= "\\'";
      }
      else if ($pattern[$i] == "\"")
      {
        $s .= "\\\"";
      }
      else if (ord($pattern[$i]) === 0)
      {
        $s .= "\\" . chr(0);
      }
      else if (ord($pattern[$i]) === 0x1a)
      {
        $s .= "\\" . chr(0x1a);
      }
      else
      {
        $s .= $pattern[$i];
      }
    }
  }
  if ($slash)
  {
    $s .= "\\\\";
  }
  return $s;
}


function mysql_found_rows ()
{
  $res = safe_mysql_query ('SELECT FOUND_ROWS()');
  $row = mysql_fetch_row ($res);
  return (int)@$row[0];
}


function mysql_init ($host, $user, $pass, $db)
{
  if (!@mysql_connect($host, $user, $pass))
  {
    fatal_error ('ERROR_DATABASE', mysql_error(), 'mysql_connect');
  }
  if (!@mysql_select_db($db))
  {
    fatal_error ('ERROR_DATABASE', mysql_error(), 'mysql_select_db');
  }
}


function mysql_query_object ($query)
{
  $res = safe_mysql_query ($query);
  return mysql_fetch_object ($res);
}


function safe_mysql_query ($query)
{
  if ($res = @mysql_query($query))
  {
    return $res;
  }
  fatal_error ('ERROR_DATABASE', mysql_error(), $query);
}

?>