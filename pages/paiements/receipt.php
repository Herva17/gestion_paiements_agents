<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}
if (!in_array($_SESSION['user_role'], ['administrateur', 'caissier'])) {
    $_SESSION['message'] = "Accès réservé au service Paiements";
    $_SESSION['message_type'] = 'error';
    header('Location: ../../Dashboard.php');
    exit;
}
require_once __DIR__ . '/../../Classes/Paiement.php';
require_once __DIR__ . '/../../Classes/Prestation.php';
require_once __DIR__ . '/../../Classes/Affectation.php';
require_once __DIR__ . '/../../Classes/Agent.php';
require_once __DIR__ . '/../../Classes/Service.php';

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

$prestation = Prestation::getById($paiement->getNumeroPrest());
$affectation = $prestation ? Affectation::getById($prestation->getIdAffectation()) : null;
// Prefer agent id stored on paiement
$agent = null;
if ($paiement->getIdAgent()) {
    $agent = Agent::getById($paiement->getIdAgent());
} else {
    $agent = $affectation ? Agent::getById($affectation->getIdAgent()) : null;
}
$service = $affectation ? Service::getById($affectation->getIdService()) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement #<?php echo htmlspecialchars($paiement->getReference()); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto py-6 px-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Reçu de paiement</p>
                <h1 class="text-2xl font-bold text-gray-900">#<?php echo htmlspecialchars($paiement->getReference()); ?></h1>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-200 p-5">
            <div class="mb-5 text-sm text-gray-500">
                Date : <?php echo htmlspecialchars($paiement->getDatePaiement()); ?>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
                <div>
                    <p class="font-semibold text-gray-900">Référence</p>
                    <p>#<?php echo htmlspecialchars($paiement->getReference()); ?></p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Statut</p>
                    <p class="<?php echo $paiement->getStatut() === 'Payé' ? 'text-green-600' : 'text-yellow-600'; ?>"><?php echo htmlspecialchars($paiement->getStatut()); ?></p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <p class="text-xs uppercase tracking-wider text-gray-500">Montant</p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo number_format($paiement->getMontant(), 2, ',', ' '); ?> $</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                <div>
                    <p class="font-semibold text-gray-900">Prestation</p>
                    <p><?php echo $prestation ? htmlspecialchars($prestation->getLibelle()) : 'N/A'; ?></p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Mode de paiement</p>
                    <p><?php echo htmlspecialchars($paiement->getModePaiement()); ?></p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Agent</p>
                    <p><?php echo $agent ? htmlspecialchars($agent->getNomComplet()) : 'N/A'; ?></p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Service</p>
                    <p><?php echo $service ? htmlspecialchars($service->getDesignation()) : 'N/A'; ?></p>
                </div>
            </div>

            <?php if ($prestation && $affectation): ?>
            <div class="mt-5 text-sm text-gray-700">
                <p class="font-semibold text-gray-900">Lieu d'affectation</p>
                <p><?php echo htmlspecialchars($affectation->getLieuAffectation()); ?></p>
            </div>
            <?php endif; ?>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                <div class="border-t border-gray-300 pt-4">
                    <p class="font-semibold text-gray-900 mb-3">Signature de l'agent</p>
                    <div class="h-16 border-b border-gray-300"></div>
                </div>
                <div class="border-t border-gray-300 pt-4">
                    <p class="font-semibold text-gray-900 mb-3">Signature du caissier</p>
                    <div class="h-16 border-b border-gray-300"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
