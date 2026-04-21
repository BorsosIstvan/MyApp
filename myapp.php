<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="manifest" href="manifest.json">
    <title>Mijn App</title>
    <style>
        /* Basis stijl - Licht en fris */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f7f9;
            color: #333;
        }

        /* Bovenbalk (Header) */
        header {
            background-color: #ffffff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-btn {
            background: #eef2f7;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #007bff;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        /* Grid voor de 4 vierkanten */
        .container {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .menu-item {
            background-color: #ffffff;
            aspect-ratio: 1 / 1; /* Maakt het een vierkant */
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #eaeaea;
        }

        .menu-item:active {
            transform: scale(0.95);
            background-color: #f0f0f0;
        }

        .menu-item .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .menu-item span {
            font-weight: 600;
            font-size: 15px;
        }

        /* Kleuren per knop voor herkenbaarheid */
        .projects { border-bottom: 4px solid #007bff; }
        .inventory { border-bottom: 4px solid #28a745; }
        .messages { border-bottom: 4px solid #ffc107; }
        .goals { border-bottom: 4px solid #dc3545; }

    </style>
</head>
<body>

    <header>
        <button class="header-btn" onclick="location.href='myapp.php'">🏠 Home</button>
        <div class="header-title">Mijn App</div>
        <button class="header-btn" style="color: #dc3545;">Uitloggen</button>
    </header>

    <div class="container">
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

</body>
</html>
