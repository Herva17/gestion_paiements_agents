<?php

require_once __DIR__ . '/../Config/Database.php';

class Service {
    private $id;
    private $designation;
    private $description;
    private $short_description;
    private $photo;
    private $db;

    // Constructeur
    public function __construct(
        $designation = null,
        $description = null,
        $short_description = null,
        $photo = null,
        $id = null
    ) {
        $this->id = $id;
        $this->designation = $designation;
        $this->description = $description;
        $this->short_description = $short_description;
        $this->photo = $photo;
        $this->db = Database::getInstance();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getDesignation() {
        return $this->designation;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getShortDescription() {
        return $this->short_description;
    }

    public function getPhoto() {
        return $this->photo;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setDesignation($designation) {
        $this->designation = $designation;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function setShortDescription($short_description) {
        $this->short_description = $short_description;
        return $this;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
        return $this;
    }

    // Méthode pour obtenir tous les attributs sous forme de tableau
    public function toArray() {
        return [
            'id' => $this->id,
            'designation' => $this->designation,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'photo' => $this->photo
        ];
    }

    // MÉTHODES DE GESTION DE BASE DE DONNÉES

    /**
     * Insère un nouveau service dans la base de données
     */
    public function insert() {
        $sql = "INSERT INTO Service (designation, description, short_description, photo)
                VALUES (:designation, :description, :short_description, :photo)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':designation', $this->designation);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':short_description', $this->short_description);
            $stmt->bindParam(':photo', $this->photo);
            
            if ($stmt->execute()) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Met à jour un service existant
     */
    public function update() {
        $sql = "UPDATE Service 
                SET designation = :designation, 
                    description = :description,
                    short_description = :short_description,
                    photo = :photo
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':designation', $this->designation);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':short_description', $this->short_description);
            $stmt->bindParam(':photo', $this->photo);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un service
     */
    public function delete() {
        $sql = "DELETE FROM Service WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère un service par son ID
     */
    public static function getById($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Service WHERE id = :id";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['designation'],
                    $row['description'],
                    $row['short_description'] ?? null,
                    $row['photo'] ?? null,
                    $row['id']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }

    /**
     * Récupère tous les services
     */
    public static function getAll($search = null) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Service";
        $services = [];
        
        if ($search) {
            $sql .= " WHERE designation LIKE :search1 OR description LIKE :search2 OR short_description LIKE :search3";
        }
        
        try {
            $stmt = $db->prepare($sql);
            if ($search) {
                $queryValue = '%' . $search . '%';
                $stmt->bindParam(':search1', $queryValue);
                $stmt->bindParam(':search2', $queryValue);
                $stmt->bindParam(':search3', $queryValue);
            }
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $service = new self(
                    $row['designation'],
                    $row['description'],
                    $row['short_description'] ?? null,
                    $row['photo'] ?? null,
                    $row['id']
                );
                $services[] = $service;
            }
            return $services;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>
