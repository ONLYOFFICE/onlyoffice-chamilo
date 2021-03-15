<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2021
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
            return $templateFolder . "/" . ltrim($extension, ".") . ".zip";
        }

        return api_get_path(SYS_PLUGIN_PATH) . "onlyoffice/assets/en/" . ltrim($extension, ".") . ".zip";
    }
}