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

$agents = Agent::getAll();

// Gestion des messages de succès/erreur
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Agents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-900">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white shadow-lg">
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
                    <li>
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
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
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des Agents</h2>
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
                <!-- Message d'alerte -->
                <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <div class="flex items-center">
                        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3"></i>
                        <?php echo $message; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Header with Button -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Liste des Agents</h3>
                    <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
                        <i class="fas fa-plus"></i> Ajouter un Agent
                    </a>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom Complet</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Téléphone</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Fonction</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Profil</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($agents)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p class="mt-2">Aucun agent trouvé</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($agents as $agent): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo $agent->getIdAgent(); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $agent->getNomComplet(); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $agent->getTelephone(); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $agent->getFonction(); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo $agent->getProfil(); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm gap-2 flex">
                                            <a href="edit.php?id=<?php echo $agent->getIdAgent(); ?>" class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $agent->getIdAgent(); ?>" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition" onclick="return confirm('Êtes-vous sûr?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
