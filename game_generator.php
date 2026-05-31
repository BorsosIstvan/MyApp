<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

// Tijdelijke test-database (Later koppelen we dit aan MySQL)
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982, "spotify" => "https://spotify.com"],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975, "spotify" => "https://spotify.com"],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020, "spotify" => "https://spotify.com"]
];

// Bepaal de huidige server URL dynamisch
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$server_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/game_play.php?id=";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Generator</title>
    <!-- QR Code Bibliotheek inladen vanaf CDN voor de test -->
    <script src="https://cloudflare.com"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; padding: 20px; text-align: center; }
        .card { background: white; padding: 20px; margin: 15px auto; max-width: 400px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .qr-box { display: flex; justify-content: center; margin: 15px 0; }
        .btn-back { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    <h1>Muziek Game QR-Codes</h1>
    <p>Scan een QR-code met je mobiel om het spel te testen.</p>

    <?php foreach ($test_songs as $id => $song): ?>
        <div class="card">
            <h3>Kaart #<?= $id ?> (Testkaart)</h3>
            <p style="color: #666; font-size: 12px;">Geheim voor spelers: <?= $song['artist'] ?> (<?= $song['year'] ?>)</p>
            
            <!-- Hier komt de QR code -->
            <div class="qr-box" id="qr-<?= $id ?>"></div>
        </div>

        <script>
            // Genereer de QR code die verwijst naar game_play.php op jouw Pi
            new QRCode(document.getElementById("qr-<?= $id ?>"), {
                text: "<?= $server_url . $id ?>",
                width: 150,
                height: 150
            });
        </script>
    <?php endforeach; ?>

</body>
</html>
