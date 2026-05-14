<?php
session_start();
require_once __DIR__ . '/Config/Database.php';
require_once __DIR__ . '/Classes/Agent.php';
require_once __DIR__ . '/Classes/Service.php';
require_once __DIR__ . '/Classes/Affectation.php';
require_once __DIR__ . '/Classes/Prestation.php';
require_once __DIR__ . '/Classes/Paiement.php';

// Récupérer les statistiques
$agents = Agent::getAll();
$services = Service::getAll();
$affectations = Affectation::getAll();
$prestations = Prestation::getAll();
$paiements = Paiement::getAll();

$totalAgents = count($agents);
$totalServices = count($services);
$totalAffectations = count($affectations);
$totalPrestations = count($prestations);
$totalPaiements = count($paiements);

// Calculer le montant total des paiements
$totalMontantPaiements = 0;
foreach ($paiements as $paiement) {
    $totalMontantPaiements += $paiement->getMontant();
}

// Calculer le montant total des prestations
$totalMontantPrestations = 0;
foreach ($prestations as $prestation) {
    $totalMontantPrestations += $prestation->getMontant();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Paiements - Dashboard</title>
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
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-chart-line mr-3"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="pages/agents/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-users mr-3"></i>Agents
                        </a>
                    </li>
                    <li>
                        <a href="pages/services/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-cogs mr-3"></i>Services
                        </a>
                    </li>
                    <li>
                        <a href="pages/affectations/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-tasks mr-3"></i>Affectations
                        </a>
                    </li>
                    <li>
                        <a href="pages/prestations/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-briefcase-medical mr-3"></i>Prestations
                        </a>
                    </li>
                    <li>
                        <a href="pages/paiements/index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition">
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
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
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

            <!-- Dashboard Content -->
            <div class="flex-1 overflow-auto p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <!-- Total Agents -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Agents</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalAgents; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Services -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Services</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalServices; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cogs text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Affectations -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Affectations</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalAffectations; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Prestations -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Prestations</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalPrestations; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-briefcase text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Paiements -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Paiements</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalPaiements; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-credit-card text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Montant Total Prestations -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Montant Total Prestations</h3>
                        <div class="text-4xl font-bold text-green-600 mb-2">
                            <?php echo number_format($totalMontantPrestations, 2, ',', ' '); ?> €
                        </div>
                        <p class="text-gray-600 text-sm">Somme de toutes les prestations</p>
                    </div>

                    <!-- Montant Total Paiements -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Montant Total Paiements</h3>
                        <div class="text-4xl font-bold text-blue-600 mb-2">
                            <?php echo number_format($totalMontantPaiements, 2, ',', ' '); ?> €
                        </div>
                        <p class="text-gray-600 text-sm">Somme de tous les paiements effectués</p>
                    </div>
                </div>

                <!-- Recent Paiements -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">Paiements Récents</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Mode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_paiements = array_slice($paiements, -5);
                                foreach ($recent_paiements as $paiement) {
                                    $statut_class = $paiement->getStatut() === 'Payé' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">#<?php echo $paiement->getReference(); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800"><?php echo number_format($paiement->getMontant(), 2, ',', ' '); ?> €</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $paiement->getModePaiement(); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statut_class; ?>">
                                            <?php echo $paiement->getStatut(); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $paiement->getDatePaiement(); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
