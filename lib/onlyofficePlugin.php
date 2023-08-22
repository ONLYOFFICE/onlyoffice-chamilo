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

/**
 * Plugin class for the Onlyoffice plugin.
 *
 * @author Asensio System SIA
 */
class OnlyofficePlugin extends Plugin implements HookPluginInterface
{

    /**
     * OnlyofficePlugin name.
     */
    private $pluginName = "onlyoffice";

    /**
     * OnlyofficePlugin constructor.
     */
    protected function __construct()
    {
        parent::__construct(
            "1.2.0",
            "Asensio System SIA",
            [
                "enable_onlyoffice_plugin" => "boolean",
                "document_server_url" => "text",
                "jwt_secret" => "text",
                "connect_demo" => "checkbox"
            ]
        );
        $this->checkDemo();
    }

    /**
     * Create OnlyofficePlugin object
     */
    public static function create(): OnlyofficePlugin
    {
        static $result = null;

        return $result ?: $result = new self();
    }

    /**
     * This method install the plugin tables.
     */
    public function install()
    {
        $this->installHook();
    }

    /**
     * This method drops the plugin tables.
     */
    public function uninstall()
    {
        $this->uninstallHook();
    }

    /**
     * Install the "create" hooks.
     */
    public function installHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->attach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->attach($actionObserver);

        $viewObserver = OnlyofficeItemViewObserver::create();
        HookDocumentItemView::create()->attach($viewObserver);
    }

    /**
     * Uninstall the "create" hooks.
     */
    public function uninstallHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->detach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->detach($actionObserver);

        $viewObserver = OnlyofficeItemViewObserver::create();
        HookDocumentItemView::create()->detach($viewObserver);
    }

    /**
     * Get status of demo server
     *
     * @return bool
     */
    public function useDemo() {
        return $this->getDemoData()["enabled"] === true;
    }

    /**
     * Get demo data
     *
     * @return array
     */
    public function getDemoData() {
        $data = api_get_setting('onlyoffice_connect_demo_data')[0];

        if (empty($data)) {
            $data = [
                "available" => true,
                "enabled" => false
            ];
            api_add_setting(json_encode($data), 'onlyoffice_connect_demo_data', null, 'setting', 'Plugins');
            return $data;
        }
        $data = json_decode($data, true);
        $overdue = isset($data["start"]) ? $data["start"]: 0;
        $overdue += 24*60*60*AppConfig::GetDemoParams()["TRIAL"];
        if ($overdue > time()) {
            $data["available"] = true;
            $data["enabled"] = $data["enabled"] === true;
        } else {
            $data["available"] = false;
            $data["enabled"] = false;
        }
        return $data;
    }

    /**
     * Switch on demo server
     *
     * @param bool $value - select demo
     *
     * @return bool
     */
    public function selectDemo($value) {
        $data = $this->getDemoData();

        if ($value === true && !$data["available"]) {
            return false;
        }

        $data["enabled"] = $value === true;

        if (!isset($data["start"])) {
            $data["start"] = time();
        }
        api_set_setting('onlyoffice_connect_demo_data', json_encode($data));
        return true;
    }

    /**
     * Check availability of demo trial
     *
     * @return void
     */
    public function checkDemo() {
        if ((bool)$this->get("connect_demo") && $this->getDemoData()["available"] === false) {
            api_set_setting('onlyoffice_connect_demo', null);
            if ($_SERVER['HTTP_REFERER'] === $this->getConfigLink()) {
                header('Location: '.$this->getConfigLink());
            }
        }
    }

    /**
     * Get the document server url
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getDocumentServerUrl($origin = false) 
    {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["ADDR"];
        }

        $url = $this->get("document_server_url");
        if ($url !== null && $url !== "/") {
            $url = rtrim($url, "/");
            if (strlen($url) > 0) {
                $url = $url . "/";
            }
        }
        return $url;
    }

    /**
     * Get the document service secret key from the application configuration
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getDocumentServerSecret($origin = false) {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["SECRET"];
        }
        return $this->get("jwt_secret");
    }

    /**
     * Get the jwt header setting
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getJwtHeader($origin = false) {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["HEADER"];
        }
        return AppConfig::JwtHeader();
    }

    /**
     * Get link to plugin settings
     *
     * @return string
     */
    public function getConfigLink() {
        return api_get_path(WEB_PATH)."main/admin/configure_plugin.php?name=".$this->pluginName;
    }
}
