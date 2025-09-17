<?php

require_once ('mysql.inc.php');
require_once ('stdlib.inc.php');

$GLOBALS['SESSION_CHANGED'] = false;
$GLOBALS['SESSION_DATA']    = array ();
$GLOBALS['SESSION_DOMAIN']  = '';
$GLOBALS['SESSION_ID']      = 0;
$GLOBALS['SESSION_UID']     = 0;


function clear_session ()
{
  $GLOBALS['SESSION_CHANGED'] = false;
  $GLOBALS['SESSION_DATA'] = array ();
  $GLOBALS['SESSION_DOMAIN'] = '';
  $GLOBALS['SESSION_ID'] = 0;
  $GLOBALS['SESSION_UID'] = 0;
}


function clear_session_data ()
{
  if ($GLOBALS['SESSION_ID'])
  {
    $GLOBALS['SESSION_DATA'] = array ();
    $GLOBALS['SESSION_CHANGED'] = true;
    return true;
  }
  return false;
}


function clear_session_variable ($key)
{
  if ($GLOBALS['SESSION_ID'] && isset($GLOBALS['SESSION_DATA'][$key]))
  {
    unset ($GLOBALS['SESSION_DATA'][$key]);
    $GLOBALS['SESSION_CHANGED'] = true;
    return true;
  }
  return false;
}


function create_session ($uid, $lifespan = 0, $domain = '')
{
  clear_session ();
  $hash = sprintf ('%s%s', sha1(uniqid(rand(),true)), sha1($_SERVER['HTTP_USER_AGENT']));
  $query  = 'INSERT INTO session SET';
  $query .= ' hash=\'' . mysql_real_escape_string($hash) . '\'';
  $query .= ',lifespan=' . (int)$lifespan;
  $query .= ',modified=NOW()';
  $query .= ',uid=' . (int)$uid;
  if (safe_mysql_query($query))
  {
    $GLOBALS['SESSION_DOMAIN'] = $domain;
    $GLOBALS['SESSION_ID'] = mysql_insert_id ();
    $GLOBALS['SESSION_UID'] = $uid;
    $hash = sprintf ('%s%s', decstr($GLOBALS['SESSION_ID']), $hash);
    setcookie ('session', base64_encode($hash), $lifespan ? (time() + $lifespan) : 0, '/', $domain);
    return true;
  }
  return false;
}


function destroy_session ()
{
  if ($GLOBALS['SESSION_ID'])
  {
    setcookie ('session', '', time(), '/', $GLOBALS['SESSION_DOMAIN']);
    safe_mysql_query ('DELETE FROM session WHERE id=' . (int)$GLOBALS['SESSION_ID']);
  }
  clear_session ();
}


function get_session_id ()
{
  return $GLOBALS['SESSION_ID'];
}


function get_session_uid ()
{
  return ($GLOBALS['SESSION_ID'] ? $GLOBALS['SESSION_UID'] : 0);
}


function get_session_variable ($key)
{
  return ($GLOBALS['SESSION_ID'] ? @$GLOBALS['SESSION_DATA'][$key] : NULL);
}


function init_session ($update = true)
{
  clear_session ();
  if (isset($_COOKIE['session']))
  {
    $hash = base64_decode ($_COOKIE['session']);
    $sid = strdec ($hash);
    if ($sid > 0 && substr($hash,44) == sha1($_SERVER['HTTP_USER_AGENT']))
    {
      $query  = 'SELECT (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(modified)) AS age,data,domain,id,lifespan,uid FROM session';
      $query .= ' WHERE id=' . (int)$sid;
      $query .= ' AND hash=\'' . mysql_real_escape_string(substr($hash, 4)) . '\'';
      if ($obj = mysql_query_object($query))
      {
        if ($obj->lifespan == 0 || $obj->age < $obj->lifespan)
        {
          $GLOBALS['SESSION_DATA'] = unserialize ($obj->data);
          $GLOBALS['SESSION_DOMAIN'] = $obj->domain;
          $GLOBALS['SESSION_ID'] = $obj->id;
          $GLOBALS['SESSION_UID'] = $obj->uid;
          if ($update)
          {
            if ($obj->lifespan)
            {
              setcookie ('session', $_COOKIE['session'], time() + $obj->lifespan, '/', $obj->domain);
            }
            @mysql_query ('UPDATE session SET modified=NOW() WHERE id=' . (int)$obj->id);
          }
          return true;
        }
        @mysql_query ('DELETE FROM session WHERE id=' . (int)$obj->id);
      }
    }
  }
  return false;
}


function set_session_variable ($key, $value)
{
  if ($GLOBALS['SESSION_ID'])
  {
    $GLOBALS['SESSION_DATA'][$key] = $value;
    $GLOBALS['SESSION_CHANGED'] = true;
    return true;
  }
  return false;
}


function update_session ()
{
  if ($GLOBALS['SESSION_ID'])
  {
    @mysql_query ('UPDATE session SET data=\'' . mysql_real_escape_string(serialize($GLOBALS['SESSION_DATA'])) . '\' WHERE id=' . (int)$GLOBALS['SESSION_ID']);
    $GLOBALS['SESSION_CHANGED'] = false;
  }
}

?>