<?php


class SetBuildSuccessfulController
{
    public $userId;

    private $projectName;
    private $value;
    private $warnings;

    public function __construct() {
        $this->projectName = filter_input(1, "project");
        $this->value = filter_input(1, "value");
        $this->warnings = file_get_contents('php://input');
    }

    public function checkInput() {
        if (!isset($this->projectName) || $this->projectName == "") {
            ApiError::throwError(1);
        }
        if (!isset($this->value) || !($this->value == 0 || $this->value == 1)) {
            ApiError::throwError(1);
        }
        if (!json_decode($this->warnings)) {
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

        // check if build is requested
        if ($projectModel->buildState != 2) {
            ApiError::throwError(17);
        }

        // set warnings
        $projectModel->setBuildWarnings($this->warnings);

        // set value
        $projectModel->setBuildState(((int)$this->value) + 3);

        // respond
        $response = new ApiResponse(1);
        $response->send();
    }
}