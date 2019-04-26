<?php
class SetChangesController {
    
    public $userId;
    
    private $projectName;
    private $changes;
    
    function __construct() {
        $this->projectName = filter_input(INPUT_GET, "project_name");
        
        // decode 
        $this->changes = json_decode(file_get_contents('php://input'));
        if ($this->changes === NULL || $this->changes == "") {
            ApiError::throwError(6);
        }
        
        if (!isset($this->projectName) || $this->projectName == "") {
            ApiError::throwError(1);
        }
        
        if (!$this->areChangesValid()) {
            ApiError::throwError(7);
        }
        
    }
    
    public function save() {
        $db = new Database();
        $conn = $db->getConnection();
        
        // проверить существует ли такой проект
        $proj = new ProjectModel($conn);
        $proj->name = $this->projectName;
        $proj->userId = $this->userId;
        $projectExists = $proj->exists();
        if (!$projectExists) {
            ApiError::throwError(5);
        }

        // check if syncing is not yet done for previous build
        if ($proj->syncState == 1 || $proj->syncState == 2) {
            ApiError::throwError(15);
        }

        // check if this project has unfinished changes
        $changes = new ChangesModel($conn);
        $changes->projectName = $this->projectName;
        $changes->userId = $this->userId;
        $unfinishedChangesExist = $changes->unfinishedChangesExist();
        if ($unfinishedChangesExist) {
            ApiError::throwError(8);
        }

        // записать и получить id
        $changes->edited = join("|", $this->changes->edited);
        $changes->deleted = join("|", $this->changes->deleted);
        $changes->created = join("|", $this->changes->created);
        $id = $changes->create();

        // set this build for sync
        $proj->setBuildToSync($id);

        // отправить id в ответе
        exit("{\"response\":\"" . $id . "\"}");
    }
    
    private function areChangesValid() {
        if (!isset($this->changes->edited) 
            && !isset($this->changes->deleted) 
            && !isset($this->changes->created)) {
            return false;
        }
        return true;
    }
}
