<?php
$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: projects.php"); exit(); }

// Gegevens updaten
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_details'])) {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    // 1. Update tekstgegevens
    $update = $db->prepare("UPDATE clients SET address = ?, phone = ? WHERE id = ?");
    $update->execute([$address, $phone, $id]);
    
    // 2. Foto uploaden (indien aanwezig)
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . $_FILES['image']['name'];
        // Zorg dat het pad naar MyData/uploads wijst
        $targetPath = "/var/www/html/MyData/uploads/" . $imgName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $db->prepare("UPDATE clients SET image = ? WHERE id = ?")->execute([$imgName, $id]);
        }
    }
    
    // 3. Pas NA het opslaan doorsturen
    header("Location: details.php?id=$id&success=1");
    exit();
} // <-- Hier stond de accolade verkeerd in jouw code

// Cliënt ophalen voor weergave
$stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Details - <?= htmlspecialchars($client['name']) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        
        header { background: #fff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; text-decoration: none; font-size: 14px; font-weight: bold; }
        .header-title { font-size: 16px; font-weight: 800; color: #222; text-transform: uppercase; }

        .content { padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px; }
        
        .profile-section { text-align: center; margin-bottom: 20px; }
        .profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .no-img { width: 120px; height: 120px; border-radius: 50%; background: #eef2f7; display: inline-flex; align-items: center; justify-content: center; font-size: 40px; color: #adb5bd; }

        label { display: block; font-weight: 700; font-size: 13px; color: #666; margin-bottom: 5px; margin-left: 5px; text-transform: uppercase; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 12px; border: 1px solid #eee; background: #fcfdfe; box-sizing: border-box; font-size: 16px; }
        
        .btn-save { width: 100%; background: #28a745; color: #fff; border: none; padding: 15px; border-radius: 12px; font-weight: bold; font-size: 16px; cursor: pointer; transition: background 0.2s; }
        .btn-save:active { background: #218838; transform: scale(0.98); }
        
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="phone-wrapper">
    <header>
        <a href="projects.php" class="header-btn">← Terug</a>
        <div class="header-title">DETAILS</div>
        <div style="width: 60px;"></div>
    </header>

    <div class="content">
        <?php if(isset($_GET['success'])): ?>
            <div class="success-msg">Gegevens succesvol opgeslagen!</div>
        <?php endif; ?>

        <div class="card">
            <div class="profile-section">
                <?php if($client['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($client['image']) ?>" class="profile-img">
                <?php else: ?>
                    <div class="no-img">👤</div>
                <?php endif; ?>
                <h2 style="margin: 10px 0 5px 0; color: #222;"><?= htmlspecialchars($client['name']) ?></h2>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <label>Adres</label>
                <input type="text" name="address" value="<?= htmlspecialchars($client['address'] ?? '') ?>" placeholder="Straatnaam 123">

                <label>Telefoon</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($client['phone'] ?? '') ?>" placeholder="06 12345678">

                <label>Foto wijzigen/toevoegen</label>
                <input type="file" name="image" accept="image/*" style="border: none; padding: 0;">

                <button type="submit" name="save_details" class="btn-save">GEGEVENS OPSLAAN</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
