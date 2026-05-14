<?php

require_once __DIR__ . '/../Config/Database.php';

class Agent {
    private $id_agent;
    private $nom_complet;
    private $adresse;
    private $date_naissance;
    private $telephone;
    private $profil;
    private $lieu_naissance;
    private $fonction;
    private $db;

    // Constructeur
    public function __construct(
        $nom_complet = null,
        $adresse = null,
        $date_naissance = null,
        $telephone = null,
        $profil = null,
        $lieu_naissance = null,
        $fonction = null,
        $id_agent = null
    ) {
        $this->id_agent = $id_agent;
        $this->nom_complet = $nom_complet;
        $this->adresse = $adresse;
        $this->date_naissance = $date_naissance;
        $this->telephone = $telephone;
        $this->profil = $profil;
        $this->lieu_naissance = $lieu_naissance;
        $this->fonction = $fonction;
        $this->db = Database::getInstance();
    }

    // Getters
    public function getIdAgent() {
        return $this->id_agent;
    }

    public function getNomComplet() {
        return $this->nom_complet;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getDateNaissance() {
        return $this->date_naissance;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function getProfil() {
        return $this->profil;
    }

    public function getLieuNaissance() {
        return $this->lieu_naissance;
    }

    public function getFonction() {
        return $this->fonction;
    }

    // Setters
    public function setIdAgent($id_agent) {
        $this->id_agent = $id_agent;
        return $this;
    }

    public function setNomComplet($nom_complet) {
        $this->nom_complet = $nom_complet;
        return $this;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
        return $this;
    }

    public function setDateNaissance($date_naissance) {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function setTelephone($telephone) {
        $this->telephone = $telephone;
        return $this;
    }

    public function setProfil($profil) {
        $this->profil = $profil;
        return $this;
    }

    public function setLieuNaissance($lieu_naissance) {
        $this->lieu_naissance = $lieu_naissance;
        return $this;
    }

    public function setFonction($fonction) {
        $this->fonction = $fonction;
        return $this;
    }

    // Méthode pour obtenir tous les attributs sous forme de tableau
    public function toArray() {
        return [
            'id_agent' => $this->id_agent,
            'nom_complet' => $this->nom_complet,
            'adresse' => $this->adresse,
            'date_naissance' => $this->date_naissance,
            'telephone' => $this->telephone,
            'profil' => $this->profil,
            'lieu_naissance' => $this->lieu_naissance,
            'fonction' => $this->fonction
        ];
    }

    // MÉTHODES DE GESTION DE BASE DE DONNÉES

    /**
     * Insère un nouvel agent dans la base de données
     */
    public function insert() {
        $sql = "INSERT INTO Agent 
                (nom_complet, adresse, date_naissance, telephone, profil, lieu_naissance, fonction)
                VALUES 
                (:nom_complet, :adresse, :date_naissance, :telephone, :profil, :lieu_naissance, :fonction)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':nom_complet', $this->nom_complet);
            $stmt->bindParam(':adresse', $this->adresse);
            $stmt->bindParam(':date_naissance', $this->date_naissance);
            $stmt->bindParam(':telephone', $this->telephone);
            $stmt->bindParam(':profil', $this->profil);
            $stmt->bindParam(':lieu_naissance', $this->lieu_naissance);
            $stmt->bindParam(':fonction', $this->fonction);
            
            if ($stmt->execute()) {
                $this->id_agent = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Met à jour un agent existant
     */
    public function update() {
        $sql = "UPDATE Agent 
                SET nom_complet = :nom_complet, 
                    adresse = :adresse, 
                    date_naissance = :date_naissance, 
                    telephone = :telephone, 
                    profil = :profil, 
                    lieu_naissance = :lieu_naissance, 
                    fonction = :fonction
                WHERE id_agent = :id_agent";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id_agent', $this->id_agent);
            $stmt->bindParam(':nom_complet', $this->nom_complet);
            $stmt->bindParam(':adresse', $this->adresse);
            $stmt->bindParam(':date_naissance', $this->date_naissance);
            $stmt->bindParam(':telephone', $this->telephone);
            $stmt->bindParam(':profil', $this->profil);
            $stmt->bindParam(':lieu_naissance', $this->lieu_naissance);
            $stmt->bindParam(':fonction', $this->fonction);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un agent
     */
    public function delete() {
        $sql = "DELETE FROM Agent WHERE id_agent = :id_agent";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_agent', $this->id_agent);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère un agent par son ID
     */
    public static function getById($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Agent WHERE id_agent = :id_agent";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_agent', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['nom_complet'],
                    $row['adresse'],
                    $row['date_naissance'],
                    $row['telephone'],
                    $row['profil'],
                    $row['lieu_naissance'],
                    $row['fonction'],
                    $row['id_agent']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }

    /**
     * Récupère tous les agents
     */
    public static function getAll() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Agent";
        $agents = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $agent = new self(
                    $row['nom_complet'],
                    $row['adresse'],
                    $row['date_naissance'],
                    $row['telephone'],
                    $row['profil'],
                    $row['lieu_naissance'],
                    $row['fonction'],
                    $row['id_agent']
                );
                $agents[] = $agent;
            }
            return $agents;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>
