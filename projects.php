<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Haal configuratie voor cliënt-gegevens op
$stmtConfig = $db->query("SELECT * FROM config WHERE step_name = 'CLIËNT'");
$clientFields = $stmtConfig->fetchAll();

// Nieuwe cliënt aanmaken
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_client'])) {
    // Maak eerst een lege cliënt aan om een ID te krijgen
    $db->exec("INSERT INTO clients (name) VALUES ('Nieuwe Cliënt')");
    $newId = $db->lastInsertId();
    $mainName = "";

    foreach ($clientFields as $field) {
        $val = $_POST[$field['field_name']] ?? '';
        if ($field['field_name'] == 'client_name') $mainName = $val;

        $stmt = $db->prepare("INSERT INTO project_results (client_id, field_name, value, step_name) VALUES (?, ?, ?, 'CLIËNT')");
        $stmt->execute([$newId, $field['field_name'], $val]);
    }
    
    // Update de hoofdnaam voor de lijst
    $db->prepare("UPDATE clients SET name = ? WHERE id = ?")->execute([$mainName, $newId]);
    header("Location: projects.php");
    exit();
}

$clients = $db->query("SELECT * FROM clients ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Projecten - Clients</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        
        header { background: #fff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; font-weight: 600; text-decoration: none; font-size: 14px; }
        .header-title { font-size: 16px; font-weight: 800; color: #222; text-transform: uppercase; }

        .content { padding: 20px; }
        
        /* Input stijl */
        .add-form { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
        input { width: 100%; padding: 12px; margin-bottom: 10px; border-radius: 10px; border: 1px solid #ddd; box-sizing: border-box; font-size: 16px; }
        .btn-add { width: 100%; background: #007bff; color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }

        /* Clienten Lijst */
        .client-card { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-left: 5px solid #007bff; }
        .client-info { font-weight: 600; color: #333; }
        .btn-view { background: #eef2f7; color: #007bff; text-decoration: none; padding: 5px 12px; border-radius: 8px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <header>
            <a href="myapp.php" class="header-btn">🏠 Home</a>
            <div class="header-title">PROJECTEN</div>
            <div style="width: 60px;"></div> <!-- Spacer voor balans -->
        </header>

		<div class="content">
			<!-- Zoekbalk -->
			<div style="margin-bottom: 15px;">
				<input type="text" id="searchInput" placeholder="🔍 Zoek cliënt..." 
					   style="margin-bottom: 0; border: 2px solid #007bff;">
			</div>

			<!-- Formulier om client toe te voegen -->
			<!-- Zoek dit blok in projects.php -->
			<?php if ($_SESSION['user'] === 'admin'): ?>
				<div class="add-form">
					<h3>Nieuwe Cliënt</h3>
					<form method="POST">
						<?php foreach ($clientFields as $field): ?>
							<label style="font-size:12px; font-weight:bold;"><?= $field['label'] ?></label>
							<input type="<?= ($field['type'] == 'number') ? 'number' : 'text' ?>" name="<?= $field['field_name'] ?>" required>
						<?php endforeach; ?>
						<button type="submit" name="add_client" class="btn-add">OPSLAAN</button>
					</form>
				</div>
			<?php endif; ?>


			<!-- Lijst van clienten -->
			<div class="list" id="clientList">
				<?php foreach ($clients as $client): ?>
					<div class="client-card" data-name="<?= strtolower(htmlspecialchars($client['name'])) ?>">
						<span class="client-info"><?= htmlspecialchars($client['name']) ?></span>
						<a href="details.php?id=<?= $client['id'] ?>" class="btn-view">OPEN</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<script>
			// JavaScript voor live filteren
			document.getElementById('searchInput').addEventListener('keyup', function() {
				let filter = this.value.toLowerCase();
				let cards = document.querySelectorAll('.client-card');

				cards.forEach(card => {
					let name = card.getAttribute('data-name');
					if (name.includes(filter)) {
						card.style.display = "flex";
					} else {
						card.style.display = "none";
					}
				});
			});
		</script>

    </div>

</body>
</html>
