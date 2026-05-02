<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }
$db = new PDO('sqlite:/var/www/html/MyData/data.db');

$id = $_GET['id'] ?? null;
if (!$id) exit("Geen cliënt geselecteerd.");

// Haal cliënt info
$client = $db->prepare("SELECT * FROM clients WHERE id = ?");
$client->execute([$id]);
$clientInfo = $client->fetch();

// Haal alle resultaten gegroepeerd per stap
$results = $db->prepare("SELECT r.*, c.label, c.type FROM project_results r JOIN config c ON r.field_name = c.field_name WHERE r.client_id = ? ORDER BY c.id ASC");
$results->execute([$id]);
$data = [];
while ($row = $results->fetch()) {
    $data[$row['step_name']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Rapport - <?= htmlspecialchars($clientInfo['name']) ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; line-height: 1.6; background: #fff; }
        .header-print { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .step-block { margin-bottom: 30px; page-break-inside: avoid; }
        .step-title { background: #f4f4f4; padding: 5px 10px; border-left: 5px solid #dc3545; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .data-row { display: flex; border-bottom: 1px solid #eee; padding: 5px 0; }
        .label { width: 40%; font-weight: bold; color: #666; }
        .value { width: 60%; }
        img { max-width: 200px; border-radius: 5px; margin-top: 5px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding:10px 20px; background:#dc3545; color:white; border:none; border-radius:5px; cursor:pointer;">🖨️ Print / Opslaan als PDF</button>
        <a href="reports_list.php" style="margin-left: 10px;">Terug naar lijst</a>
    </div>

    <div class="header-print">
        <div>
            <h1>Project Rapport</h1>
            <p><strong>Cliënt:</strong> <?= htmlspecialchars($clientInfo['name']) ?></p>
        </div>
        <div style="text-align:right;">
            <p>Datum: <?= date('d-m-Y') ?></p>
            <p>ID: #<?= $id ?></p>
        </div>
    </div>

    <?php foreach ($data as $stepName => $fields): ?>
        <div class="step-block">
            <div class="step-title"><?= htmlspecialchars($stepName) ?></div>
            <?php foreach ($fields as $f): ?>
                <div class="data-row">
                    <div class="label"><?= htmlspecialchars($f['label']) ?></div>
                    <div class="value">
                        <?php if ($f['type'] == 'image'): ?>
                            <img src="show_image.php?img=<?= $f['value'] ?>">
                        <?php elseif ($f['type'] == 'checkbox'): ?>
                            <?= ($f['value'] == 'Ja') ? '✅ Ja' : '❌ Nee' ?>
                        <?php else: ?>
                            <?= htmlspecialchars($f['value']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

</body>
</html>
