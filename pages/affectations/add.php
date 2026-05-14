<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Affectation.php';
require_once __DIR__ . '/../../Classes/Agent.php';
require_once __DIR__ . '/../../Classes/Service.php';

$affectation = null;
$is_edit = false;
$title = "Ajouter une Affectation";
$button_text = "Ajouter";

$agents = Agent::getAll();
$services = Service::getAll();

if (isset($_GET['id'])) {
    $is_edit = true;
    $affectation = Affectation::getById($_GET['id']);
    $title = "Éditer l'Affectation";
    $button_text = "Mettre à jour";
    
    if (!$affectation) {
        $_SESSION['message'] = "Affectation non trouvée";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lieu_affectation = $_POST['lieu_affectation'] ?? '';
    $date_affectation = $_POST['date_affectation'] ?? '';
    $id_agent = $_POST['id_agent'] ?? '';
    $id_service = $_POST['id_service'] ?? '';

    if (empty($lieu_affectation) || empty($id_agent) || empty($id_service)) {
        $error = "Tous les champs sont requis";
    } else {
        if ($is_edit) {
            $affectation->setLieuAffectation($lieu_affectation);
            $affectation->setDateAffectation($date_affectation);
            $affectation->setIdAgent($id_agent);
            $affectation->setIdService($id_service);
            
            if ($affectation->update()) {
                $_SESSION['message'] = "Affectation mise à jour avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $error = "Erreur lors de la mise à jour";
            }
        } else {
            $new_affectation = new Affectation($lieu_affectation, $date_affectation, $id_agent, $id_service);
            
            if ($new_affectation->insert()) {
                $_SESSION['message'] = "Affectation ajoutée avec succès";
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
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-tasks mr-3"></i>Affectations
                        </a>
                    </li>
                    <li>
                        <a href="../prestations/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
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
                            <!-- Agent -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Agent <span class="text-red-500">*</span>
                                </label>
                                <select name="id_agent" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                    <option value="">Sélectionner un agent</option>
                                    <?php foreach ($agents as $agent): ?>
                                    <option value="<?php echo $agent->getIdAgent(); ?>" <?php echo $affectation && $affectation->getIdAgent() == $agent->getIdAgent() ? 'selected' : ''; ?>>
                                        <?php echo $agent->getNomComplet(); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Service -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Service <span class="text-red-500">*</span>
                                </label>
                                <select name="id_service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                    <option value="">Sélectionner un service</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service->getId(); ?>" <?php echo $affectation && $affectation->getIdService() == $service->getId() ? 'selected' : ''; ?>>
                                        <?php echo $service->getDesignation(); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Lieu -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Lieu d'Affectation <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="lieu_affectation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                    value="<?php echo $affectation ? $affectation->getLieuAffectation() : ''; ?>" required>
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date d'Affectation
                                </label>
                                <input type="date" name="date_affectation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                    value="<?php echo $affectation ? $affectation->getDateAffectation() : ''; ?>">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
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
</body>
</html>
