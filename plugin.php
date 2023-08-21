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

/**
 * @author Asensio System SIA
 */

$plugin = OnlyofficePlugin::create();
$plugin_info = $plugin->get_info();
if ($plugin_info['settings_form']->validate()) {
    $result = $plugin_info['settings_form']->getSubmitValues();
    if (!$plugin->selectDemo((bool)$result['connect_demo'] === true)) {
            $error = $plugin->get_lang('demoPeriodIsOver');
            Display::addFlash(
                Display::return_message(
                    $error,
                    'error'
                )
            );
            $url = api_get_path(WEB_PATH)."main/admin/configure_plugin.php?name=onlyoffice";
            header('Location: '.$url);
            exit;
    }
}