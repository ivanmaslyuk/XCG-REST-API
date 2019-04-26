<?php

class FileModel {
    
    private $conn;
    private $tableName = "files";
    
    public $buildId;
    public $path;
    public $content;
    public $userId;
    public $projectName;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->tableName . "
                SET
                    build_id = :buildId,
                    path = :path,
                    content = :content,
                    user_id = :userId,
                    project_name = :projectName";
                    
        $stmt = $this->conn->prepare($query);
        
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        $this->path = htmlspecialchars(strip_tags($this->path));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        
        $stmt->bindParam(":buildId", $this->buildId);
        $stmt->bindParam(":path", $this->path);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":projectName", $this->projectName);
        
        $stmt->execute();
    }
    
    public function get() {
        
    }
    
    public function exists() {
        $query = "SELECT content FROM " . $this->tableName . "
                    WHERE build_id = :buildId AND path = :path 
                    LIMIT 0,1";
                    
        $stmt = $this->conn->prepare($query);
        
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        $this->path = htmlspecialchars(strip_tags($this->path));
        
        $stmt->bindParam(":buildId", $this->buildId);
        $stmt->bindParam(":path", $this->path);
        
        $stmt->execute();
        
        $found = $stmt->rowCount() > 0;
        
        if ($found) {
            $row = $stmt->fetch();
            $this->content = $row["content"];
        }
        
        return $found;
    }
    
    public function removeBuild() {
        $query = "DELETE FROM " . $this->tableName . " WHERE build_id = :buildId";
        $stmt = $this->conn->prepare($query);
        $this->buildId = htmlspecialchars(strip_tags($this->buildId));
        $stmt->bindParam(":buildId", $this->buildId);
        $stmt->execute();
    }
    
    public function removeByProject() {
        $query = "DELETE FROM " . $this->tableName . " WHERE project_name = :projectName AND user_id = :userId";
        $stmt = $this->conn->prepare($query);
        
        $this->userId = htmlspecialchars(strip_tags($this->userId));
        $this->projectName = htmlspecialchars(strip_tags($this->projectName));
        
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":projectName", $this->projectName);
        
        $stmt->execute();
    }
    
}