<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}
if ($_SESSION['user_role'] !== 'administrateur') {
    $_SESSION['message'] = "Accès réservé aux administrateurs";
    $_SESSION['message_type'] = 'error';
    header('Location: ../paiements/index.php');
    exit;
}
require_once __DIR__ . '/../../Classes/Agent.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$agent = Agent::getById($_GET['id']);

if (!$agent) {
    $_SESSION['message'] = "Agent non trouvé";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($agent->delete()) {
    $_SESSION['message'] = "Agent supprimé avec succès";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Erreur lors de la suppression";
    $_SESSION['message_type'] = 'error';
}

header('Location: index.php');
exit;
?>
