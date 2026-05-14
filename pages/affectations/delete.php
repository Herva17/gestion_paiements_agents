<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Affectation.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$affectation = Affectation::getById($_GET['id']);

if (!$affectation) {
    $_SESSION['message'] = "Affectation non trouvée";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($affectation->delete()) {
    $_SESSION['message'] = "Affectation supprimée avec succès";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Erreur lors de la suppression";
    $_SESSION['message_type'] = 'error';
}

header('Location: index.php');
exit;
?>
