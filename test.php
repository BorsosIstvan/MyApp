<?php
if (extension_loaded('pdo_sqlite')) {
    echo "✅ SQLite driver is geïnstalleerd!";
} else {
    echo "❌ SQLite driver MIST. Voer uit: sudo apt install php-sqlite3";
}
?>
