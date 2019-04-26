<?php


class WaitForJobsController
{
    public $userId;

    private $duties;

    private $confirmOnline;
    private $build;
    private $sync;

    public function __construct() {
        $this->duties = filter_input(1, "duties");
    }

    public function checkInput() {
        if (!isset($this->duties) || $this->duties == "") {
            ApiError::throwError(1);
        }

        $duties = explode(",", $this->duties);
        $this->confirmOnline = in_array("confirm_online", $duties);
        $this->build = in_array("build", $duties);
        $this->sync = in_array("sync", $duties);

        if (!($this->build or $this->confirmOnline or $this->sync)) {
            ApiError::throwError(1);
        }
    }

    public function handle() {
        $db = new Database();
        $conn = $db->getConnection();

        $jobs = array();
        $startTime = microtime(true);
        while (true) {

            // get all projects
            $projectModel = new ProjectModel($conn);
            $projectModel->userId = $this->userId;
            $projects = $projectModel->getAll();

            foreach ($projects as $project) {
                // check build requests
                if ($this->build) {
                    if ($project->buildState == 1) {
                        array_push($jobs, array(
                            "job" => "build",
                            "project" => $project->name
                        ));
                        $project->setBuildState(2);
                    }
                }

                // check sync requests
                if ($this->sync) {
                    if ($project->syncState == 1) {
                        array_push($jobs, array(
                            "job" => "sync",
                            "project" => $project->name,
                            "build_id" => (int)$project->buildToSync
                        ));
                        $project->setSyncState(2);
                    }
                }
            }

            // check for online requests
            if ($this->confirmOnline) {
                $userModel = new UserModel($conn);
                $userModel->id = $this->userId;
                $userModel->getById();
                if ($userModel->onlineStatus == 0) {
                    $userModel->onlineStatus = 1;
                    $userModel->save();
                }
            }

            // return result
            if (!empty($jobs)) {
                $response = new ApiResponse($jobs);
                $response->send();
            }

            // check time
            if (microtime(true) - $startTime > 25) {
                $response = new ApiResponse(json_decode("{}"));
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