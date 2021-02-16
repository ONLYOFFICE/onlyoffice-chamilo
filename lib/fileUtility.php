<?php

require_once __DIR__.'/../../../main/inc/global.inc.php';

class FileUtility {
 
    /**
     * Application name
     */
    public const app_name = "onlyoffice";

    /**
     * Extensions of files that can edit
     * 
     * @var array
     */
    public static $can_edit_types = [
        "docx", 
        "xlsx", 
        "pptx", 
        "ppsx"
    ];

    /**
     * Extensions of files that can view
     * 
     * @var array
     */
    public static $can_view_types = [
        "docx", "xlsx", "pptx", "ppsx",
        "txt", "csv", "odt", "ods","odp",
        "doc", "xls", "ppt", "pps","epub",
        "rtf", "mht", "html", "htm","xps","pdf","djvu"
    ];

    /**
     * Extensions of text files
     * 
     * @var array
     */
    public static $text_doc = [
        "docx", "txt", "odt", "doc", "rtf", "html",
        "htm", "xps", "pdf", "djvu"
    ];

    /**
     * Extensions of presentation files
     * 
     * @var array
     */
    public static $presentation_doc  = [
        "pptx", "ppsx", "odp", "ppt", "pps"
    ];

    /**
     * Extensions of spreadsheet files
     * 
     * @var array
     */
    public static $spreadsheet_doc = [
        "xlsx", "csv", "ods", "xls"
    ];

    /**
     * Return file type by extension
     * 
     * @param string $extension - extension of file
     * 
     * @return string
     */
    public static function getDocType($extension) {
        if (in_array($extension, self::$text_doc)) {
            return "text";
        }
        if (in_array($extension, self::$presentation_doc)) {
            return "presentation";
        }
        if (in_array($extension, self::$spreadsheet_doc)) {
            return "spreadsheet";
        }

        return "";
    }

    /**
     * Return file extension by file type
     * 
     * @param string $type - type of file
     * 
     * @return string
     */
    public static function getDocExt($type) {
        if ($type === "text") {
            return "docx";
        }
        if ($type === "spreadsheet") {
            return "xlsx";
        }
        if ($type === "presentation") {
            return "pptx";
        }

        return "";
    }

    /**
     * Return file url for download
     * 
     * @param int $courseId - identifier of course
     * @param int $userId - identifier of user
     * @param int $docId - identifier of document
     * @param int $sessionId - identifier of session
     * @param int $groupId - identifier of group or null if file out of group
     * 
     * @return string
     */
    public static function getFileUrl($courseId, $userId, $docId, $sessionId, $groupId) {

        $data = [
            "type" => "download",
            "courseId" => $courseId,
            "userId" => $userId,
            "docId" => $docId,
            "sessionId" => $sessionId
        ];

        if (!empty($groupId)) {
            $data["groupId"] = $groupId;
        }

        $hashUrl = Crypt::GetHash($data);

        $url = api_get_path(WEB_PLUGIN_PATH) . self::app_name . "/" . "callback.php?hash=" . $hashUrl;

        return $url;
    }

    /**
     * Return file key
     * 
     * @param string $courseCode - identifier of course
     * @param int $userId - identifier of user
     * @param int $docId - identifier of document
     * @param int $sessionId - identifier of session
     * @param int $groupId - identifier of group or null if file out of group
     * 
     * @return string
     */
    public static function getKey($courseCode, $docId) {
        $docInfo = DocumentManager::get_document_data_by_id($docId, $courseCode);
        $mtime = filemtime($docInfo["absolute_path"]);

        $key = $mtime . $courseId . $docId;
        return self::GenerateRevisionId($key);
    }

    /**
     * Translation key to a supported form
     * 
     * @param string $expected_key - Expected key
     * 
     * @return string
     */
    public static function GenerateRevisionId($expected_key) {
        if (strlen($expected_key) > 20) $expected_key = crc32( $expected_key);
        $key = preg_replace("[^0-9-.a-zA-Z_=]", "_", $expected_key);
        $key = substr($key, 0, min(array(strlen($key), 20)));
        return $key;
    }
}