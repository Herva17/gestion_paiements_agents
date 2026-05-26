<?php
session_start();
require_once __DIR__ . '/Config/Database.php';
require_once __DIR__ . '/Classes/Utilisateur.php';

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'caissier') {
        header('Location: pages/paiements/index.php');
    } else {
        header('Location: Dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($nom_utilisateur === '' || $mot_de_passe === '') {
        $error = 'Veuillez renseigner le nom d\'utilisateur et le mot de passe.';
    } else {
        $user = Utilisateur::authenticate($nom_utilisateur, $mot_de_passe);

        if ($user) {
            $_SESSION['user_id'] = $user->getIdUtilisateur();
            $_SESSION['username'] = $user->getNomUtilisateur();
            $_SESSION['user_role'] = $user->getRole();

            if ($user->getRole() === 'caissier') {
                header('Location: pages/paiements/index.php');
            } else {
                header('Location: Dashboard.php');
            }
            exit;
        }
        $error = 'Nom d\'utilisateur ou mot de passe invalide.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Connexion</h1>
            <p class="text-gray-600 mt-2">Accédez à votre espace administrateur ou caissier.</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom d'utilisateur</label>
                <input type="text" name="nom_utilisateur" value="<?php echo isset($_POST['nom_utilisateur']) ? htmlspecialchars($_POST['nom_utilisateur']) : ''; ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-blue-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-blue-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-2xl transition">Se connecter</button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Rôle admis : administrateur / caissier</p>
        </div>
    </div>
</body>
</html>
