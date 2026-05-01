<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: index.php"); exit; }

$spreadsheet_url = "JOUW_GOOGLE_CSV_LINK_HIER";

$data = [];

if ($spreadsheet_url !== "JOUW_GOOGLE_CSV_LINK_HIER") {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $spreadsheet_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Belangrijk voor Google redirects
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Omzeilt SSL problemen op de Pi
    $content = curl_exec($ch);
    curl_close($ch);

    if ($content) {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $data[] = str_getcsv($line);
        }
        // Verwijder eventuele lege laatste regel
        if (empty(end($data)[0])) array_pop($data);
    }
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Voorraad - Google Sheets</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #e0e6ed; display: flex; justify-content: center; }
        .phone-wrapper { width: 100%; max-width: 450px; min-height: 100vh; background-color: #f4f7f9; box-shadow: 0 0 20px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        
        header { background: #fff; height: 65px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .header-btn { background: #f0f3f7; border: none; padding: 8px 12px; border-radius: 10px; color: #007bff; text-decoration: none; font-size: 14px; font-weight: bold; }
        .header-title { font-size: 16px; font-weight: 800; color: #222; text-transform: uppercase; }

        .content { padding: 15px; flex: 1; }
        
        /* Tabel styling voor mobiel */
        .table-container { background: white; border-radius: 20px; overflow-x: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        table { width: 100%; border-collapse: collapse; min-width: 300px; }
        th { background: #28a745; color: white; padding: 15px 10px; text-align: left; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px 10px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #444; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) { background-color: #fafafa; }

        .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    </style>
</head>
<body>

<div class="phone-wrapper">
    <header>
        <a href="myapp.php" class="header-btn">🏠 Home</a>
        <div class="header-title">VOORRAAD</div>
        <div style="width: 60px;"></div>
    </header>

    <div class="content">
        <?php if (empty($data)): ?>
            <div class="empty-state">
                <div style="font-size: 40px; margin-bottom: 10px;">📊</div>
                <p>Nog geen data gevonden.<br><small>Check of de CSV-link correct is ingesteld.</small></p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($data[0] as $header): ?>
                                <th><?= htmlspecialchars($header) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i < count($data); $i++): ?>
                            <tr>
                                <?php foreach ($data[$i] as $cell): ?>
                                    <td><?= htmlspecialchars($cell) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
