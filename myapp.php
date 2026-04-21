<?php
$db = new PDO('sqlite:/var/www/html/clients.db');
$db->exec("CREATE TABLE IF NOT EXISTS clients (id INTEGER PRIMARY KEY, name TEXT, address TEXT, phone TEXT, image TEXT)");

// Cliënt toevoegen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_client'])) {
    $name = $_POST['name'];
    $imgName = "";
    
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imgName);
    }

    $stmt = $db->prepare("INSERT INTO clients (name, image) VALUES (?, ?)");
    $stmt->execute([$name, $imgName]);
}

$clients = $db->query("SELECT * FROM clients")->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json">
    <title>Client Manager</title>
    <style>
        body { font-family: sans-serif; background: #000; color: #fff; padding: 20px; }
        .card { background: #1a1a1a; padding: 15px; border-radius: 10px; margin-bottom: 10px; display: flex; align-items: center; }
        .card img { width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; object-fit: cover; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; border-radius: 5px; border: none; }
        button { background: #007bff; color: white; font-weight: bold; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

    <h1>Mijn Cliënten</h1>

    <!-- Formulier om nieuwe cliënt te maken -->
    <form method="POST" enctype="multipart/form-data" style="margin-bottom: 30px;">
        <input type="text" name="name" placeholder="Naam cliënt" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="add_client">Nieuwe Cliënt Toevoegen</button>
    </form>

    <!-- Lijst met cliënten -->
    <div id="client-list">
        <?php foreach ($clients as $client): ?>
            <div class="card">
                <?php if($client['image']): ?>
                    <img src="uploads/<?= $client['image'] ?>">
                <?php else: ?>
                    <div style="width:50px; height:50px; background:#333; border-radius:50%; margin-right:15px;"></div>
                <?php endif; ?>
                <div>
                    <strong><?= htmlspecialchars($client['name']) ?></strong><br>
                    <a href="details.php?id=<?= $client['id'] ?>">Bekijk details →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
