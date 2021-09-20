<?php

class PatentCategory {
    private $conn;
    private $table_name = "patent_category";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function get_patent_categories() {
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