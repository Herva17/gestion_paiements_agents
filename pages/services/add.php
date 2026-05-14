<?php
session_start();
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Classes/Service.php';

$service = null;
$is_edit = false;
$title = "Ajouter un Service";
$button_text = "Ajouter";

if (isset($_GET['id'])) {
    $is_edit = true;
    $service = Service::getById($_GET['id']);
    $title = "Éditer le Service";
    $button_text = "Mettre à jour";
    
    if (!$service) {
        $_SESSION['message'] = "Service non trouvé";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = $_POST['designation'] ?? '';
    $description = $_POST['description'] ?? '';
    $short_description = $_POST['short_description'] ?? '';
    $photoPath = $service ? $service->getPhoto() : null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/uploads/services/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = pathinfo($_FILES['photo']['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed)) {
            $error = "Format d'image non autorisé. Utilisez JPG, PNG ou WEBP.";
        } else {
            $filename = uniqid('service_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $photoPath = 'assets/uploads/services/' . $filename;
            } else {
                $error = "Impossible de déplacer l'image téléchargée.";
            }
        }
    }

    if (empty($designation) && !isset($error)) {
        $error = "La désignation est requise";
    }

    if (!isset($error)) {
        if ($is_edit) {
            $service->setDesignation($designation);
            $service->setDescription($description);
            $service->setShortDescription($short_description);
            $service->setPhoto($photoPath);
            
            if ($service->update()) {
                $_SESSION['message'] = "Service mis à jour avec succès";
                $_SESSION['message_type'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $error = "Erreur lors de la mise à jour";
            }
        } else {
            $new_service = new Service($designation, $description, $short_description, $photoPath);
            
            if ($new_service->insert()) {
                $_SESSION['message'] = "Service ajouté avec succès";
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
        <div class="w-64 bg-gray-800 text-white shadow-lg">
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
                        <a href="index.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
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
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <!-- Désignation -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Désignation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="designation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                value="<?php echo $service ? $service->getDesignation() : ''; ?>" required>
                        </div>

                        <!-- Description courte -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description courte
                            </label>
                            <input type="text" name="short_description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                value="<?php echo $service ? htmlspecialchars($service->getShortDescription()) : ''; ?>">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"><?php echo $service ? htmlspecialchars($service->getDescription()) : ''; ?></textarea>
                        </div>

                        <!-- Photo du service -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Photo du service
                            </label>
                            <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" />
                            <?php if ($service && $service->getPhoto()): ?>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Photo actuelle :</p>
                                <img src="../../<?php echo htmlspecialchars($service->getPhoto()); ?>" alt="Photo du service" class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
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
