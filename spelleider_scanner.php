<?php
session_start();
// Alleen de admin (spelleider) mag scannen
if (!isset($_SESSION['loggedin']) || $_SESSION['user'] !== 'admin') {
    header("Location: myapp.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Spelleider Scanner</title>
    
    <!-- We gebruiken de moderne en stabiele HTML5-QR-code scanner bibliotheek vanaf een betrouwbare CDN -->
    <script src="https://unpkg.com" type="text/javascript"></script>
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; margin: auto; }
        
        .scanner-container { background: #181818; padding: 25px; border-radius: 25px; border: 2px solid #007bff; margin: 20px 0; }
        
        /* Stijl voor de verborgen native camera-knop */
        input[type="file"] { display: none; }
        
        .btn-scan { background-color: #007bff; color: white; padding: 18px; border-radius: 35px; font-weight: bold; font-size: 18px; display: block; margin: 15px 0; cursor: pointer; text-transform: uppercase; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4); }
        .btn-back { background: #222; color: #007bff; padding: 12px; text-decoration: none; border-radius: 25px; font-weight: bold; border: 1px solid #007bff; }
        
        .loading { display: none; color: #ffc107; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <h2>📷 Hitscanner Spelleider</h2>
            <p style="color: #aaa;">Gebruik de camera van je Samsung om de kaart van de speler te scannen.</p>
            
            <div class="scanner-container">
                <!-- Deze label activeert de echte Samsung camera via HTML5 -->
                <label for="qr-file-input" class="btn-scan">📸 Start Samsung Camera</label>
                <input type="file" id="qr-file-input" accept="image/*" capture="environment">
                
                <div class="loading" id="loadingText">⏳ Code analyseren...</div>
            </div>
        </div>

        <a href="myapp.php" class="btn-back">⬅️ Stop Scanner</a>
    </div>

    <script type="text/javascript">
        const fileInput = document.getElementById('qr-file-input');
        const loadingText = document.getElementById('loadingText');

        // Initialiseer de HTML5 QR-code lezer in de achtergrond (zonder live video-feed, puur voor fotoverwerking)
        const html5QrCode = new Html5Qrcode("qr-file-input");

        fileInput.addEventListener('change', e => {
            if (e.target.files.length === 0) {
                return;
            }

            // Toon laad-indicator
            loadingText.style.display = "block";
            const imageFile = e.target.files[0];
            
            // Scan de gemaakte foto direct lokaal in JavaScript
            html5QrCode.scanFile(imageFile, true)
                .then(decodedText => {
                    // Succes! 'decodedText' bevat de URL naar game_play.php?id=X
                    // Stuur de spelleider direct door
                    window.location.href = decodedText;
                })
                .catch(err => {
                    // Als de foto wazig was of er geen QR-code is gevonden
                    loadingText.style.display = "none";
                    alert("Fout: Geen duidelijke QR-code herkend. Probeer de code rechter en scherper te fotograferen.");
                });
        });
    </script>

</body>
</html>
