<?php
session_start();
// Alleen inlogde mensen mogen dit zien
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$users = $db->query("SELECT id, username FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikerslijst</title>
    <style>
        body { font-family: sans-serif; background: #e0e6ed; padding: 20px; display: flex; justify-content: center; }
        .card { background: white; padding: 20px; border-radius: 15px; width: 100%; max-width: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        .back-btn { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <a href="myapp.php" class="back-btn">← Terug naar App</a>
        <h2>Geregistreerde Gebruikers</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Gebruikersnaam</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
