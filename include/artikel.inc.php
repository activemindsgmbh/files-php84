<?php

function read_artikel ($clause)
{
  $clause = (string)trim ($clause);
  if ($clause === '' || $clause === '0' || isint($clause) && !isuint($clause))
  {
    return NULL;
  }
  $query  = 'SELECT';
  $query .= ' artikel.artikelnummer';
  $query .= ',artikel.domainreg';
  $query .= ',artikel.domains';
  $query .= ',artikel.fibukonto';
  $query .= ',artikel.id';
  $query .= ',artikel.intervall';
  $query .= ',artikel.kurztext';
  $query .= ',artikel.langtext';
  $query .= ',artikel.preis';
  $query .= ',artikel.setup';
  $query .= ',artikel.textanzeige';
  $query .= ',artikel.umsatzsteuer';
  $query .= ' FROM artikel';
  $query .= ' WHERE ' . (isuint($clause) ? 'artikel.id=' : '') . $clause;
  return mysql_query_object ($query);
}

?>