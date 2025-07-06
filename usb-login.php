<?php
session_start();
$expectedSecret = "putanginamogagoka123"; // Put your real secret here

if (!isset($_FILES['usbfile']) || $_FILES['usbfile']['error'] !== UPLOAD_ERR_OK) {
    header("Location: login.html?error=1");
    exit;
}
$fileContent = trim(file_get_contents($_FILES['usbfile']['tmp_name']));

if ($fileContent === $expectedSecret) {
    $_SESSION['logged_in'] = true;
    header("Location: index.php");
    exit;
} else {
    header("Location: login.html?error=1");
    exit;
}
?>