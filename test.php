<?php
$file = 'test_bestand.txt';
if (file_put_contents($file, "Schrijven gelukt!")) {
    echo "✅ Schrijfrechten zijn OK. Bestand is gemaakt.";
    unlink($file); // Ruimt het testbestand weer op
} else {
    echo "❌ GEEN schrijfrechten. Voer de 'chown' commando's opnieuw uit.";
}
?>
