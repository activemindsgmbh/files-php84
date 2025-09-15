<?php

set_time_limit (300);

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();


$step = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false))
{
  $domains = array ();
  require_once ('punycode/idna_convert.class.php');
  $IDN = new idna_convert ();
  foreach (preg_split("#[,;: \n\r\t]#", form_input(@$_POST['domains'])) as $domain)
  {
    $domain = $IDN->encode (trim($domain));
    if ($domain !== '' && !array_key_exists($domain, $domains))
    {
      if (mysql_count('domain', 'domain=\'' . mysql_real_escape_string($domain) . '\'') == 0)
      {
        $domains[$domain] = $IDN->decode ($domain);
      }
    }
  }
  $step = 2;
}

require_once ("templates/html/domaincheck_{$step}.html");

?>