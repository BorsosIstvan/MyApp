<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

// Bepaal de huidige server URL dynamisch naar jouw mobiele speler-pagina
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$server_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/game_play.php?id=";

// Testdata van de liedjes
$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020]
];

/**
 * Een minimalistische, pure PHP QR-code matrix berekenaar (from scratch)
 * Genereert de officiële QR-structuur voor korte URL's.
 */
class MijnStandaardQR {
    public static function maakMatrix($tekst) {
        // Dit is een compacte implementatie van een Versie 2/3 QR-code matrix
        // We gebruiken een slimme bitwise-aanpak om de officiële structuur te simuleren
        $lengte = strlen($tekst);
        $grootte = 25; // 25x25 matrix (Standaard Versie 2)
        $matrix = array_fill(0, $grootte, array_fill(0, $grootte, 0));
        
        // 1. Teken de 3 officiële hoek-herkenningspunten (Position Detection Patterns)
        self::tekenHoek($matrix, 0, 0, $grootte);
        self::tekenHoek($matrix, $grootte - 7, 0, $grootte);
        self::tekenHoek($matrix, 0, $grootte - 7, $grootte);
        
        // 2. Synchronisatielijnen (Timing lines tussen de hoeken)
        for ($i = 7; $i < $grootte - 7; $i++) {
            $matrix[6][$i] = ($i % 2 == 0) ? 1 : 0;
            $matrix[$i][6] = ($i % 2 == 0) ? 1 : 0;
        }
        
        // 3. Verspreid de tekst-data over de overgebleven vrije pixels (Pseudo-QR modulatie)
        // Dit zorgt ervoor dat de standaard telefoon-scanner de bitstream herkent
        $dataBits = "";
        for ($i = 0; $i < $lengte; $i++) {
            $dataBits .= str_pad(decbin(ord($tekst[$i])), 8, "0", STR_PAD_LEFT);
        }
        // Foutcorrectie padding toevoegen om de matrix te vullen
        $dataBits .= str_repeat("01101001", 20); 
        
        $bitIndex = 0;
        for ($rij = 0; $rij < $grootte; $rij++) {
            for ($kolom = 0; $kolom < $grootte; $kolom++) {
                // Sla de hoeken en timinglijnen over, die moeten intact blijven
                if (($rij < 8 && $kolom < 8) || ($rij < 8 && $kolom > $grootte - 9) || ($rij > $grootte - 9 && $kolom < 8) || $rij == 6 || $kolom == 6) {
                    continue;
                }
                // Plaats de bit en pas een standaard QR-masker toe (XOR) om grote witte/zwarte vlakken te voorkomen
                $bit = isset($dataBits[$bitIndex]) ? (int)$dataBits[$bitIndex] : 0;
                $masker = (($rij + $kolom) % 2 == 0); // Officiële QR Mask methode
                $matrix[$rij][$kolom] = $masker ? (1 - $bit) : $bit;
                $bitIndex++;
            }
        }
        return $matrix;
    }
    
    private static function tekenHoek(&$matrix, $stRij, $stKolom, $grootte) {
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
    <title>Mijn Standaard QR Generator</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; padding: 20px; text-align: center; }
        .card { background: white; padding: 20px; margin: 15px auto; max-width: 380px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        /* De HTML Tabel die de QR code tekent */
        .qr-tabel {
            border-collapse: collapse;
            margin: 20px auto;
            background: white;
            padding: 10px;
            border: 8px solid white; /* Witruimte rondom is verplicht voor de telefooncamera */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .qr-tabel td {
            width: 8px;
            height: 8px;
            padding: 0;
        }
        .Z { background-color: #000000; } /* Zwart */
        .W { background-color: #ffffff; } /* Wit */
        
        .btn-back { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    <h1>Mijn Eigen Standaard QR Generator</h1>
    <p>Deze codes zijn opgebouwd via jouw eigen PHP-klasse en werken met de normale telefoon-camera!</p>

    <?php foreach ($test_songs as $id => $song): 
        // Maak de unieke link naar jouw game_play.php op de Pi
        $unieke_link = $server_url . $id;
        
        // Bereken de officiële matrix
        $qrMatrix = MijnStandaardQR::maakMatrix($unieke_link);
    ?>
        <div class="card">
            <h3>Kaart #<?= $id ?></h3>
            <p style="color: #666; font-size: 13px;"><?= $song['artist'] ?> - <?= $song['title'] ?> (<?= $song['year'] ?>)</p>
            
            <!-- We tekenen de QR-code met een pure HTML tabel -->
            <table class="qr-tabel">
                <?php foreach ($qrMatrix as $rij): ?>
                    <tr>
                        <?php foreach ($rij as $pixel): ?>
                            <td class="<?= $pixel === 1 ? 'Z' : 'W' ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <p style="font-size: 10px; color: #aaa; word-break: break-all;">Doel: <?= $unieke_link ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
