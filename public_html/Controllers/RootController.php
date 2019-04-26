<?php

class RootController {
    
    public function __construct() {
        
    }
    
    public function delegateRequest() {
        $method = $this->getMethodName();
        $userId = $this->getUserId();
        
        switch ($method) {
        case "setChanges":
            $scc = new SetChangesController();
            $scc->userId = $userId;
            $scc->save();
            break;
        case "cancelChanges":
            $cucc = new CancelChangesController();
            $cucc->userId = $userId;
            $cucc->cancel();
            break;
        case "uploadFile":
            $ufc = new UploadFileController();
            $ufc->userId = $userId;
            $ufc->checkInput();
            $ufc->save();
            break;
        case "setBuildReady":
            $sbrc = new SetBuildReadyController();
            $sbrc->userId = $userId;
            $sbrc->checkInput();
            $sbrc->setReady();
            break;
        case "downloadFile":
            $dfc = new DownloadFileController();
            $dfc->userId = $userId;
            $dfc->checkInput();
            $dfc->send();
            break;
        case "addProject":
            $apc = new AddProjectController();
            $apc->userId = $userId;
            $apc->checkInput();
            $apc->saveProject();
            break;
        case "removeProject":
            $rpc = new RemoveProjectController();
            $rpc->userId = $userId;
            $rpc->checkInput();
            $rpc->removeProject();
            break;
        case "getChanges":
            $gcc = new GetChangesController();
            $gcc->userId = $userId;
            $gcc->checkInput();
            $gcc->handle();
            break;
        case "getProjects":
            $gpc = new GetProjectsController();
            $gpc->userId = $userId;
            $gpc->checkInput();
            $gpc->handle();
            break;
        case "waitForJobs":
            $wfjc = new WaitForJobsController();
            $wfjc->userId = $userId;
            $wfjc->checkInput();
            $wfjc->handle();
            break;
        case "isHostOnline":
            $ihoc = new IsHostOnlineController();
            $ihoc->userId = $userId;
            $ihoc->checkInput();
            $ihoc->handle();
            break;
        case "setSyncSuccessful":
            $sssc = new SetSyncSuccessfulController();
            $sssc->userId = $userId;
            $sssc->checkInput();
            $sssc->handle();
            break;
        case "setBuildSuccessful":
            $sbsc = new SetBuildSuccessfulController();
            $sbsc->userId = $userId;
            $sbsc->checkInput();
            $sbsc->handle();
            break;
        case "requestBuild":
            $rbc = new RequestBuildController();
            $rbc->userId = $userId;
            $rbc->checkInput();
            $rbc->handle();
            break;
        case "waitForSyncFinished":
            $wfsfc = new WaitForSyncFinishedController();
            $wfsfc->userId = $userId;
            $wfsfc->checkInput();
            $wfsfc->handle();
        case "waitForBuildFinished":
            $wfbfc = new WaitForBuildFinishedController();
            $wfbfc->userId = $userId;
            $wfbfc->checkInput();
            $wfbfc->handle();
            break;
        default:
            ApiError::throwError(2);
        }
    }
    
    private function getMethodName() {
        $requestUrl = $_SERVER['REQUEST_URI'];
        return explode("?", substr($requestUrl, 1))[0];
    }
    
    private function getUserId() {
        $accessToken = filter_input(INPUT_GET, "access_token");
        return 1;
    }
}