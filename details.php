<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$current_step = $_GET['step'] ?? 'Schouwing'; // Welke stap bekijken we nu?

// Haal de configuratie voor deze stap op
$stmtConfig = $db->prepare("SELECT * FROM config WHERE step_name = ?");
$stmtConfig->execute([$current_step]);
$fields = $stmtConfig->fetchAll();

// Haal client info op
$stmtClient = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmtClient->execute([$id]);
$client = $stmtClient->fetch();

// Verwerken van het dynamische formulier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_dynamic'])) {
    foreach ($fields as $field) {
        $name = $field['field_name'];
        
        if ($field['type'] == 'image' && !empty($_FILES[$name]['name'])) {
            // Foto uploaden
            $imgName = time() . '_' . $_FILES[$name]['name'];
            move_uploaded_file($_FILES[$name]['tmp_name'], "/var/www/html/MyData/uploads/" . $imgName);
            // Sla de naam op (hiervoor moet je een tabel 'project_data' hebben, maar we doen nu even simpel)
        }
        // Hier zou je de data opslaan in een tabel 'project_data' gekoppeld aan het client_id
    }
    header("Location: details.php?id=$id&step=$current_step&success=1");
    exit;
}
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
                <?php foreach ($fields as $field): ?>
                    <div class="form-group">
                        <label><?= htmlspecialchars($field['label']) ?></label>
                        <?php if ($field['type'] == 'image'): ?>
                            <input type="file" name="<?= $field['field_name'] ?>" accept="image/*">
                        <?php elseif ($field['type'] == 'number'): ?>
                            <input type="number" name="<?= $field['field_name'] ?>" placeholder="Vul getal in">
                        <?php elseif ($field['type'] == 'checkbox'): ?>
                            <input type="checkbox" name="<?= $field['field_name'] ?>" style="width: 20px; height: 20px;"> Ja
                        <?php else: ?>
                            <input type="text" name="<?= $field['field_name'] ?>" placeholder="Typ hier...">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" name="save_dynamic" class="btn-save">STAP VOLTOOIEN</button>
            </form>
        </div>
    </div>
</body>
</html>
