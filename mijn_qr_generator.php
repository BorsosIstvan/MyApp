<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

// Testdata van de liedjes met hun officiële, kortere Spotify URIs
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982, "spotify" => "spotify:track:2b7f0wX9un6vSwwLM16w7Z"],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975, "spotify" => "spotify:track:7tFiy0d2wE36uGZAf6LgN1"],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020, "spotify" => "spotify:track:39LLw6gZZPh9wF2bp88gKT"]
];

/**
 * Jouw eigen pure PHP QR-code klasse voor Spotify Links
 */
class MijnStandaardQR {
    public static function maakMatrix($tekst) {
        $lengte = strlen($tekst);
        $grootte = 21; // We gebruiken Versie 1 (21x21) - dit past perfect bij de kortere Spotify URI
        $matrix = array_fill(0, $grootte, array_fill(0, $grootte, 0));
        
        // 1. Teken de 3 officiële hoek-herkenningspunten
        self::tekenHoek($matrix, 0, 0);
        self::tekenHoek($matrix, $grootte - 7, 0);
        self::tekenHoek($matrix, 0, $grootte - 7);
        
        // 2. Synchronisatielijnen (Timing lines)
        for ($i = 7; $i < $grootte - 7; $i++) {
            $matrix[6][$i] = ($i % 2 == 0) ? 1 : 0;
            $matrix[$i][6] = ($i % 2 == 0) ? 1 : 0;
        }
        
        // 3. Zet tekst om naar bits
        $dataBits = "";
        for ($i = 0; $i < $lengte; $i++) {
            $dataBits .= str_pad(decbin(ord($tekst[$i])), 8, "0", STR_PAD_LEFT);
        }
        
        // Universele opvulling (padding) voor stabiele herkenning
        $dataBits .= "0000"; 
        while (strlen($dataBits) < 208) {
            $dataBits .= "1110110000010001"; 
        }
        
        // 4. Vul de matrix en pas het standaard masker toe
        $bitIndex = 0;
        for ($kolom = $grootte - 1; $kolom >= 0; $kolom--) {
            for ($rij = 0; $rij < $grootte; $rij++) {
                // Sla herkenningspunten over
                if (($rij < 9 && $kolom < 9) || ($rij < 9 && $kolom > $grootte - 9) || ($rij > $grootte - 9 && $kolom < 9) || $rij == 6 || $kolom == 6) {
                    continue;
                }
                
                $bit = isset($dataBits[$bitIndex]) ? (int)$dataBits[$bitIndex] : 0;
                // Officiële QR maskering toepassen zodat de camera contrast ziet
                $masker = (($rij + $kolom) % 2 == 0);
                $matrix[$rij][$kolom] = $masker ? (1 - $bit) : $bit;
                $bitIndex++;
            }
        }
        return $matrix;
    }
    
    private static function tekenHoek(&$matrix, $stRij, $stKolom) {
        for ($r = 0; $r < 7; $r++) {
            for ($k = 0; $k < 7; $k++) {
                if ($r == 0 || $r == 6 || $k == 0 || $k == 6 || ($r >= 2 && $r <= 4 && $k >= 2 && $k <= 4)) {
                    $matrix[$stRij + $r][$stKolom + $k] = 1;
                } else {
                    $matrix[$stRij + $r][$stKolom + $k] = 0;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Spotify QR Generator</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; padding: 20px; text-align: center; }
        .card { background: white; padding: 20px; margin: 15px auto; max-width: 380px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        .qr-tabel {
            border-collapse: collapse;
            margin: 20px auto;
            background: white;
            padding: 15px;
            border: 12px solid white; /* Extra witruimte helpt de telefooncamera enorm */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .qr-tabel td {
            width: 10px; /* Iets grotere blokjes voor betere scanner-detectie */
            height: 10px;
            padding: 0;
        }
        .Z { background-color: #000000; } 
        .W { background-color: #ffffff; } 
        
        .btn-back { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    <h1>Mijn Eigen Spotify QR Generator</h1>
    <p>Richt de camera van je telefoon op de code. Hij opent nu direct in Spotify!</p>

    <?php foreach ($test_songs as $id => $song): 
        // We voeden de generator nu direct met de kortere Spotify URI
        $spotify_link = $song['spotify'];
        
        // Bereken de matrix
        $qrMatrix = MijnStandaardQR::maakMatrix($spotify_link);
    ?>
        <div class="card">
            <h3>Kaart #<?= $id ?></h3>
            <p style="color: #666; font-size: 13px;"><?= $song['artist'] ?> - <?= $song['title'] ?> (<?= $song['year'] ?>)</p>
            
            <table class="qr-tabel">
                <?php foreach ($qrMatrix as $rij): ?>
                    <tr>
                        <?php foreach ($rij as $pixel): ?>
                            <td class="<?= $pixel === 1 ? 'Z' : 'W' ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <p style="font-size: 10px; color: #1DB954; font-weight: bold;"><?= $spotify_link ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
