<?php

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

        $isEnable = $plugin->get("enableOnlyofficePlugin") === 'true';
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

        $isEnable = $plugin->get("enableOnlyofficePlugin") === 'true';
        if (!$isEnable) {
            return;
        }

        $courseId = api_get_course_int_id();
        $sessionId = api_get_session_id();
        $groupId = api_get_group_id();
        $userId = api_get_user_id();

        $urlToCreate = api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/create.php?folderId=" . $_GET["id"] 
                                                        . "&courseId=" . $courseId 
                                                        . "&groupId=" . $groupId 
                                                        . "&sessionId=" . $sessionId
                                                        . "&userId=" . $userId;

        return Display::url(Display::return_icon('../../plugin/onlyoffice/resources/onlyoffice_create.png', $plugin->get_lang('createNew')), $urlToCreate);
    }
}