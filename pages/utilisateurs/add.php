<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Utilisateur.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SESSION['user_role'] !== 'administrateur') {
    $_SESSION['message'] = "Accès réservé aux administrateurs";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$user = null;
$is_edit = false;
$title = "Ajouter un utilisateur";
$button_text = "Ajouter";
$error = null;

if (isset($_GET['id'])) {
    $is_edit = true;
    $user = Utilisateur::getById($_GET['id']);
    $title = "Éditer l'utilisateur";
    $button_text = "Mettre à jour";

    if (!$user) {
        $_SESSION['message'] = "Utilisateur non trouvé";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($nom_utilisateur === '' || $role === '') {
        $error = "Le nom d'utilisateur et le rôle sont requis.";
    } else {
        if ($is_edit) {
            $user->setNomUtilisateur($nom_utilisateur);
            $user->setRole($role);

            if ($mot_de_passe !== '') {
                $user->setMotDePasse($mot_de_passe);
            } else {
                $user->setMotDePasse(null);
            }

            if ($user->update()) {
                $_SESSION['message'] = "Utilisateur mis à jour avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            }
            // Vérifier si le nom d'utilisateur est déjà utilisé par un autre
            $existing = Utilisateur::getByUsername($nom_utilisateur);
            if ($existing && $existing->getIdUtilisateur() !== $user->getIdUtilisateur()) {
                $error = "Le nom d'utilisateur est déjà utilisé par un autre compte.";
            } else {
                $error = "Erreur lors de la mise à jour de l'utilisateur.";
            }
        } else {
            $newUser = new Utilisateur($nom_utilisateur, $mot_de_passe, $role);
            if ($newUser->insert()) {
                $_SESSION['message'] = "Utilisateur ajouté avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            }
            // Vérifier si l'utilisateur existe déjà (contrainte unique)
            $existing = Utilisateur::getByUsername($nom_utilisateur);
            if ($existing) {
                $error = "Le nom d'utilisateur existe déjà.";
            } else {
                $error = "Erreur lors de l'ajout de l'utilisateur.";
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
                            <i class="fas fa-user-lock mr-3"></i>Utilisateurs
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
                </ul>
            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center w-full">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h2>
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
                <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <?php echo $error; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="max-w-2xl bg-white rounded-lg shadow-md p-8">
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nom d'utilisateur <span class="text-red-500">*</span></label>
                            <input type="text" name="nom_utilisateur" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="<?php echo $user ? htmlspecialchars($user->getNomUtilisateur()) : ''; ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe <?php echo $is_edit ? '(laisser vide pour conserver)' : '<span class="text-red-500">*</span>'; ?></label>
                            <input type="password" name="mot_de_passe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" <?php echo $is_edit ? '' : 'required'; ?> placeholder="<?php echo $is_edit ? 'Laissez vide pour conserver le mot de passe actuel' : 'Mot de passe'; ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rôle <span class="text-red-500">*</span></label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                <option value="">Sélectionner un rôle</option>
                                <option value="administrateur" <?php echo $user && $user->getRole() === 'administrateur' ? 'selected' : ''; ?>>Administrateur</option>
                                <option value="caissier" <?php echo $user && $user->getRole() === 'caissier' ? 'selected' : ''; ?>>Caissier</option>
                            </select>
                        </div>

                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
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
