<?php
// Dezelfde database-lijst
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020]
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!array_key_exists($id, $test_songs)) {
    die("Liedje niet gevonden!");
}

$current_song = $test_songs[$id];

// 🔥 DYNAMISCHE APPLE MUSIC API ZOEKOPDRACHT
// We maken een zoekterm van de artiest en de titel (bijv. "Michael Jackson Thriller")
$zoekterm = urlencode($current_song['artist'] . " " . $current_song['title']);
$api_url = "https://apple.com" . $zoekterm . "&limit=1&media=music&entity=song";

// Haal de resultaten op van de Apple server
$response = @file_get_contents($api_url);
$preview_url = "";

if ($response) {
    $json = json_decode($response, true);
    // Als Apple het liedje heeft gevonden, pakken we de 30-seconden preview URL
    if (isset($json['results'][0]['previewUrl'])) {
        $preview_url = $json['results'][0]['previewUrl'];
    }
}

// Mocht de API offline zijn of niks vinden, dan tonen we een melding
if (empty($preview_url)) {
    die("Fout: Kon geen legale audio-preview vinden bij Apple Music voor dit liedje.");
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
        .header-game { font-size: 24px; font-weight: 800; color: #ff2d55; text-transform: uppercase; letter-spacing: 2px; } /* Apple Music Roze/Rood */
        
        /* Grote speelknop */
        .btn-audio { background-color: #ff2d55; color: white; border: none; padding: 18px; border-radius: 35px; font-weight: bold; font-size: 18px; width: 100%; cursor: pointer; margin: 30px 0; box-shadow: 0 4px 15px rgba(255, 45, 85, 0.4); transition: transform 0.1s; }
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
            
            <!-- De onzichtbare HTML5 audiospeler die de Apple link inlaadt -->
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
