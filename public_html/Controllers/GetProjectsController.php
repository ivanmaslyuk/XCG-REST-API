<?php


class GetProjectsController
{
    public $userId;

    public function __construct() {

    }

    public function checkInput() {

    }

    public function handle() {
        $db = new Database();
        $conn = $db->getConnection();

        // get projects
        $proj = new ProjectModel($conn);
        $proj->userId = $this->userId;
        $projects = $proj->getAllNames();

        // send response
        $response = new ApiResponse($projects);
        $response->send();
    }
}