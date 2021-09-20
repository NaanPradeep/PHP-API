
<?php

// 'user' object
class User{
    // database connection and table name
    private $conn;
    private $table_name = "users";
    private $LOG;
 
    // object properties
    public $id;
    public $full_name;
    public $username;
    public $email;
    public $password;
    public $status;
    public $token;
    public $timestamp;
 
    // constructor
    public function __construct($db, Logger $LOG){
        $this->conn = $db;
        $this->LOG = $LOG;
    }
 
// create new user record
    public function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                    SET
                    full_name = :full_name,
                    username = :username,
                    email = :email,
                    status = '0', 
                    token = :token,
                    token_issued_time = :timestamp,
                    password = :password,
                    created = :timestamp,
                    updated = :timestamp";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->full_name=htmlspecialchars(strip_tags($this->full_name));
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
    
        // bind the values
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':token', $this->token);
        $stmt->bindParam(':timestamp', $this->timestamp);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        $this->LOG->debug("INSERT INTO " . $this->table_name . "SET full_name = ".$this->full_name.", username = ".$this->username.", email = ".$this->email.", 
                status = '0', token = ".$this->token.", token_issued_time = ".$this->timestamp.", created = ".$this->timestamp.", updated = ".$this->timestamp."");

        // execute the query, also check if query was successful
        if($stmt->execute()){
            $this->LOG->info("Data added to the table for ".$this->email.".");
            return true;
        }
        
        $this->LOG->error("Failed to add data ".$this->email.".");
        return false;
    }

    public function verify_tokn() {
        $query = "SELECT email, token_issued_time 
                FROM " . $this->table_name ."
                WHERE token = :token
                LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->token));
    
        // bind given email and token value
        $stmt->bindParam(':token', $this->token);
        
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        $this->LOG->debug("SELECT email, token_issued_time FROM ". $this->table_name." WHERE token = ".$this->token." LIMIT 0,1");
        // if query exists, return true
        if($num>0){
            
            $this->LOG->info("Record exists for token ".$this->token.".");
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->email = $row["email"];
            $this->timestamp = $row["token_issued_time"];
            // return true because email exists in the database
            return true;
        }
        $this->LOG->error("Record does not exist for token ".$this->token.".");
        return false;
    }

    public function update_token() {
        $query = "UPDATE ". $this->table_name ."
                SET 
                token = :token,
                token_issued_time = :timestamp,
                updated = :timestamp
                WHERE email = :email";
        
        // prepare the query
        $stmt = $this->conn->prepare( $query );    
        
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':token', $this->token);
        $stmt->bindParam(':timestamp', $this->timestamp);

        $this->LOG->debug("UPDATE ". $this->table_name ." SET token = ".$this->token.", token_issued_time = ".$this->timestamp.", updated = ".$this->timestamp." WHERE email = ".$this->email."");

        // execute the query
        if($stmt->execute()) {
            $this->LOG->info("Token updated ".$this->email.".");
            return true;
        }

        $this->LOG->error("Failed to update the token for ".$this->email.".");
        return false;
    }

    public function update_status() {
        $query = "UPDATE ". $this->table_name ."
                SET 
                status = 1,
                updated = :timestamp
                WHERE email = :email";

        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->timestamp=htmlspecialchars(strip_tags($this->timestamp));
    
        // bind given email value
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':timestamp', $this->timestamp);
    
        $this->LOG->debug("UPDATE ".$this->table_name." SET status = '1', updated = ".$this->timestamp." WHERE email = ".$this->email."");

        // execute the query
        if($stmt->execute()) {
            $this->LOG->info("Status updated for ".$this->email.".");
            return true;
        }

        $this->LOG->error("Status update failed for ".$this->email.".");
        return false;
    }
 
    // check if given email exist in the database
    public function emailExists(){
    
        // query to check if email exists
        $query = "SELECT id, full_name, username, password, status
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        $this->LOG->debug("SELECT id, full_name, username, password, status FROM " . $this->table_name . " WHERE email = ".$this->email." LIMIT 0,1");
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
            
            $this->LOG->info("Record exists for ".$this->email.".");
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // assign values to object properties
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->status = $row['status'];
    
            // return true because email exists in the database
            return true;
        }
        
        $this->LOG->error("Record does not exist for ".$this->email.".");
        // return false if email does not exist in the database
        return false;
    }

    public function is_verified_user() {
        if($this->emailExists()) {
            $query = "SELECT `status`
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
            
            // prepare the query
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->email);

            // execute the query
            $stmt->execute();
        
            // get number of rows
            $num = $stmt->rowCount();

            $this->LOG->debug("SELECT `status` FROM " . $this->table_name . " WHERE email = ".$this->email." LIMIT 0,1");
            // if record exists
            if($num>0){
                
                $this->LOG->info("Record exists for ".$this->email.".");
                // get record details / values
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row["status"];
            }
        }
    }

    public function update_password() {

        $query = "UPDATE " . $this->table_name . "
                    SET
                    password = :password,
                    updated = :timestamp
                    WHERE email = :email";

        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->timestamp=htmlspecialchars(strip_tags($this->timestamp));
    
        // bind the values from the form
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':timestamp', $this->timestamp);
    
        // hash the password before saving to database
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        $this->LOG->debug("UPDATE " . $this->table_name . " SET password = :password, updated = ".$this->timestamp." WHERE email = ".$this->email."");
        // execute the query
        if($stmt->execute()){
            $this->LOG->info("Password updated for ".$this->email.".");
            return true;
        }

        $this->LOG->error("Failed to update the password ".$this->email."");
        return false;
    }

    // update a user record
    public function update_profile(){
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                    SET
                    full_name = :full_name,
                    username = :username,
                    email = :email,
                    updated = :timestamp
                    WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->full_name=htmlspecialchars(strip_tags($this->full_name));
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->timestamp=htmlspecialchars(strip_tags($this->timestamp));
    
        // bind the values from the form
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':timestamp', $this->timestamp);
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        $this->LOG->debug("UPDATE " . $this->table_name . " SET full_name = ".$this->full_name.", username = ".$this->username.", 
                            email = ".$this->email.", updated = ".$this->timestamp." WHERE id = ".$this->id."");
        // execute the query
        if($stmt->execute()){
            $this->LOG->info("Profile updated for ".$this->email.".");
            return true;
        }
        
        $this->LOG->error("Profile update failed for ".$this->email.".");
        return false;
    }
}