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

use ChamiloSession as Session;

$userId = api_get_user_id();

$title = $_POST["title"];
$url = $_POST["url"];

$folderId = !empty($_POST["folderId"]) ? $_POST["folderId"] : 0;
$sessionId = !empty($_POST["sessionId"]) ? $_POST["sessionId"] : 0;
$courseId = !empty($_POST["courseId"]) ? $_POST["courseId"] : 0;
$groupId = !empty($_POST["groupId"]) ? $_POST["groupId"] : 0;

$courseInfo = api_get_course_info_by_id($courseId);
$courseCode = $courseInfo["code"];

$isMyDir = false;
if (!empty($folderId)) {
    $folderInfo = DocumentManager::get_document_data_by_id(
        $folderId,
        $courseCode,
        true,
        $sessionId
    );
    $isMyDir = DocumentManager::is_my_shared_folder(
        $userId,
        $folderInfo["absolute_path"],
        $sessionId
    );
}
$groupRights = Session::read('group_member_with_upload_rights');
$isAllowToEdit = api_is_allowed_to_edit(true, true);
if (!($isAllowToEdit || $isMyDir || $groupRights)) {
    echo json_encode(["error" => "Not permitted"]);
    return;
}

$fileExt = strtolower(pathinfo($title, PATHINFO_EXTENSION));
$baseName = strtolower(pathinfo($title, PATHINFO_FILENAME));

$fileNamePrefix = DocumentManager::getDocumentSuffix($courseInfo, $sessionId, $groupId);
$fileName = preg_replace('/\.\./', '', $baseName) . $fileNamePrefix . "." . $fileExt;
$groupInfo = GroupManager::get_group_properties($groupId);

$folderPath = '';
$fileRelatedPath = "/";
if (!empty($folderId)) {
    $document_data = DocumentManager::get_document_data_by_id(
        $folderId,
        $courseCode,
        true,
        $sessionId
    );
    $folderPath = $document_data["absolute_path"];
    $fileRelatedPath = $fileRelatedPath . substr($document_data["absolute_path_from_document"], 10) . "/" . $fileName;
} else {
    $folderPath = api_get_path(SYS_COURSE_PATH) . api_get_course_path($courseCode) . "/document";
    if (!empty($groupId)) {
        $folderPath = $folderPath . "/" . $groupInfo["directory"];
        $fileRelatedPath = $groupInfo["directory"] . "/";
    }
    $fileRelatedPath = $fileRelatedPath . $fileName;
}
$filePath = $folderPath . "/" . $fileName;

if (file_exists($filePath)) {
    echo json_encode(["error" => "File is exist"]);
    return;
}

if ($fp = @fopen($filePath, "w")) {
    $content = file_get_contents($url);
    fputs($fp, $content);
    fclose($fp);

    chmod($filePath, api_get_permissions_for_new_files());

    $documentId = add_document(
        $courseInfo,
        $fileRelatedPath,
        "file",
        filesize($filePath),
        $title,
        null,
        false
    );
    if ($documentId) {
        api_item_property_update(
            $courseInfo,
           TOOL_DOCUMENT,
            $documentId,
            "DocumentAdded",
            $userId,
            $groupInfo,
            null,
            null,
            null,
            $sessionId
        );
    }
} else {
    echo json_encode(["error" => "File is't created"]);
    return;
}

echo json_encode(["success" => "File is created"]);