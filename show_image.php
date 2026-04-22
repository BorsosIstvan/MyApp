<?php
session_start();
if (!isset($_SESSION['loggedin'])) exit; // Beveiliging

$file = $_GET['img'];
$path = "/var/www/html/MyData/uploads/" . basename($file);

if (file_exists($path) && !empty($file)) {
    header('Content-Type: ' . mime_content_type($path));
    readfile($path);
} else {
    header("HTTP/1.0 404 Not Found");
}
?>
