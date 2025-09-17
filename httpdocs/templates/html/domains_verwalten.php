<?php require_once('_template_init.php'); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Domain verwalten</title>
    <link rel="stylesheet" type="text/css" href="/default.css">
</head>
<body>
    <?php @include('header.inc.html') ?>

    <h2>Domain verwalten</h2>

    <?php if (isset($domain) && $domain): ?>
        <table>
            <tr>
                <td>Domain:</td>
                <td>
                    <?php safe_echo($domain->domain_name) ?>
                    <?php if (str_starts_with((string)$domain->domain_name, 'xn--')): ?>
                        <br><?php safe_echo($domain->domain_utf8name) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Kunde:</td>
                <td>
                    <?php 
                    if (isset($kunde)):
                        safe_echo($kunde->name);
                        if (!empty($kunde->vorname)) {
                            echo ', ';
                            safe_echo($kunde->vorname);
                        }
                        if (!empty($kunde->firma)) {
                            echo '<br>';
                            safe_echo($kunde->firma);
                        }
                    endif;
                    ?>
                </td>
            </tr>
            <tr>
                <td>Art:</td>
                <td><?php safe_echo($domain->art) ?></td>
            </tr>
            <tr>
                <td>Start:</td>
                <td><?php safe_echo($domain->start) ?></td>
            </tr>
            <tr>
                <td>Ende:</td>
                <td><?php safe_echo($domain->ende) ?></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><?php safe_echo($domain->status) ?></td>
            </tr>
        </table>

        <p>
            <a href="/domains_bearbeiten.php?id=<?php echo safe_url($domain->id) ?>">Bearbeiten</a> |
            <a href="/domains_loeschen.php?id=<?php echo safe_url($domain->id) ?>" onclick="return confirm('Wirklich löschen?')">Löschen</a>
        </p>

        <?php if (is_valid_result($leistungen)): ?>
            <h3>Leistungen</h3>
            <table>
                <tr>
                    <th>Datum</th>
                    <th>Artikel</th>
                    <th>Bezeichnung</th>
                    <th>Preis</th>
                </tr>
                <?php while ($leistung = $leistungen->fetch_object()): ?>
                    <tr>
                        <td><?php safe_echo($leistung->datum) ?></td>
                        <td><?php safe_echo($leistung->artikel) ?></td>
                        <td><?php safe_echo($leistung->bezeichnung) ?></td>
                        <td><?php safe_echo(number_format($leistung->preis, 2, ',', '.')) ?> €</td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <p>Domain nicht gefunden.</p>
    <?php endif; ?>

    <p><a href="/domains.php">Zurück zur Übersicht</a></p>
</body>
</html>
