<?php
declare(strict_types=1);

require_once(__DIR__ . '/../include/config.inc.php');
require_once(__DIR__ . '/../include/system.inc.php');

system_init();

$query = 'SELECT ' .
         'kunde.anrede, ' .
         'kunde.kundennummer, ' .
         'kunde.name, ' .
         'kunde.vorname, ' .
         'kunde.firma, ' .
         'kunde.strasse, ' .
         'kunde.plz, ' .
         'kunde.ort, ' .
         'kunde.land, ' .
         'kunde.status ' .
         'FROM kunde ' .
         'ORDER BY kunde.name, kunde.vorname';

$result = db_query($query);
require_once(__DIR__ . '/templates/html/kunden.html');
