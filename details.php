<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$current_step = $_GET['step'] ?? 'Schouwing';

// 1. Haal configuratie en client op
$stmtConfig = $db->prepare("SELECT * FROM config WHERE step_name = ?");
$stmtConfig->execute([$current_step]);
$fields = $stmtConfig->fetchAll();

$stmtClient = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmtClient->execute([$id]);
$client = $stmtClient->fetch();

// 2. Verwerken van de invoer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_dynamic'])) {
    foreach ($fields as $field) {
        $fieldName = $field['field_name'];
        $val = "";

        if ($field['type'] == 'image' && !empty($_FILES[$fieldName]['name'])) {
            // Foto afhandelen
            $val = time() . '_' . basename($_FILES[$fieldName]['name']);
            move_uploaded_file($_FILES[$fieldName]['tmp_name'], "/var/www/html/MyData/uploads/" . $val);
        } else {
            // Tekst, getal of checkbox afhandelen
            $val = $_POST[$fieldName] ?? '';
        }

        if ($val !== "") {
            // Verwijder oud antwoord voor dit veld (overschrijven)
            $db->prepare("DELETE FROM project_results WHERE client_id = ? AND field_name = ?")
               ->execute([$id, $fieldName]);

            // Sla nieuw antwoord op
            $stmt = $db->prepare("INSERT INTO project_results (client_id, field_name, value, step_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $fieldName, $val, $current_step]);
        }
    }
    header("Location: details.php?id=$id&step=$current_step&success=1");
    exit;
}

// 3. Haal reeds ingevulde data op om te tonen in de velden
$stmtData = $db->prepare("SELECT field_name, value FROM project_results WHERE client_id = ?");
$stmtData->execute([$id]);
$savedData = $stmtData->fetchAll(PDO::FETCH_KEY_PAIR); // Maakt een handig lijstje ['veldnaam' => 'waarde']
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow - <?= htmlspecialchars($current_step) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #e0e6ed; display: flex; justify-content: center; margin:0; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background: #f4f7f9; }
        header { background: #fff; padding: 15px; display: flex; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .content { padding: 20px; }
        .form-group { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #555; font-size: 14px; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn-save { width: 100%; background: #28a745; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="phone-wrapper">
        <header>
            <a href="projects.php" style="text-decoration:none;">🏠</a>
            <div style="flex:1; text-align:center; font-weight:bold;"><?= strtoupper($current_step) ?></div>
        </header>

        <div class="content">
            <h2 style="margin-top:0;"><?= htmlspecialchars($client['name']) ?></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <?php foreach ($fields as $field): 
					$currentVal = $savedData[$field['field_name']] ?? ''; 
				?>
					<div class="form-group">
						<label><?= htmlspecialchars($field['label']) ?></label>
						
						<?php if ($field['type'] == 'image'): ?>
							<?php if($currentVal): ?>
								<p style="font-size:12px; color:green;">✅ Foto aanwezig: <?= $currentVal ?></p>
							<?php endif; ?>
							<input type="file" name="<?= $field['field_name'] ?>" accept="image/*">
							
						<?php elseif ($field['type'] == 'number'): ?>
							<input type="number" name="<?= $field['field_name'] ?>" value="<?= htmlspecialchars($currentVal) ?>">
							
						<?php elseif ($field['type'] == 'checkbox'): ?>
							<input type="checkbox" name="<?= $field['field_name'] ?>" value="Ja" <?= ($currentVal == 'Ja') ? 'checked' : '' ?>> Ja
							
						<?php else: ?>
							<input type="text" name="<?= $field['field_name'] ?>" value="<?= htmlspecialchars($currentVal) ?>">
							
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

                <button type="submit" name="save_dynamic" class="btn-save">STAP VOLTOOIEN</button>
            </form>
        </div>
    </div>
</body>
</html>
