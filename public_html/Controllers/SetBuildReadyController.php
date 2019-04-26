<?php

class SetBuildReadyController {
    
    public $userId;
    
    private $projectName;
    private $buildId;
    
    public function __construct() {
        $this->projectName = filter_input(INPUT_GET, "project");
        $this->buildId = filter_input(INPUT_GET, "build_id");
    }
    
    public function checkInput() {
        if (!isset($this->projectName) || $this->projectName == "") {
            ApiError::throwError(1);
        }
        if (!isset($this->buildId) || $this->buildId == "") {
            ApiError::throwError(1);
        }
    }
    
    public function setReady() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if such project exists
        $proj = new ProjectModel($conn);
        $proj->name = $this->projectName;
        $proj->userId = $this->userId;
        if (!$proj->exists()) {
            ApiError::throwError(5);
        }
        
        // check if such build exists
        $changes = new ChangesModel($conn);
        $changes->projectName = $this->projectName;
        $changes->userId = $this->userId;
        $changes->buildId = $this->buildId;
        if (!$changes->exists()) {
            ApiError::throwError(11);
        }
        
        // check if all files have been uploaded
        foreach ($changes->edited as $path) {
            $file = new FileModel($conn);
            $file->path = $path;
            $file->buildId = $this->buildId;
            if (!$file->exists()) {
                ApiError::throwError(14);
            }
        }
        foreach ($changes->created as $path) {
            $file = new FileModel($conn);
            $file->path = $path;
            $file->buildId = $this->buildId;
            if (!$file->exists()) {
                ApiError::throwError(14);
            }
        }

        // set as ready for download
        $changes->setReadyForDownload();

        // request sync
        $proj->setSyncState(1);

        exit(json_encode(array("response" => 1)));
    }
    
}