<?php

require_once ('.inc.php');
require_once ('system.inc.php');

system_init ();

$rrechnungen = NULL;

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && check_referer(false)))
{
  $_POST['datum'] = date ('d.m.Y');
}
else
{
  $datum = trim (form_input(@$_POST['datum']));
  if ($datum === '')
  {
    set_error ('datum', 'EMPTY');
  }
  else if (($datum = parse_date($datum)) < 0)
  {
    set_error ('datum', 'INVALID');
  }
  if (!error())
  {
    $query  = 'SELECT';
    $query .= ' kunde.kundennummer';
    $query .= ',kunde.name';
    $query .= ',kunde.vorname';
    $query .= ',kunde.waehrung';


    $sum  = 'IF(ISNULL(leistung.abgerechnet),leistung.anzahl*leistung.setup,0)+';
    $sum .= 'leistung.anzahl*leistung.preis*IF(artikel.intervall=0,IF(ISNULL(leistung.abgerechnet),1,0),FLOOR((';
    $sum .= "(FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))+0)/10000)*1200+((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))+0)%10000))";
    $sum .= "-(FLOOR((IF(ISNULL(leistung.abgerechnet),leistung.referenzdatum,leistung.abgerechnet)+0)/10000)*1200+((IF(ISNULL(leistung.abgerechnet),leistung.referenzdatum,leistung.abgerechnet)+0)%10000))";
    $sum .= '+IF(ISNULL(leistung.abgerechnet),100*artikel.intervall,0)';
    $sum .= ')/(100*artikel.intervall)))';


    $query .= ",SUM(IF(($sum)!=0.0,1,0)) AS rows";
    $query .= ",SUM($sum) AS netto";

/*
    $query .= ',SUM(leistung.anzahl*leistung.preis*IF(artikel.intervall=0,IF(ISNULL(leistung.abgerechnet),1,0),FLOOR((';
    $query .= "(FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))+0)/10000)*1200+((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))+0)%10000))";
    $query .= "-(FLOOR((IF(ISNULL(leistung.abgerechnet),leistung.referenzdatum,leistung.abgerechnet)+0)/10000)*1200+((IF(ISNULL(leistung.abgerechnet),leistung.referenzdatum,leistung.abgerechnet)+0)%10000))";
    $query .= '+IF(ISNULL(leistung.abgerechnet),100*artikel.intervall,0)';
    $query .= ')/(100*artikel.intervall)))) AS netto';
*/
    

    
//    $query .= ",SUM(leistung.anzahl*leistung.preis*(1+FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))-(leistung.referenzdatum+0))/(100*artikel.intervall))-IF(ISNULL(leistung.abgerechnet),0,FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))-(leistung.abgerechnet+0))/(100*artikel.intervall))))) AS netto";
    $query .= ' FROM leistung';
    $query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
    $query .= ' LEFT JOIN kunde ON kunde.kundennummer=leistung.kunde';
    $query .= ' WHERE leistung.referenzdatum<=' . (int)$datum;
//    $query .= ' AND (ISNULL(leistung.abgerechnet) OR leistung.abgerechnet<' . (int)$datum . ' AND artikel.intervall!=0)';
    $query .= ' AND (ISNULL(leistung.abgerechnet) OR leistung.abgerechnet<' . (int)$datum . ')';
    $query .= ' AND (ISNULL(leistung.endedatum) OR ISNULL(leistung.abgerechnet) OR leistung.endedatum>leistung.abgerechnet)';
    $query .= ' GROUP BY kunde.kundennummer';
    $query .= ' HAVING rows>0';
    $query .= ' ORDER BY kunde.name,kunde.vorname,kunde.kundennummer';
    $rrechnungen = safe_mysql_query ($query);
       echo $query;
  }
}



//1+FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))-(leistung.referenzdatum+0))/(100*artikel.intervall))-IF(ISNULL(leistung.abgerechnet),0,FLOOR((IF(ISNULL(leistung.endedatum),{$datum},LEAST({$datum},leistung.endedatum))-(leistung.abgerechnet+0))/(100*artikel.intervall)))

require_once ("templates/html/rechnungslauf.html");

?>