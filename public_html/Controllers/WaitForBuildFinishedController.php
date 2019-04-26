<?php


class WaitForBuildFinishedController
{
    public $userId;

    private $projectName;
    private $ignoreStart;

    public function __construct() {
        $this->projectName = filter_input(1, "project");
        $this->ignoreStart = filter_input(1, "ignore_start");
    }

    public function checkInput() {
        if (!isset($this->projectName) || $this->projectName == "") {
            ApiError::throwError(1);
        }
        if (isset($this->ignoreStart) && !($this->ignoreStart == 0 || $this->ignoreStart == 1)) {
            ApiError::throwError(1);
        }
        if (!isset($this->ignoreStart)) {
            $this->ignoreStart = 0;
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

        // check if build had been requested
        if ($projectModel->buildState == 0) {
            ApiError::throwError(17);
        }

        $startTime = microtime(true);
        while (true) {
            // update project model
            $projectModel->exists();

            // check if build started
            if ($projectModel->buildState == 2 && $this->ignoreStart == 0) {
                $response = new ApiResponse(array(
                    "status" => "started"
                ));
                $response->send();
            }

            // check if build failed
            if ($projectModel->buildState == 3) {
                $projectModel->setBuildState(0);
                $response = new ApiResponse(array(
                    "status" => "failed",
                    "warnings" => json_decode($projectModel->buildWarnings)
                ));
                $response->send();
            }

            // check if build succeeded
            if ($projectModel->buildState == 4) {
                $projectModel->setBuildState(0);
                $response = new ApiResponse(array(
                    "status" => "succeeded",
                    "warnings" => json_decode($projectModel->buildWarnings)
                ));
                $response->send();
            }

            // check time
            if (microtime(true) - $startTime > 25) {
                $response = new ApiResponse(array(
                    "status" => $projectModel->buildState == 2 ? "building" : "waiting"
                ));
                $response->send();
            }

            // check if connection was aborted
            echo " ";
            flush();
            if (connection_aborted() != 0) {
                break;
            }

            // sleep before continuing
            usleep(500000);
        }
    }
}