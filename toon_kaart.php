<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once('phpqrcode/qrlib.php');

// De database met liedjes
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020]
];

// Kies een willekeurig liedje voor deze speler
$willekeurig_id = array_rand($test_songs);

// Bouw de link die de spelleider straks gaat scannen
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$scan_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/game_play.php?id=" . $willekeurig_id;

// Genereer de QR-code direct in het geheugen (Base64)
ob_start();
QRcode::png($scan_url, null, QR_ECLEVEL_L, 6, 2);
$image_data = ob_get_contents();
ob_end_clean();
$base64_qr = 'data:image/png;base64,' . base64_encode($image_data);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Mijn Handkaart</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; margin: auto; }
        .card { background: #181818; padding: 20px; border-radius: 20px; border: 2px solid #ff2d55; }
        .qr-box img { width: 200px; height: 200px; border: 10px solid white; border-radius: 10px; margin: 20px 0; }
        .btn-back { background: #222; color: #ff2d55; padding: 12px; text-decoration: none; border-radius: 25px; font-weight: bold; border: 1px solid #ff2d55; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <h2>🃏 Jouw Handkaart</h2>
            <p style="color: #aaa;">Laat de spelleider deze QR-code scannen vanaf jouw scherm!</p>
        </div>

        <div class="card">
            <div class="qr-box">
                <img src="<?= $base64_qr ?>" alt="Mijn QR">
            </div>
            <p style="color: #666; font-size: 11px;">Kaart-ID: <?= $willekeurig_id ?></p>
        </div>

        <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    </div>

</body>
</html>
