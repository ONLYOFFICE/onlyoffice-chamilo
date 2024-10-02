<?php
/**
 * (c) Copyright Ascensio System SIA 2024.
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

require_once __DIR__.'/../../../main/inc/global.inc.php';

class TemplateManager
{
    /**
     * Mapping local path to templates.
     *
     * @var array
     */
    private static $localPath = [
        'ar' => 'ar',
        'bg' => 'bg',
        'cs' => 'cs',
        'de' => 'de',
        'default' => 'default',
        'el' => 'el',
        'en' => 'en',
        'en-GB' => 'en-GB',
        'es' => 'es',
        'eu' => 'eu',
        'fr' => 'fr',
        'gl' => 'gl',
        'it' => 'it',
        'ja' => 'ja',
        'ko' => 'ko',
        'lv' => 'lv',
        'nl' => 'nl',
        'ms' => 'ms',
        'pl' => 'pl',
        'pt' => 'pt',
        'pt-BR' => 'pt-BR',
        'ru' => 'ru',
        'sk' => 'sk',
        'sr' => 'sr',
        'sr-Cyrl-RS' => 'sr-Cyrl-RS',
        'sv' => 'sv',
        'tr' => 'tr',
        'uk' => 'uk',
        'vi' => 'vi',
        'zh' => 'zh',
        'zh-TW' => 'zh-TW',
    ];

    /**
     * Return path to template new file.
     */
    public static function getEmptyTemplate($fileExtension): string
    {
        $langInfo = LangManager::getLangUser();
        $lang = $langInfo['isocode'];
        if (!array_key_exists($lang, self::$localPath)) {
            $lang = 'default';
        }
        $templateFolder = api_get_path(SYS_PLUGIN_PATH).'onlyoffice/assets/'.self::$localPath[$lang];

        return $templateFolder.'/'.ltrim($fileExtension, '.').'.zip';
    }
}
