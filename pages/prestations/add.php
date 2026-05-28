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
require_once __DIR__ . '/../../Classes/Prestation.php';
require_once __DIR__ . '/../../Classes/Affectation.php';
require_once __DIR__ . '/../../Classes/Agent.php';
require_once __DIR__ . '/../../Classes/Service.php';

$prestation = null;
$is_edit = false;
$title = "Ajouter une Prestation";
$button_text = "Ajouter";

$affectations = Affectation::getAll();

if (isset($_GET['id'])) {
    $is_edit = true;
    $prestation = Prestation::getById($_GET['id']);
    $title = "Éditer la Prestation";
    $button_text = "Mettre à jour";
    
    if (!$prestation) {
        $_SESSION['message'] = "Prestation non trouvée";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = $_POST['libelle'] ?? '';
    $montant = $_POST['montant'] ?? '';
    $date_prestation = $_POST['date_prestation'] ?? '';
    $id_affectation = $_POST['id_affectation'] ?? '';

    if (empty($libelle) || empty($montant) || empty($id_affectation)) {
        $error = "Les champs requis doivent être remplis";
    } else {
        if ($is_edit) {
            $prestation->setLibelle($libelle);
            $prestation->setMontant($montant);
            $prestation->setDatePrestation($date_prestation);
            $prestation->setIdAffectation($id_affectation);
            
            if ($prestation->update()) {
                $_SESSION['message'] = "Prestation mise à jour avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $error = "Erreur lors de la mise à jour";
            }
        } else {
            $new_prestation = new Prestation($libelle, $montant, $date_prestation, $id_affectation);
            
            if ($new_prestation->insert()) {
                $_SESSION['message'] = "Prestation ajoutée avec succès";
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
                        <a href="../../index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-chart-line mr-3"></i>Dashboard
                        </a>
                    </li>
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
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-briefcase-medical mr-3"></i>Prestations
                        </a>
                    </li>
                    <li>
                        <a href="../paiements/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
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
                            <p class="text-sm text-gray-600">Administrator</p>
                            <p class="font-semibold text-gray-800">Bienvenue</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
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
                            <!-- Libellé -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Libellé <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="libelle" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    value="<?php echo $prestation ? $prestation->getLibelle() : ''; ?>" required>
                            </div>

                            <!-- Affectation -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Affectation <span class="text-red-500">*</span>
                                </label>
                                <select id="id_affectation" name="id_affectation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                    <option value="">Sélectionner une affectation</option>
                                    <?php foreach ($affectations as $affectation):
                                        $agentName = 'N/A';
                                        $serviceName = '';
                                        $agentObj = Agent::getById($affectation->getIdAgent());
                                        if ($agentObj) $agentName = $agentObj->getNomComplet();
                                        $serviceObj = Service::getById($affectation->getIdService());
                                        if ($serviceObj) $serviceName = $serviceObj->getDesignation();
                                    ?>
                                    <option value="<?php echo $affectation->getId(); ?>" data-agent="<?php echo htmlspecialchars($agentName); ?>" data-service="<?php echo htmlspecialchars($serviceName); ?>" <?php echo $prestation && $prestation->getIdAffectation() == $affectation->getId() ? 'selected' : ''; ?> >
                                        Affectation #<?php echo $affectation->getId(); ?> - <?php echo htmlspecialchars($agentName); ?> (<?php echo htmlspecialchars($serviceName); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                                <!-- Agent associé (lecture seule) -->
                                <div class="mt-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agent associé</label>
                                    <input type="text" id="agentName" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly value="<?php
                                        $initialAgent = '';
                                        if ($prestation) {
                                            $affSel = Affectation::getById($prestation->getIdAffectation());
                                            if ($affSel) {
                                                $agSel = Agent::getById($affSel->getIdAgent());
                                                if ($agSel) $initialAgent = $agSel->getNomComplet();
                                            }
                                        }
                                        echo htmlspecialchars($initialAgent);
                                    ?>">
                                </div>
                            </div>

                            <!-- Montant -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Montant ($) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="montant" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                value="<?php echo $prestation ? $prestation->getMontant() : ''; ?>" step="0.01" required>

                            <!-- Date -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date de la Prestation
                                </label>
                                <input type="date" name="date_prestation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                    value="<?php echo $prestation ? $prestation->getDatePrestation() : ''; ?>">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
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
            const select = document.getElementById('id_affectation');
            const agentInput = document.getElementById('agentName');
            if (!select || !agentInput) return;
            function updateAgent() {
                const opt = select.options[select.selectedIndex];
                agentInput.value = opt ? (opt.getAttribute('data-agent') || '') : '';
            }
            select.addEventListener('change', updateAgent);
            updateAgent();
        });
    </script>
</body>
</html>
