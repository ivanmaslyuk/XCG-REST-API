<?php

define("NO_SUCH_BUILD", 11);
define("NO_SUCH_PROJECT", 5);
define("NO_SUCH_FILE", 3);
define("BUILD_IS_CANCELED", 13);

class ApiError {
    private static function getErrorDescription($code) {
        switch ($code) {
            case 1:
                return "Not all necessary arguments were provided.";
            case 2:
                return "Access denied.";
            case 3:
                return "No such file.";
            case 4:
                return "Project with this name already exists.";
            case 5:
                return "No such project.";
            case 6:
                return "The request body does not contain valid JSON.";
            case 7:
                return "No changes were passed.";
            case 8:
                return "It is illegal to create a new changes entry if there are still unfinished ones. " .
                    "Cancel them before creating a new entry.";
            case 9:
                return "It is not possible to download this file. " .
                    "Files are deleted from the server when the Mac client reports them as synced.";
            case 10:
                return "Executable files are not allowed";
            case 11:
                return  "No such build.";
            case 12:
                return  "File was already uploaded.";
            case 13:
                return  "Changes with this build_id had been cancelled.";
            case 14:
                return  "Not all files have been uploaded.";
            case 15:
                return "Can't add new changes while another build is being synced.";
            case 16:
                return "Sync had not been requested.";
            case 17:
                return "Build had not been requested.";
            case 18:
                return "Cannot remove project when building or syncing is in progress.";
            case 19:
                return "Cannot request build when syncing is requested or in progress.";
            default:
                return "Unknown error.";
        }
    }
    
    public static function throwError($code) {
        $description = ApiError::getErrorDescription($code);
        $error = array("error" => $code, "error_description" => $description);
        exit(json_encode($error));
    }
}