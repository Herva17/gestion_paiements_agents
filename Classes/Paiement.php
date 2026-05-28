<?php

require_once __DIR__ . '/../Config/Database.php';

class Paiement {
    private $id;
    private $reference;
    private $montant;
    private $date_paiement;
    private $mode_paiement;
    private $statut;
    private $numeroPrest;
    private $id_agent;
    private $db;

    // Constructeur
    public function __construct(
        $reference = null,
        $montant = null,
        $date_paiement = null,
        $mode_paiement = null,
        $statut = null,
        $numeroPrest = null,
        $id_agent = null,
        $id = null
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->montant = $montant;
        $this->date_paiement = $date_paiement;
        $this->mode_paiement = $mode_paiement;
        $this->statut = $statut;
        $this->numeroPrest = $numeroPrest;
        $this->id_agent = $id_agent;
        $this->db = Database::getInstance();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getMontant() {
        return $this->montant;
    }

    public function getDatePaiement() {
        return $this->date_paiement;
    }

    public function getModePaiement() {
        return $this->mode_paiement;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getNumeroPrest() {
        return $this->numeroPrest;
    }

    public function getIdAgent() {
        return $this->id_agent;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setReference($reference) {
        $this->reference = $reference;
        return $this;
    }

    public function setMontant($montant) {
        $this->montant = $montant;
        return $this;
    }

    public function setDatePaiement($date_paiement) {
        $this->date_paiement = $date_paiement;
        return $this;
    }

    public function setModePaiement($mode_paiement) {
        $this->mode_paiement = $mode_paiement;
        return $this;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }

    public function setNumeroPrest($numeroPrest) {
        $this->numeroPrest = $numeroPrest;
        return $this;
    }

    public function setIdAgent($id_agent) {
        $this->id_agent = $id_agent;
        return $this;
    }

    // Méthode pour obtenir tous les attributs sous forme de tableau
    public function toArray() {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'montant' => $this->montant,
            'date_paiement' => $this->date_paiement,
            'mode_paiement' => $this->mode_paiement,
            'statut' => $this->statut,
            'numeroPrest' => $this->numeroPrest,
            'id_agent' => $this->id_agent
        ];
    }

    // MÉTHODES DE GESTION DE BASE DE DONNÉES

    /**
     * Insère un nouveau paiement dans la base de données
     */
    public function insert() {
        $sql = "INSERT INTO Paiement (reference, montant, date_paiement, mode_paiement, statut, numeroPrest, id_agent)
                VALUES (:reference, :montant, :date_paiement, :mode_paiement, :statut, :numeroPrest, :id_agent)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':reference', $this->reference);
            $stmt->bindParam(':montant', $this->montant);
            $stmt->bindParam(':date_paiement', $this->date_paiement);
            $stmt->bindParam(':mode_paiement', $this->mode_paiement);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':numeroPrest', $this->numeroPrest);
            $stmt->bindParam(':id_agent', $this->id_agent);
            
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
     * Met à jour un paiement existant
     */
    public function update() {
        $sql = "UPDATE Paiement 
                SET reference = :reference, 
                    montant = :montant, 
                    date_paiement = :date_paiement, 
                    mode_paiement = :mode_paiement, 
                    statut = :statut, 
                    numeroPrest = :numeroPrest,
                    id_agent = :id_agent
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':reference', $this->reference);
            $stmt->bindParam(':montant', $this->montant);
            $stmt->bindParam(':date_paiement', $this->date_paiement);
            $stmt->bindParam(':mode_paiement', $this->mode_paiement);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':numeroPrest', $this->numeroPrest);
            $stmt->bindParam(':id_agent', $this->id_agent);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un paiement
     */
    public function delete() {
        $sql = "DELETE FROM Paiement WHERE id = :id";
        
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
     * Récupère un paiement par son ID
     */
    public static function getById($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Paiement WHERE id = :id";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return new self(
                    $row['reference'],
                    $row['montant'],
                    $row['date_paiement'],
                    $row['mode_paiement'],
                    $row['statut'],
                    $row['numeroPrest'],
                    isset($row['id_agent']) ? $row['id_agent'] : null,
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
     * Récupère tous les paiements
     */
    public static function getAll() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Paiement";
        $paiements = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $paiement = new self(
                    $row['reference'],
                    $row['montant'],
                    $row['date_paiement'],
                    $row['mode_paiement'],
                    $row['statut'],
                    $row['numeroPrest'],
                    isset($row['id_agent']) ? $row['id_agent'] : null,
                    $row['id']
                );
                $paiements[] = $paiement;
            }
            return $paiements;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère les paiements par prestation
     */
    public static function getByPrestation($numeroPrest) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Paiement WHERE numeroPrest = :numeroPrest";
        $paiements = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':numeroPrest', $numeroPrest);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $paiement = new self(
                    $row['reference'],
                    $row['montant'],
                    $row['date_paiement'],
                    $row['mode_paiement'],
                    $row['statut'],
                    $row['numeroPrest'],
                    isset($row['id_agent']) ? $row['id_agent'] : null,
                    $row['id']
                );
                $paiements[] = $paiement;
            }
            return $paiements;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère les paiements par statut
     */
    public static function getByStatut($statut) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Paiement WHERE statut = :statut";
        $paiements = [];
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':statut', $statut);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $paiement = new self(
                    $row['reference'],
                    $row['montant'],
                    $row['date_paiement'],
                    $row['mode_paiement'],
                    $row['statut'],
                    $row['numeroPrest'],
                    isset($row['id_agent']) ? $row['id_agent'] : null,
                    $row['id']
                );
                $paiements[] = $paiement;
            }
            return $paiements;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return [];
        }
    }
}
?>
