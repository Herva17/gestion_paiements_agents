<?php

require_once __DIR__ . '/../Config/Database.php';

class Prestation {
    private $numeroPrest;
    private $libelle;
    private $montant;
    private $date_prestation;
    private $id_affectation;
    private $db;

    // Constructeur
    public function __construct(
        $libelle = null,
        $montant = null,
        $date_prestation = null,
        $id_affectation = null,
        $numeroPrest = null
    ) {
        $this->numeroPrest = $numeroPrest;
        $this->libelle = $libelle;
        $this->montant = $montant;
        $this->date_prestation = $date_prestation;
        $this->id_affectation = $id_affectation;
        $this->db = Database::getInstance();
    }

    // Getters
    public function getNumeroPrest() {
        return $this->numeroPrest;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function getMontant() {
        return $this->montant;
    }

    public function getDatePrestation() {
        return $this->date_prestation;
    }

    public function getIdAffectation() {
        return $this->id_affectation;
    }

    // Setters
    public function setNumeroPrest($numeroPrest) {
        $this->numeroPrest = $numeroPrest;
        return $this;
    }

    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    public function setMontant($montant) {
        $this->montant = $montant;
        return $this;
    }

    public function setDatePrestation($date_prestation) {
        $this->date_prestation = $date_prestation;
        return $this;
    }

    public function setIdAffectation($id_affectation) {
        $this->id_affectation = $id_affectation;
        return $this;
    }

    // Méthode pour obtenir tous les attributs sous forme de tableau
    public function toArray() {
        return [
            'numeroPrest' => $this->numeroPrest,
            'libelle' => $this->libelle,
            'montant' => $this->montant,
            'date_prestation' => $this->date_prestation,
            'id_affectation' => $this->id_affectation
        ];
    }

    // MÉTHODES DE GESTION DE BASE DE DONNÉES

    /**
     * Insère une nouvelle prestation dans la base de données
     */
    public function insert() {
        $sql = "INSERT INTO Prestation (libelle, montant, date_prestation, id_affectation)
                VALUES (:libelle, :montant, :date_prestation, :id_affectation)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':libelle', $this->libelle);
            $stmt->bindParam(':montant', $this->montant);
            $stmt->bindParam(':date_prestation', $this->date_prestation);
            $stmt->bindParam(':id_affectation', $this->id_affectation);
            
            if ($stmt->execute()) {
                $this->numeroPrest = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Met à jour une prestation existante
     */
    public function update() {
        $sql = "UPDATE Prestation 
                SET libelle = :libelle, 
                    montant = :montant, 
                    date_prestation = :date_prestation, 
                    id_affectation = :id_affectation
                WHERE numeroPrest = :numeroPrest";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':numeroPrest', $this->numeroPrest);
            $stmt->bindParam(':libelle', $this->libelle);
            $stmt->bindParam(':montant', $this->montant);
            $stmt->bindParam(':date_prestation', $this->date_prestation);
            $stmt->bindParam(':id_affectation', $this->id_affectation);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime une prestation
     */
    public function delete() {
        $sql = "DELETE FROM Prestation WHERE numeroPrest = :numeroPrest";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numeroPrest', $this->numeroPrest);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère une prestation par son numéro
     */
    public static function getById($numeroPrest) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Prestation WHERE numeroPrest = :numeroPrest";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':numeroPrest', $numeroPrest);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['libelle'],
                    $row['montant'],
                    $row['date_prestation'],
                    $row['id_affectation'],
                    $row['numeroPrest']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }

    /**
     * Récupère toutes les prestations
     */
    public static function getAll() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Prestation";
        $prestations = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prestation = new self(
                    $row['libelle'],
                    $row['montant'],
                    $row['date_prestation'],
                    $row['id_affectation'],
                    $row['numeroPrest']
                );
                $prestations[] = $prestation;
            }
            return $prestations;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère les prestations par affectation
     */
    public static function getByAffectation($id_affectation) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Prestation WHERE id_affectation = :id_affectation";
        $prestations = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_affectation', $id_affectation);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prestation = new self(
                    $row['libelle'],
                    $row['montant'],
                    $row['date_prestation'],
                    $row['id_affectation'],
                    $row['numeroPrest']
                );
                $prestations[] = $prestation;
            }
            return $prestations;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>
