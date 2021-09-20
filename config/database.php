<?php

require 'config.php';

// used to get mysql database connection
class Database{
 
    // specify your own database credentials
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASSWORD;
    private $LOG;
    public $conn;
 
    // constructor
    public function __construct(Logger $LOG){
        $this->LOG = $LOG;
    }

    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->LOG->info("Database connection established");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
            $this->LOG->error("Database connection failed");
        }
 
        return $this->conn;
    }
}
?> 