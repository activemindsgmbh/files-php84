<?php

$GLOBALS['error_inc_errors'] = array ();
$GLOBALS['error_inc_fatal_error_handler'] = NULL;


function clear_errors ()
{
  $GLOBALS['error_inc_errors'] = array ();
}


function error ($key = '')
{
  if (!isset($key) || $key === '')
  {
    return count($GLOBALS['error_inc_errors']);
  }
  if (array_key_exists($key, $GLOBALS['error_inc_errors']))
  {
    return $GLOBALS['error_inc_errors'][$key];
  }
  return '';
}


function errormsg ($key, $key2 = '')
{
  if (error($key))
  {
    $str = ($key2 ? $key2 : $key) . '::' . error($key);
    echo @$GLOBALS['conf_error_prefix'];
    if (is_array($GLOBALS['conf_errormsg']) && array_key_exists($str, $GLOBALS['conf_errormsg']))
    {
//      echo htmlspecialchars ($GLOBALS['conf_errormsg'][$str]);
      echo $GLOBALS['conf_errormsg'][$str];
    }
    else
    {
      echo htmlspecialchars ($str);
    }
    echo @$GLOBALS['conf_error_suffix'];
  }
}


function fatal_error ($err, $msg = '', $msg2 = '')
{
  if (!function_exists($GLOBALS['error_inc_fatal_error_handler']))
  {
    echo '<html>';
    echo '<body>';
    echo '<h1>fatal error</h1>';
    echo "<h2>{$err}</h2>";
    echo "<p>{$msg}</p>";
    echo "<p>{$msg2}</p>";
    echo '</body>';
    echo '</html>';
    exit;
  }
  call_user_func ($GLOBALS['error_inc_fatal_error_handler'], $err, $msg, $msg2);
}


function premise ($cond, $err, $msg = '')
{
  if ($cond == false)
  {
    fatal_error ($err, $msg);
  }
}


function set_fatal_error_handler ($func)
{
  $GLOBALS['error_inc_fatal_error_handler'] = $func;
}


function set_error ($key, $err)
{
  $GLOBALS['error_inc_errors'][$key] = $err;
}


function set_error_if ($cond, $key, $err)
{
  if ($cond)
  {
    set_error ($key, $err);
    return true;
  }
  return false;
}

?>