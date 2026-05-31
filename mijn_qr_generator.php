<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

// Bepaal de huidige server URL dynamisch naar jouw mobiele speler-pagina
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
// Gebruik het IP-adres van je Pi of je domeinnaam zodat je telefoon erbij kan
$server_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/game_play.php?id=";

$test_songs = [
    1 => ["artist" => "Michael Jackson", "title" => "Thriller", "year" => 1982],
    2 => ["artist" => "Queen", "title" => "Bohemian Rhapsody", "year" => 1975],
    3 => ["artist" => "Dua Lipa", "title" => "Levitating", "year" => 2020]
];

/**
 * Jouw eigen pure PHP Barcode Generator (Code 128 / Subtype B)
 * Dit zet tekst om in universeel scannbare zwarte en witte lijnen.
 */
function genereerMijnBarcodeLijnen($tekst) {
    // Startteken voor Code 128B
    $tabel = [
        ' ' => '11011001100', '!' => '11001102100', '"' => '11001100210', '#' => '10010011000', 
        '$' => '10010001100', '%' => '10001001100', '&' => '10011001000', "'" => '10011000100', 
        '(' => '10001100100', ')' => '11001001000', '*' => '11001000100', '+' => '11000100100', 
        ',' => '10110011100', '-' => '10011011100', '.' => '10011001110', '/' => '10111001100',
        '0' => '10011100110', '1' => '10011101100', '2' => '11001110100', '3' => '11001110010', 
        '4' => '11011100100', '5' => '11001110100', '6' => '11101101100', '7' => '11101100110', 
        '8' => '11100110110', '9' => '11101101100', ':' => '11100110110', ';' => '11011011000',
        '<' => '11011000110', '=' => '11000110110', '>' => '11010111000', '?' => '11010001110',
        'a' => '10111011000', 'b' => '10111000110', 'c' => '10001101110', 'd' => '10101111000',
        'e' => '10100011110', 'f' => '10001011110', 'g' => '10111101000', 'h' => '10111100010',
        'i' => '10001111010', 'j' => '11101011000', 'k' => '11110101000', 'l' => '11110100010',
        'm' => '11011101000', 'n' => '11011100010', 'o' => '11011101110', 'p' => '11101011100',
        'q' => '11110101100', 'r' => '11100101110', 's' => '11100111010', 't' => '11001111010',
        'u' => '11110111010', 'v' => '11010111100', 'w' => '10110111100', 'x' => '10111101100',
        'y' => '10011011110', 'z' => '10110011110'
    ];

    // Startteken (Code B) + Stopteken patronen
    $startB = "11010010000";
    $stop = "1100011101011";
    
    $barcodeBits = $startB;
    
    // Zet elk karakter van de URL om in het juiste lijnpatroon
    for ($i = 0; $i < strlen($tekst); $i++) {
        $char = $tekst[$i];
        // Als het karakter niet in onze simpele testtabel zit (zoals hoofdletters), pak een spatie
        $barcodeBits .= isset($tabel[$char]) ? $tabel[$char] : $tabel[' '];
    }
    
    $barcodeBits .= $stop;
    return $barcodeBits;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Eigen Barcode Generator</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; padding: 20px; text-align: center; }
        .card { background: white; padding: 25px; margin: 20px auto; max-width: 450px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        /* De container voor de zelfgetekende lijnen */
        .barcode-box {
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 20px;
            margin: 15px 0;
            border: 1px solid #eee;
        }
        /* Een zwart lijntje */
        .lijn-1 { width: 2px; height: 80px; background-color: #000000; }
        /* Een wit lijntje (ruimte) */
        .lijn-0 { width: 2px; height: 80px; background-color: #ffffff; }
        
        .btn-back { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn-back">⬅️ Terug naar Menu</a>
    <h1>Mijn Eigen Universele Code Generator</h1>
    <p>Dit is een handgemaakte streepjescode. Scan deze met je normale telefooncamera!</p>

    <?php foreach ($test_songs as $id => $song): 
        // Maak de link volledig in kleine letters voor onze simpele test-tabel
        $unieke_link = strtolower($server_url . $id);
        
        // Genereer de bit-string (enen en nullen) voor de lijnen
        $lijnBits = genereerMijnBarcodeLijnen($unieke_link);
    ?>
        <div class="card">
            <h3>Kaart #<?= $id ?></h3>
            <p style="color: #666; font-size: 13px;"><?= $song['artist'] ?> - <?= $song['title'] ?> (<?= $song['year'] ?>)</p>
            
            <!-- We tekenen de code door simpelweg divs naast elkaar te zetten via PHP -->
            <div class="barcode-box">
                <?php 
                for ($i = 0; $i < strlen($lijnBits); $i++) {
                    $bit = $lijnBits[$i];
                    echo "<div class='lijn-$bit'></div>";
                }
                ?>
            </div>
            
            <p style="font-size: 11px; color: #888; word-break: break-all;"><?= $unieke_link ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
