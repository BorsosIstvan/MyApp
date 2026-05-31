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
    <!-- We laden de Instascan bibliotheek in voor live camera scanning via JS -->
    <script src="https://rawgit.com"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; margin: auto; }
        
        /* De camera preview window */
        #preview { width: 100%; max-width: 350px; background: #222; border-radius: 20px; border: 2px solid #007bff; transform: scaleX(1); margin: 20px auto; }
        
        .btn-back { background: #222; color: #007bff; padding: 12px; text-decoration: none; border-radius: 25px; font-weight: bold; border: 1px solid #007bff; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <h2>📷 Hitscanner Spelleider</h2>
            <p style="color: #aaa;">Richt je camera op de QR-code van de speler.</p>
            
            <!-- Hier komt het live camerabeeld -->
            <video id="preview"></video>
        </div>

        <a href="myapp.php" class="btn-back">⬅️ Stop Scanner</a>
    </div>

    <script type="text/javascript">
        // Start de QR scanner logica
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview'), mirror: false });
        
        // Zodra er een QR code succesvol wordt gelezen:
        scanner.addListener('scan', function (content) {
            // 'content' bevat de complete URL (bijv. https://n-soft.net)
            // We sturen de spelleider direct door naar die pagina!
            window.location.href = content;
        });

        // Zoek naar de camera's op de telefoon
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                // Gebruik de achtercamera (meestal de laatste in de lijst bij telefoons)
                let achterCamera = cameras[cameras.length - 1];
                if (cameras.length > 1) {
                    // Forceer achtercamera op Android/iPhone indien beschikbaar
                    achterCamera = cameras[1]; 
                }
                scanner.start(achterCamera);
            } else {
                alert('Fout: Geen camera gevonden op dit apparaat.');
            }
        }).catch(function (e) {
            alert('Camera fout: ' + e);
        });
    </script>

</body>
</html>
