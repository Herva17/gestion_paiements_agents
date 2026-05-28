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

$role = $_SESSION['user_role'] ?? 'Invité';

$paiement = null;
$is_edit = false;
$title = "Ajouter un Paiement";
$button_text = "Ajouter";

$prestations = Prestation::getAll();

if (isset($_GET['id'])) {
    $is_edit = true;
    $paiement = Paiement::getById($_GET['id']);
    $title = "Éditer le Paiement";
    $button_text = "Mettre à jour";
    
    if (!$paiement) {
        $_SESSION['message'] = "Paiement non trouvé";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'] ?? '';
    $montant = $_POST['montant'] ?? '';
    $date_paiement = $_POST['date_paiement'] ?? '';
    $mode_paiement = $_POST['mode_paiement'] ?? '';
    $statut = $_POST['statut'] ?? '';
    $numeroPrest = $_POST['numeroPrest'] ?? '';
    $id_agent = $_POST['id_agent'] ?? '';

    if (empty($reference) || empty($numeroPrest)) {
        $error = "Les champs requis doivent être remplis";
    } else {
        if ($is_edit) {
            $paiement->setReference($reference);
            $paiement->setMontant($montant);
            $paiement->setDatePaiement($date_paiement);
            $paiement->setModePaiement($mode_paiement);
            $paiement->setStatut($statut);
            $paiement->setNumeroPrest($numeroPrest);
            $paiement->setIdAgent($id_agent);
            
            if ($paiement->update()) {
                $_SESSION['message'] = "Paiement mis à jour avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $error = "Erreur lors de la mise à jour";
            }
        } else {
            $new_paiement = new Paiement($reference, $montant, $date_paiement, $mode_paiement, $statut, $numeroPrest, $id_agent);
            
            if ($new_paiement->insert()) {
                $_SESSION['message'] = "Paiement ajouté avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $error = "Erreur lors de l'ajout";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-900">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white shadow-lg overflow-y-auto">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-briefcase mr-2"></i>Gestion Paiements
                </h1>
            </div>
            
            <nav class="mt-6 px-4">
                <ul class="space-y-2">
                    <li>
                        <a href="../../Dashboard.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-chart-line mr-3"></i>Dashboard
                        </a>
                    </li>
                    <?php if ($role === 'administrateur'): ?>
                    <li>
                        <a href="../agents/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-users mr-3"></i>Agents
                        </a>
                    </li>
                    <li>
                        <a href="../services/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-cogs mr-3"></i>Services
                        </a>
                    </li>
                    <li>
                        <a href="../affectations/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-tasks mr-3"></i>Affectations
                        </a>
                    </li>
                    <li>
                        <a href="../prestations/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-briefcase-medical mr-3"></i>Prestations
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-credit-card mr-3"></i>Paiements
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <div class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center w-full">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h2>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                        <a href="../../logout.php" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Déconnexion</a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto p-8">
                <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <?php echo $error; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Form -->
                <div class="max-w-2xl bg-white rounded-lg shadow-md p-8">
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Référence -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Référence <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="reference" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    value="<?php echo $paiement ? $paiement->getReference() : ''; ?>" required>
                            </div>

                            <!-- Prestation -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Prestation <span class="text-red-500">*</span>
                                </label>
                                <select name="numeroPrest" id="numeroPrest" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                    <option value="">Sélectionner une prestation</option>
                                    <?php foreach ($prestations as $prestation):
                                        $agentName = 'N/A';
                                        $agentId = '';
                                        $aff = Affectation::getById($prestation->getIdAffectation());
                                        if ($aff) {
                                            $agentObj = Agent::getById($aff->getIdAgent());
                                            if ($agentObj) {
                                                $agentName = $agentObj->getNomComplet();
                                                $agentId = $agentObj->getIdAgent();
                                            }
                                        }
                                    ?>
                                    <option value="<?php echo $prestation->getNumeroPrest(); ?>" data-agent="<?php echo htmlspecialchars($agentName); ?>" data-agent-id="<?php echo htmlspecialchars($agentId); ?>" <?php echo $paiement && $paiement->getNumeroPrest() == $prestation->getNumeroPrest() ? 'selected' : ''; ?> >
                                        Prestation #<?php echo $prestation->getNumeroPrest(); ?> - <?php echo htmlspecialchars($prestation->getLibelle()); ?> - Agent: <?php echo htmlspecialchars($agentName); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                                <!-- Agent associé (lecture seule) -->
                                <div class="mt-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agent associé</label>
                                    <input type="text" id="agentName" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly value="<?php
                                        $initialAgent = '';
                                        $initialAgentId = '';
                                        if ($paiement) {
                                            $selPrest = Prestation::getById($paiement->getNumeroPrest());
                                            if ($selPrest) {
                                                $affSel = Affectation::getById($selPrest->getIdAffectation());
                                                if ($affSel) {
                                                    $agSel = Agent::getById($affSel->getIdAgent());
                                                    if ($agSel) {
                                                        $initialAgent = $agSel->getNomComplet();
                                                        $initialAgentId = $agSel->getIdAgent();
                                                    }
                                                }
                                            }
                                        }
                                        echo htmlspecialchars($initialAgent);
                                    ?>">
                                    <input type="hidden" name="id_agent" id="id_agent" value="<?php echo htmlspecialchars($initialAgentId); ?>">
                                </div>
                            </div>

                            <!-- Montant -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Montant ($)
                                </label>
                                <input type="number" name="montant" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    value="<?php echo $paiement ? $paiement->getMontant() : ''; ?>" step="0.01">
                            </div>

                            <!-- Mode de paiement -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Mode de Paiement
                                </label>
                                <select name="mode_paiement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                    <option value="">Sélectionner un mode</option>
                                    <option value="Virement" <?php echo $paiement && $paiement->getModePaiement() === 'Virement' ? 'selected' : ''; ?>>Virement</option>
                                    <option value="Chèque" <?php echo $paiement && $paiement->getModePaiement() === 'Chèque' ? 'selected' : ''; ?>>Chèque</option>
                                    <option value="Espèces" <?php echo $paiement && $paiement->getModePaiement() === 'Espèces' ? 'selected' : ''; ?>>Espèces</option>
                                    <option value="Carte" <?php echo $paiement && $paiement->getModePaiement() === 'Carte' ? 'selected' : ''; ?>>Carte</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Statut
                                </label>
                                <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                    <option value="">Sélectionner un statut</option>
                                    <option value="Payé" <?php echo $paiement && $paiement->getStatut() === 'Payé' ? 'selected' : ''; ?>>Payé</option>
                                    <option value="En attente" <?php echo $paiement && $paiement->getStatut() === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                                    <option value="Annulé" <?php echo $paiement && $paiement->getStatut() === 'Annulé' ? 'selected' : ''; ?>>Annulé</option>
                                </select>
                            </div>

                            <!-- Date du paiement -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date du Paiement
                                </label>
                                <input type="date" name="date_paiement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                    value="<?php echo $paiement ? $paiement->getDatePaiement() : ''; ?>">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
                                <i class="fas fa-save"></i> <?php echo $button_text; ?>
                            </button>
                            <a href="index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prestSelect = document.getElementById('numeroPrest');
            const agentInput = document.getElementById('agentName');
            const agentIdInput = document.getElementById('id_agent');
            if (!prestSelect || !agentInput || !agentIdInput) return;
            function updateAgent() {
                const opt = prestSelect.options[prestSelect.selectedIndex];
                agentInput.value = opt ? (opt.getAttribute('data-agent') || '') : '';
                agentIdInput.value = opt ? (opt.getAttribute('data-agent-id') || '') : '';
            }
            prestSelect.addEventListener('change', updateAgent);
            updateAgent();
        });
    </script>
</body>
</html>
