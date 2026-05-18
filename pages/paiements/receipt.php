<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}
if (!in_array($_SESSION['user_role'], ['administrateur', 'comptable'])) {
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
$agent = $affectation ? Agent::getById($affectation->getIdAgent()) : null;
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
    <div class="max-w-5xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="text-sm text-gray-500">Reçu de Paiement</p>
                <h1 class="text-3xl font-bold text-gray-900">#<?php echo htmlspecialchars($paiement->getReference()); ?></h1>
            </div>
            <div class="flex gap-3">
                <a href="index.php" class="px-5 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <button onclick="window.print()" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
            <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                <div class="flex justify-between items-start gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold">Reçu de paiement</h2>
                        <p class="mt-2 text-sm text-blue-100">Merci pour votre règlement. Voici le détail du paiement.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wider text-blue-100">Date du reçu</p>
                        <p class="mt-1 text-xl font-bold"><?php echo htmlspecialchars($paiement->getDatePaiement()); ?></p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Référence</p>
                        <p class="text-lg font-semibold text-gray-900">#<?php echo htmlspecialchars($paiement->getReference()); ?></p>
                    </div>
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Statut</p>
                        <p class="text-lg font-semibold <?php echo $paiement->getStatut() === 'Payé' ? 'text-green-600' : 'text-yellow-600'; ?>"><?php echo htmlspecialchars($paiement->getStatut()); ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Montant</p>
                        <p class="mt-3 text-3xl font-bold text-gray-900"><?php echo number_format($paiement->getMontant(), 2, ',', ' '); ?> €</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Mode de paiement</p>
                        <p class="mt-3 text-lg text-gray-800"><?php echo htmlspecialchars($paiement->getModePaiement()); ?></p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Prestation</p>
                        <p class="mt-3 text-lg text-gray-800"><?php echo $prestation ? htmlspecialchars($prestation->getLibelle()) : 'N/A'; ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Agent</p>
                        <p class="mt-3 text-lg text-gray-800"><?php echo $agent ? htmlspecialchars($agent->getNomComplet()) : 'N/A'; ?></p>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $agent ? htmlspecialchars($agent->getFonction()) : ''; ?></p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs uppercase tracking-wider text-gray-500">Service</p>
                        <p class="mt-3 text-lg text-gray-800"><?php echo $service ? htmlspecialchars($service->getDesignation()) : 'N/A'; ?></p>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $service ? htmlspecialchars($service->getDescription()) : ''; ?></p>
                    </div>
                </div>

                <?php if ($prestation && $affectation): ?>
                <div class="mt-10 rounded-2xl border border-gray-200 p-6 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Détails de la prestation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nombre d'heures</p>
                            <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($prestation->getNbreHeure()); ?> h</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Salaire horaire</p>
                            <p class="text-lg font-semibold text-gray-800"><?php echo number_format($prestation->getSalaireHoraire(), 2, ',', ' '); ?> €</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Lieu d'affectation</p>
                            <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($affectation->getLieuAffectation()); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
