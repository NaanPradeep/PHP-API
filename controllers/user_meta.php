<?php 

class UserMeta {
    // database connection and table name
    private $conn;
    private $table_name = "user_meta";
    private $LOG;
 
    // object properties
    public $id;
    public $user_id;
    // public $meta_key;
    // public $meta_value;
    public $created;
    public $updated;
 
    // constructor
    public function __construct($db, Logger $LOG){
        $this->conn = $db;
        $this->LOG = $LOG;
    }

    public function update_meta($meta_key, $meta_value) {
        $query = "UPDATE ". $this->table_name ."
                    SET 
                    meta_value = :meta_value,
                    updated = :updated
                    WHERE user_id = :user_id
                    AND meta_key= :meta_key";
        
        $stmt = $this->conn->prepare($query);

        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $meta_key=htmlspecialchars(strip_tags($meta_key));
        $meta_value=htmlspecialchars(strip_tags($meta_value));
        $this->updated=htmlspecialchars(strip_tags($this->updated));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':meta_key', $meta_key);
        $stmt->bindParam(':meta_value', $meta_value);
        $stmt->bindParam(':updated', $this->updated);

        $this->LOG->debug("UPDATE ". $this->table_name ." SET meta_value = ".$meta_value.", updated = ".$this->updated." 
                            WHERE user_id = ".$this->user_id." AND meta_key= ".$meta_key."");
        
        if($stmt->execute()) {
            $this->LOG->info("User meta updated for user id ".$this->user_id.".");
            return true;
        }
        $this->LOG->error("Failed to update user meta for user id ".$this->user_id.".");
        return false;
    }

    public function create_default_meta($meta_key, $meta_value) {
        $query = "INSERT INTO " .$this->table_name. "
                    SET 
                    user_id = :user_id,
                    meta_key = :meta_key,
                    meta_value = :meta_value,
                    created = :created,
                    updated = :updated";

        $stmt = $this->conn->prepare($query);
        
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $meta_key=htmlspecialchars(strip_tags($meta_key));
        $meta_value=htmlspecialchars(strip_tags($meta_value));
        $this->created=htmlspecialchars(strip_tags($this->created));
        $this->updated=htmlspecialchars(strip_tags($this->updated));


        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':meta_key', $meta_key);
        $stmt->bindParam(':meta_value', $meta_value);
        $stmt->bindParam(':created', $this->created);
        $stmt->bindParam(':updated', $this->updated);

        $this->LOG->debug("INSERT INTO " .$this->table_name. " SET user_id = ".$this->user_id.", meta_key = ".$meta_key.",
                            meta_value = ".$meta_value.", created = ".$this->created.", updated = ".$this->updated."");

        if($stmt->execute()) {
            $this->LOG->info("Default meta created for user id ".$this->user_id.".");
            return true;
        }
        $this->LOG->error("Failed to create default meta for user id ".$this->user_id.".");
        return false;
    }

    public function get_account_fullname(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'full_name'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'full_name' LIMIT 0,1");

        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_username(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'username'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'username' LIMIT 0,1");
        
        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_userimage(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'userimage'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'userimage' LIMIT 0,1");
        
        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_notification_settings(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'Notification_settings'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'Notification_settings' LIMIT 0,1");
        
        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_min_bid_threshold(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'minimum_bid_threshold'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'minimum_bid_threshold' LIMIT 0,1");
        
        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_email(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'email'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
        
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'email' LIMIT 0,1");
        
        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_language(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'language'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
    
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'language' LIMIT 0,1");

        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_time_zone(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'time_zone'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
    
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'time_zone' LIMIT 0,1");

        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }

    public function get_account_currency(){
        $query = "SELECT meta_value
                FROM " . $this->table_name . "
                WHERE user_id = :user_id
                AND meta_key = 'currency'
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        $num = $stmt->rowCount();
    
        $this->LOG->debug("SELECT meta_value FROM " . $this->table_name . " WHERE user_id = ".$this->user_id." AND meta_key = 'currency' LIMIT 0,1");

        if($num>0){
            $this->LOG->info("Record exists for user id ".$this->user_id.".");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row["meta_value"];
        }
        $this->LOG->error("Record does not exist for user id ".$this->user_id.".");
        return null;
    }
}