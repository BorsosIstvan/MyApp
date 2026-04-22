<?php
$db = new PDO('sqlite:/var/www/html/MyData/data.db');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Check of gebruiker al bestaat
    $check = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $check->execute([$user]);
    
    if ($check->fetchColumn() > 0) {
        $error = "Deze gebruikersnaam bestaat al!";
    } else {
        // Gebruiker toevoegen
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$user, $pass])) {
            header("Location: index.php?registered=1");
            exit;
        } else {
            $error = "Er ging iets mis.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aanmelden</title>
    <style>
        body { font-family: sans-serif; background: #e0e6ed; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 10px; border: 1px solid #ddd; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; }
        .error { color: red; font-size: 14px; }
        .back { display: block; margin-top: 15px; color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Nieuw Account</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Kies gebruikersnaam" required>
            <input type="password" name="password" placeholder="Kies wachtwoord" required>
            <button type="submit">ACCOUNT MAKEN</button>
        </form>
        <a href="index.php" class="back">Terug naar inloggen</a>
    </div>
</body>
</html>
