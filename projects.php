<?php
// Verbinding met de gloednieuwe database
$db = new PDO('sqlite:/var/www/html/MyData/data.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Maak de tabel aan als deze nog niet bestaat
$db->exec("CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    name TEXT, 
    address TEXT, 
    phone TEXT, 
    image TEXT
)");

// Nieuwe client opslaan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_client'])) {
    $name = $_POST['name'];
    $stmt = $db->prepare("INSERT INTO clients (name) VALUES (?)");
    $stmt->execute([$name]);
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
            <!-- Formulier om client toe te voegen -->
            <div class="add-form">
                <form method="POST">
                    <input type="text" name="name" placeholder="Naam nieuwe client..." required>
                    <button type="submit" name="add_client" class="btn-add">+ Toevoegen</button>
                </form>
            </div>

            <!-- Lijst van clienten -->
            <div class="list">
                <?php foreach ($clients as $client): ?>
                    <div class="client-card">
                        <span class="client-info"><?= htmlspecialchars($client['name']) ?></span>
                        <a href="details.php?id=<?= $client['id'] ?>" class="btn-view">OPEN</a>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($clients)) echo "<p style='text-align:center; color:#999;'>Nog geen clienten toegevoegd.</p>"; ?>
            </div>
        </div>
    </div>

</body>
</html>
