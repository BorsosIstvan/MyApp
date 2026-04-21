<?php
$db = new PDO('sqlite:/var/www/html/clients.db');
$id = $_GET['id'];

// Haal cliëntgegevens op
$stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) { die("Cliënt niet gevonden."); }

// Gegevens updaten
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_details'])) {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    // Update de database (we voegen kolommen toe als ze nog niet bestaan)
    $db->exec("ALTER TABLE clients ADD COLUMN address TEXT");
    $db->exec("ALTER TABLE clients ADD COLUMN phone TEXT");
    
    $update = $db->prepare("UPDATE clients SET address = ?, phone = ? WHERE id = ?");
    $update->execute([$address, $phone, $id]);
    header("Location: details.php?id=$id&saved=1");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($client['name']) ?> - Details</title>
    <style>
        body { font-family: sans-serif; background: #000; color: #fff; padding: 20px; }
        .container { background: #1a1a1a; padding: 20px; border-radius: 15px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #333; background: #222; color: #fff; box-sizing: border-box; }
        .btn { background: #28a745; color: white; padding: 12px; border: none; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto 20px; border: 2px solid #007bff; }
    </style>
</head>
<body>

    <a href="myapp.php" class="back-btn">← Terug naar lijst</a>

    <div class="container">
        <?php if($client['image']): ?>
            <img src="uploads/<?= $client['image'] ?>" class="profile-img">
        <?php endif; ?>

        <h1><?= htmlspecialchars($client['name']) ?></h1>

        <?php if(isset($_GET['saved'])): ?>
            <p style="color: #28a745;">✅ Gegevens opgeslagen!</p>
        <?php endif; ?>

        <form method="POST">
            <label>Adres:</label>
            <input type="text" name="address" value="<?= htmlspecialchars($client['address'] ?? '') ?>" placeholder="Straat 1, Stad">
            
            <label>Telefoonnummer:</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($client['phone'] ?? '') ?>" placeholder="0612345678">
            
            <button type="submit" name="save_details" class="btn">Gegevens Opslaan</button>
        </form>
    </div>

</body>
</html>
