<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }
$db = new PDO('sqlite:/var/www/html/MyData/data.db');

$clients = $db->query("SELECT * FROM clients ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapporten</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #e0e6ed; display: flex; justify-content: center; margin:0; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background: #f4f7f9; }
        header { background: #fff; padding: 15px; display: flex; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .content { padding: 20px; }
        .report-card { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: #333; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #dc3545; }
    </style>
</head>
<body>
    <div class="phone-wrapper">
        <header><a href="myapp.php" style="text-decoration:none;">🏠</a><div style="flex:1; text-align:center; font-weight:bold;">RAPPORTEN</div></header>
        <div class="content">
            <?php foreach ($clients as $c): ?>
                <a href="report.php?id=<?= $c['id'] ?>" class="report-card">
                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                    <span style="color:#dc3545; font-size:12px; font-weight:bold;">BEKIJK PDF →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
