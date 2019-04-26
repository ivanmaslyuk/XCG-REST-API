<?php

class DownloadFileController {
    
    public $userId;
    
    private $projectName;
    private $buildId;
    private $path;
    
    public function __construct() {
        $this->projectName = filter_input(INPUT_GET, "project");
        $this->buildId = filter_input(INPUT_GET, "build_id");
        $this->path = filter_input(INPUT_GET, "path");
    }
    
    public function checkInput() {
        if ($this->projectName == null || $this->projectName == "") {
            ApiError::throwError(1);
        }
        if ($this->buildId == null || $this->buildId == "") {
            ApiError::throwError(1);
        }
        if ($this->path == null || $this->path == "") {
            ApiError::throwError(1);
        }
    }
    
    public function send() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // check if such build exists
        $changes = new ChangesModel($conn);
        $changes->projectName = $this->projectName;
        $changes->buildId = $this->buildId;
        $changes->userId = $this->userId;
        if (!$changes->exists()) {
            ApiError::throwError(11);
        }
        
        // check if buildis ready for download
        if (!$changes->readyForDownload) {
            ApiError::throwError(14);
        }
        
        // check if changes are cancelled
        if ($changes->cancelled) {
            ApiError::throwError(9);
        }
        
        // check if build has such file
        $hasFile = in_array($this->path, $changes->edited) 
                    || in_array($this->path, $changes->created);
        if (!$hasFile) {
            ApiError::throwError(3);
        }
        
        // send file
        $file = new FileModel($conn);
        $file->path = $this->path;
        $file->buildId = $this->buildId;
        if (!$file->exists()) {
            ApiError::throwError(3);
        }
        
        exit(json_encode(array("response" => $file->content)));
    }
}