<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$current_step = $_GET['step'] ?? 'Schouwing';

$stmtSteps = $db->query("SELECT DISTINCT step_name FROM config ORDER BY id ASC");
$allSteps = $stmtSteps->fetchAll(PDO::FETCH_COLUMN);

$stmtConfig = $db->prepare("SELECT * FROM config WHERE step_name = ?");
$stmtConfig->execute([$current_step]);
$fields = $stmtConfig->fetchAll();

$stmtClient = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmtClient->execute([$id]);
$client = $stmtClient->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_dynamic'])) {
    foreach ($fields as $field) {
        $fieldName = $field['field_name'];
        $val = "";
        if ($field['type'] == 'image' && !empty($_FILES[$fieldName]['name'])) {
            $val = time() . '_' . basename($_FILES[$fieldName]['name']);
            move_uploaded_file($_FILES[$fieldName]['tmp_name'], "/var/www/html/MyData/uploads/" . $val);
        } else { $val = $_POST[$fieldName] ?? ''; }

        if ($val !== "") {
            $db->prepare("DELETE FROM project_results WHERE client_id = ? AND field_name = ?")->execute([$id, $fieldName]);
            $stmt = $db->prepare("INSERT INTO project_results (client_id, field_name, value, step_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $fieldName, $val, $current_step]);
        }
    }
    header("Location: details.php?id=$id&step=$current_step&success=1");
    exit;
}

$stmtData = $db->prepare("SELECT field_name, value FROM project_results WHERE client_id = ?");
$stmtData->execute([$id]);
$savedData = $stmtData->fetchAll(PDO::FETCH_KEY_PAIR);

$totalFields = count($fields);
$filledFields = 0;
foreach ($fields as $field) { if (!empty($savedData[$field['field_name']])) $filledFields++; }
$percentage = ($totalFields > 0) ? round(($filledFields / $totalFields) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Workflow - <?= htmlspecialchars($current_step) ?></title>
    <!-- JUISTE BIBLIOTHEEK LINK -->
    <script src="https://unpkg.com"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; display: flex; flex-direction: column; }
        header { background: #fff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .content { padding: 20px; }
        .nav-steps { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 10px; }
        .step-link { text-decoration: none; padding: 8px 18px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-group { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #555; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn-save { width: 100%; background: #28a745; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; }
        #scanner-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; flex-direction:column; align-items:center; justify-content:center; }
    </style>
</head>
<body>

    <div id="scanner-modal">
        <div id="reader" style="width: 300px; min-height: 300px; background: #000; border-radius: 10px;"></div>
        <button type="button" onclick="stopScanner()" style="margin-top:20px; padding:12px 25px; background:#dc3545; color:white; border:none; border-radius:10px;">STOP</button>
    </div>

    <div class="phone-wrapper">
        <header>
            <a href="projects.php" style="background:#f0f3f7; padding:8px 12px; border-radius:10px; text-decoration:none; color:#007bff; font-weight:bold;">🏠 Home</a>
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

            <!-- Menu -->
            <div class="nav-steps">
                <?php foreach ($allSteps as $s): if ($s === 'CLIËNT') continue;
                    $active = ($s == $current_step) ? 'background: #007bff; color: white;' : 'background: #fff; color: #555;';
                ?>
                    <a href="details.php?id=<?= $id ?>&step=<?= urlencode($s) ?>" class="step-link" style="<?= $active ?>"><?= strtoupper(htmlspecialchars($s)) ?></a>
                <?php endforeach; ?>
            </div>

            <h2 style="margin-top:0;"><?= htmlspecialchars($client['name']) ?></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <?php foreach ($fields as $field): $currentVal = $savedData[$field['field_name']] ?? ''; ?>
                    <div class="form-group">
                        <label><?= htmlspecialchars($field['label']) ?></label>
                        <?php if ($field['type'] == 'image'): ?>
                            <?php if($currentVal): ?>
                                <img src="show_image.php?img=<?= htmlspecialchars($currentVal) ?>" style="max-width: 100%; border-radius: 8px; margin-bottom:10px;">
                            <?php endif; ?>
                            <input type="file" name="<?= $field['field_name'] ?>" accept="image/*" capture="environment">
                        <?php elseif ($field['type'] == 'number'): ?>
                            <input type="number" name="<?= $field['field_name'] ?>" value="<?= htmlspecialchars($currentVal) ?>">
                        <?php elseif ($field['type'] == 'checkbox'): ?>
                            <input type="checkbox" name="<?= $field['field_name'] ?>" value="Ja" <?= ($currentVal == 'Ja') ? 'checked' : '' ?>> Ja
                        <?php else: ?>
                            <div style="display: flex; gap: 5px;">
                                <input type="text" id="input_<?= $field['field_name'] ?>" name="<?= $field['field_name'] ?>" value="<?= htmlspecialchars($currentVal) ?>">
                                <?php if (strpos($field['field_name'], 'sn') !== false): ?>
                                    <button type="button" onclick="startScanner('input_<?= $field['field_name'] ?>')" style="background:#007bff; color:white; border:none; padding:10px; border-radius:8px;">📷</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="save_dynamic" class="btn-save">STAP VOLTOOIEN</button>
            </form>
        </div>
    </div>
    <script>
    let html5QrCode;
	function startScanner(targetId) {
		// Toon de modal
		document.getElementById('scanner-modal').style.display = 'flex';
		
		// Initialiseer de scanner
		html5QrCode = new Html5Qrcode("reader");
		
		// Start de camera
		html5QrCode.start(
			{ facingMode: "environment" }, // Gebruik achtercamera
			{ 
				fps: 10, 
				qrbox: { width: 250, height: 250 } 
			},
			(decodedText) => {
				// Succes: vul het juiste inputveld in
				document.getElementById(targetId).value = decodedText;
				stopScanner();
			},
			(errorMessage) => {
				// Dit zijn foutjes tijdens het scannen (bijv. geen QR in beeld), 
				// deze negeren we om de console schoon te houden.
			}
		).catch(err => {
			alert("Camera fout: " + err);
			document.getElementById(targetId).value = "ERROR: " + err;
			stopScanner();
		});
	}
    function stopScanner() {
        if (html5QrCode) { html5QrCode.stop().then(() => { document.getElementById('scanner-modal').style.display = 'none'; }); }
        else { document.getElementById('scanner-modal').style.display = 'none'; }
    }
    </script>
</body>
</html>
