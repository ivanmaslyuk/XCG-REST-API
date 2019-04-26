<?php

class CancelChangesController {
    
    public $userId;
    
    private $projectName;
    private $buildId;
    
    public function __construct() {
        $this->projectName = filter_input(INPUT_GET, "project_name");
        $this->buildId = filter_input(INPUT_GET, "build_id");
        
        if (!isset($this->projectName) || trim($this->projectName) == "") {
            ApiError::throwError(1);
        }
        if (!isset($this->buildId) || trim($this->buildId) == "") {
            ApiError::throwError(1);
        }
        
    }
    
    public function cancel() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if such project exists for this user
        $proj = new ProjectModel($conn);
        $proj->userId = $this->userId;
        $proj->name = $this->projectName;
        if (!$proj->exists()) {
            ApiError::throwError(5);
        }
        
        // mark changes as cancelled
        $changes = new ChangesModel($conn);
        $changes->projectName = $this->projectName;
        $changes->userId = $this->userId;
        $changes->buildId = $this->buildId;
        $changes->setCancelled();
        
        // delete files from disk if any were uploaded
        $file = new FileModel($conn);
        $file->buildId = $this->buildId;
        $file->removeBuild();
        
        exit(json_encode(array("response" => 1)));
    }
    
}