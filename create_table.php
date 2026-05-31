<?php
try {
    $db = new PDO('sqlite:/var/www/html/MyData/data.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Maak de tabel aan voor de liedjes
    $db->exec("CREATE TABLE IF NOT EXISTS game_songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        artist TEXT NOT NULL,
        title TEXT NOT NULL,
        year INTEGER NOT NULL,
        theme TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Voeg direct 3 test-liedjes toe als de tabel leeg is
    $check = $db->query("SELECT COUNT(*) FROM game_songs")->fetchColumn();
    if ($check == 0) {
        $db->exec("INSERT INTO game_songs (artist, title, year, theme) VALUES 
            ('Michael Jackson', 'Thriller', 1982, 'Pop'),
            ('Queen', 'Bohemian Rhapsody', 1975, 'Rock'),
            ('Dua Lipa', 'Levitating', 2020, 'Pop')");
    }

    echo "✅ Tabel 'game_songs' is succesvol aangemaakt en gevuld in SQLite!";
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage();
}
?>
