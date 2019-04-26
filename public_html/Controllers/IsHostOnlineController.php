<?php


class IsHostOnlineController
{
    public $userId;

    public function __construct() {

    }

    public function checkInput() {

    }

    public  function handle() {
        $db = new Database();
        $conn = $db->getConnection();

        // get user
        $userModel = new UserModel($conn);
        $userModel->id = $this->userId;
        $userModel->getById();

        // set online status to 0
        $userModel->onlineStatus = 0;
        $userModel->save();

        // wait for confirmation
        $start_time = microtime(true);
        while (true) {
            // update user model
            $userModel->getById();

            // check if online status was confirmed
            if ($userModel->onlineStatus == 1) {
                $response = new ApiResponse(true);
                $response->send();
            }

            // check time
            if (microtime(true) - $start_time > 25) {
                $userModel->onlineStatus = 1;
                $userModel->save();
                $response = new ApiResponse(false);
                $response->send();
            }

            // check if connection was aborted
            echo " ";
            flush();
            if (connection_aborted() != 0) {
                $userModel->onlineStatus = 1;
                $userModel->save();
                break;
            }

            // sleep before continuing
            usleep(500000);
        }
    }
}