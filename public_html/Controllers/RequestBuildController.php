<?php


class RequestBuildController
{
    public $userId;

    private $projectName;

    public function __construct() {
        $this->projectName = filter_input(1, "project");
    }

    public function checkInput() {
        if (!isset($this->projectName) || $this->projectName == "") {
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

        // check is sync is in progress or requested
        if ($projectModel->syncState == 1 || $projectModel->syncState == 2) {
            ApiError::throwError(19);
        }

        // set build_state as requested
        $projectModel->setBuildState(1);

        // respond
        $response = new ApiResponse(1);
        $response->send();
    }
}