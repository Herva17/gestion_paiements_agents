<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Paiement.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$paiement = Paiement::getById($_GET['id']);

if (!$paiement) {
    $_SESSION['message'] = "Paiement non trouvé";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($paiement->delete()) {
    $_SESSION['message'] = "Paiement supprimé avec succès";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Erreur lors de la suppression";
    $_SESSION['message_type'] = 'error';
}

header('Location: index.php');
exit;
?>
