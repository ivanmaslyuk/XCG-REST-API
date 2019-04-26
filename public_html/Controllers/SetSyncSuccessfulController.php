<?php


class SetSyncSuccessfulController
{
    public $userId;

    private $projectName;
    private $value;

    public function __construct() {
        $this->projectName = filter_input(1, "project");
        $this->value = filter_input(1, "value");
    }

    public function checkInput() {
        if (!isset($this->projectName) || $this->projectName == "") {
            ApiError::throwError(1);
        }
        if (!isset($this->value) || !($this->value == "1" || $this->value == "0")) {
            ApiError::throwError(1);
        }
    }

    public function handle() {
        $db = new Database();
        $conn = $db->getConnection();

        // check if project exists
        $projectModel = new ProjectModel($conn);
        $projectModel->userId = $this->userId;
        $projectModel->name = $this->projectName;
        if (!$projectModel->exists()) {
            ApiError::throwError(NO_SUCH_PROJECT);
        }

        // check if sync had been requested
        if ($projectModel->syncState != 2) {
            ApiError::throwError(16);
        }

        // set value
        $projectModel->setSyncState(((int)$this->value) + 3);

        // respond
        $response = new ApiResponse(1);
        $response->send();
    }


}