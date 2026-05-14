<?php
require_once __DIR__ . '/Database.php';

$db = Database::getInstance();

try {
    $result = $db->query("SHOW COLUMNS FROM Service LIKE 'photo'");
    if ($result->rowCount() === 0) {
        $db->exec("ALTER TABLE Service ADD COLUMN photo VARCHAR(255) NULL DEFAULT NULL");
        echo "Colonne 'photo' ajoutée à la table Service.\n";
    } else {
        echo "La colonne 'photo' existe déjà dans la table Service.\n";
    }

    $result2 = $db->query("SHOW COLUMNS FROM Service LIKE 'short_description'");
    if ($result2->rowCount() === 0) {
        $db->exec("ALTER TABLE Service ADD COLUMN short_description VARCHAR(255) NULL DEFAULT NULL");
        echo "Colonne 'short_description' ajoutée à la table Service.\n";
    } else {
        echo "La colonne 'short_description' existe déjà dans la table Service.\n";
    }
} catch (PDOException $e) {
    echo "Erreur de migration : " . $e->getMessage();
}
?>