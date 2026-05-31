<?php
session_start();

// Functie die een uniek 5x5 raster genereert op basis van een Liedje ID
function genereerMijnMatrix($id) {
    // We maken een leeg raster van 5x5 (allemaal witte blokjes = 0)
    $matrix = array_fill(0, 5, array_fill(0, 5, 0));
    
    // Vaste herkenningspunten in de hoeken (zodat de scanner de code herkent)
    $matrix[0][0] = 1; // Links-boven zwart
    $matrix[0][4] = 1; // Rechts-boven zwart
    $matrix[4][0] = 1; // Links-onder zwart
    
    // Zet het ID om naar een 5-bits binaire string (maximaal ID 31 voor deze simpele test)
    // Bijvoorbeeld: ID 3 wordt "00011"
    $binair = str_pad(decbin($id), 5, "0", STR_PAD_LEFT);
    
    // We plaatsen deze 5 bits in het midden van ons raster (rij index 2)
    for ($i = 0; $i < 5; $i++) {
        $matrix[2][$i] = (int)$binair[$i];
    }
    
    // Voeg een controle-bit toe op de bodem (rij index 4, kolom index 4) voor validatie
    $matrix[4][4] = ($id % 2 === 0) ? 0 : 1;

    return $matrix;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Eigen Code Generator</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #e0e6ed; text-align: center; padding: 20px; }
        .card { background: white; padding: 25px; margin: 20px auto; max-width: 350px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        /* Het raster voor onze eigen code */
        .mijn-code-raster {
            display: grid;
            grid-template-columns: repeat(5, 30px);
            grid-template-rows: repeat(5, 30px);
            gap: 2px;
            justify-content: center;
            background-color: #ccc;
            padding: 5px;
            width: 158px;
            margin: 20px auto;
            border: 4px solid #222;
        }
        
        /* De blokjes (pixels) */
        .pixel { width: 30px; height: 30px; }
        .zwart { background-color: #111111; }
        .wit { background-color: #ffffff; }
        
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; display: inline-block; }
    </style>
</head>
<body>

    <a href="myapp.php" class="btn">⬅️ Menu</a>
    <h1>Mijn Eigen Code Generator</h1>
    <p>Dit zijn handgemaakte, unieke codes gegenereerd door jouw eigen algoritme!</p>

    <?php
    // Testdata met 3 liedjes
    $liedjes = [
        1 => "Michael Jackson - Thriller",
        2 => "Queen - Bohemian Rhapsody",
        3 => "Dua Lipa - Levitating"
    ];

    foreach ($liedjes as $id => $naam): 
        // Genereer de matrix voor dit specifieke ID
        $mijnMatrix = genereerMijnMatrix($id);
    ?>
        <div class="card">
            <h3>Liedje #<?= $id ?></h3>
            <p style="font-size: 14px; color: #555;"><?= $naam ?></p>
            
            <!-- Teken het raster op het scherm -->
            <div class="mijn-code-raster">
                <?php for ($rij = 0; $rij < 5; $rij++): ?>
                    <?php for ($kolom = 0; $kolom < 5; $kolom++): ?>
                        <?php $klasse = ($mijnMatrix[$rij][$kolom] === 1) ? 'zwart' : 'wit'; ?>
                        <div class="pixel <?= $klasse ?>"></div>
                    <?php endfor; ?>
                <?php endfor; ?>
            </div>
            
            <p style="font-size: 11px; color: #999;">Code data: <?= decbin($id) ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
