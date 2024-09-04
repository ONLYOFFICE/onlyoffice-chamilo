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

require_once __DIR__.'/../../main/inc/global.inc.php';

use \Firebase\JWT\JWT;

const USER_AGENT_MOBILE = "/android|avantgo|playbook|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i";

$plugin = OnlyofficePlugin::create();

$isEnable = $plugin->get("enable_onlyoffice_plugin") === 'true';
if (!$isEnable) {
    die ("Document server isn't enabled");
    return;
}

$appSettings = new OnlyofficeAppsettings($plugin);
$documentServerUrl = $appSettings->getDocumentServerUrl();
if (empty($documentServerUrl)) {
    die ("Document server isn't configured");
    return;
}

$config = [];

$docApiUrl = $appSettings->getDocumentServerApiUrl();

$docId = $_GET["docId"];
$groupId = isset($_GET["groupId"]) && !empty($_GET["groupId"]) ? $_GET["groupId"] : null;

$userId = api_get_user_id();

$userInfo = api_get_user_info($userId);

$sessionId = api_get_session_id();
$courseId = api_get_course_int_id();
$courseInfo = api_get_course_info();
if (empty($courseInfo)) {
    api_not_allowed(true);
}
$courseCode = $courseInfo["code"];
$docInfo = DocumentManager::get_document_data_by_id($docId, $courseCode, false, $sessionId);
$langInfo = LangManager::getLangUser();
$jwtManager = new OnlyofficeJwtManager($appSettings);
$documentManager = new OnlyofficeDocumentManager($appSettings, $docInfo);


$extension = $documentManager->getExt($documentManager->getDocInfo("title"));
$docType = $documentManager->getDocType($extension);
$key = $documentManager->getDocumentKey($docId, $courseCode);
$fileUrl = $documentManager->getFileUrl($docId);

if (!empty($appSettings->getStorageUrl())) {
    $fileUrl = str_replace(api_get_path(WEB_PATH), $appSettings->getStorageUrl(), $fileUrl);
}


$configService = new OnlyofficeConfigService($appSettings, $jwtManager, $documentManager);

$editorsMode = $configService->getEditorsMode();

$config = $configService->createConfig($docId, $editorsMode, $_SERVER["HTTP_USER_AGENT"]);
$config = json_decode(json_encode($config), true);

$userAgent = $_SERVER["HTTP_USER_AGENT"];

$isMobileAgent = preg_match(USER_AGENT_MOBILE, $userAgent);

$isAllowToEdit = api_is_allowed_to_edit(true, true);
$isMyDir = DocumentManager::is_my_shared_folder(
    $userId,
    $docInfo["absolute_parent_path"],
    $sessionId
);

$accessRights = $isAllowToEdit || $isMyDir || $isGroupAccess;
$canEdit = in_array($extension, FileUtility::$can_edit_types);
$isReadonly = $docInfo["readonly"];

$config["document"]["permissions"]["edit"] = $accessRights && !$isReadonly;


?>
<title>ONLYOFFICE</title>
<style>
    #app > iframe {
        height: calc(100% - 140px);
    }
    body {
        height: 100%;
    }
    .chatboxheadmain,
    .pull-right,
    .breadcrumb {
        display: none;
    }
</style>
<script type="text/javascript" src=<?php echo $docApiUrl?>></script>
<script type="text/javascript">
    var onAppReady = function () {
        innerAlert("Document editor ready");
    };

    var onRequestSaveAs = function (event) {
        var url = <?php echo json_encode(api_get_path(WEB_PLUGIN_PATH))?> + "onlyoffice/ajax/saveas.php";
        var folderId = <?php echo json_encode($docInfo["parent_id"])?>;
        var saveData = {
            title: event.data.title,
            url: event.data.url,
            folderId: folderId ? folderId : 0,
            sessionId: <?php echo json_encode($sessionId)?>,
            courseId: <?php echo json_encode($courseId)?>,
            groupId: <?php echo json_encode($groupId)?>
        };

        $.ajax(url, {
            method: "POST",
            data: JSON.stringify(saveData),
            processData: false,
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.error) {
                    console.error("Create error: ", response.error);
                }
            },
            error: function (e) {
                console.error("Create error: ", e);
            }
        });
    };

    var connectEditor = function () {
        var config = <?php echo json_encode($config)?>;
        var errorPage = <?php echo json_encode(api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/error.php")?>;

        var docsVersion = DocsAPI.DocEditor.version().split(".");
        if ((config.document.fileType === "docxf" || config.document.fileType === "oform")
            && docsVersion[0] < 7) {
            window.location.href = errorPage + "?status=" + 1;
            return;
        }
        if (docsVersion[0] < 6
            || docsVersion[0] == 6 && docsVersion[1] == 0) {
            window.location.href = errorPage + "?status=" + 2;
            return;
        }

        $("#cm-content")[0].remove(".container");
        $("#main").append('<div id="app-onlyoffice">' +
                            '<div id="app">' +
                                '<div id="iframeEditor">' +
                                '</div>' +
                            '</div>' +
                          '</div>');

        var isMobileAgent = <?php echo json_encode($isMobileAgent)?>;

        config.events = {
            "onAppReady": onAppReady,
            "onRequestSaveAs": onRequestSaveAs
        };

        docEditor = new DocsAPI.DocEditor("iframeEditor", config);

        $(".navbar").css({"margin-bottom": "0px"});
        $("body").css({"margin": "0 0 0px"});
        if (isMobileAgent) {
            var frameEditor = $("#app > iframe")[0];
            $(frameEditor).css({"height": "100%", "top": "0px"});
        }
    }

    if (window.addEventListener) {
        window.addEventListener("load", connectEditor);
    } else if (window.attachEvent) {
        window.attachEvent("load", connectEditor);
    }

</script>
<?php echo Display::display_header(); ?>
