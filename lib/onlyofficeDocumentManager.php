<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2023
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
use Onlyoffice\DocsIntegrationSdk\Manager\Document\DocumentManager;

class OnlyofficeDocumentManager extends DocumentManager
{
    private $docInfo;

    public function __construct($settingsManager, array $docInfo, $formats = null, $systemLangCode = "en")
    {
        parent::__construct($settingsManager, $formats, $systemLangCode);
        $this->docInfo = $docInfo;
    }
    public function getDocumentKey(string $fileId, $courseCode, bool $embedded = false)
    {
        if (!isset($this->docInfo["absolute_path"])) {
            return null;
        }
        $mtime = filemtime($this->docInfo["absolute_path"]);
        $key = $mtime . $courseCode . $fileId;
        return self::generateRevisionId($key);
    }
    public function getDocumentName(string $fileId = "")
    {
        return $this->docInfo["title"];
    }
    public static function getLangMapping()
    {

    }
    public function getFileUrl(string $fileId)
    {

        $data = [
            "type" => "download",
            "courseId" => api_get_course_int_id(),
            "userId" => api_get_user_id(),
            "docId" => $fileId,
            "sessionId" => api_get_session_id()
        ];

        if (!empty($this->getGroupId())) {
            $data["groupId"] = $groupId;
        }
        $jwtManager = new OnlyofficeJwtManager($this->settingsManager);
        $hashUrl = $jwtManager->getHash($data);
        return api_get_path(WEB_PLUGIN_PATH) . $this->settingsManager->plugin->getPluginName() . "/" . "callback.php?hash=" . $hashUrl;
    }

    public function getGroupId()
    {
        $groupId = isset($_GET["groupId"]) && !empty($_GET["groupId"]) ? $_GET["groupId"] : null;
        return $groupId;
    }

    public function getCallbackUrl(string $fileId)
    {
        $url = "";

        $data = [
            "type" => "track",
            "courseId" => api_get_course_int_id(),
            "userId" => api_get_user_id(),
            "docId" => $fileId,
            "sessionId" => api_get_session_id()
        ];
    
        if (!empty($this->getGroupId())) {
            $data["groupId"] = $groupId;
        }
    
        $jwtManager = new OnlyofficeJwtManager($this->settingsManager);
        $hashUrl = $jwtManager->getHash($data);
        return $url . api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/callback.php?hash=" . $hashUrl;
    }
    public function getGobackUrl(string $fileId)
    {
        return api_get_path(WEB_CODE_PATH)."document/document.php"
                                            . "?cidReq=" . Security::remove_XSS(api_get_course_int_id())
                                            . "&id_session=" . Security::remove_XSS(api_get_session_id())
                                            . "&gidReq=" . Security::remove_XSS($this->getGroupId())
                                            . "&id=" . Security::remove_XSS($this->docInfo["parent_id"]);
    }
    public function getCreateUrl(string $fileId)
    {

    }

    /**
     * Get the value of docInfo
     */ 
    public function getDocInfo($elem = null)
    {
        if (empty($elem)) {
            return $this->docInfo;
        } else {
            if (isset($this->docInfo[$elem]))
            {
                return $this->docInfo[$elem];
            }
            return [];
        }
    }

    /**
     * Set the value of docInfo
     */ 
    public function setDocInfo($docInfo)
    {
        $this->docInfo = $docInfo;
    }
}