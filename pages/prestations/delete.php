<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Prestation.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$prestation = Prestation::getById($_GET['id']);

if (!$prestation) {
    $_SESSION['message'] = "Prestation non trouvée";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($prestation->delete()) {
    $_SESSION['message'] = "Prestation supprimée avec succès";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Erreur lors de la suppression";
    $_SESSION['message_type'] = 'error';
}

header('Location: index.php');
exit;
?>
