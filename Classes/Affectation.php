<?php

require_once __DIR__ . '/../Config/Database.php';

class Affectation {
    private $id;
    private $lieu_affectation;
    private $date_affectation;
    private $id_agent;
    private $id_service;
    private $db;

    // Constructeur
    public function __construct(
        $lieu_affectation = null,
        $date_affectation = null,
        $id_agent = null,
        $id_service = null,
        $id = null
    ) {
        $this->id = $id;
        $this->lieu_affectation = $lieu_affectation;
        $this->date_affectation = $date_affectation;
        $this->id_agent = $id_agent;
        $this->id_service = $id_service;
        $this->db = Database::getInstance();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getLieuAffectation() {
        return $this->lieu_affectation;
    }

    public function getDateAffectation() {
        return $this->date_affectation;
    }

    public function getIdAgent() {
        return $this->id_agent;
    }

    public function getIdService() {
        return $this->id_service;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setLieuAffectation($lieu_affectation) {
        $this->lieu_affectation = $lieu_affectation;
        return $this;
    }

    public function setDateAffectation($date_affectation) {
        $this->date_affectation = $date_affectation;
        return $this;
    }

    public function setIdAgent($id_agent) {
        $this->id_agent = $id_agent;
        return $this;
    }

    public function setIdService($id_service) {
        $this->id_service = $id_service;
        return $this;
    }

    // Méthode pour obtenir tous les attributs sous forme de tableau
    public function toArray() {
        return [
            'id' => $this->id,
            'lieu_affectation' => $this->lieu_affectation,
            'date_affectation' => $this->date_affectation,
            'id_agent' => $this->id_agent,
            'id_service' => $this->id_service
        ];
    }

    // MÉTHODES DE GESTION DE BASE DE DONNÉES

    /**
     * Insère une nouvelle affectation dans la base de données
     */
    public function insert() {
        $sql = "INSERT INTO Affectation (lieu_affectation, date_affectation, id_agent, id_service)
                VALUES (:lieu_affectation, :date_affectation, :id_agent, :id_service)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':lieu_affectation', $this->lieu_affectation);
            $stmt->bindParam(':date_affectation', $this->date_affectation);
            $stmt->bindParam(':id_agent', $this->id_agent);
            $stmt->bindParam(':id_service', $this->id_service);
            
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
     * Met à jour une affectation existante
     */
    public function update() {
        $sql = "UPDATE Affectation 
                SET lieu_affectation = :lieu_affectation, 
                    date_affectation = :date_affectation, 
                    id_agent = :id_agent, 
                    id_service = :id_service
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':lieu_affectation', $this->lieu_affectation);
            $stmt->bindParam(':date_affectation', $this->date_affectation);
            $stmt->bindParam(':id_agent', $this->id_agent);
            $stmt->bindParam(':id_service', $this->id_service);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime une affectation
     */
    public function delete() {
        $sql = "DELETE FROM Affectation WHERE id = :id";
        
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
     * Récupère une affectation par son ID
     */
    public static function getById($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Affectation WHERE id = :id";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['lieu_affectation'],
                    $row['date_affectation'],
                    $row['id_agent'],
                    $row['id_service'],
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
     * Récupère toutes les affectations
     */
    public static function getAll() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Affectation";
        $affectations = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $affectation = new self(
                    $row['lieu_affectation'],
                    $row['date_affectation'],
                    $row['id_agent'],
                    $row['id_service'],
                    $row['id']
                );
                $affectations[] = $affectation;
            }
            return $affectations;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère les affectations par agent
     */
    public static function getByAgent($id_agent) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Affectation WHERE id_agent = :id_agent";
        $affectations = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_agent', $id_agent);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $affectation = new self(
                    $row['lieu_affectation'],
                    $row['date_affectation'],
                    $row['id_agent'],
                    $row['id_service'],
                    $row['id']
                );
                $affectations[] = $affectation;
            }
            return $affectations;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>
