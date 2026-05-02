<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$current_step = $_GET['step'] ?? 'Schouwing';

// 1. Haal alle unieke stappen op voor het menu
$stmtSteps = $db->query("SELECT DISTINCT step_name FROM config ORDER BY id ASC");
$allSteps = $stmtSteps->fetchAll(PDO::FETCH_COLUMN);

// 2. Haal configuratie voor huidige stap en client op
$stmtConfig = $db->prepare("SELECT * FROM config WHERE step_name = ?");
$stmtConfig->execute([$current_step]);
$fields = $stmtConfig->fetchAll();

$stmtClient = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmtClient->execute([$id]);
$client = $stmtClient->fetch();

// 3. Verwerken van de invoer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_dynamic'])) {
    foreach ($fields as $field) {
        $fieldName = $field['field_name'];
        $val = "";

        if ($field['type'] == 'image' && !empty($_FILES[$fieldName]['name'])) {
            $val = time() . '_' . basename($_FILES[$fieldName]['name']);
            move_uploaded_file($_FILES[$fieldName]['tmp_name'], "/var/www/html/MyData/uploads/" . $val);
        } else {
            $val = $_POST[$fieldName] ?? '';
        }

        if ($val !== "") {
            $db->prepare("DELETE FROM project_results WHERE client_id = ? AND field_name = ?")
               ->execute([$id, $fieldName]);

            $stmt = $db->prepare("INSERT INTO project_results (client_id, field_name, value, step_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $fieldName, $val, $current_step]);
        }
    }
    header("Location: details.php?id=$id&step=$current_step&success=1");
    exit;
}

// 4. Haal reeds ingevulde data op
$stmtData = $db->prepare("SELECT field_name, value FROM project_results WHERE client_id = ?");
$stmtData->execute([$id]);
$savedData = $stmtData->fetchAll(PDO::FETCH_KEY_PAIR);

// 5. Bereken voortgang
$totalFields = count($fields);
$filledFields = 0;
foreach ($fields as $field) {
    if (!empty($savedData[$field['field_name']])) {
        $filledFields++;
    }
}
$percentage = ($totalFields > 0) ? round(($filledFields / $totalFields) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Workflow - <?= htmlspecialchars($current_step) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        header { background-color: #ffffff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; text-decoration: none; font-weight: bold; font-size: 14px; }
        .content { padding: 20px; }
        .nav-steps { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 10px; -webkit-overflow-scrolling: touch; }
        .step-link { text-decoration: none; padding: 8px 18px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-group { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #555; font-size: 14px; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn-save { width: 100%; background: #28a745; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <header>
            <a href="projects.php" class="header-btn">🏠 Home</a>
            <div style="font-weight: 800;"><?= strtoupper($current_step) ?></div>
            <div style="width: 60px;"></div>
        </header>

        <div class="content">
            <!-- Voortgangsbalk -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #555;">
                    <span>VOORTGANG: <?= $current_step ?></span>
                    <span><?= $percentage ?>%</span>
                </div>
                <div style="width: 100%; background: #ddd; height: 10px; border-radius: 5px; overflow: hidden;">
                    <div style="width: <?= $percentage ?>%; background: #28a745; height: 100%; transition: width 0.5s;"></div>
                </div>
            </div>

            <!-- Stappen Navigatie Menu -->
            <div class="nav-steps">
                <?php foreach ($allSteps as $s): 
                    $active = ($s == $current_step) ? 'background: #007bff; color: white;' : 'background: #fff; color: #555;';
                ?>
                    <a href="details.php?id=<?= $id ?>&step=<?= urlencode($s) ?>" class="step-link" style="<?= $active ?>">
                        <?= strtoupper(htmlspecialchars($s)) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <h2 style="margin-top:0;"><?= htmlspecialchars($client['name']) ?></h2>
            
            <?php if(isset($_GET['success'])): ?>
                <p style="color: green; font-weight: bold; font-size: 14px;">✅ Gegevens opgeslagen!</p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <?php foreach ($fields as $field): 
                    $currentVal = $savedData[$field['field_name']] ?? ''; 
                ?>
                    <div class="form-group">
                        <label><?= htmlspecialchars($field['label']) ?></label>
                        
                        <?php if ($field['type'] == 'image'): ?>
							<?php if($currentVal): ?>
								<!-- Hier tonen we de foto als hij bestaat -->
								<div style="margin-bottom: 10px; text-align: center; background: #eee; border-radius: 10px; padding: 10px;">
									<img src="show_image.php?img=<?= htmlspecialchars($currentVal) ?>" 
										 style="max-width: 100%; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
									<p style="font-size:11px; color:#666; margin-top:5px;">Huidige foto: <?= htmlspecialchars($currentVal) ?></p>
								</div>
							<?php endif; ?>
							
							<label style="font-size: 12px; color: #007bff;">Nieuwe foto uploaden:</label>
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
