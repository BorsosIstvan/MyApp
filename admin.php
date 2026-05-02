<?php
session_start();
if ($_SESSION['user'] !== 'admin') { header("Location: myapp.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Nieuw veld toevoegen
if (isset($_POST['add_config'])) {
    $stmt = $db->prepare("INSERT INTO config (step_name, label, type, field_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['step'], $_POST['label'], $_POST['type'], $_POST['f_name']]);
    header("Location: admin.php"); exit;
}

// 2. Veld verwijderen
if (isset($_GET['del'])) {
    $db->prepare("DELETE FROM config WHERE id = ?")->execute([$_GET['del']]);
    header("Location: admin.php"); exit;
}

$config = $db->query("SELECT * FROM config ORDER BY step_name, id")->fetchAll();

// Cliënt verwijderen via admin
if (isset($_GET['del_client'])) {
    $clientId = $_GET['del_client'];
    // 1. Verwijder alle resultaten/foto-namen uit project_results
    // (Optioneel: je zou hier ook de fysieke foto's kunnen unlinken)
    $db->prepare("DELETE FROM project_results WHERE client_id = ?")->execute([$clientId]);
    // 2. Verwijder de cliënt zelf
    $db->prepare("DELETE FROM clients WHERE id = ?")->execute([$clientId]);
    header("Location: admin.php?success=client_deleted"); exit;
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Config</title>
    <style>
        body { font-family: sans-serif; background: #e0e6ed; padding: 10px; display: flex; justify-content: center; }
        .wrapper { width: 100%; max-width: 500px; background: #f4f7f9; min-height: 100vh; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        header { background: #fff; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: #fff; margin: 15px; padding: 15px; border-radius: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { background: #007bff; color: #fff; border: none; padding: 12px; width: 100%; border-radius: 10px; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; }
        .del-btn { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <a href="myapp.php" style="text-decoration:none;">🏠</a>
        <strong>APP CONFIGURATIE</strong>
        <div style="width:20px;"></div>
    </header>
	<div class="card">
		<h3>Cliënten Beheren</h3>
		<table>
			<tr><th>Naam</th><th>Actie</th></tr>
			<?php 
			$allClients = $db->query("SELECT * FROM clients ORDER BY name ASC")->fetchAll();
			foreach ($allClients as $cli): 
			?>
			<tr>
				<td><?= htmlspecialchars($cli['name']) ?></td>
				<td>
					<a href="admin.php?del_client=<?= $cli['id'] ?>" 
					   class="del-btn" 
					   onclick="return confirm('WEET JE HET ZEKER? Alle data en foto\'s van deze cliënt gaan verloren!')">
					   VERWIJDEREN
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>


    <div class="card">
        <h3>Nieuw veld toevoegen</h3>
        <form method="POST">
            <label>Stap Naam (bijv. CLIËNT, Schouwing)</label>
            <input type="text" name="step" placeholder="Naam van de fase" required>
            
            <label>Veld Label</label>
            <input type="text" name="label" placeholder="Vraag aan de gebruiker" required>
            
            <label>Type invoer</label>
            <select name="type">
                <option value="text">Tekst</option>
                <option value="number">Getal</option>
                <option value="image">Foto Upload</option>
                <option value="checkbox">Vinkje (Ja/Nee)</option>
            </select>

            <label>Database Naam (uniek, geen spaties!)</label>
            <input type="text" name="f_name" placeholder="bijv. foto_dak_1" required>

            <button type="submit" name="add_config" class="btn">VELD TOEVOEGEN</button>
        </form>
    </div>

    <div class="card">
        <h3>Huidige Structuur</h3>
        <table>
            <tr><th>Stap</th><th>Label</th><th>Actie</th></tr>
            <?php foreach ($config as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['step_name']) ?></td>
                <td><?= htmlspecialchars($c['label']) ?></td>
                <td><a href="admin.php?del=<?= $c['id'] ?>" class="del-btn" onclick="return confirm('Veld verwijderen?')">X</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
