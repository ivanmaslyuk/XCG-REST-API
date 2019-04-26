<?php

class ProjectModel {
    
    private $conn;
    private $tableName = "projects";
    
    public $id;
    public $userId; // owner
    public $name;
    public $syncState;
    public $buildToSync;
    public $buildState;
    public $buildWarnings;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        
         // insert query
        $query = "INSERT INTO " . $this->tableName . "
                SET
                    name = :name,
                    user_id = :userId";
                    
        // prepare the query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        
        // bind the values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        
        $stmt->execute();
    }
    
    public function exists() {
        $query = "SELECT name, sync_state, build_state, build_warnings, build_to_sync 
                FROM " . $this->tableName ."
                WHERE user_id = :userId AND name = :name 
                LIMIT 0,1";
                
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        
        $stmt->execute();
        
        $exists = $stmt->rowCount();
        if ($exists) {
            $row = $stmt->fetch();
            $this->syncState = $row["sync_state"];
            $this->buildState = $row["build_state"];
            $this->buildWarnings = $row["build_warnings"];
            $this->buildToSync = $row["build_to_sync"];
        }
        
        return $exists;
    }
    
    public function remove() {
        $query = "DELETE FROM " . $this->tableName ."
                WHERE user_id = :userId AND name = :name";
                
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        
        $stmt->execute();
    }
    
    public function getAllNames() {
        $query = "SELECT name FROM " . $this->tableName . " WHERE user_id = :userId";
        $stmt = $this->conn->prepare($query);
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $stmt->bindParam(":userId", $this->userId);
        $stmt->execute();

        // if there are no projects
        if ($stmt->rowCount() == 0) return array();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        foreach ($rows as $row) {
            array_push($result, $row["name"]);
        }
        return $result;
    }

    public function getAll() {
        $query = "SELECT id, name, sync_state, build_state, build_warnings, build_to_sync FROM " . $this->tableName . " WHERE user_id = :userId";
        $stmt = $this->conn->prepare($query);
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $stmt->bindParam(":userId", $this->userId);
        $stmt->execute();

        // if there are no projects
        if ($stmt->rowCount() == 0) return array();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = array();
        foreach ($rows as $row) {
            $proj = new ProjectModel($this->conn);
            $proj->id = $row["id"];
            $proj->name = $row["name"];
            $proj->syncState = $row["sync_state"];
            $proj->buildState = $row["build_state"];
            $proj->buildWarnings = $row["build_warnings"];
            $proj->buildToSync = $row["build_to_sync"];
            $proj->userId = $this->userId;
            array_push($result, $proj);
        }
        return $result;
    }

    public function setBuildState($newState) {
        $query = "UPDATE " . $this->tableName . " 
                SET build_state = :newState 
                WHERE user_id = :userId AND name = :name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));

        $this->buildState = $newState;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':newState', $newState);

        $stmt->execute();
    }

    public function setSyncState($newState) {
        $query = "UPDATE " . $this->tableName . " 
                SET sync_state = :newState 
                WHERE user_id = :userId AND name = :name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));

        $this->syncState = $newState;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':newState', $newState);

        $stmt->execute();
    }

    public function setBuildWarnings($newValue) {
        $query = "UPDATE " . $this->tableName . " 
                SET build_warnings = :newValue 
                WHERE user_id = :userId AND name = :name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));

        $this->buildWarnings = $newValue;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':newValue', $newValue);

        $stmt->execute();
    }

    public function setBuildToSync($newValue) {
        $query = "UPDATE " . $this->tableName . " 
                SET build_to_sync = :newValue 
                WHERE user_id = :userId AND name = :name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->userId = htmlspecialchars(strip_tags($this->userId));

        $this->buildToSync = $newValue;

        $stmt->bindParam(':projectName', $this->name);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':newValue', $newValue);

        $stmt->execute();
    }

}