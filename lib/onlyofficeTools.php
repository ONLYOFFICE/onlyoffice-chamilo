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

class OnlyofficeTools {

    /**
     * Return button-link to onlyoffice editor for file
     *
     * @param array $document_data - document info
     *
     * @return Display
     */
    public static function getButtonEdit ($document_data) {

        $plugin = OnlyofficePlugin::create();

        $isEnable = $plugin->get("enable_onlyoffice_plugin") === "true";
        if (!$isEnable) {
            return;
        }

        $urlToEdit = api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/editor.php";

        $extension = strtolower(pathinfo($document_data["title"], PATHINFO_EXTENSION));

        $canEdit = in_array($extension, FileUtility::$can_edit_types) ? true : false;
        $canView = in_array($extension, FileUtility::$can_view_types) ? true : false;

        $groupId = api_get_group_id();
        if (!empty($groupId)) {
            $urlToEdit = $urlToEdit . "?groupId=" . $groupId . "&";
        } else {
            $urlToEdit = $urlToEdit . "?";
        }

        $documentId = $document_data["id"];
        $urlToEdit = $urlToEdit . "docId=" . $documentId;

        if ($canEdit || $canView) {
            return Display::url(Display::return_icon('../../plugin/onlyoffice/resources/onlyoffice_edit.png', $plugin->get_lang('openByOnlyoffice')), $urlToEdit);
        }
    }

    /**
     * Return button-link to onlyoffice create new
     *
     * @return Display
     */
    public static function getButtonCreateNew () {

        $plugin = OnlyofficePlugin::create();

        $isEnable = $plugin->get("enable_onlyoffice_plugin") === "true";
        if (!$isEnable) {
            return;
        }

        $courseId = api_get_course_int_id();
        $sessionId = api_get_session_id();
        $groupId = api_get_group_id();
        $userId = api_get_user_id();

        $urlToCreate = api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/create.php?folderId=" . (empty($_GET["id"])?'0':(int)$_GET["id"])
                                                        . "&courseId=" . $courseId 
                                                        . "&groupId=" . $groupId 
                                                        . "&sessionId=" . $sessionId
                                                        . "&userId=" . $userId;

        return Display::url(Display::return_icon("../../plugin/onlyoffice/resources/onlyoffice_create.png", $plugin->get_lang("createNew")), $urlToCreate);
    }
}