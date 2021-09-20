<?php 

class Patents {
    private $conn;
    private $table_name = "patent";

    public $created;
    public $updated;
    public $id;
    public $user_id;
    public $patent_name;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    public function create_patent() {
        $query = "INSERT INTO ".$this->table_name."
                   SET
                   user_id = :user_id,
                   patent_name = :patent_name,
                   views = 0,
                   created = :created,
                   updated = :updated";

        $stmt = $this->conn->prepare($query);
       
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $this->patent_name=htmlspecialchars(strip_tags($this->patent_name));
        $this->created=htmlspecialchars(strip_tags($this->created));
        $this->updated=htmlspecialchars(strip_tags($this->updated));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':patent_name', $this->patent_name);
        $stmt->bindParam(':created', $this->created);
        $stmt->bindParam(':updated', $this->updated);

        // $this->LOG->debug("INSERT INTO " .$this->table_name. " SET user_id = ".$this->user_id.", 
        //                     created = ".$this->created.", updated = ".$this->updated."");

        if($stmt->execute()) {
            // $this->LOG->info("Patent created for user id ".$this->user_id.".");
            $last_id = $this->conn->lastInsertId();
            return $last_id;
        }
        // $this->LOG->error("Failed to create default meta for user id ".$this->user_id.".");
        return false;

    }

    public function get_patents_all() {
        $query = "SELECT * FROM ".$this->table_name."";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // execute the query
        $stmt->execute();

        // get number of rows
        $num = $stmt->rowCount();

        if($num>0){
            // get record details / values
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        }
        return null;
    }

    public function update_patent() {
        $query = "UPDATE ". $this->table_name ."
                SET 
                patent_name = :patent_name,
                updated = :updated
                WHERE id = :id";

         // prepare the query
         $stmt = $this->conn->prepare( $query );    
        
         // sanitize
         $this->id=htmlspecialchars(strip_tags($this->id));
 
         $stmt->bindParam(':id', $this->id);
         $stmt->bindParam(':patent_name', $this->patent_name);
         $stmt->bindParam(':updated', $this->updated);

         // execute the query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function get_patent_by_id() {
        
        $query = "SELECT * FROM ".$this->table_name." WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return null;
    }

    public function get_patent_name() {
        $query = "SELECT patent_name FROM ".$this->table_name." WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['patent_name'];
        }
        return null;
    }

    public function delete_patent() {
        $query = "DELETE FROM ".$this->table_name." WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':id', $this->id);

        

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}