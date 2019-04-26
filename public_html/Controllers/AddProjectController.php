<?php

class AddProjectController {
    
    public $userId;
    
    private $projectName;
    
    public function __construct() {
        $this->projectName = filter_input(INPUT_GET, "name");
    }
    
    public function checkInput() {
        if ($this->projectName == null || $this->projectName == "") {
            ApiError::throwError(1);
        }
    }
    
    public function saveProject() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if such project already exists
        $proj = new ProjectModel($conn);
        $proj->name = $this->projectName;
        $proj->userId = $this->userId;
        $alreadyExists = $proj->exists();
        if ($alreadyExists) {
            ApiError::throwError(4);
        }
        
        // create project
        $proj->create();
        
        exit(json_encode(array("response" => 1)));
    }
    
}