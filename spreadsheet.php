<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

// PLAK HIER DE LINK DIE JE VAN GOOGLE HEBT GEKOPIEERD
$spreadsheet_url = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQkke25VqYtWDNH9rcTBu1uKNaioH4n4kPQU5CA48S-0IX7Z_fFh1dAyKfhnvCX2qUmw-vQzNu8qQGa/pub?output=csv";

$data = [];
if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Data</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        header { background: #fff; height: 65px; display: flex; align-items: center; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; text-decoration: none; font-weight: bold; }
        .content { padding: 15px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; font-size: 14px; }
        th { background: #007bff; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <div class="phone-wrapper">
        <header>
            <a href="myapp.php" class="header-btn">🏠 Home</a>
            <div style="flex:1; text-align:center; font-weight:800;">SPREADSHEET</div>
        </header>

        <div class="content">
            <table>
                <?php foreach ($data as $index => $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <?php if ($index === 0): ?>
                                <th><?= htmlspecialchars($cell) ?></th>
                            <?php else: ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
