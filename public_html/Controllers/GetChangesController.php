<?php


class GetChangesController
{

    public $userId;

    private $buildId;

    public function __construct() {
        $this->buildId = filter_input(INPUT_GET, "build_id");
    }

    public function checkInput() {
        if ($this->buildId === null or $this->buildId == "") {
            ApiError::throwError(1);
        }
    }

    public function handle() {
        $db = new Database();
        $conn = $db->getConnection();

        // check if this user has access to the build
        $build = new ChangesModel($conn);
        $build->buildId = $this->buildId;
        $build->userId = $this->userId;
        if (!$build->exists()) {
            ApiError::throwError(NO_SUCH_BUILD);
        }

        // check if build is canceled
        if ($build->cancelled) {
            ApiError::throwError(BUILD_IS_CANCELED);
        }

        // generate json
        $data = array();
        if (!empty($build->edited)) $data["edited"] = $build->edited;
        if (!empty($build->created)) $data["created"] = $build->created;
        if (!empty($build->deleted)) $data["deleted"] = $build->deleted;

        // send response
        $response = json_encode(array(
            "response" => $data
        ));
        exit($response);
    }

}