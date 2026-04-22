<?php
session_start();

// Check of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="manifest" href="manifest.json">
    <title>Mijn App</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); position: relative; display: flex; flex-direction: column; }
        header { background-color: #ffffff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 14px; }
        .header-title { font-size: 18px; font-weight: 800; color: #222; text-transform: uppercase; letter-spacing: 1px; }
        .menu-grid { padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .menu-item { background-color: #ffffff; aspect-ratio: 1 / 1; border-radius: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #333; box-shadow: 0 8px 15px rgba(0,0,0,0.04); transition: all 0.2s ease; border: 1px solid #f0f0f0; }
        .menu-item:active { transform: scale(0.92); }
        .menu-item .icon { font-size: 35px; margin-bottom: 12px; }
        .menu-item span { font-weight: 700; font-size: 14px; color: #555; }
        .projects { border-top: 5px solid #007bff; }
        .inventory { border-top: 5px solid #28a745; }
        .messages { border-top: 5px solid #ffc107; }
        .goals { border-top: 5px solid #dc3545; }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <header>
            <button class="header-btn" onclick="location.href='myapp.php'">🏠</button>
            <div class="header-title">MY APP</div>
            <!-- De Uit-knop linkt nu naar logout.php -->
            <a href="logout.php" class="header-btn" style="color: #dc3545;">Uit</a>
        </header>

        <div class="menu-grid">
            <a href="projects.php" class="menu-item projects">
                <div class="icon">📂</div>
                <span>Projecten</span>
            </a>
            <a href="inventory.php" class="menu-item inventory">
                <div class="icon">📦</div>
                <span>Voorraden</span>
            </a>
            <a href="messages.php" class="menu-item messages">
                <div class="icon">💬</div>
                <span>Berichten</span>
            </a>
            <a href="goals.php" class="menu-item goals">
                <div class="icon">🎯</div>
                <span>Doelen</span>
            </a>
        </div>
    </div>

</body>
</html>
