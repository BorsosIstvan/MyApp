<?php
session_start();
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
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #121212; color: white; text-align: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; padding: 25px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; margin: auto; }
        .instruction-box { background: #181818; padding: 30px 20px; border-radius: 25px; border: 2px solid #007bff; margin: 30px 0; }
        .step { font-size: 18px; margin: 15px 0; text-align: left; display: flex; align-items: center; }
        .step-num { background: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; }
        .btn-back { background: #222; color: #007bff; padding: 12px; text-decoration: none; border-radius: 25px; font-weight: bold; border: 1px solid #007bff; display: block; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <div>
            <h2>📷 QR Code Scannen</h2>
            <p style="color: #aaa;">Gebruik de ingebouwde camera van je telefoon.</p>
            
            <div class="instruction-box">
                <div class="step">
                    <div class="step-num">1</div>
                    <span>Sluit deze app (of druk op de Home-knop).</span>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <span>Open de **normale Camera-app** van je Samsung.</span>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <span>Richt op de QR-code van de speler en klik op de link die in beeld verschijnt.</span>
                </div>
            </div>
        </div>

        <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    </div>

</body>
</html>
