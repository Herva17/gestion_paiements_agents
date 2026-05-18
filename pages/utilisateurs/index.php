<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Utilisateur.php';
require_once __DIR__ . '/../../Classes/Agent.php';
require_once __DIR__ . '/../../Classes/Affectation.php';
require_once __DIR__ . '/../../Classes/Prestation.php';
require_once __DIR__ . '/../../Classes/Paiement.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SESSION['user_role'] !== 'administrateur') {
    $_SESSION['message'] = "Accès réservé aux administrateurs";
    $_SESSION['message_type'] = 'error';
    header('Location: ../../Dashboard.php');
    exit;
}

$search = $_GET['search'] ?? '';
$users = Utilisateur::getAll($search);

$agents = Agent::getAll();
$agents_paid = [];
$agents_unpaid = [];

foreach ($agents as $agent) {
    $affectations = Affectation::getByAgent($agent->getIdAgent());
    $hasPrestation = false;
    $allPaid = true;

    foreach ($affectations as $affectation) {
        $prestations = Prestation::getByAffectation($affectation->getId());
        foreach ($prestations as $prestation) {
            $hasPrestation = true;
            $paiements = Paiement::getByPrestation($prestation->getNumeroPrest());
            $prestationPaid = false;

            foreach ($paiements as $paiement) {
                if (strtolower($paiement->getStatut()) === 'payé' || strtolower($paiement->getStatut()) === 'paye') {
                    $prestationPaid = true;
                    break;
                }
            }

            if (!$prestationPaid) {
                $allPaid = false;
                break 2;
            }
        }
    }

    if ($hasPrestation && $allPaid) {
        $agents_paid[] = $agent;
    } else {
        $agents_unpaid[] = $agent;
    }
}

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
    <title>Gestion des Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-900">
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
                            <i class="fas fa-user-lock mr-3"></i>Utilisateurs
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

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center w-full">
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des Utilisateurs</h2>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        </div>
                        <a href="../../logout.php" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Déconnexion</a>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-8">
                <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <div class="flex items-center">
                        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3"></i>
                        <?php echo $message; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Liste des utilisateurs</h3>
                    </div>
                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <form method="GET" class="flex w-full lg:w-auto">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rechercher un utilisateur..." class="w-full lg:w-72 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:border-blue-500" />
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <?php if ($search): ?>
                        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">Effacer</a>
                        <?php endif; ?>
                    </div>
                    <a href="add.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
                        <i class="fas fa-plus"></i> Ajouter un utilisateur
                    </a>
                </div>

                <div class="mb-8 text-sm text-gray-600">
                    <?php if ($search !== ''): ?>
                        Résultats pour "<?php echo htmlspecialchars($search); ?>"
                    <?php else: ?>
                        Tous les utilisateurs
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow-md border border-green-200">
                        <h3 class="text-xl font-semibold text-green-700 mb-3">Agents payés</h3>
                        <p class="text-sm text-gray-600 mb-4">Nombre : <?php echo count($agents_paid); ?></p>
                        <?php if (count($agents_paid) > 0): ?>
                        <ul class="list-disc list-inside text-gray-700 space-y-2">
                            <?php foreach ($agents_paid as $agent): ?>
                            <li><?php echo htmlspecialchars($agent->getNomComplet()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-gray-500">Aucun agent entièrement payé pour le moment.</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-200">
                        <h3 class="text-xl font-semibold text-yellow-700 mb-3">Agents non payés</h3>
                        <p class="text-sm text-gray-600 mb-4">Nombre : <?php echo count($agents_unpaid); ?></p>
                        <?php if (count($agents_unpaid) > 0): ?>
                        <ul class="list-disc list-inside text-gray-700 space-y-2">
                            <?php foreach ($agents_unpaid as $agent): ?>
                            <li><?php echo htmlspecialchars($agent->getNomComplet()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-gray-500">Tous les agents sont à jour de paiement.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom utilisateur</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Rôle</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Créé le</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p class="mt-2">Aucun utilisateur trouvé</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo $user->getIdUtilisateur(); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo htmlspecialchars($user->getNomUtilisateur()); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($user->getRole()); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($user->getDateCreation()); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm gap-2 flex">
                                            <a href="add.php?id=<?php echo $user->getIdUtilisateur(); ?>" class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user->getIdUtilisateur() !== $_SESSION['user_id']): ?>
                                            <a href="delete.php?id=<?php echo $user->getIdUtilisateur(); ?>" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition" onclick="return confirm('Êtes-vous sûr de supprimer cet utilisateur ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
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
