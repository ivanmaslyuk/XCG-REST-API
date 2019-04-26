<?php

class ProjectStructureWizard {
    
    public function getPathsFromStructure($json) {
        $files = json_decode($json)["files"];
        if ($objects === NULL) {
            return null;
        }
        
        return $files;
    }
    
}