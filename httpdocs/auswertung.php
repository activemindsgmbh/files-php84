<?php
require_once ('.inc.php');
require_once ('system.inc.php');
system_init ();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style type="text/css">
			table {
				border-collapse: collapse;
			}
			td, th {
				border: 1px solid black;
				font-family: Courier New;
				font-size: 9pt;
			}
			th {
				background-color: #EEE;
				text-align: left;
			}
			.bold {
				font-weight: bold;
			}
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
		</style>
	</head>
	<body>
		<table cellpadding="4" cellspacing="0">
<?php
$query  = 'SELECT kundennummer,name,vorname FROM kunde ORDER BY kundennummer';
$kunden = safe_mysql_query ($query);
while ($kunde = mysql_fetch_object($kunden))
{
	$query = 'SELECT';
	$query .= ' artikel.artikelnummer';
	$query .= ',artikel.intervall';
	$query .= ',artikel.kurztext';
	$query .= ',domain.domain';
	$query .= ',DATE_FORMAT(leistung.abgerechnet,\'%d.%m.%Y\') AS abgerechnet';
	$query .= ',leistung.anzahl';
	$query .= ',leistung.artikel';
	$query .= ',leistung.endedatum';
	$query .= ',leistung.preis';
	$query .= ',DATE_FORMAT(leistung.referenzdatum,\'%d.%m.%Y\') AS referenzdatum';
	$query .= ' FROM leistung';
	$query .= ' LEFT JOIN artikel ON artikel.id=leistung.artikel';
	$query .= ' LEFT JOIN domain ON domain.id=leistung.domain';
	$query .= ' WHERE leistung.kunde=' . (int)$kunde->kundennummer;
	$query .= ' AND leistung.preis>0';
	$query .= ' AND artikel.intervall>0';
//	$query .= ' AND (ISNULL(leistung.abgerechnet) OR DATE_ADD(leistung.abgerechnet,INTERVAL artikel.intervall MONTH)<DATE_SUB(CURDATE(),INTERVAL 6 MONTH))';
	$query .= ' AND (DATE_ADD(IF(ISNULL(leistung.abgerechnet),leistung.referenzdatum,leistung.abgerechnet),INTERVAL artikel.intervall MONTH)<DATE_SUB(CURDATE(),INTERVAL 6 MONTH) OR NOT ISNULL(leistung.abgerechnet) AND leistung.abgerechnet>CURDATE())';
	$query .= ' AND ISNULL(leistung.endedatum)';
	$query .= ' ORDER BY artikel.artikelnummer';
	$leistungen = safe_mysql_query ($query);
	$str = '';
	while ($leistung = mysql_fetch_object($leistungen))
	{
		$str .= '<tr>';
		$str .= '<td>' . htmlspecialchars($leistung->artikelnummer) . '</td>';
		$str .= '<td>' . htmlspecialchars($leistung->kurztext) . '</td>';
		$str .= '<td>' . htmlspecialchars($leistung->domain) . '</td>';
		$str .= '<td class="right">' . htmlspecialchars(number_format($leistung->anzahl,2,',','.')) . '</td>';
		$str .= '<td class="right">' . htmlspecialchars(number_format($leistung->preis,2,',','.')) . '</td>';
		$str .= '<td class="center">' . htmlspecialchars($leistung->referenzdatum) . '</td>';
		$str .= '<td class="right">' . htmlspecialchars($leistung->intervall) . '</td>';
		$str .= '<td class="center">' . htmlspecialchars($leistung->abgerechnet) . '</td>';
		$str .= '</tr>';
	}
	if ($str !== '')
	{
?>
			<tr>
				<td class="bold" colspan="8" style="border:none">
					<br>
					<?php echo htmlspecialchars($kunde->kundennummer)?>
					<a href="/kundendaten.php?kunde=<?php echo urlencode($kunde->kundennummer)?>"><?php echo htmlspecialchars($kunde->name);if($kunde->vorname)echo', '.htmlspecialchars($kunde->vorname)?></a>
				</td>
			</tr>
			<tr>
				<th>Artikelnummer</th>
				<th>Artikel</th>
				<th>Domain</th>
				<th>Anzahl</th>
				<th>Preis</th>
				<th>Referenzdatum</th>
				<th>Intervall</th>
				<th>Abgerechnet</th>
			</tr>
<?php
		echo $str;
	}
}
?>
		</table>
	</body>
</html>