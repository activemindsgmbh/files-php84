<?php

$GLOBALS['conf_umsatzsteuer'] = 19.0;

$GLOBALS['conf_default_waehrung']     = 'EUR';
$GLOBALS['conf_default_zahlungsziel'] = 7;

$GLOBALS['conf_email_return_path'] = 'buchhaltung@activeminds.de';

$GLOBALS['conf_min_rechnung_id'] = 11692;

$GLOBALS['conf_logo']                = '/var/www/vhosts/amfakt.activeminds.net/data/logo.png';
$GLOBALS['conf_dir_mahnung']         = '/var/www/vhosts/amfakt.activeminds.net/httpdocs/pdf/mahnungen';
$GLOBALS['conf_dir_rechnung_intern'] = '/var/www/vhosts/amfakt.activeminds.net/httpdocs/pdf/rechnungen/intern';
$GLOBALS['conf_dir_rechnung_kunde']  = '/var/www/vhosts/amfakt.activeminds.net/httpdocs/pdf/rechnungen/kunde';
$GLOBALS['conf_url_mahnung']         = '/pdf/mahnungen';
$GLOBALS['conf_url_rechnung_intern'] = '/pdf/rechnungen/intern';
$GLOBALS['conf_url_rechnung_kunde']  = '/pdf/rechnungen/kunde';

$GLOBALS['conf_mysql_db']   = 'amfakt';
$GLOBALS['conf_mysql_host'] = 'localhost';
$GLOBALS['conf_mysql_pass'] = 'iB4y5^2nqyObQqjh';
$GLOBALS['conf_mysql_user'] = 'amfakt';


$GLOBALS['conf_error_prefix'] = '<div class="errormsg">';
$GLOBALS['conf_error_suffix'] = '</div>';

$GLOBALS['conf_errormsg'] = array
(
  'artikel.artikelnummer::EMPTY'      => 'Artikelnummer fehlt',
  'artikel.artikelnummer::INVALID'    => 'Artikelnummer ist ung&uuml;ltig',
  'artikel.artikelnummer::NOT UNIQUE' => 'Artikelnummer ist bereits vergeben',
  'artikel.domains::INVALID'          => 'Domainanzahl ist ung&uuml;ltig',
  'artikel.fibukonto::EMPTY'          => 'Fibu Kontonummer fehlt',
  'artikel.fibukonto::INVALID'        => 'Fibu Kontonummer ist ung&uuml;ltig',
  'artikel.intervall::INVALID'        => 'Abrechnungsintervall ist ung&uuml;ltig',
  'artikel.kurztext::EMPTY'           => 'Artikeltext fehlt',
  'artikel.preis::EMPTY'              => 'Preis fehlt',
  'artikel.preis::INVALID'            => 'Preis ist ung&uuml;ltig',
  'artikel.umsatzsteuer::INVALID'     => 'Umsatzsteuer ist ung&uuml;ltig',

  'artikelupdate.preis::EMPTY'        => 'Preis fehlt',
  'artikelupdate.preis::INVALID'      => 'Preis ist ung&uuml;ltig',
  'artikelupdate.termin::EMPTY'       => 'Datum fehlt',
  'artikelupdate.termin::INVALID'     => 'Datum ist ung&uuml;ltig',
  'artikelupdate.termin::NOT UNIQUE'  => 'Termin ist bereits vergeben',

  'konto.name::NOT UNIQUE'            => 'Es existiert bereits ein Bankkonto mit diesem Namen',

	'kostenstelle.comment::EMPTY'       => 'Beschreibung fehlt',
	'kostenstelle.name::EMPTY'          => 'Kostenstelle fehlt',

  'kunde.anrede::EMPTY'               => 'Anrede fehlt',
  'kunde.anrede::INVALID'             => 'Anrede ist ung&uuml;ltig',
  'kunde.bankleitzahl::EMPTY'         => 'Bankleitzahl fehlt',
  'kunde.bankleitzahl::INVALID'       => 'Bankleitzahl ist ung&uuml;ltig',
  'kunde.email::INVALID'              => 'E-Mailadresse ist ung&uuml;ltig',
  'kunde.kontoinhaber::EMPTY'         => 'Kontoinhaber fehlt',
  'kunde.kontonummer::EMPTY'          => 'Kontonummer fehlt',
  'kunde.kontonummer::INVALID'        => 'Kontonummer ist ung&uuml;ltig',
  'kunde.kundennummer::EMPTY'         => 'Kundennummer fehlt',
  'kunde.kundennummer::INVALID'       => 'Kundennummer ist ung&uuml;ltig',
  'kunde.kundennummer::NOT UNIQUE'    => 'Kundennummer ist bereits vergeben',
  'kunde.land::EMPTY'                 => 'Land fehlt',
  'kunde.name::EMPTY'                 => 'Nachname oder Firmenname fehlt',
  'kunde.ort::EMPTY'                  => 'Ort fehlt',
  'kunde.postleitzahl::EMPTY'         => 'Postleitzahl fehlt',
  'kunde.rechnungsemail::INVALID'     => 'E-Mailadresse ist ung&uuml;ltig',
  'kunde.strasse::EMPTY'              => 'Stra&szlig;e fehlt',
  'kunde.waehrung::EMPTY'             => 'W&auml;hrung fehlt',
  'kunde.waehrung::INVALID'           => 'W&auml;hrung ist ung&uuml;ltig',
  'kunde.zahlungsziel::EMPTY'         => 'Zahlungsziel fehlt',
  'kunde.zahlungsziel::INVALID'       => 'Zahlungsziel ist ung&uuml;ltig',
  'kunde.zahlweise::EMPTY'            => 'Zahlweise fehlt',
  'kunde.zahlweise::INVALID'          => 'Zahlweise ist ung&uuml;ltig',

  'kundenpreis.bis::IMPLAUSIBLE'      => 'Datum ist unsinnig',
  'kundenpreis.bis::INVALID'          => 'Datum ist ung&uuml;ltig',
  'kundenpreis.preis::EMPTY'          => 'Preis fehlt',
  'kundenpreis.preis::INVALID'        => 'Preis ist ung&uuml;ltig',
  'kundenpreis.von::INVALID'          => 'Datum ist ung&uuml;ltig',
);

?>
