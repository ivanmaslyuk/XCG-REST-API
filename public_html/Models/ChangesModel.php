<?php
// 'user' object
class ChangesModel {
 
    // database connection and table name
    private $conn;
    private $tableName = "changes";
 
    // object properties
    public $buildId;
    public $projectName;
    public $userId;
    public $readyForDownload;
    public $edited;
    public $deleted;
    public $created;
    public $downloaded;
    public $cancelled;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    public function create() {
     
        // insert query
        $query = "INSERT INTO " . $this->tableName . "
                SET
                    project_name = :projectName,
                    user_id = :userId,
                    edited = :edited,
                    deleted = :deleted,
                    created = :created";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->edited = htmlspecialchars(strip_tags($this->edited));
        $this->deleted = htmlspecialchars(strip_tags($this->deleted));
        $this->created = htmlspecialchars(strip_tags($this->created));
     
        // bind the values
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':edited', $this->edited);
        $stmt->bindParam(':deleted', $this->deleted);
        $stmt->bindParam(':created', $this->created);
     
        // hash the password before saving to database
        //$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        //$stmt->bindParam(':password', $password_hash);
     
        // execute the query, also check if query was successful
        /*if($stmt->execute()){
            return true;
        }
     
        return false;*/
        if ($stmt->execute()) {
            return $this->conn->lastInsertId("build_id");
        }
        return -1;
    }
    
    public function exists() {
        $query = "SELECT cancelled, ready_for_download, edited, deleted, created 
                FROM " . $this->tableName . " 
                WHERE user_id = :userId AND build_id = :buildId 
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);

        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));

        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':buildId', $this->buildId);
        
        $stmt->execute();
        $count = $stmt->rowCount();
        
        $found = $count > 0;
        if ($found) {
            $row = $stmt->fetch();
            $this->edited = explode("|", $row["edited"]);
            $this->created = explode("|", $row["created"]);
            $this->deleted = explode("|", $row["deleted"]);
            $this->readyForDownload = $row["ready_for_download"];
            $this->cancelled = $row["cancelled"];
        }

        // check validity of values
        if (sizeof($this->created) == 1 && $this->created[0] == "") {
            $this->created = null;
        }
        if (sizeof($this->edited) == 1 && $this->edited[0] == "") {
            $this->edited = null;
        }
        if (sizeof($this->deleted) == 1 && $this->deleted[0] == "") {
            $this->deleted = null;
        }

        return $found;
    }

    public function setCancelled() {
        $query = "UPDATE " . $this->tableName . " 
                SET cancelled = true 
                WHERE user_id = :userId AND project_name = :projectName 
                AND build_id = :buildId";
        
        $stmt = $this->conn->prepare($query);
        
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':buildId', $this->buildId);
        
        $stmt->execute();
    }
    
    public function unfinishedChangesExist() {
        $query = "SELECT build_id FROM " . $this->tableName . "
                    WHERE user_id = :userId AND project_name = :projectName 
                    AND cancelled = false AND ready_for_download = false LIMIT 0,1";
                    
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /*public function save() {
        public $buildId;
        public $projectName;
        public $userId;
        public $readyForDownload;
        public $edited;
        public $deleted;
        public $created;
        public $downloaded;
        public $cancelled;
        $query = "UPDATE " . $this->tableName . " 
                    SET ready_for_download = :ready 
                    edited"
    }*/
    
    public function setReadyForDownload() {
        $query = "UPDATE " . $this->tableName . " 
                SET ready_for_download = true 
                WHERE user_id = :userId AND project_name = :projectName 
                AND build_id = :buildId";
        
        $stmt = $this->conn->prepare($query);
        
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':buildId', $this->buildId);
        
        $stmt->execute();
    }
    
    
    public function setDownloaded() {
        $query = "UPDATE " . $this->tableName . " 
                SET downloaded = true 
                WHERE user_id = :userId AND project_name = :projectName";
        
        $stmt = $this->conn->prepare($query);
        
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':buildId', $this->buildId);
        
        $stmt->execute();
    }
    
    public function removeByProject() {
        $query = "DELETE FROM " . $this->tableName . " 
                WHERE user_id = :userId AND project_name = :projectName";
        
        $stmt = $this->conn->prepare($query);
        
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        
        $stmt->bindParam(':projectName', $this->projectName);
        $stmt->bindParam(':userId', $this->userId);
        
        $stmt->execute();
    }

    /*public function getAllPendingBuilds() {
        $query = "SELECT FROM " . $this->tableName . " 
                    WHERE user_id = :userId AND project_name = :projectName 
                    AND ready_for_download = true AND downloaded = false 
                    AND cancelled"
    }*/
    
}