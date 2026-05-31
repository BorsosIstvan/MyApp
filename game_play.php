<?php
// Tijdelijke test-database (Zorg dat deze ID's matchen met je generator)
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982, "spotify" => "https://spotify.com"],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975, "spotify" => "https://spotify.com"],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020, "spotify" => "https://spotify.com"]
];

// Haal het ID op uit de URL (bijv. ?id=1)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!array_key_exists($id, $test_songs)) {
    die("Liedje niet gevonden of ongeldige QR-code!");
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
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #121212; /* Donkere Spotify-stijl voor mobiel */
            color: white;
            display: flex;
            justify-content: center;
        }

        .phone-wrapper {
            width: 100%;
            max-width: 450px;
            min-height: 100vh;
            padding: 40px 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-game {
            font-size: 24px;
            font-weight: 800;
            color: #1DB954;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .btn-spotify {
            background-color: #1DB954;
            color: white;
            border: none;
            padding: 18px;
            border-radius: 35px;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
            cursor: pointer;
            display: block;
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(29, 185, 84, 0.4);
            transition: transform 0.1s;
        }

        .btn-spotify:active {
            transform: scale(0.95);
        }

        .btn-reveal {
            background-color: #ffffff;
            color: #121212;
            border: none;
            padding: 18px;
            border-radius: 35px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .secret-info {
            display: none;
            margin-top: 20px;
            background: #181818;
            padding: 25px;
            border-radius: 20px;
            border: 2px dashed #1DB954;
        }

        .year {
            font-size: 55px;
            font-weight: 900;
            color: #1DB954;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <div class="header-game">🎵 Hitscanner</div>
            <p style="color: #b3b3b3;">Druk op de groene knop om het nummer te luisteren.</p>
            
            <!-- Link die de Spotify app opent -->
            <a href="<?= $current_song['spotify'] ?>" class="btn-spotify" target="_blank">▶️ Start Muziek</a>
        </div>

        <div>
            <!-- Knop om het jaartal te onthullen -->
            <button class="btn-reveal" id="revealBtn" onclick="revealInfo()">👁️ Onthul het Antwoord</button>

            <!-- De verborgen kaartgegevens -->
            <div class="secret-info" id="secretBox">
                <p style="color: #b3b3b3; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Uitgebracht in:</p>
                <div class="year"><?= $current_song['year'] ?></div>
                <h3 style="margin: 5px 0; font-size: 20px;"><?= htmlspecialchars($current_song['title']) ?></h3>
                <p style="color: #b3b3b3; margin: 0; font-size: 16px;"><?= htmlspecialchars($current_song['artist']) ?></p>
            </div>
        </div>
    </div>

    <script>
        function revealInfo() {
            // Toon het antwoordblok
            document.getElementById('secretBox').style.display = 'block';
            // Verberg de onthulknop
            document.getElementById('revealBtn').style.style.display = 'none';
        }
    </script>

</body>
</html>
