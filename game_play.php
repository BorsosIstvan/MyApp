<?php
// Dezelfde test-database (In de toekomst halen we dit live uit MySQL op basis van de ID)
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982, "spotify" => "https://spotify.com"],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975, "spotify" => "https://spotify.com"],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020, "spotify" => "https://spotify.com"]
];

$id = isset($_GET['id']) ? (int)$GET['id'] : 0;

if (!array_key_exists($id, $test_songs)) {
    die("Liedje niet gevonden!");
}

$current_song = $test_songs[$id];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Raad het Jaar!</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; display: flex; justify-content: center; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 30px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; }
        .btn-spotify { background-color: #1DB954; color: white; border: none; padding: 15px; border-radius: 30px; font-weight: bold; font-size: 18px; text-decoration: none; cursor: pointer; display: block; margin: 20px 0; }
        .btn-reveal { background-color: #ffffff; color: #121212; border: none; padding: 15px; border-radius: 30px; font-weight: bold; font-size: 18px; cursor: pointer; width: 100%; }
        .secret-info { display: none; margin-top: 20px; background: #282828; padding: 20px; border-radius: 15px; border: 2px dashed #1DB954; }
        .year { font-size: 45px; font-weight: 800; color: #1DB954; margin: 10px 0; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <h2>🎵 Hitscanner Base Test</h2>
            <p>Klik op de groene knop om de muziek te starten op je telefoon.</p>
            
            <!-- Link die direct de Spotify app triggert -->
            <a href="<?= $current_song['spotify'] ?>" class="btn-spotify" target="_blank">▶️ Start Muziek op Spotify</a>
        </div>

        <div>
            <!-- Knop om de gegevens te onthullen -->
            <button class="btn-reveal" id="revealBtn" onclick="revealInfo()">👁️ Onthul het Jaartal</button>

            <!-- Verborgen gegevens -->
            <div class="secret-info" id="secretBox">
                <h3>Antwoord:</h3>
                <div class="year"><?= $current_song['year'] ?></div>
                <p><strong><?= htmlspecialchars($current_song['title']) ?></strong></p>
                <p style="color: #aaa;"><?= htmlspecialchars($current_song['artist']) ?></p>
            </div>
        </div>
    </div>

    <script>
        function revealInfo() {
            // Laat de verborgen gegevens zien
            document.getElementById('secretBox').style.display = 'block';
            // Verberg de onthulknop
            document.getElementById('revealBtn').style.display = 'none';
        }
    </script>

</body>
</html>
