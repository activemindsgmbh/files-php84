<?php

function adjustdate (&$y, &$m, &$d)
{
  if ($m == 0 || $m > 12)
  {
    $m = 12;
  }
  switch ($m)
  {
    case 2:
      if ($d > 29 && isleapyear($y))
      {
        $d = 29;
      }
      else if ($d > 28)
      {
        $d = 28;
      }
      break;
    case 4:
    case 6:
    case 9:
    case 11:
      if ($d > 30)
      {
        $d = 30;
      }
      break;
    default:
      if ($d > 31)
      {
        $d = 31;
      }
  }
  return ($y * 10000 + $m * 100 + $d);
}


function advance_date ($date, $interval, $stop/*, $debug = false*/)
{
//if ($debug) echo "advance_date ($date, $interval, $stop, true)\n";
  if (!isuint($interval) || $interval < 1 || !isuint($date) || !isuint($stop))
  {
//if ($debug && !isuint($interval)) echo "!isuint(interval = $interval)\n";
//if ($debug && $interval < 1) echo "interval = $interval < 1\n";
//if ($debug && !isuint($date)) echo "!isuint(date = $date)\n";
//if ($debug && !isuint($stop)) echo "!isuint(stop = $stop)\n";
    return 0;
  }
  if ($date > $stop)
  {
//if ($debug) echo "{$date} > {$stop}\n";
    return $date;
  }
  if ($interval == 1)
  {
    $y = floor ($stop / 10000);
    $m = floor ($stop / 100) % 100;
  }
  else
  {
    $y = floor ($date / 10000);
    $m = floor ($date / 100) % 100;
  }
  $d = $date % 100;
  $ymd = fixdate ($y, $m, $d);
//if ($debug) echo "y = {$y}\n";
//if ($debug) echo "m = {$m}\n";
//if ($debug) echo "d = {$d}\n";
//if ($debug) echo "ymd = {$ymd}\n";
  while ($ymd <= $stop)
  {
//if ($debug) echo "loop\n";
    $m += $interval;
    while ($m > 12)
    {
      $m -= 12;
      ++$y;
    }
    $ymd = fixdate ($y, $m, $d);
//if ($debug) echo "y = {$y}\n";
//if ($debug) echo "m = {$m}\n";
//if ($debug) echo "d = {$d}\n";
//if ($debug) echo "ymd = {$ymd}\n";
  }
  return $ymd;
}


function fixdate ($y, $m, $d)
{
  adjustdate ($y, $m, $d);
  return ($y * 10000 + $m * 100 + $d);
}


function isleapyear ($y)
{
  return (($y % 4) == 0 && ($y % 100) != 0 || ($y % 400) == 0);
}

?>