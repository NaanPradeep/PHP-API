<?php

class PatentEvents {
    private $conn;
    private $table_name = "events";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function get_patent_events() {
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
}