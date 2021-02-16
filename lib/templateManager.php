<?php

require_once __DIR__.'/../../../main/inc/global.inc.php';

class TemplateManager {

    /**
     * Return path to template new file
     * 
     * @param string $extension - extension of file
     * 
     * @return string
     */
    public static function getEmptyTemplate($extension) {
        $langId = SubLanguageManager::get_platform_language_id();
        $lang = api_get_language_info($langId);
        $templateFolder = api_get_path(SYS_PLUGIN_PATH) . "onlyoffice/assets/" . $lang["isocode"];
        if (file_exists($templateFolder)) {
            return $templateFolder . "/new." . $extension;
        }

        return api_get_path(SYS_PLUGIN_PATH) . "onlyoffice/assets/en/new." . $extension;
    }
}