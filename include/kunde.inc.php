<?php

function read_kunde ($clause)
{
  $clause = (string)trim ($clause);
  if ($clause === '' || $clause === '0' || isint($clause) && !isuint($clause))
  {
    return NULL;
  }
  $query  = 'SELECT';
  $query .= ' kunde.anrede';
  $query .= ',kunde.anschrift';
  $query .= ',kunde.ansprechpartner';
  $query .= ',kunde.bankleitzahl';
  $query .= ',kunde.domainzusammenfassung';
  $query .= ',kunde.email';
  $query .= ',kunde.kommentar';
  $query .= ',kunde.kontoinhaber';
  $query .= ',kunde.kontonummer';
  $query .= ',kunde.kundennummer';
  $query .= ',kunde.land';
  $query .= ',land.code AS land_code';
  $query .= ',land.name AS land_name';
  $query .= ',kunde.mobil';
  $query .= ',kunde.name';
  $query .= ',kunde.ort';
  $query .= ',kunde.postleitzahl';
  $query .= ',kunde.rechnungsemail';
  $query .= ',kunde.strasse';
  $query .= ',kunde.telefax';
  $query .= ',kunde.telefon';
  $query .= ',kunde.telefon2';
  $query .= ',kunde.umsatzsteuerbefreit';
  $query .= ',kunde.umsatzsteuerid';
  $query .= ',kunde.vorname';
  $query .= ',kunde.waehrung';
  $query .= ',kunde.zahlungsziel';
  $query .= ',kunde.zahlweise';
  $query .= ' FROM kunde';
  $query .= ' LEFT JOIN land ON land.code=kunde.land';
  $query .= ' WHERE ' . (isuint($clause) ? 'kunde.kundennummer=' : '') . $clause;
  return mysql_query_object ($query);
}

?>