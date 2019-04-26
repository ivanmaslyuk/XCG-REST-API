<?php
class RemoveProjectController {
    
    public $userId;
    
    private $projectName;
    
    public function __construct() {
        $this->projectName = filter_input(INPUT_GET, "name");
    }
    
    public function checkInput() {
        if ($this->projectName === null || $this->projectName == "") {
            ApiError::throwError(1);
        }
    }
    
    public function removeProject() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if project exists
        $proj = new ProjectModel($conn);
        $proj->name = $this->projectName;
        $proj->userId = $this->userId;
        if (!$proj->exists()) {
            ApiError::throwError(5);
        }

        // check if sync is requested or in progress
        if ($proj->syncState == 1 || $proj->syncState == 2) {
            ApiError::throwError(18);
        }

        // check if build is in progress or requested
        if ($proj->buildState == 1 || $proj->buildState == 2) {
            ApiError::throwError(18);
        }
        
        // remove from projects table
        $proj->remove();
        
        // remove from files table
        $file = new FileModel($conn);
        $file->userId = $this->userId;
        $file->projectName = $this->projectName;
        $file->removeByProject();
        
        // remove from changes table
        $changes = new ChangesModel($conn);
        $changes->userId = $this->userId;
        $changes->projectName = $this->projectName;
        $changes->removeByProject();
        
        exit(json_encode(array("response" => 1)));
    }
    
}