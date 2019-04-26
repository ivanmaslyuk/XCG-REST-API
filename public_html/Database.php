<?php
// used to get mysql database connection
class Database{
 
    private $host = "localhost";
    private $db_name = "ivantnk_xcg_api";
    private $username = "ivantnk_xcg_api";
    private $password = "777ivan888";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>