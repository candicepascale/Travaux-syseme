<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: panier.php");
    exit;
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if (isset($_SESSION['panier'][$index])) {
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']);
}

header("Location: panier.php");
exit;
