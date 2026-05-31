<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

// 1. Koppel het officiële bibliotheekbestand dat je net hebt gedownload
require_once('phpqrcode/qrlib.php');

// Bepaal de huidige server URL dynamisch naar jouw mobiele game_play.php pagina
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$server_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/game_play.php?id=";

// Testdata van de liedjes
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020]
];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echte QR Generator</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; padding: 20px; text-align: center; }
        .card { background: white; padding: 25px; margin: 20px auto; max-width: 380px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
        
        /* Stijling voor de officiële QR-afbeelding */
        .qr-box { margin: 20px 0; display: flex; justify-content: center; }
        .qr-box img { width: 180px; height: 180px; border: 1px solid #eaeaea; padding: 10px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .btn-back { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; margin-bottom: 20px; font-weight: 600; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    <h1>Muziek Game QR-Codes</h1>
    <p>Dit zijn officiële QR-codes. Scan ze met de normale camera van je telefoon!</p>

    <?php foreach ($test_songs as $id => $song): 
        // Maak de unieke link die de telefoon straks moet openen
        $unieke_link = $server_url . $id;
        
        // 1. Start de buffer om de afbeelding op te vangen
        ob_start();
        
        // 2. Genereer de QR-code bitstream in het geheugen
        QRcode::png($unieke_link, null, QR_ECLEVEL_L, 6, 2);
        $image_data = ob_get_contents();
        
        // 3. Sluit de buffer netjes af
        ob_end_clean();
        
        // 🔥 CRUCIALE FIX: Vertel Apache dat we weer gewone HTML-tekst sturen!
        // Dit voorkomt dat je server denkt dat de hele pagina één grote afbeelding is.
        header("Content-Type: text/html");
        
        // 4. Zet de ruwe afbeelding om in een Base64-tekststring
        $base64_qr = 'data:image/png;base64,' . base64_encode($image_data);
    ?>
        <div class="card">
            <h3>Kaart #<?= $id ?></h3>
            <p style="color: #555; font-size: 14px; font-weight: 600;"><?= $song['artist'] ?> - <?= $song['title'] ?></p>
            <p style="color: #999; font-size: 12px; margin-top: -10px;">Jaartal: <?= $song['year'] ?></p>
            
            <!-- Hier tonen we de échte QR-code afbeelding -->
            <div class="qr-box">
                <img src="<?= $base64_qr ?>" alt="QR Code voor Liedje <?= $id ?>">
            </div>
            
            <p style="font-size: 10px; color: #aaa; word-break: break-all; margin-top: 15px;">Link: <?= $unieke_link ?></p>
        </div>
    <?php endforeach; ?>


</body>
</html>
