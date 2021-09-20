<?php

class PatentMeta {
    private $conn;
    private $table_name = "patent_meta";

    public $patent_id;
    public $created;
    public $updated;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create_patent_meta($meta_key, $meta_value) {
        $query = "INSERT INTO " .$this->table_name. "
                    SET 
                    patent_id = :patent_id,
                    meta_key = :meta_key,
                    meta_value = :meta_value,
                    created = :created,
                    updated = :updated";

        $stmt = $this->conn->prepare($query);
        
        $this->patent_id=htmlspecialchars(strip_tags($this->patent_id));
        $meta_key=htmlspecialchars(strip_tags($meta_key));
        $meta_value=htmlspecialchars(strip_tags($meta_value));
        $this->created=htmlspecialchars(strip_tags($this->created));
        $this->updated=htmlspecialchars(strip_tags($this->updated));


        $stmt->bindParam(':patent_id', $this->patent_id);
        $stmt->bindParam(':meta_key', $meta_key);
        $stmt->bindParam(':meta_value', $meta_value);
        $stmt->bindParam(':created', $this->created);
        $stmt->bindParam(':updated', $this->updated);

        // $this->LOG->debug("INSERT INTO " .$this->table_name. " SET patent_id = ".$this->patent_id.", meta_key = ".$meta_key.",
        //                     meta_value = ".$meta_value.", created = ".$this->created.", updated = ".$this->updated."");

        if($stmt->execute()) {
            // $this->LOG->info("Patent meta created for user id ".$this->patent_id.".");
            return true;
        }
        // $this->LOG->error("Failed to create default meta for user id ".$this->user_id.".");
        return false;
    }

    public function get_patent_meta_all() {
        $query = "SELECT * FROM ".$this->table_name."";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        }
        return null;
    }

    public function update_patent_metas($meta_key, $meta_value) {
        $query = "UPDATE ". $this->table_name ." SET 
                    meta_value = :meta_value, 
                    updated = :updated 
                    WHERE patent_id = :patent_id 
                    AND meta_key= :meta_key";

        $stmt = $this->conn->prepare($query);

        $this->patent_id=htmlspecialchars(strip_tags($this->patent_id));
        $meta_key=htmlspecialchars(strip_tags($meta_key));
        $meta_value=htmlspecialchars(strip_tags($meta_value));
        $this->updated=htmlspecialchars(strip_tags($this->updated));

        $stmt->bindParam(':patent_id', $this->patent_id);
        $stmt->bindParam(':meta_key', $meta_key);
        $stmt->bindParam(':meta_value', $meta_value);
        $stmt->bindParam(':updated', $this->updated);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function get_meta_value($meta_key) {
        $query = "SELECT meta_value FROM ".$this->table_name." WHERE patent_id = :patent_id AND meta_key = :meta_key";
        $stmt = $this->conn->prepare($query);

        $this->patent_id = htmlspecialchars(strip_tags($this->patent_id));
        $meta_key=htmlspecialchars(strip_tags($meta_key));

        $stmt->bindParam(':patent_id', $this->patent_id);
        $stmt->bindParam(':meta_key', $meta_key);

        $stmt->execute();

        $num = $stmt->rowCount();

        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        
        return null;
    }

    public function delete_pro_hist($meta_key) {
        $query = "DELETE FROM ".$this->table_name." 
                    WHERE patent_id = :patent_id
                    AND meta_key = :meta_key";

        $stmt = $this->conn->prepare($query);

        $this->patent_id = htmlspecialchars(strip_tags($this->patent_id));
        $meta_key = htmlspecialchars(strip_tags($meta_key));

        $stmt->bindParam(':patent_id', $this->patent_id);
        $stmt->bindParam(':meta_key', $meta_key);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}