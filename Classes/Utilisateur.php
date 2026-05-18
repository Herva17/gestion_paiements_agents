<?php

require_once __DIR__ . '/../Config/Database.php';

class Utilisateur {
    private $id_utilisateur;
    private $nom_utilisateur;
    private $mot_de_passe;
    private $role;
    private $date_creation;
    private $db;

    public function __construct(
        $nom_utilisateur = null,
        $mot_de_passe = null,
        $role = null,
        $date_creation = null,
        $id_utilisateur = null
    ) {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
        $this->date_creation = $date_creation;
        $this->db = Database::getInstance();
    }

    public function getIdUtilisateur() {
        return $this->id_utilisateur;
    }

    public function getNomUtilisateur() {
        return $this->nom_utilisateur;
    }

    public function getMotDePasse() {
        return $this->mot_de_passe;
    }

    public function getRole() {
        return $this->role;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function setIdUtilisateur($id_utilisateur) {
        $this->id_utilisateur = $id_utilisateur;
        return $this;
    }

    public function setNomUtilisateur($nom_utilisateur) {
        $this->nom_utilisateur = $nom_utilisateur;
        return $this;
    }

    public function setMotDePasse($mot_de_passe) {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function insert() {
        if (empty($this->nom_utilisateur) || empty($this->mot_de_passe) || empty($this->role)) {
            return false;
        }

        $sql = "INSERT INTO utilisateur (nom_utilisateur, mot_de_passe, role) VALUES (:nom_utilisateur, :mot_de_passe, :role)";

        try {
            $stmt = $this->db->prepare($sql);
            $passwordHash = password_hash($this->mot_de_passe, PASSWORD_DEFAULT);
            $stmt->bindParam(':nom_utilisateur', $this->nom_utilisateur);
            $stmt->bindParam(':mot_de_passe', $passwordHash);
            $stmt->bindParam(':role', $this->role);

            if ($stmt->execute()) {
                $this->id_utilisateur = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
            return false;
        }
    }

    public function update() {
        if (empty($this->nom_utilisateur) || empty($this->role) || empty($this->id_utilisateur)) {
            return false;
        }

        $sql = "UPDATE utilisateur SET nom_utilisateur = :nom_utilisateur, role = :role";
        if ($this->mot_de_passe !== null) {
            $sql .= ", mot_de_passe = :mot_de_passe";
        }
        $sql .= " WHERE id_utilisateur = :id_utilisateur";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nom_utilisateur', $this->nom_utilisateur);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':id_utilisateur', $this->id_utilisateur);

            if ($this->mot_de_passe !== null) {
                $passwordHash = password_hash($this->mot_de_passe, PASSWORD_DEFAULT);
                $stmt->bindParam(':mot_de_passe', $passwordHash);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    public function delete() {
        if (empty($this->id_utilisateur)) {
            return false;
        }

        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_utilisateur', $this->id_utilisateur);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_utilisateur', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['nom_utilisateur'],
                    $row['mot_de_passe'],
                    $row['role'],
                    $row['date_creation'],
                    $row['id_utilisateur']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }

    public static function getByUsername($nom_utilisateur) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM utilisateur WHERE nom_utilisateur = :nom_utilisateur LIMIT 1";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['nom_utilisateur'],
                    $row['mot_de_passe'],
                    $row['role'],
                    $row['date_creation'],
                    $row['id_utilisateur']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }

    public static function authenticate($nom_utilisateur, $mot_de_passe) {
        $user = self::getByUsername($nom_utilisateur);
        if (!$user) {
            return null;
        }

        $storedPassword = $user->getMotDePasse();
        if (password_verify($mot_de_passe, $storedPassword) || $mot_de_passe === $storedPassword) {
            return $user;
        }

        return null;
    }

    public static function getAll($search = '') {
        $db = Database::getInstance();
        $sql = "SELECT * FROM utilisateur";
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE nom_utilisateur LIKE :search OR role LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        try {
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new self(
                    $row['nom_utilisateur'],
                    $row['mot_de_passe'],
                    $row['role'],
                    $row['date_creation'],
                    $row['id_utilisateur']
                );
            }
            return $users;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>