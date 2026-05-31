<?php
// Foutrapportage tijdelijk aan om te zien wat er gebeurt
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // 1. Verbinding maken met SQLite
    $db = new PDO('sqlite:/var/www/html/MyData/data.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. 🔥 AUTOMATISCHE CHECK: Bestaat de tabel wel? Zo nee, maak hem aan!
    $db->exec("CREATE TABLE IF NOT EXISTS game_songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        artist TEXT NOT NULL,
        title TEXT NOT NULL,
        year INTEGER NOT NULL,
        theme TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Als de tabel leeg is, stoppen we direct de 3 testnummers erin
    $check = $db->query("SELECT COUNT(*) FROM game_songs")->fetchColumn();
    if ($check == 0) {
        $db->exec("INSERT INTO game_songs (artist, title, year, theme) VALUES 
            ('Michael Jackson', 'Thriller', 1982, 'Pop'),
            ('Queen', 'Bohemian Rhapsody', 1975, 'Rock'),
            ('Dua Lipa', 'Levitating', 2020, 'Pop')");
    }
    
    // 3. Zoek het liedje op basis van het ID
    $stmt = $db->prepare("SELECT artist, title, year FROM game_songs WHERE id = ?");
    $stmt->execute([$id]);
    $current_song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 4. Foutopsporing (Als het liedje niet wordt gevonden)
    if (!$current_song) {
        echo "<div style='background: #dc3545; color: white; padding: 20px; text-align: center; font-family: sans-serif;'>";
        echo "<h3>❌ Fout: Liedje niet gevonden!</h3>";
        echo "<p>Jouw telefoon scant nu <strong>ID-nummer: " . $id . "</strong></p>";
        
        // Laat zien welke ID's wél in de database staan
        $alle_ids = $db->query("SELECT id FROM game_songs")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Beschikbare ID's in jouw SQLite database: " . implode(', ', $alle_ids) . "</p>";
        echo "<p>Sluit dit venster en probeer de QR-code opnieuw te scannen.</p>";
        echo "</div>";
        exit;
    }

} catch (Exception $e) {
    die("Database of SQLite maprechten fout: " . $e->getMessage());
}

// 5. Maak de juiste zoekterm voor Apple Music (blijft hetzelfde)
$zoekterm = urlencode($current_song['artist'] . " " . $current_song['title']);
$api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$preview_url = "";
if ($response) {
    $json = json_decode($response, true);
    if (isset($json['results'][0]['previewUrl'])) {
        $preview_url = $json['results'][0]['previewUrl'];
    }
}

if (empty($preview_url)) {
    die("Fout: Kon geen audio-preview vinden voor '" . htmlspecialchars($current_song['artist'] . " - " . $current_song['title']) . "'.");
}
?>



<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Raad het Jaar!</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; display: flex; justify-content: center; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 40px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; }
        .header-game { font-size: 24px; font-weight: 800; color: #ff2d55; text-transform: uppercase; letter-spacing: 2px; }
        
        .btn-audio { background-color: #ff2d55; color: white; border: none; padding: 18px; border-radius: 35px; font-weight: bold; font-size: 18px; width: 100%; cursor: pointer; margin: 30px 0; box-shadow: 0 4px 15px rgba(255, 45, 85, 0.4); }
        .btn-audio.playing { background-color: #222; border: 2px solid #ff2d55; box-shadow: none; }
        
        .btn-reveal { background-color: #ffffff; color: #121212; border: none; padding: 18px; border-radius: 35px; font-weight: bold; font-size: 18px; cursor: pointer; width: 100%; }
        .secret-info { display: none; margin-top: 20px; background: #181818; padding: 25px; border-radius: 20px; border: 2px dashed #ff2d55; }
        .year { font-size: 55px; font-weight: 900; color: #ff2d55; margin: 10px 0; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <div class="header-game">🎵 Hitscanner Stream</div>
            <p style="color: #b3b3b3;">Gekoppeld met de legale Apple Music database.</p>
            
            <!-- De audiospeler die de .m4a preview van Apple inlaadt -->
            <audio id="audioPlayer" src="<?= $preview_url ?>"></audio>
            
            <button class="btn-audio" id="playBtn" onclick="toggleAudio()">▶️ Luister Preview (30s)</button>
        </div>

        <div>
            <button class="btn-reveal" id="revealBtn" onclick="revealInfo()">👁️ Onthul het Antwoord</button>

            <div class="secret-info" id="secretBox">
                <p style="color: #b3b3b3; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Uitgebracht in:</p>
                <div class="year"><?= $current_song['year'] ?></div>
                <h3 style="margin: 5px 0; font-size: 20px;"><?= htmlspecialchars($current_song['title']) ?></h3>
                <p style="color: #b3b3b3; margin: 0; font-size: 16px;"><?= htmlspecialchars($current_song['artist']) ?></p>
            </div>
        </div>
    </div>

    <script>
        var audio = document.getElementById('audioPlayer');
        var playBtn = document.getElementById('playBtn');

        function toggleAudio() {
            if (audio.paused) {
                audio.play();
                playBtn.innerHTML = "⏸️ Pauzeer Preview";
                playBtn.classList.add('playing');
            } else {
                audio.pause();
                playBtn.innerHTML = "▶️ Luister Preview (30s)";
                playBtn.classList.remove('playing');
            }
        }

        audio.onended = function() {
            playBtn.innerHTML = "▶️ Luister Preview (30s)";
            playBtn.classList.remove('playing');
        };

        function revealInfo() {
            document.getElementById('secretBox').style.display = 'block';
            document.getElementById('revealBtn').style.display = 'none';
            audio.pause();
        }
    </script>

</body>
</html>
