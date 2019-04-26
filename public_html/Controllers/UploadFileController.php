<?php

class UploadFileController {
    
    public $userId;
    
    private $pathInProject;
    private $projectName;
    private $buildId;
    private $content;
    
    public function __construct() {
        $this->pathInProject = filter_input(INPUT_GET, "path");
        $this->projectName = filter_input(INPUT_GET, "project");
        $this->buildId = filter_input(INPUT_GET, "build_id");
        $this->content = file_get_contents('php://input');
    }
    
    public function checkInput() {
        if ($this->isempty($this->pathInProject) ||
        $this->isempty($this->projectName) || $this->isempty($this->buildId)) {
            ApiError::throwError(1);
        }
        if ($this->content === NULL) {
            $this->content = "";
        }
    }
    
    private function isempty($val) {
        return !isset($val) || $val == "";
    }
    
    public function save() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if project exists
        $project = new ProjectModel($conn);
        $project->name = $this->projectName;
        $project->userId = $this->userId;
        if (!$project->exists()) {
            ApiError::throwError(5);
        }
        
        // check if there is an unfinished project
        $changes = new ChangesModel($conn);
        $changes->projectName = $this->projectName;
        $changes->userId = $this->userId;
        $changes->buildId = $this->buildId;
        if (!$changes->exists()) {
            ApiError::throwError(11);
        }
        if ($changes->cancelled) {
            ApiError::throwError(13);
        }
        
        // check if it has a file with this name
        if (in_array($this->pathInProject, $changes->edited)
        && in_array($this->pathInProject, $changes->created)) {
            ApiError::throwError(3);
        }
        
        // check if file was already uploaded
        $file = new FileModel($conn);
        $file->buildId = $this->buildId;
        $file->path = $this->pathInProject;
        $file->userId = $this->userId;
        $file->projectName = $this->projectName;
        if ($file->exists()) {
            ApiError::throwError(12);
        }
        
        // write file
        $file->content = $this->content;
        $file->create();
        
        exit(json_encode(array("response" => 1)));
    }
    
    
    
}