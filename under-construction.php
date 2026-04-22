<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$pagina = $_GET['p'] ?? 'Pagina';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Onder Constructie</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        header { background: #fff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; text-decoration: none; font-size: 14px; font-weight: bold; }
        .content { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px; color: #666; }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { color: #222; margin-bottom: 10px; text-transform: uppercase; font-size: 20px; }
    </style>
</head>
<body>
    <div class="phone-wrapper">
        <header>
            <a href="myapp.php" class="header-btn">🏠 Home</a>
            <div style="font-weight: 800;"><?= htmlspecialchars(ucfirst($pagina)) ?></div>
            <div style="width: 60px;"></div>
        </header>

        <div class="content">
            <div class="icon">🚧</div>
            <h1>Onder Constructie</h1>
            <p>We werken hard aan de <strong><?= htmlspecialchars($pagina) ?></strong> module. Kom snel terug!</p>
        </div>
    </div>
</body>
</html>
