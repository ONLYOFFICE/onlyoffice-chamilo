<?php
/**
 * (c) Copyright Ascensio System SIA 2023.
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
 */
use Onlyoffice\DocsIntegrationSdk\Service\Request\RequestService;

class OnlyofficeAppRequests extends RequestService
{
    /**
     * File url to test convert service.
     *
     * @var string
     */
    private $convertFileUrl;
    private $convertFilePath;

    public function __construct($settingsManager, $httpClient, $jwtManager)
    {
        parent::__construct($settingsManager, $httpClient, $jwtManager);
        $tempFile = self::createTempFile();
        $this->convertFileUrl = $tempFile['fileUrl'];
        $this->convertFilePath = $tempFile['filePath'];
    }

    public function __destruct()
    {
        unlink($this->convertFilePath);
    }

    /**
     * Create temporary file for convert service testing.
     *
     * @return array
     */
    private function createTempFile()
    {
        $fileUrl = null;
        $fileName = 'convert.docx';
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $baseName = strtolower(pathinfo($fileName, PATHINFO_FILENAME));
        $templatePath = TemplateManager::getEmptyTemplate($fileExt);
        $folderPath = api_get_path(SYS_PLUGIN_PATH).$this->settingsManager->plugin->getPluginName();
        $filePath = $folderPath.'/'.$fileName;

        if ($fp = @fopen($filePath, 'w')) {
            $content = file_get_contents($templatePath);
            fputs($fp, $content);
            fclose($fp);
            chmod($filePath, api_get_permissions_for_new_files());
            $fileUrl = api_get_path(WEB_PLUGIN_PATH).$this->settingsManager->plugin->getPluginName().'/'.$fileName;
        }

        return [
            'fileUrl' => $fileUrl,
            'filePath' => $filePath,
        ];
    }

    public function getFileUrlForConvert()
    {
        return $this->convertFileUrl;
    }
}
