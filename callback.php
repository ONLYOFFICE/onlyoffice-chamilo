<?php

require_once __DIR__.'/../../main/inc/global.inc.php';

use ChamiloSession as Session;

/**
 * Status of the document
 */
const TrackerStatus_Editing = 1;
const TrackerStatus_MustSave = 2;
const TrackerStatus_Corrupted = 3;
const TrackerStatus_Closed = 4;
const TrackerStatus_ForceSave = 6;
const TrackerStatus_CorruptedForceSave = 7;

if (isset($_GET["hash"]) && !empty($_GET["hash"])) {
    $response_array;
    @header( 'Content-Type: application/json; charset==utf-8');
    @header( 'X-Robots-Tag: noindex' );
    @header( 'X-Content-Type-Options: nosniff' );

    list ($hashData, $error) = Crypt::ReadHash($_GET["hash"]);
    if ($hashData === null) {
        $response_array["status"] = "error";
        $response_array["error"] = $error;
        die(json_encode($response_array));
    }

    $type = $hashData->type;
    $courseId = $hashData->courseId;
    $userId = $hashData->userId;
    $docId = $hashData->docId;
    $groupId = $hashData->groupId;
    $sessionId = $hashData->sessionId;

    $courseInfo = api_get_course_info_by_id($courseId);
    $courseCode = $courseInfo["code"];

    if (!empty($userId)) {
        $userInfo = api_get_user_info($userId);
    } else {
        $result["error"] = "User not found";
        die (json_encode($result));
    }

    if (api_is_anonymous()) {
        $loggedUser = [
            "user_id" => $userInfo["id"],
            "status" => $userInfo["status"],
            "uidReset" => true,
        ];

        Session::write("_user", $loggedUser);
        Login::init_user($loggedUser["user_id"], true);
    } else {
        $userId = api_get_user_id();
    }

    switch($type) {
        case "track":
            $response_array = track();
            die (json_encode($response_array));
        case "download":
            $response_array = download();
            die (json_encode($response_array));
        default:
            $response_array["status"] = "error";
            $response_array["error"] = "404 Method not found";
            die(json_encode($response_array));
    }
}

/**
 * Handle request from the document server with the document status information
 */
function track() {
    $result = [];

    global $courseCode;
    global $userId;
    global $docId;
    global $groupId;
    global $sessionId;
    global $courseInfo;

    if (($body_stream = file_get_contents("php://input")) === false) {
        $result["error"] = "Bad Request";
        return $result;
    }

    $data = json_decode($body_stream, true);

    if ($data === null) {
        $result["error"] = "Bad Response";
        return $result;
    }

    $status = $data["status"];

    $track_result = 1;
    switch ($status) {
        case TrackerStatus_MustSave:
        case TrackerStatus_Corrupted:

            $downloadUri = $data["url"];

            if (!empty($docId) && !empty($courseCode)) {
                $docInfo = DocumentManager::get_document_data_by_id($docId, $courseCode, false, $sessionId);

                if ($docInfo === false) {
                    $result["error"] = "File not found";
                    return $result;
                }

                $filePath = $docInfo["absolute_path"];
            } else {
                $result["error"] = "Bad Request";
                return $result;
            }

            list ($isAllowToEdit, $isMyDir, $isGroupAccess) = getPermissions($docInfo, $userId, $courseCode, $groupId, $sessionId);

            if (($new_data = file_get_contents($downloadUri)) === false) {
                break;
            }

            if ($isAllowToEdit || $isMyDir || $isGroupAccess) {
                $groupInfo = GroupManager::get_group_properties($groupId);

                if ($fp = @fopen($filePath, "w")) {
                    fputs($fp, $new_data);
                    fclose($fp);
                    api_item_property_update($courseInfo,
                                                TOOL_DOCUMENT,
                                                $docId,
                                                "DocumentUpdated",
                                                $userId,
                                                $groupInfo,
                                                null,
                                                null,
                                                null,
                                                $sessionId);
                    update_existing_document($courseInfo,
                                                $docId,
                                                filesize($filePath),
                                                false);
                    $track_result = 0;
                    break;
                }
            }

        case TrackerStatus_Editing:
        case TrackerStatus_Closed:

            $track_result = 0;
            break;
    }

    $result["error"] = $track_result;
    return $result;
}

/**
 * Downloading file by the document service
 */
function download() {
    global $courseCode;
    global $userId;
    global $docId;
    global $groupId;
    global $sessionId;
    global $courseInfo;

    if (!empty($docId) && !empty($courseCode)) {
        $docInfo = DocumentManager::get_document_data_by_id($docId, $courseCode, false, $sessionId);

        if ($docInfo === false) {
            $result["error"] = "File not found";
            return $result;
        }

        $filePath = $docInfo["absolute_path"];
    } else {
        $result["error"] = "File not found";
        return $result;
    }

    @header("Content-Type: application/octet-stream");
    @header("Content-Disposition: attachment; filename=" . $docInfo["title"]);

    readfile($filePath);
}

/**
 * Method checks access rights to document and returns permissions
 * 
 * @param array $docInfo - identifier of document
 * @param int $userId - identifier of user
 * @param string $courseCode - identifier of course
 * @param int $groupId - identifier of group or null if file out of group
 * @param int $sessionId - identifier of session
 * 
 * @return array
 */
function getPermissions($docInfo, $userId, $courseCode, $groupId, $sessionId) {
    $isAllowToEdit = api_is_allowed_to_edit(true, true);
    $isMyDir = DocumentManager::is_my_shared_folder($userId, $docInfo["absolute_parent_path"], $sessionId);

    $isGroupAccess = false;
    if (!empty($groupId)) {
        $courseInfo = api_get_course_info($courseCode);
        Session::write("_real_cid", $courseInfo["real_id"]);
        $groupProperties = GroupManager::get_group_properties($groupId);
        $docInfoGroup = api_get_item_property_info($courseInfo["real_id"], "document", $docInfo["id"], $sessionId);
        $isGroupAccess = GroupManager::allowUploadEditDocument($userId, $courseCode, $groupProperties, $docInfoGroup);
    }

    return [$isAllowToEdit, $isMyDir, $isGroupAccess];
}